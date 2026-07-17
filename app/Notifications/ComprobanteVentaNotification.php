<?php

namespace App\Notifications;

use App\Models\Venta;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class ComprobanteVentaNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public readonly Venta  $venta,
        public readonly string $nombreVendedor,
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $detalles = $this->venta->detalles;
        $total    = $detalles->sum(fn($d) => $d->precio_unitario * $d->cantidad);
        $cliente  = $this->venta->cliente;

        return (new MailMessage)
            ->subject('Tu comprobante de compra — ArrowK')
            ->view('emails.comprobante-venta', [
                'venta'          => $this->venta,
                'detalles'       => $detalles,
                'total'          => $total,
                'cliente'        => $cliente,
                'nombreVendedor' => $this->nombreVendedor,
                'fecha'          => $this->venta->created_at->format('d/m/Y H:i'),
            ]);
    }
}