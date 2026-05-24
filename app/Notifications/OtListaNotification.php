<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class OtListaNotification extends Notification
{
    use Queueable;

    public function __construct(
        public readonly string $idReparacion,
        public readonly string $numSerie,
        public readonly string $nombreCliente,
        public readonly string $nombreNegocio,
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("¡Tu vehículo está listo! — {$this->nombreNegocio}")
            ->view('emails.ot-lista', [
                'idReparacion'  => $this->idReparacion,
                'numSerie'      => $this->numSerie,
                'nombreCliente' => $this->nombreCliente,
                'nombreNegocio' => $this->nombreNegocio,
            ]);
    }
}