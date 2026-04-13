<?php

namespace App\Events;

use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UsuarioCerroSesion implements ShouldBroadcastNow
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public readonly string $idUsuario,
        public readonly string $motivo = 'logout'
    ) {}

    // UsuarioCerroSesion.php
    public function broadcastOn(): array
    {

        return [new PrivateChannel("user.{$this->idUsuario}")];
    }

    public function broadcastAs(): string
    {
        return 'sesion.cerrada';
    }
}
