<?php
// app/Services/PdfGarantiaService.php

namespace App\Services;

use App\Models\GarantiaComponenteDef;
use App\Models\MarcaGarantiaConfig;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Smalot\PdfParser\Parser;

class PdfGarantiaService
{
    // ─── Llamado en la REQUEST (síncrono, rápido) ──────────────────────────
    // Extrae el texto del PDF y lo guarda como string plano.
    // Retorna el texto para que el controlador pueda encolar el job.
    public function extraerTextoPdf(string $rutaTemporal): string
    {
        $parser = new Parser();
        $pdf    = $parser->parseFile($rutaTemporal);
        $texto  = $pdf->getText();

        // Limpieza básica: colapsar espacios y líneas vacías múltiples
        $texto = preg_replace('/\n{3,}/', "\n\n", trim($texto));
        $texto = preg_replace('/[ \t]+/', ' ', $texto);

        return mb_substr($texto, 0, 40000); // tope seguro para el prompt
    }

    // ─── Llamado en el JOB (asíncrono, toca la red) ───────────────────────
    // Recibe el id de la config (ya tiene el texto guardado).
    public function procesarConIA(string $configId): void
    {
        $config = MarcaGarantiaConfig::findOrFail($configId);
        $texto  = $config->pdf_texto_extraido;

        if (blank($texto)) {
            $config->update(['estado_procesamiento' => 'error']);
            return;
        }

        $config->update(['estado_procesamiento' => 'procesando']);

        try {
            $componentes = $this->llamarGroq($texto);

            if (empty($componentes)) {
                $config->update(['estado_procesamiento' => 'error']);
                return;
            }

            $config->update([
                // Solo guardamos el array de componentes, no el response completo
                'ia_raw_json'          => json_encode($componentes, JSON_UNESCAPED_UNICODE),
                'estado_procesamiento' => 'completado',
                'ia_procesado_at'      => now(),
            ]);

        } catch (\Exception $e) {
            Log::error('Groq procesamiento falló', [
                'config'  => $configId,
                'mensaje' => $e->getMessage(),
            ]);
            $config->update(['estado_procesamiento' => 'error']);
        }
    }

    // ─── Groq ──────────────────────────────────────────────────────────────
    private function llamarGroq(string $textoPdf): array
    {
        $prompt = <<<PROMPT
Analiza esta póliza de garantía y extrae ÚNICAMENTE los componentes cubiertos.

Devuelve SOLO un JSON válido con este formato exacto, sin texto adicional:
{
  "componentes": [
    {
      "clave": "motor",
      "nombre": "Motor eléctrico",
      "duracion": 24,
      "cobertura": "Defectos de fabricación",
      "incluye": ["bobinas", "estátor"],
      "serializable": false,
      "excluido": false
    }
  ]
}

Reglas:
- "clave": snake_case, sin espacios, única por componente
- "duracion": meses como entero (0 si no se especifica)
- "incluye": array de strings (puede ser vacío [])
- "serializable": true solo si el componente tiene número de serie propio
- "excluido": false siempre (el admin decide después)

PÓLIZA:
{$textoPdf}
PROMPT;

        $response = Http::withToken(config('services.groq.key'))
            ->timeout(60)
            ->post('https://api.groq.com/openai/v1/chat/completions', [
                'model'       => 'llama-3.3-70b-versatile',
                'temperature' => 0.1,
                'max_tokens'  => 2048,
                'messages'    => [
                    [
                        'role'    => 'system',
                        'content' => 'Eres un extractor de datos estructurados. Responde SOLO con JSON válido, sin markdown ni explicaciones.',
                    ],
                    ['role' => 'user', 'content' => $prompt],
                ],
            ]);

        if (!$response->successful()) {
            throw new \RuntimeException('Groq HTTP ' . $response->status());
        }

        $content = $response->json('choices.0.message.content', '');

        // Limpiar posibles backticks que Groq a veces añade
        $content = preg_replace('/^```json\s*/i', '', trim($content));
        $content = preg_replace('/\s*```$/', '', $content);

        $decoded = json_decode($content, true);

        if (json_last_error() !== JSON_ERROR_NONE || !isset($decoded['componentes'])) {
            Log::warning('Groq devolvió JSON inválido', ['raw' => mb_substr($content, 0, 500)]);
            return [];
        }

        return $this->validarComponentes($decoded['componentes']);
    }

    // ─── Validación estricta del array ─────────────────────────────────────
    private function validarComponentes(array $raw): array
    {
        $validos = [];

        foreach ($raw as $comp) {
            $clave = preg_replace('/[^a-z0-9_]/', '_', strtolower($comp['clave'] ?? ''));

            if (blank($clave) || blank($comp['nombre'] ?? '')) continue;

            $validos[] = [
                'clave'        => mb_substr($clave, 0, 60),
                'nombre'       => mb_substr($comp['nombre'], 0, 120),
                'duracion'     => (int) ($comp['duracion'] ?? 0),
                'cobertura'    => mb_substr($comp['cobertura'] ?? '', 0, 255) ?: null,
                'incluye'      => array_values(array_filter((array) ($comp['incluye'] ?? []))),
                'serializable' => (bool) ($comp['serializable'] ?? false),
                'excluido'     => false, // siempre false desde IA
            ];
        }

        return $validos;
    }
}