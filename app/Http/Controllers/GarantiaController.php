<?php

namespace App\Http\Controllers;

use App\Models\Bicicleta;
use App\Models\BicicletaGarantia;
use App\Models\GarantiaReclamo;
use App\Models\Mantenimiento;
use App\Services\CatalogService;
use App\Services\GarantiaService;
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
            ]);

        $reclamos = GarantiaReclamo::with(['mantenimiento', 'bicicletaGarantia.garantiaDef'])
            ->where('num_serie', $numSerie)
            ->where('id_negocio', $user->id_negocio)
            ->latest()
            ->get();

        return view('vendedor.garantias.show', compact('bici', 'garantias', 'reclamos'));
    }

    // ─── POST: abrir reclamo ──────────────────────────────────────────────

    public function reclamo(Request $request)
    {
        $user = auth()->user();
        if ($user->id_rol != 2) abort(403);

        $request->validate([
            'num_serie'             => 'required|string',
            'id_bicicleta_garantia' => 'required|string',
            'motivo_reclamo'        => 'required|string|max:1000',
        ]);

        $garantia = BicicletaGarantia::where('id_bicicleta_garantia', $request->id_bicicleta_garantia)
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
            ->whereNotIn('estado', ['finalizado', 'rechazado'])
            ->exists();

        if ($reclamoActivo) {
            return response()->json([
                'ok'      => false,
                'mensaje' => 'Ya existe un reclamo activo para este componente.',
            ], 422);
        }

        try {
            DB::beginTransaction();

            $mantenimiento = new Mantenimiento([
                'id_negocio'    => $user->id_negocio,
                'num_serie'     => $request->num_serie,
                'estado'        => 'EN_REPARACION',
                'ubicacion'     => 'EN_TIENDA',
                'fecha_ingreso' => now(),
                'motivo'        => 'Reclamo de garantía: ' . $request->motivo_reclamo,
            ]);
            $mantenimiento->save();

            GarantiaReclamo::create([
                'id_negocio'            => $user->id_negocio,
                'id_mantenimiento'      => $mantenimiento->id_mantenimiento,
                'id_bicicleta_garantia' => $garantia->id_bicicleta_garantia,
                'num_serie'             => $request->num_serie,
                'clave_componente'      => $garantia->clave_componente,
                'estado'                => 'pendiente',
                'motivo_reclamo'        => $request->motivo_reclamo,
            ]);

            DB::commit();

            CatalogService::invalidateGarantiasIndex($user->id_negocio);

            return response()->json([
                'ok'      => true,
                'mensaje' => 'Reclamo registrado. Se abrió un mantenimiento automáticamente.',
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

    // ─── PATCH: actualizar estado de reclamo ─────────────────────────────

    public function estado(Request $request, string $id)
    {
        $user = auth()->user();
        if ($user->id_rol != 2) abort(403);

        $request->validate(['estado' => 'required|string|max:40']);

        $estadosPermitidos = ['pendiente', 'en_diagnostico', 'finalizado', 'rechazado'];

        if (!in_array($request->estado, $estadosPermitidos)) {
            return response()->json(['ok' => false, 'mensaje' => 'Estado no permitido.'], 422);
        }

        $reclamo = GarantiaReclamo::where('id_reclamo', $id)
            ->where('id_negocio', $user->id_negocio)
            ->firstOrFail();

        $reclamo->update(['estado' => $request->estado]);

        CatalogService::invalidateGarantiasIndex($user->id_negocio);

        return response()->json(['ok' => true, 'mensaje' => 'Estado actualizado.']);
    }
}