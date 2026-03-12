<?php

namespace App\Events;

use App\Models\Enlace;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class EnlaceUpdated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $enlace;
    public $action;

    public function __construct(Enlace $enlace, string $action = 'updated')
    {
        $this->enlace = $enlace;
        $this->action = $action;
    }

    public function broadcastOn(): array
    {
        // Canal privado del vendedor (rol 1) dueño del enlace
        return [
            new PrivateChannel('enlace-vendedor.' . $this->enlace->id_usuario1),
        ];
    }

    public function broadcastAs(): string
    {
        return 'enlace.updated';
    }

    public function broadcastWith(): array
    {
        return [
            'action' => $this->action,
            'enlace' => [
                'id_enlace'      => $this->enlace->id_enlace,
                'estado'         => $this->enlace->estado,
                'token_enlace'   => $this->enlace->token_enlace,
                'gestor'         => $this->enlace->usuarioDestino->nombre_usuario ?? '—',
                'gestor_correo'  => $this->enlace->usuarioDestino->correo ?? '—',
            ],
        ];
    }
}