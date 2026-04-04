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
        $pm = \App\Models\ProductoModelo::where('id_modelo',  $event->idModelo)
            ->where('id_voltaje', $event->idVoltaje)
            ->first();

        if (!$pm) return;

        $inv = \App\Models\Inventario::firstOrCreate(
            [
                'id_producto_modelo' => $pm->id_producto_modelo,
                'id_negocio'         => $event->idNegocio,
                'id_usuario'         => $event->idUsuario, // null = admin
            ],
            [
                'id_inventario' => 'INV' . strtoupper(substr(md5(uniqid()), 0, 13)),
                'cantidad'       => 0,
                'stock_minimo'   => 3,
            ]
        );

        $inv->increment('cantidad');
    }
}
