<?php


namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * AuditLineWritten
 *
 * Se dispara cada vez que AuditLogger escribe una línea.
 * Usa ShouldBroadcastNow para no depender de la queue (inmediato).
 * Canal privado: solo el root puede escucharlo.
 */
class AuditLineWritten implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public readonly string $line
    ) {}

    /**
     * Canal privado exclusivo para root.
     * El frontend se suscribe a Echo.private('audit.root')
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('audit.root'),
        ];
    }

    /**
     * Nombre del evento que escucha Alpine:
     * .listen('.audit.line', ...)
     */
    public function broadcastAs(): string
    {
        return 'audit.line';
    }

    public function broadcastWith(): array
    {
        return [
            'line' => $this->line,
        ];
    }
}