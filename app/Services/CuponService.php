<?php

namespace App\Services;

use App\Models\Cupon;
use App\Models\CuponUso;
use Illuminate\Support\Facades\Cache;

class CuponService
{
    const TTL = 300;

    public static function buscarPorCodigo(string $codigo, string $idNegocio): ?Cupon
    {
        return Cache::remember(
            "cupon:codigo:{$idNegocio}:" . strtoupper(trim($codigo)),
            self::TTL,
            fn() => Cupon::with('reglas')
                ->where('codigo', strtoupper(trim($codigo)))
                ->where('id_negocio', $idNegocio)
                ->first()
        );
    }

    public static function validar(Cupon $cupon, array $items, string $idUsuario, string $contexto = 'venta'): array
    {
        // Tipos permitidos por contexto:
        // 'venta'        → tipo 1 (descuento) y tipo 2 (accesorio gratis)
        // 'mantenimiento' → tipo 3 (mantenimiento)
        $tiposPermitidos = match ($contexto) {
            'mantenimiento' => ['3'],
            default         => ['1', '2'],
        };

        if (!in_array($cupon->tipo_cupon, $tiposPermitidos, strict: true)) {
            return ['valido' => false, 'mensaje' => 'Este cupón no es válido para ventas.', 'descuento' => 0];
        }

        if (!$cupon->estaVigente()) {
            return ['valido' => false, 'mensaje' => 'El cupón no está vigente o ya fue agotado.', 'descuento' => 0];
        }

        // ── Monto mínimo (campo directo del cupón) ────────────────────────────
        if ($cupon->monto_minimo > 0) {
            $totalCarrito = collect($items)->sum(fn($i) => $i['precio'] * $i['cantidad']);
            if ($totalCarrito < $cupon->monto_minimo) {
                return [
                    'valido'    => false,
                    'mensaje'   => 'Este cupón requiere una compra mínima de $'
                                . number_format($cupon->monto_minimo, 2) . '.',
                    'descuento' => 0,
                ];
            }
        }

        // ── Reglas (sucursal, marca, modelo, voltaje, monto_minimo como regla) ─
        foreach ($cupon->reglas as $regla) {
            switch ($regla->tipo) {

                case 'sucursal':
                    // null = aplica a todas las sucursales
                    if ($regla->valor !== null && $regla->valor !== $idUsuario) {
                        return ['valido' => false, 'mensaje' => 'Este cupón no aplica para tu sucursal.', 'descuento' => 0];
                    }
                    break;

                case 'voltaje':
                    if ($regla->valor !== null) {
                        // FIX: compara id_voltaje (string) con el valor de la regla usando
                        // comparación laxa para evitar falsos negativos por tipo (int vs string)
                        $tieneVoltaje = collect($items)->contains(
                            fn($i) => (string) ($i['id_voltaje'] ?? '') === (string) $regla->valor
                        );
                        if (!$tieneVoltaje) {
                            return ['valido' => false, 'mensaje' => 'El cupón no aplica para el voltaje de los productos seleccionados.', 'descuento' => 0];
                        }
                    }
                    break;

                case 'modelo':
                    if ($regla->valor !== null) {
                        $tieneModelo = collect($items)->contains(
                            fn($i) => (string) ($i['id_modelo'] ?? '') === (string) $regla->valor
                        );
                        if (!$tieneModelo) {
                            return ['valido' => false, 'mensaje' => 'El cupón no aplica para los productos seleccionados.', 'descuento' => 0];
                        }
                    }
                    break;

                case 'marca':
                    if ($regla->valor !== null) {
                        $tieneMarca = collect($items)->contains(
                            fn($i) => (string) ($i['id_marca'] ?? '') === (string) $regla->valor
                        );
                        if (!$tieneMarca) {
                            return ['valido' => false, 'mensaje' => 'El cupón no aplica para la marca seleccionada.', 'descuento' => 0];
                        }
                    }
                    break;

                // FIX: regla monto_minimo guardada en cupon_reglas (alternativa al campo directo)
                case 'monto_minimo':
                    if ($regla->valor !== null && (float) $regla->valor > 0) {
                        $totalCarrito = collect($items)->sum(fn($i) => $i['precio'] * $i['cantidad']);
                        if ($totalCarrito < (float) $regla->valor) {
                            return [
                                'valido'    => false,
                                'mensaje'   => 'Este cupón requiere una compra mínima de $'
                                            . number_format((float) $regla->valor, 2) . '.',
                                'descuento' => 0,
                            ];
                        }
                    }
                    break;
            }
        }

        $descuento = self::calcularDescuento($cupon, $items);

        $mensaje = $cupon->mensaje_vendedor
            ? $cupon->mensaje_vendedor
            : '¡Cupón aplicado correctamente!';

        return [
            'valido'    => true,
            'mensaje'   => $mensaje,
            'descuento' => $descuento,
        ];
    }

    public static function calcularDescuento(Cupon $cupon, array $items): float
    {
        if (empty($cupon->tipo_descuento)) {
            return 0.0;
        }

        $total = collect($items)->sum(fn($i) => $i['precio'] * $i['cantidad']);

        if ($cupon->aplicaAlTotal()) {
            if ($cupon->esPorcentaje()) {
                return round($total * ($cupon->valor_descuento / 100), 2);
            }
            return min((float) $cupon->valor_descuento, $total);
        }

        $reglas    = $cupon->reglas->keyBy('tipo');
        $idModelo  = $reglas->get('modelo')?->valor;
        $idVoltaje = $reglas->get('voltaje')?->valor;
        $idMarca   = $reglas->get('marca')?->valor;

        $itemsAplicables = collect($items)->filter(function ($item) use ($idModelo, $idVoltaje, $idMarca) {
            if ($idModelo  && (string) ($item['id_modelo']  ?? '') !== (string) $idModelo)  return false;
            if ($idVoltaje && (string) ($item['id_voltaje'] ?? '') !== (string) $idVoltaje) return false;
            if ($idMarca   && (string) ($item['id_marca']   ?? '') !== (string) $idMarca)   return false;
            return true;
        });

        if ($itemsAplicables->isEmpty()) return 0.0;

        if ($cupon->esPorcentaje()) {
            $subtotal = $itemsAplicables->sum(fn($i) => $i['precio'] * $i['cantidad']);
            return round($subtotal * ($cupon->valor_descuento / 100), 2);
        }

        $primerItem = $itemsAplicables->first();
        return min((float) $cupon->valor_descuento, (float) $primerItem['precio']);
    }

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

    public static function invalidar(string $idCupon, string $idNegocio): void
    {
        Cache::forget("cupones:negocio:{$idNegocio}");

        if ($idCupon) {
            $cupon = Cupon::find($idCupon);
            if ($cupon) {
                Cache::forget("cupon:codigo:{$idNegocio}:" . strtoupper($cupon->codigo));
            }
        }
    }

    public static function getCuponesByNegocio(string $idNegocio)
    {
        return Cache::remember(
            "cupones:negocio:{$idNegocio}",
            self::TTL,
            fn() => Cupon::with('reglas')
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
            'id_producto'     => $producto->id_producto,
            'nombre_producto' => $producto->nombre_producto,
            'precio'          => (float) $producto->precio,
        ];
    }
}