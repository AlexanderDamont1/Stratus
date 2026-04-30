<?php

namespace App\Notifications;

use App\Models\ReporteRobo;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class ConfirmarReporteRoboNotification extends Notification
{
    public function __construct(
        public readonly ReporteRobo $reporte,
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $url    = route('robo.confirmar', $this->reporte->token_confirmacion);
        $bici   = $this->reporte->bicicleta;
        $modelo = $bici?->modelo?->nombre_modelo ?? '—';
        $marca  = $bici?->modelo?->marca?->nombre_marca ?? '—';
        $serie  = $this->reporte->num_serie;

        return (new MailMessage)
            ->subject('Reporte de robo registrado — confirma tu vehículo')
            ->greeting("Hola, {$notifiable->nombre_cliente}")
            ->line('Hemos recibido un reporte de robo para uno de tus vehículos registrados en ArrowX.')
            ->line("**Vehículo:** {$marca} {$modelo}")
            ->line("**N° de serie:** {$serie}")
            ->line('Si fuiste tú quien reportó el robo, confirma haciendo clic en el botón de abajo. El enlace expira en **48 horas**.')
            ->action('Confirmar reporte de robo', $url)
            ->line('Si no reconoces este reporte, ignora este correo.')
            ->salutation('Equipo ArrowX — CloudLabs');
    }
}