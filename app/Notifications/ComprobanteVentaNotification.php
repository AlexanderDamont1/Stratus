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

        $mail = (new MailMessage)
            ->subject('Tu comprobante de compra — ArrowX')
            ->greeting("Hola, {$cliente->nombre_cliente}")
            ->line('Gracias por tu compra. Aquí está el resumen:')
            ->line('---');

        foreach ($detalles as $detalle) {
            $nombre = $detalle->producto->nombre_producto ?? '—';
            $precio = number_format($detalle->precio_unitario, 2);
            $cant   = $detalle->cantidad;

            $linea = "{$nombre} x{$cant} — \${$precio}";

            if ($detalle->bicicleta) {
                $bici   = $detalle->bicicleta;
                $linea .= " | Serie: {$bici->num_serie}";
                $linea .= " | {$bici->modelo->nombre_modelo} {$bici->voltaje->voltaje}";
            }

            $mail->line($linea);
        }

        $mail->line('---')
             ->line("**Total: $" . number_format($total, 2) . "**")
             ->line("Vendedor: {$this->nombreVendedor}")
             ->line("Fecha: " . $this->venta->created_at->format('d/m/Y H:i'));

        return $mail;
    }
}