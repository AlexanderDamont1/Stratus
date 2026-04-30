<?php

namespace App\Notifications;

use App\Models\ReporteRobo;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class ReporteRoboConfirmadoNotification extends Notification
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
        $bici   = $this->reporte->bicicleta;
        $modelo = $bici?->modelo?->nombre_modelo ?? '—';
        $marca  = $bici?->modelo?->marca?->nombre_marca ?? '—';
        $serie  = $this->reporte->num_serie;
        $folio  = $this->reporte->id_reporte;

        return (new MailMessage)
            ->subject('Reporte de robo confirmado — ArrowX')
            ->greeting("Hola, {$notifiable->nombre_cliente}")
            ->line('Tu reporte de robo ha sido **confirmado** exitosamente.')
            ->line("**Vehículo:** {$marca} {$modelo}")
            ->line("**N° de serie:** {$serie}")
            ->line("**Folio del reporte:** {$folio}")
            ->line('Tu vehículo ha sido marcado en toda la red ArrowX. Si alguna sucursal detecta el vehículo, serás notificado de inmediato.')
            ->line('Guarda tu folio para cualquier aclaración.')
            ->salutation('Equipo ArrowX — CloudLabs');
    }
}