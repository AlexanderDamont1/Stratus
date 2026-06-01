<?php

namespace App\Http\Controllers;

use App\Events\StockActualizado;
use App\Models\Inventario;
use App\Models\ProductoModelo;
use App\Services\CatalogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class InventarioController extends Controller
{
    /* =====================================================
     | INDEX
     ===================================================== */

    public function index()
    {
        $user      = auth()->user();
        $idNegocio = $user->id_negocio;
        $esAdmin   = $user->id_rol === 1;

        if ($esAdmin) {
            $inventario = CatalogService::getInventarioByNegocio($idNegocio);
        } else {
            $inventario = CatalogService::getInventarioBySucursal($idNegocio, $user->id_usuario);
        }

        return view('inventario.index', compact('inventario', 'esAdmin'));
    }

    /* =====================================================
     | SINCRONIZAR DESDE BICICLETAS
     ===================================================== */

    public function sincronizar()
    {
        $user = auth()->user();
        if ($user->id_rol !== 1) abort(403);

        $idNegocio = $user->id_negocio;

        $registros = Inventario::where('id_negocio', $idNegocio)
            ->whereNotNull('id_producto_modelo')
            ->get();

        DB::transaction(function () use ($registros, $idNegocio) {
            foreach ($registros as $inv) {
                $pm = ProductoModelo::find($inv->id_producto_modelo);
                if (!$pm) continue;

                $cantidad = \App\Models\Bicicleta::where('id_negocio', $idNegocio)
                    ->where('id_modelo',  $pm->id_modelo)
                    ->where('id_voltaje', $pm->id_voltaje)
                    ->where('id_usuario', $inv->id_usuario)
                    ->where('status', 1)
                    ->count();

                $inv->update(['cantidad' => $cantidad]);
            }
        });

        CatalogService::invalidateInventario($idNegocio);

        return response()->json([
            'ok'      => true,
            'mensaje' => 'Inventario sincronizado correctamente.',
        ]);
    }

    /* =====================================================
     | UPDATE
     | Solo stock_minimo para bicicletas
     | cantidad + stock_minimo para accesorios
     ===================================================== */

    public function update(Request $request, string $idInventario)
    {
        $user      = auth()->user();
        $idNegocio = $user->id_negocio;

        $inventario = Inventario::where('id_inventario', $idInventario)
            ->where('id_negocio', $idNegocio)
            ->firstOrFail();

        $request->validate([
            'stock_minimo' => 'nullable|integer|min:0',
            'cantidad'     => 'nullable|integer|min:0',
        ]);

        $data = [];

        if ($request->has('stock_minimo')) {
            $data['stock_minimo'] = $request->stock_minimo;
        }

        $esBicicleta = $inventario->productoModelo?->producto?->tipo === '2';
        $esAccesorio = !$esBicicleta && $inventario->id_producto !== null;

        if ($request->has('cantidad') && !$esBicicleta) {
            $data['cantidad'] = $request->cantidad;
        }

        $inventario->update($data);

        CatalogService::invalidateInventario($idNegocio, $inventario->id_usuario);

        // ── Notificar stock actualizado a vendedores via Reverb ───────────
        // Solo accesorios con id_usuario asignado (stock de sucursal)
        if ($esAccesorio && $inventario->id_usuario && $request->has('cantidad')) {
            event(new StockActualizado(
                idNegocio:  $idNegocio,
                idUsuario:  $inventario->id_usuario,
                accesorios: [[
                    'id_producto' => $inventario->id_producto,
                    'stock'       => (int) $inventario->cantidad,
                ]],
            ));
        }

        return response()->json(['ok' => true]);
    }

    /* =====================================================
     | STOCK BAJO — ALERTA
     ===================================================== */

    public function stockBajo()
    {
        $user      = auth()->user();
        $idNegocio = $user->id_negocio;
        $esAdmin   = $user->id_rol === 1;

        $query = Inventario::with(['productoModelo.producto', 'sucursal'])
            ->where('id_negocio', $idNegocio)
            ->stockBajo();

        if (!$esAdmin) {
            $query->sucursal($user->id_usuario);
        }

        return response()->json($query->get());
    }
}