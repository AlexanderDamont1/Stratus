<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CatalogoActualizado implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public $idNegocio,
        public $tipo,    // 'marca' | 'modelo' | 'color' | 'voltaje'
        public $accion,  // 'creado' | 'actualizado' | 'eliminado'
        public $idMarca, // siempre se manda para saber qué card recargar
    ) {}

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel("catalogo.{$this->idNegocio}"),
        ];
    }

    public function broadcastAs(): string
    {
        return 'catalogo.actualizado';
    }

    public function broadcastWith(): array
    {
        return [
            'tipo'     => $this->tipo,
            'accion'   => $this->accion,
            'id_marca' => $this->idMarca,
        ];
    }
}