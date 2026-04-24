<?php

namespace App\Events;

use App\Models\Negocio;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;

class StatsActualizadas implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets;

    public function broadcastOn(): PrivateChannel
    {
        return new PrivateChannel('root');
    }

    public function broadcastAs(): string
    {
        return 'stats.actualizadas';
    }

    public function broadcastWith(): array
    {
        return [
            'en_trial'             => Negocio::where('negocio_status', 'trial')->count(),
            'activos'              => Negocio::where('negocio_status', 'activo')->count(),
            'trial_expirado'       => Negocio::where('negocio_status', 'trial_expirado')->count(),
            'suscripcion_expirada' => Negocio::where('negocio_status', 'suscripcion_expirada')->count(),
            'suspendidos'          => Negocio::where('negocio_status', 'suspendido')->count(),
        ];
    }
}