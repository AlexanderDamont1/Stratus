<?php

namespace App\Services;

use App\Events\BicicletaMovimientoRegistrado;
use App\Models\Bicicleta;
use App\Models\BicicletaMovimiento;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class BicicletaMovimientoService
{
    public function registrar(
    string $num_serie,
    string $tipo_movimiento,
    array  $extra = []
    ): BicicletaMovimiento {

        Log::info('[BicicletaMovimientoService] registrar inicio', [
            'num_serie'       => $num_serie,
            'tipo_movimiento' => $tipo_movimiento,
        ]);

        // ✅ Usar cache — nunca tocar Bicicleta directamente
        $bicicleta = CatalogService::getBicicletaBySerie($num_serie, $extra['id_negocio'] ?? null);

        Log::info('[BicicletaMovimientoService] bicicleta encontrada', [
            'bicicleta'  => $bicicleta?->toArray(),
        ]);

        abort_if(!$bicicleta, 404, "Serie {$num_serie} no encontrada.");

        $movimiento = BicicletaMovimiento::create([
            'id_movimiento'    => 'MOV' . strtoupper(substr(md5(uniqid('', true)), 0, 12)),
            'num_serie'        => $num_serie,
            'id_negocio'       => $bicicleta->id_negocio,
            'id_usuario'       => $extra['id_usuario'] ?? Auth::user()?->id_usuario,
            'tipo_movimiento'  => $tipo_movimiento,
            'origen'           => $extra['origen']    ?? null,
            'destino'          => $extra['destino']   ?? null,
            'id_pedido'        => $extra['id_pedido'] ?? $bicicleta->id_pedido,
            'notas'            => $extra['notas']     ?? null,
            'fecha_movimiento' => $extra['fecha']     ?? now(),
        ]);

        Log::info('[BicicletaMovimientoService] movimiento creado', [
            'id_movimiento' => $movimiento->id_movimiento,
            'id_negocio'    => $movimiento->id_negocio,
        ]);

        $movimiento->load('usuario');

        CatalogService::invalidateMovimientos($num_serie, $bicicleta->id_negocio);

        broadcast(new BicicletaMovimientoRegistrado($movimiento))->toOthers();

        return $movimiento;
    }

    /* ============ MÉTODOS SEMÁNTICOS ============ */

    public function entradaStockGeneral(string $num_serie, ?string $id_pedido = null, ?string $id_negocio = null): BicicletaMovimiento
    {
        $extra = [
            'origen'  => 'Proveedor',
            'destino' => 'Stock general',
            'notas'   => $id_pedido 
                ? "Ingreso al stock general vía pedido #{$id_pedido}" 
                : "Ingreso al stock general (carga masiva)",
        ];

        if ($id_pedido) {
            $extra['id_pedido'] = $id_pedido;
        }

        if ($id_negocio) {
            $extra['id_negocio'] = $id_negocio;
        }

        return $this->registrar($num_serie, 'entrada_stock', $extra);
    }

    public function transferenciaASucursal(string $num_serie, string $nombre_sucursal): BicicletaMovimiento
    {
        return $this->registrar($num_serie, 'transferencia_sucursal', [
            'origen'  => 'Stock general',
            'destino' => $nombre_sucursal,
            'notas'   => "Transferido a sucursal: {$nombre_sucursal}",
        ]);
    }

    public function venta(string $num_serie, ?string $nombre_cliente = null): BicicletaMovimiento
    {
        return $this->registrar($num_serie, 'venta', [
            'origen'  => 'Sucursal',
            'destino' => 'Cliente',
            'notas'   => $nombre_cliente ? "Vendido a: {$nombre_cliente}" : 'Venta POS',
        ]);
    }

    public function entradaMantenimiento(string $num_serie, ?string $motivo = null): BicicletaMovimiento
    {
        return $this->registrar($num_serie, 'mantenimiento', [
            'origen'  => 'Sucursal',
            'destino' => 'Taller',
            'notas'   => $motivo ?? 'Ingreso a mantenimiento',
        ]);
    }

    /**
     * Vehículo que entra a taller por una OT (reparación, garantía o
     * mantenimiento — el sistema unificado de Reparaciones). Se dispara
     * al crear la OT.
     */
    public function ingresoOt(
        string  $num_serie,
        string  $idNegocio,
        string  $tipoOt,
        string  $idReparacion,
        ?string $motivo = null
    ): BicicletaMovimiento {
        $labelTipo = match ($tipoOt) {
            'garantia'      => 'Garantía',
            'mantenimiento' => 'Mantenimiento',
            default         => 'Reparación',
        };

        return $this->registrar($num_serie, 'ingreso_ot', [
            'id_negocio' => $idNegocio,
            'origen'     => 'Cliente',
            'destino'    => 'Taller',
            'notas'      => "Ingreso a taller — {$labelTipo} ({$idReparacion})"
                . ($motivo ? ": {$motivo}" : ''),
        ]);
    }

    /**
     * Vehículo que se entrega de vuelta al cliente al cerrar una OT
     * (estado 'entregada'). Contraparte de ingresoOt().
     */
    public function entregaOt(
        string $num_serie,
        string $idNegocio,
        string $tipoOt,
        string $idReparacion
    ): BicicletaMovimiento {
        $labelTipo = match ($tipoOt) {
            'garantia'      => 'Garantía',
            'mantenimiento' => 'Mantenimiento',
            default         => 'Reparación',
        };

        return $this->registrar($num_serie, 'entrega_ot', [
            'id_negocio' => $idNegocio,
            'origen'     => 'Taller',
            'destino'    => 'Cliente',
            'notas'      => "Entrega tras {$labelTipo} ({$idReparacion})",
        ]);
    }

    public function ajuste(string $num_serie, string $notas): BicicletaMovimiento
    {
        return $this->registrar($num_serie, 'ajuste', ['notas' => $notas]);
    }
}