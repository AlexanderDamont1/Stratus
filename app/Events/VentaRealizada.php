<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class VentaRealizada implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public readonly string $idVenta,
        public readonly string $idNegocio,
        public readonly string $idVendedor,
        public readonly string $nombreVendedor,
        public readonly string $nombreCliente,
        public readonly float  $total,
        public readonly array  $bicicletas, // [['num_serie'=>..,'modelo'=>..,'color'=>..,'voltaje'=>..]]
    ) {}

    public function broadcastOn(): array
    {
        return [
            // Admin escucha en catalogo.{idNegocio}
            new PrivateChannel("catalogo.{$this->idNegocio}"),
            // Vendedor escucha su propio canal
            new PrivateChannel("user.{$this->idVendedor}"),
        ];
    }

    public function broadcastAs(): string
    {
        return 'venta.realizada';
    }

    public function broadcastWith(): array
    {
        Log::info('VentaRealizada broadcast', [
            'canal'    => "catalogo.{$this->idNegocio}",
            'id_venta' => $this->idVenta,
        ]);

        return [
            'id_venta'        => $this->idVenta,
            'id_negocio'      => $this->idNegocio,
            'id_vendedor'     => $this->idVendedor,
            'nombre_vendedor' => $this->nombreVendedor,
            'nombre_cliente'  => $this->nombreCliente,
            'total'           => $this->total,
            'bicicletas'      => $this->bicicletas,
        ];
    }
}