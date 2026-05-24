<?php

namespace App\Jobs;

use App\Models\Cotizacion;
use App\Services\CatalogService;
use App\Notifications\CotizacionNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class EnviarCotizacion implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries   = 3;
    public int $backoff = 60;

    public function __construct(
        public readonly string $idCotizacion,  // ← Ahora recibe el ID, no el modelo
        public readonly string $idNegocio,
    ) {}

    public function handle(): void
    {
        $cotizacion = Cotizacion::with('reparacion.cliente')->find($this->idCotizacion);

        if (! $cotizacion) {
            Log::warning("Cotización {$this->idCotizacion} no encontrada.");
            return;
        }

        $reparacion = $cotizacion->reparacion;

        if (! $reparacion) {
            Log::warning("Cotización {$this->idCotizacion} sin reparación.");
            return;
        }

        // Guardia explícita — no dependas de routeNotificationForMail aquí
        $email = $reparacion->cliente_email ?? $reparacion->cliente?->correo ?? null;

        if (! $email) {
            Log::warning("EnviarCotizacion: sin email para cotización {$this->idCotizacion}");
            return;
        }

        $negocio       = CatalogService::getNegocioById($this->idNegocio);
        $nombreNegocio = $negocio?->nombre_negocio ?? config('app.name');
        $nombreCliente = $reparacion->cliente_nombre
            ?? ($reparacion->cliente ? trim("{$reparacion->cliente->nombre_cliente} {$reparacion->cliente->apellido1}") : 'Cliente');

        $reparacion->notify(new CotizacionNotification(
            cotizacion:    $cotizacion,
            nombreCliente: $nombreCliente,
            nombreNegocio: $nombreNegocio,
            tipoOt:        $reparacion->tipo,
        ));
    }

    public function failed(\Throwable $e): void
    {
        Log::error("EnviarCotizacion falló para cotización {$this->idCotizacion}: " . $e->getMessage());
    }
}