<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\CatalogService;
use App\Services\BicicletaMovimientoService;
use Illuminate\Http\Request;

class MovimientoController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        if (!in_array($user->id_rol, [1, 2])) abort(403);

        $recientes = CatalogService::getMovimientosRecientes($user->id_negocio)
            ->map(fn ($m) => [
                'id'               => $m->id_movimiento,
                'num_serie'        => $m->num_serie,
                'tipo_movimiento'  => $m->tipo_movimiento,
                'origen'           => $m->origen,
                'destino'          => $m->destino,
                'notas'            => $m->notas,
                'fecha_movimiento' => $m->fecha_movimiento->toDateTimeString(),
                'usuario'          => $m->usuario?->nombre_usuario,
            ]);

        return view('administrador.movimientos.index', compact('recientes'));
    }

    /* ─── AUTOCOMPLETE ─────────────────────────────────── */
    public function buscar(Request $request)
    {
        $user = auth()->user();
        $q    = trim($request->get('q', ''));

        if (strlen($q) < 2) return response()->json([]);

        // ✅ searchBicicletas ya usa cache (TTL 1h, clave md5 del query)
        $resultados = CatalogService::searchBicicletas($q, $user->id_negocio, 8);

        return response()->json($resultados);
    }

    /* ─── HISTORIAL POR SERIE ──────────────────────────── */
    public function historial(string $numSerie)
    {
        $user = auth()->user();

        // ✅ Cache — no toca la tabla bicicletas directamente
        $bicicleta = CatalogService::getBicicletaBySerie($numSerie, $user->id_negocio);

        if (!$bicicleta || $bicicleta->id_negocio !== $user->id_negocio) {
            return response()->json(['error' => 'Serie no encontrada.'], 404);
        }

        // ✅ Cache — no toca la tabla bicicleta_movimientos directamente
        $movimientos = CatalogService::getHistorialMovimientos($numSerie, $user->id_negocio)
            ->map(fn ($m) => [
                'id'               => $m->id_movimiento,
                'num_serie'        => $m->num_serie,
                'tipo_movimiento'  => $m->tipo_movimiento,
                'origen'           => $m->origen,
                'destino'          => $m->destino,
                'notas'            => $m->notas,
                'fecha_movimiento' => $m->fecha_movimiento->toDateTimeString(),
                'usuario'          => $m->usuario?->nombre_usuario,
            ]);

        return response()->json([
            'movimientos' => $movimientos,
            'bicicleta'   => [
                'num_serie' => $bicicleta->num_serie,
                'marca'     => $bicicleta->modelo?->marca?->nombre_marca ?? '—',
                'modelo'    => $bicicleta->modelo?->nombre_modelo         ?? '—',
                'voltaje'   => $bicicleta->voltaje?->voltaje              ?? '—',
                'status'    => $bicicleta->status,
            ],
        ]);
    }
}
