<?php

namespace App\Services;

use App\Models\MarcaGarantiaConfig;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PdfGarantiaService
{
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

    // NUEVO: limite de tokens por minuto del tier gratis para gpt-oss-20b es 8000.
    // Probamos groq/compound-mini para tener mas TPM (70000), pero resulto poco
    // confiable generando JSON largo/anidado: se corta a medio string con
    // finish_reason "stop" (no es limite de tokens ni tool call, es el modelo
    // fallando su propio json_mode). Regresamos a gpt-oss-20b que si es consistente.
    // Si el limite de 8000 TPM sigue apretando, la solucion real es subir a Dev Tier
    // en Groq (250000 TPM con el mismo modelo, mismo comportamiento).
    private const GROQ_TPM_LIMIT   = 8000;
    private const MAX_OUTPUT_TOKENS = 2000;
    private const SYSTEM_PROMPT_TOKENS_ESTIMADO = 300;
    private const CHARS_POR_TOKEN_ESTIMADO = 3.3; // margen conservador para español

    public function procesarConIA(string $idMarcaGarantia): void
    {
        $config = MarcaGarantiaConfig::find($idMarcaGarantia);

        if (!$config) {
            Log::error('procesarConIA: config no encontrada', ['id' => $idMarcaGarantia]);
            return;
        }

        $texto = $config->pdf_texto_extraido;

        Log::info('procesarConIA inicio', [
            'config_id' => $idMarcaGarantia,
            'longitud'  => strlen($texto ?? ''),
            'preview'   => mb_substr($texto ?? '', 0, 80),
        ]);

        if (blank($texto)) {
            Log::error('procesarConIA: pdf_texto_extraido vacío', ['id' => $idMarcaGarantia]);
            $config->update(['estado_procesamiento' => 'error']);
            return;
        }

        // NUEVO: detectar texto "basura" (PDF escaneado / sin capa de texto real)
        if (!$this->esTextoUtilizable($texto)) {
            Log::error('procesarConIA: texto extraido parece basura (posible PDF escaneado)', [
                'id'          => $idMarcaGarantia,
                'longitud'    => strlen($texto),
                'ratio_util'  => $this->ratioCaracteresUtiles($texto),
            ]);
            $config->update(['estado_procesamiento' => 'error']);
            return;
        }

        $config->update(['estado_procesamiento' => 'procesando']);

        try {
            $texto = $this->truncarParaLimiteTokens($texto, $idMarcaGarantia);

            $jsonIA   = $this->enviarAGroq($texto);
            $validado = $this->validarJson($jsonIA);

            Log::info('procesarConIA completado', [
                'config_id'   => $idMarcaGarantia,
                'componentes' => count($validado['garantias'] ?? []),
            ]);

            $config->update([
                'ia_raw_json'          => $validado,
                'ia_procesado_at'      => now(),
                'estado_procesamiento' => 'completado',
            ]);

        } catch (\Exception $e) {
            Log::error('procesarConIA error', [
                'id'    => $idMarcaGarantia,
                'error' => $e->getMessage(),
            ]);
            $config->update(['estado_procesamiento' => 'error']);
        }
    }

    public function extraerTextoPdf(string $base64): string
    {
        $binario = base64_decode($base64);
        $tmpPath = tempnam(sys_get_temp_dir(), 'garantia_pdf_');
        file_put_contents($tmpPath, $binario);

        try {
            $parser = new \Smalot\PdfParser\Parser();
            $pdf    = $parser->parseFile($tmpPath);
            $texto  = $pdf->getText();
        } finally {
            @unlink($tmpPath);
        }

        return $texto;
    }

    /**
     * NUEVO: valida que el texto extraído del PDF tenga suficiente contenido
     * "real" (letras/números) y no sea puro whitespace o basura binaria.
     * Esto pasa típicamente con PDFs escaneados (solo imagen, sin capa de texto).
     */
    private function esTextoUtilizable(string $texto): bool
    {
        $limpio = trim($texto);

        if (mb_strlen($limpio) < 30) {
            return false;
        }

        return $this->ratioCaracteresUtiles($texto) >= 0.15;
    }

    private function ratioCaracteresUtiles(string $texto): float
    {
        $longitudTotal = mb_strlen($texto);

        if ($longitudTotal === 0) {
            return 0.0;
        }

        // cuenta letras (con acentos) y numeros
        preg_match_all('/[\p{L}\p{N}]/u', $texto, $matches);
        $utiles = count($matches[0]);

        return $utiles / $longitudTotal;
    }

    /**
     * NUEVO: recorta el texto del PDF para que (system prompt + texto + respuesta)
     * quepa dentro del limite de tokens por minuto del tier on_demand de Groq (8000).
     * Esto es lo que estaba causando el 413 "Request too large" con PDFs de ~40K chars.
     */
    private function truncarParaLimiteTokens(string $texto, string $idMarcaGarantia): string
    {
        $tokensDisponiblesParaTexto = self::GROQ_TPM_LIMIT
            - self::SYSTEM_PROMPT_TOKENS_ESTIMADO
            - self::MAX_OUTPUT_TOKENS;

        $charsMaximos = (int) floor($tokensDisponiblesParaTexto * self::CHARS_POR_TOKEN_ESTIMADO);

        if (mb_strlen($texto) <= $charsMaximos) {
            return $texto;
        }

        Log::warning('Texto de PDF truncado por limite TPM de Groq', [
            'id'                => $idMarcaGarantia,
            'longitud_original' => mb_strlen($texto),
            'longitud_truncada' => $charsMaximos,
        ]);

        return mb_substr($texto, 0, $charsMaximos);
    }

    private function enviarAGroq(string $textoPdf): array
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . config('services.groq.key'),
            'Content-Type'  => 'application/json',
        ])->timeout(60)->post('https://api.groq.com/openai/v1/chat/completions', [
            'model'       => 'openai/gpt-oss-20b',
            'temperature' => 0.1,
            'max_tokens'  => self::MAX_OUTPUT_TOKENS,

            // Es un modelo de razonamiento (reasoning). Sin esto, el modelo gasta el
            // max_tokens "pensando" y no le queda presupuesto para escribir el JSON
            // final -> failed_generation vacio -> error 400 json_validate_failed.
            'reasoning_effort' => 'low',
            'reasoning_format' => 'hidden',

            // NUEVO: fuerza a Groq a devolver JSON puro (el modelo soporta json_mode)
            'response_format' => [
                'type' => 'json_object',
            ],

            'messages' => [
                ['role' => 'system', 'content' => self::SYSTEM_PROMPT],
                ['role' => 'user',   'content' => $textoPdf],
            ],
        ]);

        if (!$response->successful()) {
            // NUEVO: loguea el body completo del error, ya no se descarta
            Log::error('Groq respondió con error HTTP', [
                'status'            => $response->status(),
                'body'              => $response->body(),
                'texto_caracteres'  => mb_strlen($textoPdf),
                'failed_generation' => $response->json('error.failed_generation'),
            ]);

            throw new \Exception('Groq API error: ' . $response->status());
        }

        $content = $response->json('choices.0.message.content', '');

        $decoded = $this->extraerJsonDeRespuesta($content);

        if ($decoded === null) {
            // NUEVO: loguea el contenido crudo que mandó el modelo, más finish_reason
            // y executed_tools para saber si se cortó por tools, por longitud, etc.
            Log::error('Groq devolvió JSON inválido', [
                'content_raw'     => $content,
                'content_length'  => mb_strlen($content),
                'finish_reason'   => $response->json('choices.0.finish_reason'),
                'executed_tools'  => $response->json('choices.0.message.executed_tools'),
            ]);

            throw new \Exception('JSON inválido de Groq: no se pudo parsear la respuesta');
        }

        return $decoded;
    }

    /**
     * NUEVO: extracción robusta de JSON. Aunque con json_mode Groq casi
     * siempre devuelve JSON limpio, esto sirve de red de seguridad por si
     * el modelo mete texto extra alrededor.
     */
    private function extraerJsonDeRespuesta(string $content): ?array
    {
        $content = trim($content);

        // 1. Intento directo
        $decoded = json_decode($content, true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
            return $decoded;
        }

        // 2. Quitar fences de markdown si existen
        $sinFences = preg_replace('/^```json\s*/i', '', $content);
        $sinFences = preg_replace('/^```\s*/i', '', $sinFences);
        $sinFences = preg_replace('/\s*```$/', '', $sinFences);
        $sinFences = trim($sinFences);

        $decoded = json_decode($sinFences, true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
            return $decoded;
        }

        // 3. Extraer solo el bloque entre la primera { y la última }
        $inicio = mb_strpos($sinFences, '{');
        $fin    = mb_strrpos($sinFences, '}');

        if ($inicio !== false && $fin !== false && $fin > $inicio) {
            $bloque  = mb_substr($sinFences, $inicio, $fin - $inicio + 1);
            $decoded = json_decode($bloque, true);

            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                return $decoded;
            }
        }

        return null;
    }

    private function validarJson(array $data): array
    {
        $garantiasValidas = [];

        foreach ($data['garantias'] ?? [] as $g) {
            if (
                empty($g['componente']) ||
                !isset($g['duracion_meses']) ||
                !is_numeric($g['duracion_meses']) ||
                $g['duracion_meses'] < 0 ||
                !is_array($g['incluye'] ?? null)
            ) {
                continue;
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
            'marca'            => isset($data['marca'])
                ? substr(strtolower(trim($data['marca'])), 0, 100)
                : null,
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
                0, 15
            ),
        ];
    }
}