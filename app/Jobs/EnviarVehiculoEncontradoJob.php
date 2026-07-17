<?php

namespace App\Jobs;

use App\Models\ReporteRobo;
use App\Notifications\VehiculoEncontradoNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Notification;

class EnviarVehiculoEncontradoJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public function __construct(
        public readonly string $idReporte,
    ) {}

    public function handle(): void
    {
        $reporte = ReporteRobo::with([
            'cliente',
            'negocioEncontrado',
            'usuarioEncontrado',
            'bicicleta.modelo.marca',
            'bicicleta.voltaje',
        ])->find($this->idReporte);

        if (!$reporte || !$reporte->cliente || empty($reporte->cliente->correo)) return;
        if (!$reporte->negocioEncontrado) return;

        Notification::sendNow(
            $reporte->cliente,
            new VehiculoEncontradoNotification($reporte, $reporte->negocioEncontrado)
        );
    }
}