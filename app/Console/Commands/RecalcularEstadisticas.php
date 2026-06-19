<?php

namespace App\Console\Commands;

use App\Jobs\ActualizarEstadisticasDiarias;
use App\Models\Venta;
use Carbon\Carbon;
use Illuminate\Console\Command;

class RecalcularEstadisticas extends Command
{
    protected $signature   = 'estadisticas:recalcular
                                {--negocio= : id_negocio específico (opcional, si no se pasa recalcula todos)}
                                {--desde=   : fecha inicio Y-m-d (default: primera venta)}
                                {--hasta=   : fecha fin Y-m-d (default: hoy)}';

    protected $description = 'Recalcula estadisticas_diarias para uno o todos los negocios en un rango de fechas';

    public function handle(): void
    {
        $hasta    = Carbon::parse($this->option('hasta') ?? now()->toDateString());
        $idNegocio = $this->option('negocio');

        // Determinar fecha inicio
        $query = Venta::query();
        if ($idNegocio) {
            $query->where('id_negocio', $idNegocio);
        }
        $primeraVenta = $query->min('created_at');

        $desde = Carbon::parse($this->option('desde') ?? $primeraVenta ?? now()->toDateString());

        // Negocios a procesar
        $negocios = $idNegocio
            ? collect([$idNegocio])
            : Venta::distinct()->pluck('id_negocio');

        $totalDias = $desde->diffInDays($hasta) + 1;

        $this->info("Recalculando {$totalDias} día(s) para {$negocios->count()} negocio(s)...");

        $bar = $this->output->createProgressBar($negocios->count() * $totalDias);
        $bar->start();

        foreach ($negocios as $neg) {
            $dia = $desde->copy();
            while ($dia->lte($hasta)) {
                ActualizarEstadisticasDiarias::dispatch($neg, $dia->toDateString())
                    ->onQueue('default');
                $dia->addDay();
                $bar->advance();
            }
        }

        $bar->finish();
        $this->newLine();
        $this->info('Jobs despachados. Revisa la queue para ver el progreso.');
    }
}