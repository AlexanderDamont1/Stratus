<?php

namespace App\Services;

use App\Models\Cupon;
use App\Models\CuponUso;
use Illuminate\Support\Facades\Cache;

class CuponService
{
    const TTL = 300; // 5 min

    // ── Buscar y validar cupón por código ────────────────────────────────

    public static function buscarPorCodigo(string $codigo, string $idNegocio): ?Cupon
    {
        return Cache::remember(
            "cupon:codigo:{$idNegocio}:" . strtoupper(trim($codigo)),
            self::TTL,
            fn () => Cupon::with('reglas')
                ->where('codigo', strtoupper(trim($codigo)))
                ->where('id_negocio', $idNegocio)
                ->first()
        );
    }

    public static function validar(
        Cupon $cupon,
        array $items,
        string $idUsuario, // sucursal que aplica el cupón
    ): array // ['valido' => bool, 'mensaje' => string, 'descuento' => float]
    {
        if (!$cupon->estaVigente()) {
            return ['valido' => false, 'mensaje' => 'El cupón no está vigente o ya fue agotado.', 'descuento' => 0];
        }

        $reglas = $cupon->reglas;

        // ── Validar reglas ────────────────────────────────────────────────

        foreach ($reglas as $regla) {
            switch ($regla->tipo) {

                case 'sucursal':
                    // null = todas las sucursales
                    if ($regla->valor !== null && $regla->valor !== $idUsuario) {
                        return ['valido' => false, 'mensaje' => 'Este cupón no aplica para tu sucursal.', 'descuento' => 0];
                    }
                    break;

                case 'cantidad_minima':
                    $totalCantidad = collect($items)->sum('cantidad');
                    if ($totalCantidad < (int) $regla->valor) {
                        return [
                            'valido'    => false,
                            'mensaje'   => "El cupón requiere mínimo {$regla->valor} productos.",
                            'descuento' => 0,
                        ];
                    }
                    break;

                case 'modelo':
                    // Al menos un item debe ser del modelo requerido
                    if ($regla->valor !== null) {
                        $tieneModelo = collect($items)->contains('id_modelo', $regla->valor);
                        if (!$tieneModelo) {
                            return ['valido' => false, 'mensaje' => 'El cupón no aplica para los productos seleccionados.', 'descuento' => 0];
                        }
                    }
                    break;

                case 'marca':
                    if ($regla->valor !== null) {
                        $tieneMarca = collect($items)->contains('id_marca', $regla->valor);
                        if (!$tieneMarca) {
                            return ['valido' => false, 'mensaje' => 'El cupón no aplica para la marca seleccionada.', 'descuento' => 0];
                        }
                    }
                    break;
            }
        }

        // ── Calcular descuento ────────────────────────────────────────────

        $descuento = self::calcularDescuento($cupon, $items);

        return [
            'valido'    => true,
            'mensaje'   => '¡Cupón aplicado correctamente!',
            'descuento' => $descuento,
        ];
    }

    public static function calcularDescuento(Cupon $cupon, array $items): float
    {
        $total = collect($items)->sum(fn($i) => $i['precio'] * $i['cantidad']);

        if ($cupon->aplicaAlTotal()) {
            if ($cupon->esPorcentaje()) {
                return round($total * ($cupon->valor_descuento / 100), 2);
            }
            return min($cupon->valor_descuento, $total); // no puede ser mayor al total
        }

        // Aplica por producto — solo a los items que cumplen las reglas
        $reglas   = $cupon->reglas->keyBy('tipo');
        $idModelo = $reglas->get('modelo')?->valor;
        $idVoltaje = $reglas->get('voltaje')?->valor;
        $idMarca  = $reglas->get('marca')?->valor;

        $itemsAplicables = collect($items)->filter(function ($item) use ($idModelo, $idVoltaje, $idMarca) {
            if ($idModelo  && ($item['id_modelo']  ?? null) !== $idModelo)  return false;
            if ($idVoltaje && ($item['id_voltaje'] ?? null) !== $idVoltaje) return false;
            if ($idMarca   && ($item['id_marca']   ?? null) !== $idMarca)   return false;
            return true;
        });

        $subtotalAplicable = $itemsAplicables->sum(fn($i) => $i['precio'] * $i['cantidad']);

        if ($cupon->esPorcentaje()) {
            return round($subtotalAplicable * ($cupon->valor_descuento / 100), 2);
        }

        return min($cupon->valor_descuento, $subtotalAplicable);
    }

    // ── Registrar uso ─────────────────────────────────────────────────────

    public static function registrarUso(
        string $idCupon,
        string $idVenta,
        string $idNegocio,
        string $idUsuario,
        float  $descuentoAplicado,
    ): void {
        CuponUso::create([
            'id_cupon'           => $idCupon,
            'id_venta'           => $idVenta,
            'id_negocio'         => $idNegocio,
            'id_usuario'         => $idUsuario,
            'descuento_aplicado' => $descuentoAplicado,
        ]);

        Cupon::where('id_cupon', $idCupon)->increment('usos_actuales');

        self::invalidar($idCupon, $idNegocio);
    }

    // ── Invalidar caché ───────────────────────────────────────────────────

    public static function invalidar(string $idCupon, string $idNegocio): void
    {
        $cupon = Cupon::find($idCupon);
        if ($cupon) {
            Cache::forget("cupon:codigo:{$idNegocio}:" . strtoupper($cupon->codigo));
        }
        Cache::forget("cupones:negocio:{$idNegocio}");
    }

    public static function getCuponesByNegocio(string $idNegocio)
    {
        return Cache::remember(
            "cupones:negocio:{$idNegocio}",
            self::TTL,
            fn () => Cupon::with('reglas')
                ->where('id_negocio', $idNegocio)
                ->orderByDesc('created_at')
                ->get()
        );
    }


    public static function getProductoGratis(Cupon $cupon): ?array
{
    if (!$cupon->id_producto_gratis) return null;

    $producto = $cupon->productoGratis ?? \App\Models\Producto::find($cupon->id_producto_gratis);
    if (!$producto) return null;

    return [
        'id_producto'    => $producto->id_producto,
        'nombre_producto'=> $producto->nombre_producto,
        'precio'         => (float) $producto->precio,
    ];
}
}