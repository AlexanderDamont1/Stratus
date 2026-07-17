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
            ->view('emails.confirmar-robo', [
                'nombreCliente' => $notifiable->nombre_cliente,
                'marca'         => $marca,
                'modelo'        => $modelo,
                'serie'         => $serie,
                'url'           => $url,
            ]);
    }
}