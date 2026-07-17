<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class VerificarEmailNotification extends Notification
{
    public function __construct(
        public readonly string $token,
        public readonly string $nombre,
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $url = route('verificar.email', ['token' => $this->token]);

        return (new MailMessage)
            ->subject('Verifica tu cuenta — ArrowK')
            ->view('emails.verificar-email', [
                'nombre' => $this->nombre,
                'url'    => $url,
            ]);
    }
}