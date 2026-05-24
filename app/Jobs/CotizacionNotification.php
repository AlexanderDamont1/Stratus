<?php

namespace App\Notifications;

use App\Models\Cotizacion;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class CotizacionNotification extends Notification
{
    use Queueable;

    public function __construct(
        public readonly Cotizacion $cotizacion,
        public readonly string     $nombreCliente,
        public readonly string     $nombreNegocio,
        public readonly string     $tipoOt,        // 'reparacion' | 'mantenimiento'
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $urlResponder = route('cotizacion.show', $this->cotizacion->token);

        // Vista diferente según tipo de OT
        $view = $this->tipoOt === 'mantenimiento'
            ? 'emails.cotizacion-mantenimiento'
            : 'emails.cotizacion-reparacion';

        return (new MailMessage)
            ->subject("Cotización de servicio — {$this->nombreNegocio}")
            ->view($view, [
                'cotizacion'    => $this->cotizacion,
                'nombreCliente' => $this->nombreCliente,
                'nombreNegocio' => $this->nombreNegocio,
                'urlResponder'  => $urlResponder,
            ]);
    }
}