<?php

namespace App\Notifications;

use App\Models\Cotizacion;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class CotizacionNotification extends Notification
{
    public function __construct(
        public readonly Cotizacion $cotizacion,
        public readonly string     $nombreCliente,
        public readonly string     $nombreNegocio,
        public readonly string     $tipoOt, // 'reparacion' o 'mantenimiento'
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $subject = match ($this->tipoOt) {
            'mantenimiento' => "Sugerencia de piezas para tu mantenimiento — {$this->cotizacion->id_cotizacion}",
            'garantia'      => "Cotización de tu reclamo de garantía — {$this->cotizacion->id_cotizacion}",
            default         => "Cotización de reparación lista — {$this->cotizacion->id_cotizacion}",
        };

        $nombreVista = "emails.cotizacion-{$this->tipoOt}";
        $urlBase = route('cotizacion.responder', ['token' => $this->cotizacion->token]);
        $urlResponder = route('cotizacion.show', $this->cotizacion->token);
    return (new MailMessage)
        ->subject($subject)
        ->greeting("Hola, {$this->nombreCliente}")
        ->line("Tu cotización está lista. Total: $" . number_format($this->cotizacion->costo_total, 2))
        ->action('Ver cotización', $urlResponder)
        ->line('El enlace expira en 12 horas.')
            ->view($nombreVista, [
                'cotizacion'    => $this->cotizacion,
                'nombreCliente' => $this->nombreCliente,
                'nombreNegocio' => $this->nombreNegocio,
                'tipoOt'        => $this->tipoOt,
                'urlResponder'  => $urlBase,
                'urlAceptar'    => $urlBase . '?respuesta=1',
                'urlRechazar'   => $urlBase . '?respuesta=0',
                'urlVer'        => route('cotizacion.show', ['token' => $this->cotizacion->token]),
                'expiresAt'     => $this->cotizacion->expires_at,
            ]);
    }


    
}

