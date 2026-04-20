<?php
// app/Services/PdfGarantiaService.php

namespace App\Services;

use App\Models\GarantiaComponenteDef;
use App\Models\MarcaGarantiaConfig;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PdfGarantiaService
{
    // Prompt del sistema — copiado exactamente del contexto del sistema
    private const SYSTEM_PROMPT = <<<'PROMPT'
Eres un motor de interpretación de pólizas de garantía para un sistema ERP de gestión de bicicletas eléctricas.
Tu tarea es analizar el contenido de una póliza de garantía en texto plano y devolver una estructura JSON limpia.

Responde ÚNICAMENTE con JSON válido, sin texto adicional, sin explicaciones, sin markdown.

Estructura obligatoria:
{
  "marca": "string o null",
  "garantias": [
    {
      "componente": "string",
      "incluye": ["string"],
      "duracion_meses": number,
      "cobertura": "string o null"
    }
  ],
  "exclusiones": ["string"],
  "reglas_generales": ["string"]
}

Reglas:
- Todo en minúsculas, sin acentos, sin caracteres especiales
- Duración siempre en meses (1 año = 12)
- Si duración no es clara, omitir el componente
- Máximo 15 reglas_generales
PROMPT;

    public function procesarConIA(string $idMarcaGarantia): void
    {
        $config = MarcaGarantiaConfig::find($idMarcaGarantia);
        if (!$config || !$config->pdf_base64) return;

        $config->update(['estado_procesamiento' => 'procesando']);

        try {
            // 1. Extraer texto del PDF desde base64
            $textoExtraido = $this->extraerTextoPdf($config->pdf_base64);

            if (empty(trim($textoExtraido))) {
                throw new \Exception('No se pudo extraer texto del PDF.');
            }

            // 2. Enviar a IA (Groq)
            $jsonIA = $this->enviarAGroq($textoExtraido);

            // 3. Validar estructura mínima
            $validado = $this->validarJson($jsonIA);

            // 4. Guardar raw — el admin lo revisa antes de persistir como defs
            $config->update([
                'ia_raw_json'         => $validado,
                'ia_procesado_at'     => now(),
                'estado_procesamiento' => 'completado',
            ]);

        } catch (\Exception $e) {
            Log::error('Error procesando PDF con IA', [
                'id_marca_garantia' => $idMarcaGarantia,
                'error'             => $e->getMessage(),
            ]);

            $config->update(['estado_procesamiento' => 'error']);
        }
    }

    // ─── Extracción de texto del PDF (sin filesystem) ────────────────────
    // Usa pdfparser via Composer — texto plano, sin coordenadas
    private function extraerTextoPdf(string $base64): string
    {
        // Decodificar temporalmente en memoria
        $binario  = base64_decode($base64);
        $tmpPath  = tempnam(sys_get_temp_dir(), 'garantia_pdf_');

        file_put_contents($tmpPath, $binario);

        try {
            $parser  = new \Smalot\PdfParser\Parser();
            $pdf     = $parser->parseFile($tmpPath);
            $texto   = $pdf->getText();
        } finally {
            @unlink($tmpPath); // Siempre limpiar
        }

        return $texto;
    }

    // ─── Envío a Groq ─────────────────────────────────────────────────────
    private function enviarAGroq(string $textoPdf): array
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . config('services.groq.key'),
            'Content-Type'  => 'application/json',
        ])->timeout(60)->post('https://api.groq.com/openai/v1/chat/completions', [
            'model'       => 'llama-3.3-70b-versatile',
            'temperature' => 0.1, // Baja temperatura = más determinista para datos estructurados
            'messages'    => [
                ['role' => 'system', 'content' => self::SYSTEM_PROMPT],
                ['role' => 'user',   'content' => $textoPdf],
            ],
        ]);

        if (!$response->successful()) {
            throw new \Exception('Groq API error: ' . $response->status());
        }

        $content = $response->json('choices.0.message.content', '');

        // Limpiar posibles markdown fences que la IA agregue a pesar del prompt
        $content = preg_replace('/^```json\s*/i', '', trim($content));
        $content = preg_replace('/\s*```$/', '', $content);

        $decoded = json_decode($content, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception('La IA devolvió JSON inválido: ' . json_last_error_msg());
        }

        return $decoded;
    }

    // ─── Validación mínima del JSON de IA ────────────────────────────────
    // No confiamos ciegamente — validamos estructura antes de guardar
    private function validarJson(array $data): array
    {
        $garantiasValidas = [];

        foreach ($data['garantias'] ?? [] as $g) {
            // Requeridos: componente, duracion_meses, incluye
            if (
                empty($g['componente']) ||
                !isset($g['duracion_meses']) ||
                !is_numeric($g['duracion_meses']) ||
                $g['duracion_meses'] < 0 ||
                !is_array($g['incluye'] ?? null)
            ) {
                continue; // Omitir componentes inválidos
            }

            $garantiasValidas[] = [
                'componente'     => substr(strtolower(trim($g['componente'])), 0, 60),
                'incluye'        => array_values(array_filter(
                    array_map(fn($i) => substr(strtolower(trim($i)), 0, 100), $g['incluye']),
                    fn($i) => !empty($i)
                )),
                'duracion_meses' => (int) $g['duracion_meses'],
                'cobertura'      => isset($g['cobertura'])
                    ? substr(strtolower(trim($g['cobertura'])), 0, 255)
                    : null,
            ];
        }

        return [
            'marca'            => isset($data['marca']) ? substr(strtolower(trim($data['marca'])), 0, 100) : null,
            'garantias'        => $garantiasValidas,
            'exclusiones'      => array_values(array_filter(
                array_map(fn($e) => substr(strtolower(trim($e)), 0, 100), $data['exclusiones'] ?? []),
                fn($e) => !empty($e)
            )),
            'reglas_generales' => array_slice(
                array_values(array_filter(
                    array_map(fn($r) => substr(trim($r), 0, 255), $data['reglas_generales'] ?? []),
                    fn($r) => !empty($r)
                )),
                0, 15 // Máximo 15
            ),
        ];
    }
}