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
            ->subject('Verifica tu cuenta — ArrowX')
            ->greeting("Hola, {$this->nombre}")
            ->line('Gracias por registrarte. Para activar tu cuenta haz clic en el botón.')
            ->action('Verificar cuenta', $url)
            ->line('Este enlace expira en 24 horas.')
            ->line('Si no creaste esta cuenta, ignora este correo.');
    }
}