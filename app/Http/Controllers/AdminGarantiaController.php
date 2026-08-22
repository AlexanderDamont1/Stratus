<?php

// app/Http/Controllers/AdminGarantiaController.php

namespace App\Http\Controllers;

use App\Models\GarantiaComponenteDef;
use App\Models\GarantiaReclamo;
use App\Models\GarantiaReemplazo;
use App\Models\MarcaGarantiaConfig;
use App\Services\CatalogService;
use App\Services\GarantiaService;
use App\Services\PdfGarantiaService;
use App\Services\ReparacionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AdminGarantiaController extends Controller
{
    public function __construct(
        protected GarantiaService    $garantiaService,
        protected PdfGarantiaService $pdfService,
    ) {}

    // ─── INDEX ────────────────────────────────────────────────────────────
    public function index()
    {
        $user = auth()->user();
        if ($user->id_rol != 1) abort(403);

        $marcas = CatalogService::getMarcasByNegocio($user->id_negocio);

        $configs = MarcaGarantiaConfig::where('id_negocio', $user->id_negocio)
            ->with('componenteDefs')
            ->get()
            ->keyBy('id_marca');

        return view('administrador.garantias.index', compact('marcas', 'configs'));
    }

    // ─── RECLAMOS ─────────────────────────────────────────────────────────
    public function reclamos(Request $request)
    {
        $user = auth()->user();
        if ($user->id_rol != 1) abort(403);

        $query = GarantiaReclamo::with([
            'bicicletaGarantia.garantiaDef',
            'reparacion',
        ])->where('id_negocio', $user->id_negocio);

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }
        if ($request->filled('num_serie')) {
            $query->where('num_serie', 'like', '%' . strtoupper($request->num_serie) . '%');
        }

        $reclamos   = $query->latest()->paginate(20);
        $sucursales = CatalogService::getSucursalesByNegocio($user->id_negocio)->keyBy('id_usuario');

        return view('administrador.garantias.reclamos', compact('reclamos', 'sucursales'));
    }

    // ─── MARCAS ───────────────────────────────────────────────────────────
    public function marcas()
    {
        $user = auth()->user();
        if ($user->id_rol != 1) abort(403);

        $marcas  = CatalogService::getMarcasByNegocio($user->id_negocio);
        $configs = MarcaGarantiaConfig::with('componenteDefs')
            ->where('id_negocio', $user->id_negocio)
            ->get()
            ->keyBy('id_marca');

        return view('administrador.garantias.marcas', compact('marcas', 'configs'));
    }

    // ─── EDITAR MARCA ─────────────────────────────────────────────────────
    public function editarMarca(Request $request, string $idMarca)
    {
        $user = auth()->user();
        if ($user->id_rol != 1) abort(403);

        $marca = CatalogService::getMarcaById($idMarca);
        if (!$marca || $marca->id_negocio !== $user->id_negocio) abort(404);

        $config = MarcaGarantiaConfig::with('componenteDefs')
            ->where('id_marca', $idMarca)
            ->where('id_negocio', $user->id_negocio)
            ->first();

        // Polling desde Alpine
        if ($request->wantsJson() || $request->has('json')) {
            return response()->json([
                'estado'      => $config?->estado_procesamiento ?? 'sin_pdf',
                'ia_raw_json' => $config?->ia_raw_json,
            ]);
        }

        // Excepciones ya configuradas en OTRAS marcas del mismo negocio, para que el
        // admin pueda reutilizarlas (copiar) en vez de volver a escribirlas.
        $excepcionesDisponibles = GarantiaComponenteDef::with('marcaGarantia.marca')
            ->where('id_negocio', $user->id_negocio)
            ->get()
            ->filter(fn($d) => !empty($d->excepciones))
            ->map(fn($d) => [
                'clave'       => $d->clave_componente,
                'nombre'      => $d->nombre_componente,
                'marca'       => $d->marcaGarantia?->marca?->nombre_marca ?? '—',
                'excepciones' => $d->excepciones,
            ])
            ->values();

        return view('administrador.garantias.editar-marca', compact('marca', 'config', 'excepcionesDisponibles'));
    }

    // ─── POST: subir PDF ──────────────────────────────────────────────────
    public function subirPdf(Request $request, string $idMarca)
    {
        $user = auth()->user();
        if ($user->id_rol != 1) abort(403);

        $request->validate(['pdf' => 'required|file|max:4096']);

        $archivo = $request->file('pdf');
        if (strtolower($archivo->getClientOriginalExtension()) !== 'pdf') {
            return response()->json(['ok' => false, 'mensaje' => 'El archivo debe ser un PDF.'], 422);
        }

        $marca = CatalogService::getMarcaById($idMarca);
        if (!$marca || $marca->id_negocio !== $user->id_negocio) abort(404);

        try {
            $parser = new \Smalot\PdfParser\Parser();
            $pdf    = $parser->parseFile($archivo->getRealPath());
            $texto  = trim($pdf->getText());
            $texto  = preg_replace('/\n{3,}/', "\n\n", $texto);
            $texto  = preg_replace('/[ \t]+/', ' ', $texto);
            $texto  = mb_substr($texto, 0, 40000);

            if (blank($texto)) {
                return response()->json([
                    'ok'      => false,
                    'mensaje' => 'No se pudo extraer texto. Sube la póliza original del fabricante.',
                ], 422);
            }

            $config = MarcaGarantiaConfig::where('id_marca', $idMarca)
                ->where('id_negocio', $user->id_negocio)
                ->first();

            if (!$config) {
                $config = MarcaGarantiaConfig::create([
                    'id_marca'   => $idMarca,
                    'id_negocio' => $user->id_negocio,
                ]);
            }

            $config->update([
                'pdf_nombre_original'  => $archivo->getClientOriginalName(),
                'pdf_texto_extraido'   => $texto,
                'estado_procesamiento' => 'pendiente',
                'ia_raw_json'          => null,
                'ia_procesado_at'      => null,
            ]);

            $configId = $config->id_marca_garantia;
            dispatch(function () use ($configId) {
                app(PdfGarantiaService::class)->procesarConIA($configId);
            });

            return response()->json([
                'ok'      => true,
                'mensaje' => 'PDF recibido. Procesando con IA, espera unos segundos.',
                'config'  => [
                    'id'     => $config->id_marca_garantia,
                    'estado' => 'pendiente',
                    'nombre' => $archivo->getClientOriginalName(),
                ],
            ]);

        } catch (\Exception $e) {
            Log::error('Error al subir PDF de garantía', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return response()->json([
                'ok'      => false,
                'mensaje' => 'Error: ' . $e->getMessage(),
            ], 500);
        }
    }

    // ─── POST: activar/desactivar garantía de una marca ──────────────────
    public function activar(Request $request, string $idMarca)
    {
        $user = auth()->user();
        if ($user->id_rol != 1) abort(403);

        $config = MarcaGarantiaConfig::where('id_marca', $idMarca)
            ->where('id_negocio', $user->id_negocio)
            ->firstOrFail();

        $config->update(['activa' => !$config->activa]);

        return response()->json(['ok' => true, 'activa' => $config->activa]);
    }

    // ─── POST: guardar definiciones de componentes ────────────────────────
    public function guardarDefs(Request $request)
    {
        $user = auth()->user();
        if ($user->id_rol != 1) abort(403);

        $request->validate([
            'id_marca'                     => 'required|string',
            'componentes'                  => 'required|array|min:1',
            'componentes.*.clave'          => 'required|string|max:60',
            'componentes.*.nombre'         => 'required|string|max:120',
            'componentes.*.incluye'        => 'nullable|array',
            'componentes.*.duracion'       => 'required|integer|min:0',
            'componentes.*.cobertura'      => 'nullable|string|max:255',
            'componentes.*.excepciones'    => 'nullable|array',
            'componentes.*.excepciones.*'  => 'string|max:255',
            'componentes.*.serializable'   => 'boolean',
            'componentes.*.excluido'       => 'boolean',
        ]);

        $marca = CatalogService::getMarcaById($request->id_marca);
        if (!$marca || $marca->id_negocio !== $user->id_negocio) abort(404);

        // Puede no existir aún si el admin agrega componentes manualmente sin
        // haber subido un PDF antes — se crea igual que en subirPdf().
        $config = MarcaGarantiaConfig::firstOrCreate([
            'id_marca'   => $request->id_marca,
            'id_negocio' => $user->id_negocio,
        ]);

        DB::transaction(function () use ($request, $config) {
            foreach ($request->componentes as $comp) {
                GarantiaComponenteDef::updateOrCreate(
                    [
                        'id_marca_garantia' => $config->id_marca_garantia,
                        'clave_componente'  => $comp['clave'],
                    ],
                    [
                        'id_negocio'       => $config->id_negocio,
                        'nombre_componente' => $comp['nombre'],
                        'incluye'          => $comp['incluye'] ?? [],
                        'duracion_meses'   => $comp['duracion'],
                        'cobertura'        => $comp['cobertura'] ?? null,
                        'excepciones'      => $comp['excepciones'] ?? [],
                        'serializable'     => $comp['serializable'] ?? false,
                        'excluido'         => $comp['excluido'] ?? false,
                        'activo'           => true,
                    ]
                );
            }

            $config->update(['estado_procesamiento' => 'completado']);
        });

        return response()->json([
            'ok'                => true,
            'mensaje'           => 'Componentes guardados correctamente.',
            'id_marca_garantia' => $config->id_marca_garantia,
        ]);
    }

    // ─── POST: sugerir excepciones de garantía por IA (Groq) ───────────────
    // ─── POST: sugerir excepciones de garantía por IA (Groq) ───────────────
    public function sugerirExcepciones(Request $request)
    {
        $user = auth()->user();
        if ($user->id_rol != 1) abort(403);

        $request->validate([
            'nombre_componente' => 'required|string|max:120',
            'cobertura'         => 'nullable|string|max:255',
            'incluye'           => 'nullable|array',
        ]);

        $contexto = "Componente: {$request->nombre_componente}.";
        if ($request->filled('cobertura')) {
            $contexto .= " Cobertura: {$request->cobertura}.";
        }
        if ($request->filled('incluye')) {
            $contexto .= ' Incluye: ' . implode(', ', $request->incluye) . '.';
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . config('services.groq.key'),
                'Content-Type'  => 'application/json',
            ])->timeout(15)->post('https://api.groq.com/openai/v1/chat/completions', [
                // FIX: llama-3.1-8b-instant ya no existe en Groq (deprecado), no
                // aparece en el /v1/models de la cuenta. Este endpoint estaba
                // tronando en silencio. Usamos el mismo modelo que ya validamos
                // en el resto del sistema.
                'model'       => 'openai/gpt-oss-20b',
                'temperature' => 0.2,

                // Es reasoning model, sin esto se gasta el max_tokens pensando.
                'reasoning_effort' => 'low',
                'reasoning_format' => 'hidden',

                // json_object exige raiz de objeto, no de array -> le pedimos
                // que envuelva el array en {"excepciones": [...]}.
                'response_format' => [
                    'type' => 'json_object',
                ],

                'max_tokens' => 500,

                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'Eres un asistente que redacta excepciones de garantía para tiendas de bicicletas y vehículos eléctricos. '
                            . 'Dado un componente, responde SOLO con un objeto JSON con esta forma: {"excepciones": ["...", "...", "..."]}, '
                            . 'con 3 a 5 strings cortos en español, cada uno una causa típica de exclusión de garantía para ese componente '
                            . '(ej. mal uso, modificaciones, condiciones ambientales, desgaste natural). Sin explicaciones, sin texto extra, solo el JSON.',
                    ],
                    ['role' => 'user', 'content' => $contexto],
                ],
            ]);

            if (!$response->successful()) {
                Log::error('sugerirExcepciones: Groq respondio con error', [
                    'status'  => $response->status(),
                    'body'    => $response->body(),
                    'contexto' => $contexto,
                ]);

                return response()->json(['ok' => false, 'mensaje' => 'Error al conectar con la IA.'], 500);
            }

            $texto = trim($response->json('choices.0.message.content') ?? '');
            // Salvaguarda por si aun asi envuelve la respuesta en ```json ... ```
            $texto = preg_replace('/^```(?:json)?|```$/m', '', $texto);
            $texto = trim($texto);

            $decoded = json_decode($texto, true);

            if (json_last_error() !== JSON_ERROR_NONE || !is_array($decoded)) {
                Log::error('sugerirExcepciones: JSON invalido de Groq', [
                    'content_raw'   => $texto,
                    'finish_reason' => $response->json('choices.0.finish_reason'),
                    'contexto'      => $contexto,
                ]);

                return response()->json(['ok' => false, 'mensaje' => 'La IA no devolvió una respuesta válida.'], 422);
            }

            // Acepta tanto {"excepciones": [...]} (formato pedido) como un array
            // suelto [...] por si el modelo lo manda asi de todos modos.
            $excepciones = $decoded['excepciones'] ?? $decoded;

            if (!is_array($excepciones)) {
                Log::error('sugerirExcepciones: estructura inesperada', [
                    'decoded'  => $decoded,
                    'contexto' => $contexto,
                ]);

                return response()->json(['ok' => false, 'mensaje' => 'La IA no devolvió una respuesta válida.'], 422);
            }

            $excepciones = collect($excepciones)
                ->filter(fn($e) => is_string($e) && trim($e) !== '')
                ->map(fn($e) => trim($e))
                ->values()
                ->all();

            return response()->json(['ok' => true, 'excepciones' => $excepciones]);
        } catch (\Exception $e) {
            Log::error('Error al sugerir excepciones de garantía con IA', [
                'error'    => $e->getMessage(),
                'contexto' => $contexto,
            ]);
            return response()->json(['ok' => false, 'mensaje' => 'Error al conectar con la IA.'], 500);
        }
    }

    // ─── POST: guardar política de reemplazo POR MARCA ────────────────────
    public function guardarPolitica(Request $request, string $idMarca)
    {
        $user = auth()->user();
        if ($user->id_rol != 1) abort(403);

        $request->validate([
            'politica_reemplazo' => 'required|in:heredar,nueva,mini',
            'mini_garantia_dias' => 'required_if:politica_reemplazo,mini|integer|min:1|max:90',
        ]);

        $config = MarcaGarantiaConfig::where('id_marca', $idMarca)
            ->where('id_negocio', $user->id_negocio)
            ->firstOrFail();

        $config->update([
            'politica_reemplazo' => $request->politica_reemplazo,
            'mini_garantia_dias' => $request->mini_garantia_dias ?? 7,
        ]);

        return response()->json(['ok' => true]);
    }

    // ─── DELETE: borrar definición de componente ──────────────────────────
    public function borrarDef(string $id)
    {
        $user = auth()->user();
        if ($user->id_rol != 1) abort(403);

        $def = GarantiaComponenteDef::where('id_garantia_def', $id)
            ->where('id_negocio', $user->id_negocio)
            ->firstOrFail();

        $def->delete();

        return response()->json(['ok' => true]);
    }

    // ─── PATCH: decisión del admin sobre el reclamo (aprobar / rechazar) ──
    //
    // Ya no se elige un "estado" libre — el admin aprueba o rechaza, y eso
    // se traduce en un avance de la OT vinculada (en_revision → recibida o
    // cancelada). De ahí en adelante la OT sigue su flujo normal.
    public function estadoReclamo(Request $request, string $id)
    {
        $user = auth()->user();
        if ($user->id_rol != 1) abort(403);

        $request->validate([
            'decision'  => 'required|in:aprobar,rechazar',
            'resultado' => 'nullable|string|max:1000',
        ]);

        $reclamo = GarantiaReclamo::where('id_reclamo', $id)
            ->where('id_negocio', $user->id_negocio)
            ->firstOrFail();

        if (!$reclamo->id_reparacion) {
            return response()->json([
                'ok'      => false,
                'mensaje' => 'Este reclamo no tiene una orden de trabajo vinculada.',
            ], 422);
        }

        $nuevoEstadoOt = $request->decision === 'aprobar' ? 'recibida' : 'cancelada';

        try {
            $rep = ReparacionService::avanzarEstado(
                $reclamo->id_reparacion,
                $nuevoEstadoOt,
                $user->id_usuario,
                $user->id_negocio,
                $request->resultado,
            );

            // Al aprobar, la pieza cubierta por la garantía se registra sola —
            // el vendedor no la vuelve a capturar manualmente.
            if ($request->decision === 'aprobar') {
                $reclamo->loadMissing('bicicletaGarantia.garantiaDef');
                $nombreComponente = $reclamo->bicicletaGarantia?->garantiaDef?->nombre_componente
                    ?? $reclamo->clave_componente;

                ReparacionService::registrarPiezaGarantia($rep, $nombreComponente);
            }
        } catch (\Throwable $e) {
            Log::error('Error al decidir reclamo de garantía', ['error' => $e->getMessage()]);
            return response()->json([
                'ok'      => false,
                'mensaje' => 'No se pudo actualizar la OT: ' . $e->getMessage(),
            ], 422);
        }

        $reclamo->update([
            'estado'    => $request->decision === 'aprobar' ? 'aprobado' : 'rechazado',
            'resultado' => $request->resultado,
        ]);

        CatalogService::invalidateGarantiasIndex($user->id_negocio);

        return response()->json([
            'ok'      => true,
            'mensaje' => $request->decision === 'aprobar'
                ? 'Reclamo aprobado. La OT sigue el flujo normal de reparación.'
                : 'Reclamo rechazado.',
        ]);
    }

    // ─── POST: procesar reemplazo ─────────────────────────────────────────
    public function reemplazo(Request $request, string $id)
    {
        $user = auth()->user();
        if ($user->id_rol != 1) abort(403);

        $request->validate([
            'num_serie_nuevo_componente' => 'nullable|string|max:60',
            'notas'                      => 'nullable|string|max:1000',
        ]);

        $reclamo = GarantiaReclamo::with('reparacion')
            ->where('id_reclamo', $id)
            ->where('id_negocio', $user->id_negocio)
            ->firstOrFail();

        $estadoOt = $reclamo->reparacion?->estado;

        if (!$estadoOt || in_array($estadoOt, ['en_revision', 'cancelada'])) {
            return response()->json([
                'ok'      => false,
                'mensaje' => 'La OT debe estar aprobada (fuera de revisión) antes de procesar el reemplazo.',
            ], 422);
        }

        try {
            $nuevaGarantia = $this->garantiaService->procesarReemplazo(
                idReclamo:               $id,
                numSerieNuevoComponente: $request->num_serie_nuevo_componente,
                notas:                   $request->notas ?? '',
            );

            return response()->json([
                'ok'               => true,
                'mensaje'          => 'Reemplazo procesado. Nueva garantía generada.',
                'nueva_expiracion' => $nuevaGarantia->fecha_expiracion->format('d/m/Y'),
            ]);

        } catch (\Exception $e) {
            Log::error('Error al procesar reemplazo', ['error' => $e->getMessage()]);
            return response()->json(['ok' => false, 'mensaje' => 'Error al procesar el reemplazo.'], 500);
        }
    }
}