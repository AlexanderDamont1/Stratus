<?php

namespace App\Events;

use App\Models\Negocio;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;

class NegocioExpirado implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets;

    public function __construct(
        public readonly Negocio $negocio,
        public readonly string  $tipo,
    ) {}

    // ── Manda a DOS canales ──────────────────────────────
    public function broadcastOn(): array
    {
        return [
            new Channel('negocio.' . $this->negocio->id_negocio), // usuarios del negocio
            new PrivateChannel('root'),                            // root lo ve en tiempo real
        ];
    }

    public function broadcastAs(): string
    {
        return 'negocio.expirado';
    }

    public function broadcastWith(): array
    {
        return [
            'id_negocio'     => $this->negocio->id_negocio,
            'nombre_negocio' => $this->negocio->nombre_negocio,
            'tipo'           => $this->tipo,
            'redirect'       => $this->tipo === 'trial_expirado'
                ? route('trial.expirado')
                : route('suscripcion.expirada'),
        ];
    }
}