<?php

namespace App\Http\Controllers;

use App\Services\CatalogService;
use App\Services\CuponService;
use Illuminate\Http\Request;

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

     
        $todosLosProductos = CatalogService::getProductosConRelaciones(
            $user->id_negocio,
            $user->id_usuario
        )->keyBy('id_producto');

        // ── Enriquecer items con datos de modelo/marca/voltaje ────────────────
        $items = collect($request->items)->map(function ($item) use ($todosLosProductos, $user) {
            $producto = $todosLosProductos[$item['id_producto']] ?? null;

            if (!$producto) return null;

            $pms = collect($producto->productoModelo ?? []);

            // Priorizar el ProductoModelo de esta sucursal, luego genérico, luego cualquiera
            $pm = $pms->firstWhere('id_usuario', $user->id_usuario)
                ?? $pms->firstWhere('id_usuario', null)
                ?? $pms->first();

            return [
                'id_producto' => $item['id_producto'],
                'id_voltaje'  => $pm?->id_voltaje,
                'id_modelo'   => $pm?->id_modelo,
              
                'id_marca'    => $pm?->modelo?->id_marca,
                'precio'      => (float) ($producto->precio ?? 0),
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
        $productoGratis    = CuponService::getProductoGratis($cupon);
        $gratisYaEnCarrito = false;
        $cantidadEnCarrito = 0;

        if ($productoGratis) {
            $idGratis      = $productoGratis['id_producto'];
            $itemEnCarrito = collect($request->items)->firstWhere('id_producto', $idGratis);

            if ($itemEnCarrito) {
                $gratisYaEnCarrito = true;
                $cantidadEnCarrito = (int) $itemEnCarrito['cantidad'];
            }
        }

        return response()->json([
            'valido'               => true,
            'mensaje'              => $resultado['mensaje'],
            'descuento'            => $resultado['descuento'],
            'producto_gratis'      => $productoGratis,
            'cantidad_en_carrito'  => $cantidadEnCarrito,
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