<?php
// app/Notifications/VehiculoEncontradoNotification.php

namespace App\Notifications;

use App\Models\Negocio;
use App\Models\ReporteRobo;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class VehiculoEncontradoNotification extends Notification
{
    public function __construct(
        public readonly ReporteRobo $reporte,
        public readonly Negocio     $negocio,
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
        $sucursal = $this->negocio->nombre_negocio;
        $fecha  = $this->reporte->encontrado_at?->format('d/m/Y H:i') ?? now()->format('d/m/Y H:i');

        return (new MailMessage)
            ->subject('¡Tu vehículo ha sido encontrado! — ArrowX')
            ->greeting("Hola, {$notifiable->nombre_cliente}")
            ->line('¡Buenas noticias! Tu vehículo con reporte de robo ha sido detectado en la red ArrowX.')
            ->line("**Vehículo:** {$marca} {$modelo}")
            ->line("**N° de serie:** {$serie}")
            ->line("**Folio del reporte:** {$folio}")
            ->line("**Detectado en:** {$sucursal}")
            ->line("**Fecha de detección:** {$fecha}")
            ->line('El vehículo ha sido resguardado por la sucursal. Comunícate con ellos para coordinar la recuperación.')
            ->line('Gracias por confiar en la tecnología de **CloudLabs**.')
            ->salutation('Equipo ArrowX — CloudLabs');
    }
}