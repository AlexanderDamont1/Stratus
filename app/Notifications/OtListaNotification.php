<?php
// app/Notifications/OtListaNotification.php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class OtListaNotification extends Notification
{
    public function __construct(
        public readonly string $idOt,
        public readonly string $numSerie,
        public readonly string $nombreCliente,
        public readonly string $nombreNegocio,
        public readonly string $nombreSucursal,
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("Tu bicicleta está lista para recoger — {$this->idOt}")
            ->view('emails.ot-lista', [
                'idOt'          => $this->idOt,
                'numSerie'      => $this->numSerie,
                'nombreCliente' => $this->nombreCliente,
                'nombreNegocio' => $this->nombreNegocio,
                'nombreSucursal'=> $this->nombreSucursal,
            ]);
    }
}