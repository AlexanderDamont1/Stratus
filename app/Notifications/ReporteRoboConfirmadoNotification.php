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
            ->subject('Reporte de robo confirmado — ArrowK')
            ->view('emails.robo-confirmado', [
                'nombreCliente' => $notifiable->nombre_cliente,
                'marca'         => $marca,
                'modelo'        => $modelo,
                'serie'         => $serie,
                'folio'         => $folio,
            ]);
    }
}