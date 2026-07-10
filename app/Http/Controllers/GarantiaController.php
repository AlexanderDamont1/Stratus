<?php

namespace App\Http\Controllers;

use App\Models\Bicicleta;
use App\Models\BicicletaGarantia;
use App\Models\GarantiaReclamo;
use App\Services\CatalogService;
use App\Services\GarantiaIAService;
use App\Services\GarantiaService;
use App\Services\ReparacionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class GarantiaController extends Controller
{
    public function __construct(protected GarantiaService $garantiaService)
    {
    }

    // ─── INDEX: buscador ─────────────────────────────────────────────────

    public function index()
    {
        $user      = auth()->user();
        $idNegocio = $user->id_negocio;
        if ($user->id_rol != 2) abort(403);

        $page = request()->get('page', 1);

        ['bicicletas' => $bicicletas, 'ultimaGarantia' => $ultimaGarantia, 'stats' => $stats]
            = CatalogService::getGarantiasIndex($idNegocio, $page);

        return view('vendedor.garantias.index', compact('bicicletas', 'ultimaGarantia', 'stats'));
    }

    // ─── AJAX: buscar bicicleta ───────────────────────────────────────────

    public function buscar(Request $request)
    {
        $user = auth()->user();
        if ($user->id_rol != 2) abort(403);

        $numSerie = strtoupper(trim($request->get('num_serie', '')));

        if (!$numSerie) {
            return response()->json(['ok' => false, 'mensaje' => 'Indica un número de serie.'], 422);
        }

        $bici = CatalogService::getBicicletaBySerie($numSerie, $user->id_negocio);

        if (!$bici || $bici->id_negocio !== $user->id_negocio) {
            return response()->json(['ok' => false, 'mensaje' => 'Bicicleta no encontrada.'], 404);
        }

        if ($bici->status != 2) {
            return response()->json([
                'ok'      => false,
                'mensaje' => 'Esta bicicleta no ha sido vendida, no tiene garantía activa.',
            ], 422);
        }

        return response()->json([
            'ok'       => true,
            'redirect' => route('garantias.show', $numSerie),
        ]);
    }

    // ─── SHOW: mapa + reclamos ────────────────────────────────────────────

    public function show(string $numSerie)
    {
        $user = auth()->user();
        if ($user->id_rol != 2) abort(403);

        $bici = CatalogService::getBicicletaBySerie($numSerie, $user->id_negocio);

        if (!$bici || $bici->id_negocio !== $user->id_negocio || $bici->status != 2) {
            abort(404);
        }

        $idsConReclamoActivo = GarantiaReclamo::where('num_serie', $numSerie)
            ->where('id_negocio', $user->id_negocio)
            ->whereHas('reparacion', fn($q) => $q->whereNotIn('estado', ['entregada', 'cancelada']))
            ->pluck('id_bicicleta_garantia')
            ->toArray();

        $garantias = BicicletaGarantia::with('garantiaDef')
            ->where('num_serie', $numSerie)
            ->where('id_negocio', $user->id_negocio)
            ->whereIn('estado', ['vigente', 'por_vencer', 'expirada'])
            ->whereNull('id_reemplazada_por')
            ->get()
            ->map(fn($g) => [
                'id'               => $g->id_bicicleta_garantia,
                'clave'            => $g->clave_componente,
                'nombre'           => $g->garantiaDef->nombre_componente ?? $g->clave_componente,
                'incluye'          => $g->garantiaDef->incluye ?? [],
                'estado_visual'    => $g->estado_visual,
                'color_mapa'       => $g->color_mapa,
                'dias_restantes'   => $g->dias_restantes,
                'porcentaje_vida'  => $g->porcentaje_vida,
                'fecha_expiracion' => $g->fecha_expiracion->format('d/m/Y'),
                'num_serie_comp'   => $g->num_serie_componente,
                'cobertura'        => $g->garantiaDef->cobertura ?? null,
                'reclamo_activo'   => in_array($g->id_bicicleta_garantia, $idsConReclamoActivo),
            ]);

        $reclamos = GarantiaReclamo::with(['reparacion', 'bicicletaGarantia.garantiaDef'])
            ->where('num_serie', $numSerie)
            ->where('id_negocio', $user->id_negocio)
            ->latest()
            ->get()
            ->map(fn($r) => $this->formatearReclamo($r));

        return view('vendedor.garantias.show', compact('bici', 'garantias', 'reclamos'));
    }

    private function formatearReclamo(GarantiaReclamo $r): array
    {
        return [
            'id_reclamo'      => $r->id_reclamo,
            'componente'      => $r->bicicletaGarantia->garantiaDef->nombre_componente ?? $r->clave_componente,
            'motivo'          => $r->motivo_reclamo,
            'kilometraje'     => $r->kilometraje,
            'ia_sugerencia'   => $r->ia_sugerencia,
            'ia_razonamiento' => $r->ia_razonamiento,
            'resultado'       => $r->resultado,
            'id_reparacion'   => $r->id_reparacion,
            'ot_estado'       => $r->reparacion?->estado,
            'reclamo_estado'  => $r->estado,
            'created_at'      => $r->created_at->format('d/m/Y H:i'),
        ];
    }

    // ─── POST: abrir reclamo ──────────────────────────────────────────────
    //
    // Un reclamo de garantía crea una OT (Reparaciones) con tipo=garantia y
    // estado=en_revision, con los datos del vehículo y del cliente ya
    // precargados. El admin la aprueba (→ recibida, entra al flujo normal
    // de OT) o la rechaza (→ cancelada). Ya no se maneja como una lógica
    // aparte con su propio "estado" independiente.

    public function reclamo(Request $request)
    {
        $user = auth()->user();
        if ($user->id_rol != 2) abort(403);

        $request->validate([
            'num_serie'             => 'required|string',
            'id_bicicleta_garantia' => 'required|string',
            'motivo_reclamo'        => 'required|string|max:1000',
            'kilometraje'           => 'nullable|integer|min:0|max:999999',
        ]);

        $garantia = BicicletaGarantia::with('garantiaDef')
            ->where('id_bicicleta_garantia', $request->id_bicicleta_garantia)
            ->where('num_serie', $request->num_serie)
            ->where('id_negocio', $user->id_negocio)
            ->where('estado', 'vigente')
            ->first();

        if (!$garantia || $garantia->dias_restantes < 0) {
            return response()->json([
                'ok'      => false,
                'mensaje' => 'La garantía de este componente no está vigente.',
            ], 422);
        }

        $reclamoActivo = GarantiaReclamo::where('id_bicicleta_garantia', $request->id_bicicleta_garantia)
            ->whereHas('reparacion', fn($q) => $q->whereNotIn('estado', ['entregada', 'cancelada']))
            ->exists();

        if ($reclamoActivo) {
            return response()->json([
                'ok'      => false,
                'mensaje' => 'Ya existe un reclamo activo para este componente.',
            ], 422);
        }

        $cliente = CatalogService::getClienteByNumSerie($request->num_serie, $user->id_negocio);
        $nombreComponente = $garantia->garantiaDef->nombre_componente ?? $garantia->clave_componente;

        try {
            DB::beginTransaction();

            $reparacion = ReparacionService::crear(
                datos: [
                    'num_serie'          => $request->num_serie,
                    'id_cliente'         => $cliente?->id_cliente,
                    'cliente_nombre'     => $cliente
                        ? trim("{$cliente->nombre_cliente} {$cliente->apellido1}")
                        : null,
                    'cliente_email'      => $cliente?->correo,
                    'cliente_telefono'   => $cliente?->telefono,
                    'tipo'               => 'garantia',
                    'problema_reportado' => $request->motivo_reclamo,
                    'notas_internas'     => "Reclamo de garantía — componente: {$nombreComponente}.",
                ],
                idNegocio: $user->id_negocio,
                idUsuario: $user->id_usuario,
                estadoInicial: 'en_revision',
            );

            $reclamo = GarantiaReclamo::create([
                'id_negocio'            => $user->id_negocio,
                'id_reparacion'         => $reparacion->id_reparacion,
                'id_bicicleta_garantia' => $garantia->id_bicicleta_garantia,
                'num_serie'             => $request->num_serie,
                'clave_componente'      => $garantia->clave_componente,
                'estado'                => 'pendiente',
                'motivo_reclamo'        => $request->motivo_reclamo,
                'kilometraje'           => $request->kilometraje,
            ]);

            DB::commit();

            CatalogService::invalidateGarantiasIndex($user->id_negocio);

            // Opinión de IA (Groq) — se dispara DESPUÉS de responder al navegador,
            // para que el vendedor no espere a que Groq conteste. El frontend
            // hace polling a reclamo.ia hasta que ia_sugerencia deje de ser null.
            dispatch(function () use ($reclamo, $garantia, $nombreComponente) {
                try {
                    $opinion = app(GarantiaIAService::class)->evaluarReclamo([
                        'componente'          => $nombreComponente,
                        'cobertura'           => $garantia->garantiaDef->cobertura ?? null,
                        'dias_restantes'      => $garantia->dias_restantes,
                        'kilometraje'         => $reclamo->kilometraje,
                        'descripcion_cliente' => $reclamo->motivo_reclamo,
                    ]);

                    if ($opinion) {
                        $reclamo->update([
                            'ia_sugerencia'   => $opinion['sugerencia'],
                            'ia_razonamiento' => $opinion['razonamiento'],
                        ]);
                    }
                } catch (\Throwable $e) {
                    Log::warning('Reclamo creado, pero la opinión de IA falló', ['error' => $e->getMessage()]);
                }
            })->afterResponse();

            return response()->json([
                'ok'      => true,
                'mensaje' => "Reclamo registrado. Se abrió la OT {$reparacion->id_reparacion} para revisión del admin.",
                'reclamo' => $this->formatearReclamo(
                    $reclamo->fresh(['reparacion', 'bicicletaGarantia.garantiaDef'])
                ),
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al crear reclamo de garantía', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return response()->json([
                'ok'      => false,
                'mensaje' => 'Error: ' . $e->getMessage(),
            ], 500);
        }
    }

    // ─── GET: sondeo de la opinión de IA (polling desde el frontend) ──────
    //
    // La IA corre después de responder al reclamo (ver arriba), así que el
    // frontend consulta este endpoint cada pocos segundos hasta que
    // ia_sugerencia deje de ser null, sin recargar la página.
    public function estadoIA(string $id)
    {
        $user = auth()->user();
        if ($user->id_rol != 2) abort(403);

        $reclamo = GarantiaReclamo::where('id_reclamo', $id)
            ->where('id_negocio', $user->id_negocio)
            ->first();

        if (!$reclamo) {
            return response()->json(['ok' => false], 404);
        }

        return response()->json([
            'ok'              => true,
            'listo'           => !is_null($reclamo->ia_sugerencia),
            'ia_sugerencia'   => $reclamo->ia_sugerencia,
            'ia_razonamiento' => $reclamo->ia_razonamiento,
        ]);
    }

    // Nota: ya no existe un endpoint para que el vendedor cambie el "estado"
    // del reclamo directamente — el progreso del reclamo ahora lo dicta el
    // estado de la OT vinculada (ver ReparacionController::avanzarEstado y
    // AdminGarantiaController::estadoReclamo para la aprobación del admin).
}