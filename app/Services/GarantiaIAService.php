<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GarantiaIAService
{
    private const SYSTEM_PROMPT = <<<'PROMPT'
Eres un asistente que ayuda a un administrador de una tienda de bicicletas/motos eléctricas a
evaluar un reclamo de garantía ANTES de que el administrador tome la decisión final.

Tu opinión es solo una sugerencia de apoyo — nunca es la decisión final ni es vinculante.
El administrador siempre puede ignorarla.

Responde ÚNICAMENTE con JSON válido, sin texto adicional, sin markdown:
{
  "sugerencia": "cubre" | "no_cubre" | "revisar",
  "razonamiento": "string breve (máximo 400 caracteres), en español, dirigido al administrador"
}

Guías para tu análisis:
- Si el kilometraje es muy alto para el tiempo transcurrido desde la compra, es señal de uso
  intensivo — puede ser motivo para dudar del reclamo, pero no es concluyente por sí solo.
- Si la descripción del cliente suena a mal uso, accidente, agua, caída o desgaste normal
  (no defecto de fábrica), inclina a "no_cubre" o "revisar".
- Si la cláusula de cobertura del componente aplica claramente al problema descrito, inclina
  a "cubre".
- Si falta información para decidir con confianza, usa "revisar" y dilo en el razonamiento.
PROMPT;

    private const SUGERENCIAS_VALIDAS = ['cubre', 'no_cubre', 'revisar'];

    /**
     * Devuelve ['sugerencia' => ..., 'razonamiento' => ...] o null si Groq
     * no está disponible / la respuesta no se pudo interpretar. Nunca lanza
     * excepción — esto es una opinión de apoyo, no debe romper el reclamo.
     */
    public function evaluarReclamo(array $datos): ?array
    {
        if (blank(config('services.groq.key'))) {
            Log::info('GarantiaIAService: sin GROQ_API_KEY configurada, se omite la opinión de IA.');
            return null;
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . config('services.groq.key'),
                'Content-Type'  => 'application/json',
            ])->timeout(15)->post('https://api.groq.com/openai/v1/chat/completions', [
                'model'       => 'llama-3.3-70b-versatile',
                'temperature' => 0.2,
                'messages'    => [
                    ['role' => 'system', 'content' => self::SYSTEM_PROMPT],
                    ['role' => 'user',   'content' => json_encode($datos, JSON_UNESCAPED_UNICODE)],
                ],
            ]);

            if (!$response->successful()) {
                Log::warning('GarantiaIAService: Groq respondió con error', [
                    'status' => $response->status(),
                ]);
                return null;
            }

            $content = $response->json('choices.0.message.content', '');
            $content = preg_replace('/^```json\s*/i', '', trim($content));
            $content = preg_replace('/\s*```$/', '', $content);

            $decoded = json_decode($content, true);

            if (json_last_error() !== JSON_ERROR_NONE || empty($decoded['sugerencia'])) {
                Log::warning('GarantiaIAService: respuesta no interpretable', ['content' => $content]);
                return null;
            }

            $sugerencia = in_array($decoded['sugerencia'], self::SUGERENCIAS_VALIDAS, true)
                ? $decoded['sugerencia']
                : 'revisar';

            return [
                'sugerencia'   => $sugerencia,
                'razonamiento' => mb_substr((string) ($decoded['razonamiento'] ?? ''), 0, 500),
            ];

        } catch (\Throwable $e) {
            Log::error('GarantiaIAService: excepción al consultar Groq', ['error' => $e->getMessage()]);
            return null;
        }
    }
}
