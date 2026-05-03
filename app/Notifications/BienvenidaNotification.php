<?php
// app/Notifications/BienvenidaNotification.php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Carbon\Carbon;

class BienvenidaNotification extends Notification
{
    public function __construct(
        public readonly string $nombre,
        public readonly string $nombreNegocio,
        public readonly ?string $trialEndsAt = null,
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $trialFecha = $this->trialEndsAt
            ? Carbon::parse($this->trialEndsAt)->translatedFormat('d \d\e F \d\e Y')
            : null;

        return (new MailMessage)
            ->subject('¡Bienvenido a ArrowX! ')
            ->view('emails.bienvenida', [
                'nombre'       => $this->nombre,
                'nombreNegocio'=> $this->nombreNegocio,
                'trialFecha'   => $trialFecha,
            ]);
    }
}