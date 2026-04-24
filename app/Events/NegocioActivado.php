<?php

namespace App\Events;

use App\Models\Negocio;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;

class NegocioActivado implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets;

    public function __construct(public readonly Negocio $negocio) {}

    public function broadcastOn(): PrivateChannel
    {
        return new PrivateChannel('root');
    }

    public function broadcastAs(): string
    {
        return 'negocio.activado';
    }

    public function broadcastWith(): array
    {
        return [
            'id_negocio'       => $this->negocio->id_negocio,
            'negocio_status'   => $this->negocio->negocio_status,
            'subscribed_until' => $this->negocio->subscribed_until?->format('d/m/Y'),
            'dias_restantes'   => $this->negocio->diasRestantes(),
        ];
    }
}