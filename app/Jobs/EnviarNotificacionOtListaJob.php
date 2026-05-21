<?php

namespace App\Jobs;

use App\Models\OrdenTrabajo;
use App\Models\Usuario;
use App\Notifications\OtListaNotification;
use App\Services\OtService;
use App\Services\CatalogService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class EnviarNotificacionOtListaJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries  = 3;
    public int $backoff = 60;

    public function __construct(
        public readonly string $idOt,
        public readonly string $idNegocio,
    ) {}

    public function handle(): void
    {
        // Todo desde Redis — OtService::getOt ya cachea
        $ot = OtService::getOt($this->idOt, $this->idNegocio);

        if (!$ot) {
            Log::warning("OtListaJob: OT {$this->idOt} no encontrada.");
            return;
        }

        $email = $ot->cliente_email ?? $ot->cliente?->correo;

        if (empty($email)) {
            Log::info("OtListaJob: OT {$this->idOt} sin email, omitida.");
            return;
        }

        $negocio       = CatalogService::getNegocioById($this->idNegocio);
        $nombreCliente = $ot->cliente_nombre
            ?? ($ot->cliente
                ? trim("{$ot->cliente->nombre_cliente} {$ot->cliente->apellido1}")
                : 'Cliente');

   
        $notifiable = new \App\Notifications\AnonymousNotifiable($email);

        $notifiable->notify(new OtListaNotification(
            idOt:          $ot->id_ot,
            numSerie:      $ot->num_serie ?? $ot->bici_descripcion ?? '—',
            nombreCliente: $nombreCliente,
            nombreNegocio: $negocio?->nombre_negocio ?? config('app.name'),
            nombreSucursal: $ot->sucursal?->nombre_usuario ?? $negocio?->nombre_negocio ?? config('app.name'),
        ));
    }

    public function failed(\Throwable $e): void
    {
        Log::error("OtListaJob falló para OT {$this->idOt}: " . $e->getMessage());
    }
}