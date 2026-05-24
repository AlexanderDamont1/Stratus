<?php

namespace App\Jobs;

use App\Models\Reparaciones;
use App\Services\CatalogService;
use App\Notifications\OtListaNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class NotificarOtLista implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries   = 3;
    public int $backoff = 60;

    public function __construct(
        public readonly Reparaciones $reparacion, 
        public readonly string $idNegocio,
    ) {}

    public function handle(): void
    {
        // 1. Validamos usando el método nativo del modelo
        if (empty($this->reparacion->routeNotificationForMail(null))) {
            Log::info("NotificarOtListaJob: OT {$this->reparacion->id_reparacion} sin email, omitida.");
            return;
        }

        // 2. Obtenemos datos complementarios
        $negocio = CatalogService::getNegocioById($this->idNegocio);
        
        $nombreCliente = $this->reparacion->cliente_nombre
            ?? ($this->reparacion->cliente
                ? trim("{$this->reparacion->cliente->nombre_cliente} {$this->reparacion->cliente->apellido1}")
                : 'Cliente');

        // 3. Notificamos directo sobre el modelo
        $this->reparacion->notify(new OtListaNotification(
            idReparacion:  $this->reparacion->id_reparacion,
            numSerie:      $this->reparacion->num_serie ?? $this->reparacion->unidad_descripcion ?? '—',
            nombreCliente: $nombreCliente,
            nombreNegocio: $negocio?->nombre_negocio ?? config('app.name'),
        ));
    }

    public function failed(\Throwable $e): void
    {
        $id = $this->reparacion->id_reparacion ?? 'Desconocida';
        Log::error("NotificarOtListaJob falló para {$id}: " . $e->getMessage());
    }
}