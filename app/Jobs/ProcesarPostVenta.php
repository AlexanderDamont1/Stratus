<?php

namespace App\Jobs;

use App\Models\Venta;
use App\Notifications\ComprobanteVentaNotification;
use App\Services\BicicletaMovimientoService;
use App\Services\GarantiaService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcesarPostVenta implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int  $tries = 3;
     

    public function __construct(
        public readonly string $numSerie,
        public readonly string $idMarca,
        public readonly string $idNegocio,
        public readonly string $nombreCliente,
        public readonly string $idVenta,
        public readonly string $nombreVendedor,
        public bool $enviarCorreo = false,
    ) {}

    public function handle(): void
    {
        app(BicicletaMovimientoService::class)->venta(
            $this->numSerie,
            $this->nombreCliente
        );

        app(GarantiaService::class)->generarGarantiasParaVenta(
            numSerie:   $this->numSerie,
            idMarca:    $this->idMarca,
            idNegocio:  $this->idNegocio,
            fechaVenta: now(),
        );

        if ($this->enviarCorreo) {
            $venta = Venta::with([
                'cliente',
                'detalles.producto',
                'detalles.bicicleta.modelo',
                'detalles.bicicleta.voltaje',
                'detalles.bicicleta.color',
            ])->find($this->idVenta);

            if ($venta?->cliente && !empty($venta->cliente->correo)) {
                $venta->cliente->notify(
                    new ComprobanteVentaNotification($venta, $this->nombreVendedor)
                );
            }
        }
    }
}