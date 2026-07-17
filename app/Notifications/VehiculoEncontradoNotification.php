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
            ->subject('¡Tu vehículo ha sido encontrado! — ArrowK')
            ->view('emails.vehiculo-encontrado', [
                'nombreCliente' => $notifiable->nombre_cliente,
                'marca'         => $marca,
                'modelo'        => $modelo,
                'serie'         => $serie,
                'folio'         => $folio,
                'sucursal'      => $sucursal,
                'fecha'         => $fecha,
                'ubicacionUrl'  => $this->ubicacionUrl(),
            ]);
    }

    // Enlace a Google Maps de la sucursal que detectó el vehículo. Usa
    // coordenadas si la sucursal las tiene cargadas; si no, busca por su
    // dirección. Si no hay ninguna de las dos, no hay enlace que mostrar.
    protected function ubicacionUrl(): ?string
    {
        $usuario = $this->reporte->usuarioEncontrado;

        if ($usuario?->lat && $usuario?->lng) {
            return "https://www.google.com/maps?q={$usuario->lat},{$usuario->lng}";
        }

        if (!empty($usuario?->direccion)) {
            return 'https://www.google.com/maps/search/?api=1&query=' . urlencode($usuario->direccion);
        }

        return null;
    }
}