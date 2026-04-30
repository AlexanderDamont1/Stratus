<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Services\CuponService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CuponValidarController extends Controller
{
    public function validar(Request $request)
    {
        $user = auth()->user();
        if ($user->id_rol != 2) abort(403);

        $request->validate([
            'codigo'              => 'required|string',
            'items'               => 'required|array|min:1',
            'items.*.id_producto' => 'required|string',
            'items.*.cantidad'    => 'required|integer|min:1',
        ]);

        $cupon = CuponService::buscarPorCodigo($request->codigo, $user->id_negocio);

        if (!$cupon) {
            return response()->json(['valido' => false, 'mensaje' => 'Cupón no encontrado.'], 404);
        }

        // ── Enriquecer items con datos de modelo/marca ────────────────────────
        $items = collect($request->items)->map(function ($item) use ($user) {
            $producto = Producto::with([
                'productoModelo.modelo.marca',
                'productoModelo.voltaje',
            ])
                ->where('id_producto', $item['id_producto'])
                ->where('id_negocio', $user->id_negocio)
                ->first();

            if (!$producto) return null;

            // productoModelo puede ser hasMany → tomamos el primero
            $pm = is_iterable($producto->productoModelo)
                ? collect($producto->productoModelo)->first()
                : $producto->productoModelo;

            return [
                'id_producto' => $item['id_producto'],
                'id_modelo'   => $pm?->id_modelo,
                'id_voltaje'  => $pm?->id_voltaje ?? $pm?->voltaje?->id_voltaje,
                'id_marca'    => $pm?->modelo?->id_marca,
                'precio'      => (float) $producto->precio,
                'cantidad'    => (int) $item['cantidad'],
            ];
        })->filter()->values()->toArray();

        $resultado = CuponService::validar($cupon, $items, $user->id_usuario);

        if (!$resultado['valido']) {
            return response()->json([
                'valido'  => false,
                'mensaje' => $resultado['mensaje'],
            ]);
        }

        // ── Resolver producto gratis ──────────────────────────────────────────
        $productoGratis      = CuponService::getProductoGratis($cupon);
        $gratisYaEnCarrito   = false;

        if ($productoGratis) {
            $idGratis          = $productoGratis['id_producto'];
            $gratisYaEnCarrito = collect($request->items)
                ->contains('id_producto', $idGratis);
        }

        return response()->json([
            'valido'               => true,
            'mensaje'              => $resultado['mensaje'],
            'descuento'            => $resultado['descuento'],
            'producto_gratis'      => $productoGratis,
            // true  → ya está en carrito, el front lo marca como "gratis" sin duplicar
            // false → no está, el front puede mostrarlo aparte como regalo
            'gratis_ya_en_carrito' => $gratisYaEnCarrito,
            'cupon' => [
                'id_cupon'           => $cupon->id_cupon,
                'nombre'             => $cupon->nombre,
                'tipo_descuento'     => $cupon->tipo_descuento,
                'valor_descuento'    => $cupon->valor_descuento,
                'aplica_a'           => $cupon->aplica_a,
                'id_producto_gratis' => $cupon->id_producto_gratis,
            ],
        ]);
    }
}