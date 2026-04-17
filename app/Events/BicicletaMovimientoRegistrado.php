<?php

namespace App\Events;

use App\Models\BicicletaMovimiento;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BicicletaMovimientoRegistrado implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public BicicletaMovimiento $movimiento
    ) {}

    public function broadcastOn(): array
    {
        return [
            new Channel("movimientos.{$this->movimiento->id_negocio}"),
        ];
    }

    public function broadcastAs(): string
    {
        return 'movimiento.nuevo';
    }

    public function broadcastWith(): array
    {
        return [
            'id'               => $this->movimiento->id,
            'num_serie'        => $this->movimiento->num_serie,
            'tipo_movimiento'  => $this->movimiento->tipo_movimiento,
            'origen'           => $this->movimiento->origen,
            'destino'          => $this->movimiento->destino,
            'notas'            => $this->movimiento->notas,
            'fecha_movimiento' => $this->movimiento->fecha_movimiento->toDateTimeString(),
            // ✅ nombre_usuario — no 'name'
            'usuario'          => $this->movimiento->usuario?->nombre_usuario,
        ];
    }
}