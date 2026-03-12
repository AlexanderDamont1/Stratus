<?php

namespace App\Events;

use App\Models\Pedido;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PedidoUpdated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $pedido;
    public $action;

    public function __construct(Pedido $pedido, $action = 'updated')
    {
        $this->pedido = $pedido;
        $this->action = $action;
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('vendedor.' . $this->pedido->id_usuario),
        ];
    }

    public function broadcastAs(): string
    {
        return 'pedido.updated';
    }

    public function broadcastWith(): array
    {
        return [
            'id_pedido' => $this->pedido->id_pedido,
            'action'    => $this->action,
            'pedido'    => [
                'id_pedido'  => $this->pedido->id_pedido,
                'negocio'    => $this->pedido->negocio->nombre_negocio ?? '—',
                'usuario'    => $this->pedido->usuario->nombre_usuario ?? '—',
                'status'     => $this->getStatusLabel($this->pedido->status),
                'status_num' => $this->pedido->status,
                'notas'      => $this->pedido->notas ?? '',
                'fecha'      => $this->pedido->created_at
                                    ? $this->pedido->created_at->format('d/m/Y H:i')
                                    : now()->format('d/m/Y H:i'),
                'updated_at' => $this->pedido->updated_at
                                    ? $this->pedido->updated_at->format('d/m/Y H:i')
                                    : now()->format('d/m/Y H:i'),
                'items' => $this->pedido->items->map(fn($i) => [
                    'id_modelo'  => $i->id_modelo,
                    'id_voltaje' => $i->id_voltaje,
                    'id_color'   => $i->id_color,
                    'modelo'     => $i->modelo->nombre_modelo ?? '—',
                    'voltaje'    => $i->voltaje->voltaje ?? '—',
                    'color'      => $i->color->color ?? '—',
                    'cantidad'   => $i->cantidad,
                ]),
            ],
        ];
    }

    private function getStatusLabel($status): string
    {
        return match ($status) {
            1       => 'Solicitado',
            2       => 'Preparado',
            3       => 'Entregado',
            default => 'Desconocido',
        };
    }
}