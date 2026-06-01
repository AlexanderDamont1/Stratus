<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class StockActualizado implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public readonly string $idNegocio,
        public readonly string $idUsuario,
        public readonly array  $accesorios, // [['id_producto' => '...', 'stock' => 3], ...]
    ) {}

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel("negocio.{$this->idNegocio}"),
        ];
    }

    public function broadcastAs(): string
    {
        return 'stock.actualizado';
    }

    public function broadcastWith(): array
    {
        return [
            'id_usuario' => $this->idUsuario,
            'accesorios' => $this->accesorios,
        ];
    }
}