<?php

namespace App\Services;

use App\Models\DetalleVenta;
use App\Models\NegocioConfig;
use App\Models\Venta;
use App\Models\VentaPago;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Núcleo compartido de "crear una venta con pagos divididos + registrarla en
 * caja", usado tanto por el cobro de OTs (ReparacionService::cobrar) como por
 * la venta directa de piezas sueltas (StockService::venderSuelta). El flujo
 * de venta normal de mostrador (VentaController::store) es más complejo
 * (carrito de bicicletas/accesorios, cupones, stock de inventario) y no pasa
 * por aquí — pero comparte exactamente el mismo patrón de Venta+VentaPago+
 * CajaService::registrarVenta().
 */
class VentaService
{
    /**
     * @param array $datos ['id_cliente','total','origen','id_reparacion'?,
     *                       'lineas' => [['id_producto'?,'id_pieza'?,'concepto'?,
     *                                     'precio_unitario','cantidad'?,'num_serie'?], ...]]
     * @param array $pagos [['metodo','monto','referencia'?], ...]
     */
    public static function crearVentaConPagos(
        array  $datos,
        array  $pagos,
        string $idUsuario,
        string $idNegocio
    ): Venta {
        self::validarPagos($pagos, (float) $datos['total'], $idNegocio);

        $venta = DB::transaction(function () use ($datos, $pagos, $idUsuario, $idNegocio) {
            $venta = Venta::create([
                'id_negocio'    => $idNegocio,
                'id_cliente'    => $datos['id_cliente'],
                'id_usuario'    => $idUsuario,
                'total'         => $datos['total'],
                'origen'        => $datos['origen'],
                'id_reparacion' => $datos['id_reparacion'] ?? null,
            ]);

            foreach ($datos['lineas'] as $linea) {
                DetalleVenta::create([
                    'id_venta'        => $venta->id_venta,
                    'id_negocio'      => $idNegocio,
                    'id_producto'     => $linea['id_producto'] ?? null,
                    'id_pieza'        => $linea['id_pieza']    ?? null,
                    'concepto'        => $linea['concepto']    ?? null,
                    'num_serie'       => $linea['num_serie']   ?? null,
                    'precio_unitario' => $linea['precio_unitario'],
                    'cantidad'        => $linea['cantidad']    ?? 1,
                ]);
            }

            foreach ($pagos as $pago) {
                VentaPago::create([
                    'id_venta'   => $venta->id_venta,
                    'id_negocio' => $idNegocio,
                    'metodo'     => $pago['metodo'],
                    'monto'      => $pago['monto'],
                    'referencia' => $pago['referencia'] ?? null,
                ]);
            }

            return $venta;
        });

        self::registrarEnCaja($venta, $idUsuario, $idNegocio);

        // Recalcula estadisticas_diarias (Ingresos totales, distribución por
        // método de pago, etc.) — sin esto, el cobro de una OT o la venta de
        // una pieza suelta nunca aparecería en esas cifras del dashboard,
        // igual que ya hace VentaController::store() para la venta normal.
        \App\Jobs\ActualizarEstadisticasDiarias::dispatch($idNegocio, now()->toDateString())
            ->onQueue('default');

        return $venta;
    }

    private static function validarPagos(array $pagos, float $total, string $idNegocio): void
    {
        if (empty($pagos)) {
            abort(422, 'Debes indicar al menos un método de pago.');
        }

        $config         = CatalogService::getConfigNegocio($idNegocio);
        $metodosActivos = $config['metodos_pago'] ?? ['efectivo'];

        $opcionesMap = collect(
            NegocioConfig::where('clave', 'metodos_pago')
                ->where('activo', true)
                ->value('opciones') ?? []
        )->keyBy('value');

        $efectivos = 0;
        foreach ($pagos as $pago) {
            if (!in_array($pago['metodo'], $metodosActivos)) {
                abort(422, "Método de pago '{$pago['metodo']}' no está habilitado para este negocio.");
            }
            if ($opcionesMap[$pago['metodo']]['es_efectivo'] ?? false) {
                $efectivos++;
            }
        }

        if ($efectivos > 1) {
            abort(422, 'Solo puede haber un método de efectivo por venta.');
        }

        $suma = array_sum(array_column($pagos, 'monto'));
        if (round($suma, 2) < round($total, 2)) {
            abort(422, 'La suma de pagos no cubre el total.');
        }
    }

    /**
     * Best-effort, igual que en VentaController::store: si falla el registro
     * en caja la venta ya quedó guardada (commit previo) — no se revierte,
     * solo se loguea. Auto-abre sesión con fondo 0 si hace falta.
     */
    private static function registrarEnCaja(Venta $venta, string $idUsuario, string $idNegocio): void
    {
        try {
            $caja = CajaService::cajaDeUsuario($idUsuario, $idNegocio);
            if (!$caja) {
                Log::warning('VentaService: sin caja configurada, no se registra en caja', [
                    'id_venta' => $venta->id_venta,
                ]);
                return;
            }

            $sesion = CajaService::sesionActiva($caja->id_caja)
                ?? CajaService::abrirSesion($caja, $idUsuario, 0.0);

            CajaService::registrarVenta($venta, $idUsuario, $idNegocio);
        } catch (\Throwable $e) {
            Log::error('VentaService: error inesperado al registrar en caja', [
                'id_venta' => $venta->id_venta,
                'mensaje'  => $e->getMessage(),
            ]);
        }
    }
}
