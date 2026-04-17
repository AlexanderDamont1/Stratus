<?php

namespace App\Services;

use App\Events\BicicletaMovimientoRegistrado;
use App\Models\Bicicleta;
use App\Models\BicicletaMovimiento;
use Illuminate\Support\Facades\Auth;

class BicicletaMovimientoService
{
    public function registrar(
        string $num_serie,
        string $tipo_movimiento,
        array  $extra = []
    ): BicicletaMovimiento {

        // ✅ Usar cache — nunca tocar Bicicleta directamente
        $bicicleta = CatalogService::getBicicletaBySerie($num_serie);

        abort_if(!$bicicleta, 404, "Serie {$num_serie} no encontrada.");

        $movimiento = BicicletaMovimiento::create([
            // ✅ Generar PK string (igual al patrón del resto del proyecto)
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

        // ✅ Eager-load usuario ANTES del broadcast para evitar N+1 en el evento
        $movimiento->load('usuario');

        // Invalidar caches afectados
        CatalogService::invalidateMovimientos($num_serie, $bicicleta->id_negocio);

        // Broadcast (toOthers para no duplicar en quien lo registró)
        broadcast(new BicicletaMovimientoRegistrado($movimiento))->toOthers();

        return $movimiento;
    }

    /* ============ MÉTODOS SEMÁNTICOS ============ */

    public function entradaStockGeneral(string $num_serie, ?string $id_pedido = null): BicicletaMovimiento
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

    public function ajuste(string $num_serie, string $notas): BicicletaMovimiento
    {
        return $this->registrar($num_serie, 'ajuste', ['notas' => $notas]);
    }
}