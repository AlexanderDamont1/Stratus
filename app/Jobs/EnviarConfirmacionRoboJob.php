<?php

namespace App\Jobs;

use App\Models\ReporteRobo;
use App\Notifications\ConfirmarReporteRoboNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Notification;

class EnviarConfirmacionRoboJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public function __construct(
        public readonly string $idReporte,
    ) {}

    public function handle(): void
    {
        $reporte = ReporteRobo::with(['cliente'])->find($this->idReporte);

        if (!$reporte || !$reporte->cliente || empty($reporte->cliente->correo)) return;

        Notification::sendNow(
            $reporte->cliente,
            new ConfirmarReporteRoboNotification($reporte)
        );
    }
}