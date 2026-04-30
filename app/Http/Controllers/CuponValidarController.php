<?php


namespace App\Http\Controllers;

use App\Models\Producto;
use App\Services\CuponService;
use Illuminate\Http\Request;

class CuponValidarController extends Controller
{
    public function validar(Request $request)
    {
        $user = auth()->user();
        if ($user->id_rol != 2) abort(403);

        $request->validate([
            'codigo' => 'required|string',
            'items'  => 'required|array|min:1',
            'items.*.id_producto' => 'required|string',
            'items.*.cantidad'    => 'required|integer|min:1',
        ]);

        $cupon = CuponService::buscarPorCodigo($request->codigo, $user->id_negocio);

        if (!$cupon) {
            return response()->json(['valido' => false, 'mensaje' => 'Cupón no encontrado.'], 404);
        }

        // Enriquecer items con datos del producto/modelo para validar reglas
        $items = collect($request->items)->map(function ($item) use ($user) {
            $producto = Producto::with('productoModelo.modelo.marca')
                ->where('id_producto', $item['id_producto'])
                ->where('id_negocio', $user->id_negocio)
                ->first();

            if (!$producto) return null;

            $pm = $producto->productoModelo?->first();

            return [
                'id_producto' => $item['id_producto'],
                'id_modelo'   => $pm?->id_modelo,
                'id_voltaje'  => $pm?->id_voltaje,
                'id_marca'    => $pm?->modelo?->id_marca,
                'precio'      => (float) $producto->precio,
                'cantidad'    => (int) $item['cantidad'],
            ];
        })->filter()->values()->toArray();

        $resultado = CuponService::validar($cupon, $items, $user->id_usuario);

        return response()->json([
            'valido'    => $resultado['valido'],
            'mensaje'   => $resultado['mensaje'],
            'descuento' => $resultado['descuento'],
            'producto_gratis'  => $resultado['valido']
                ? \App\Services\CuponService::getProductoGratis($cupon)
                : null,
            'cupon'     => $resultado['valido'] ? [
                'id_cupon'        => $cupon->id_cupon,
                'nombre'          => $cupon->nombre,
                'tipo_descuento'  => $cupon->tipo_descuento,
                'valor_descuento' => $cupon->valor_descuento,
                'aplica_a'        => $cupon->aplica_a,
                'id_producto_gratis' => $cupon->id_producto_gratis,
            ] : null,
        ]);
    }
}