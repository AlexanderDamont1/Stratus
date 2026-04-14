<?php

namespace App\Listeners;

use App\Events\BicicletaCreada;
use App\Models\Inventario;
use App\Models\ProductoModelo;
use Illuminate\Support\Facades\Log;

class ActualizarInventarioAlCrear
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(BicicletaCreada $event): void
    {
        Log::info('[ActualizarInventarioAlCrear] Evento recibido', [
            'id_modelo'  => $event->idModelo,
            'id_voltaje' => $event->idVoltaje,
            'id_negocio' => $event->idNegocio,
            'id_usuario' => $event->idUsuario,
        ]);

        $pm = ProductoModelo::where('id_modelo',  $event->idModelo)
            ->where('id_voltaje', $event->idVoltaje)
            ->where('id_negocio', $event->idNegocio)
            ->when(
                $event->idUsuario,
                fn($q) => $q->where('id_usuario', $event->idUsuario),
                fn($q) => $q->whereNull('id_usuario')
            )
            ->first();

        if (!$pm) {
            Log::warning('[ActualizarInventarioAlCrear] No existe ProductoModelo', [
                'id_modelo'  => $event->idModelo,
                'id_voltaje' => $event->idVoltaje,
                'id_negocio' => $event->idNegocio,
                'id_usuario' => $event->idUsuario,
            ]);
            return;
        }

        $inv = Inventario::firstOrCreate(
            [
                'id_producto_modelo' => $pm->id_producto_modelo,
                'id_negocio'         => $event->idNegocio,
                'id_usuario'         => $event->idUsuario,
            ],
            [
                'id_inventario' => 'INV' . strtoupper(substr(md5(uniqid()), 0, 13)),
                'cantidad'      => 0,
                'stock_minimo'  => 3,
            ]
        );

        $inv->increment('cantidad');

        \App\Services\CatalogService::invalidateInventario($event->idNegocio, $event->idUsuario);
        \App\Services\CatalogService::invalidateProductosConRelaciones($event->idNegocio, $event->idUsuario);

        Log::info('[ActualizarInventarioAlCrear] Inventario actualizado', [
            'id_inventario'  => $inv->id_inventario,
            'cantidad_nueva' => $inv->fresh()->cantidad,
        ]);
    }
}
