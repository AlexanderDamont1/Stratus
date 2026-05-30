<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class VentaRegistrada implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public readonly string $idNegocio,
        public readonly string $idUsuario,      // vendedor que hizo la venta
        public readonly string $nombreVendedor,
        public readonly float  $total,
        public readonly int    $ventasCount,    // total ventas de la sesión
        public readonly float  $totalSistema,   // total acumulado sesión
    ) {}

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel("negocio.{$this->idNegocio}"),
        ];
    }

    public function broadcastAs(): string
    {
        return 'venta.registrada';
    }

    public function broadcastWith(): array
    {
        return [
            'id_usuario'      => $this->idUsuario,
            'nombre_vendedor' => $this->nombreVendedor,
            'total'           => $this->total,
            'ventas_count'    => $this->ventasCount,
            'total_sistema'   => $this->totalSistema,
            'hora'            => now()->format('H:i'),
        ];
    }
}