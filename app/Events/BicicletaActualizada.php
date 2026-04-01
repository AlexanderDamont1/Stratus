<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;


class BicicletaActualizada implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public readonly string  $numSerie,
        public readonly string  $idNegocio,
        public readonly string  $idUsuario,
        public readonly string  $nombreVendedor,
        public readonly string  $modelo,
        public readonly string  $voltaje,
        public readonly string  $color,
        public readonly int     $status,
    ) {}

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel("catalogo.{$this->idNegocio}"),
        ];
    }

    public function broadcastAs(): string
    {
        return 'bicicleta.asignada';
    }

    public function broadcastWith(): array
    {

    Log::info('BicicletaActualizada broadcast', [
        'canal' => "catalogo.{$this->idNegocio}",
        'data'  => $this->numSerie,
    ]);
        return [
            'num_serie'       => $this->numSerie,
            'id_usuario'      => $this->idUsuario,
            'nombre_vendedor' => $this->nombreVendedor,
            'modelo'          => $this->modelo,
            'voltaje'         => $this->voltaje,
            'color'           => $this->color,
            'status'          => $this->status,
        ];
    }
}