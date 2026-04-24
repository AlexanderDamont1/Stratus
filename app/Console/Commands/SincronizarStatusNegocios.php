<?php

namespace App\Console\Commands;

use App\Events\NegocioExpirado;
use App\Events\StatsActualizadas;
use App\Models\Negocio;
use Illuminate\Console\Command;

class SincronizarStatusNegocios extends Command
{
    protected $signature   = 'negocios:sincronizar-status';
    protected $description = 'Revisa trials y suscripciones expiradas';

    public function handle(): void
    {
        $huboCambios = false;

        Negocio::whereIn('negocio_status', ['trial', 'activo'])->each(function ($negocio) use (&$huboCambios) {
            $statusAnterior = $negocio->negocio_status;
            $negocio->sincronizarStatus();
            $negocio->refresh();

            if ($negocio->negocio_status !== $statusAnterior) {
                broadcast(new NegocioExpirado($negocio, $negocio->negocio_status));
                $huboCambios = true;
                $this->info("✓ {$negocio->nombre_negocio} → {$negocio->negocio_status}");
            }
        });

        if ($huboCambios) {
            broadcast(new StatsActualizadas());
        }
    }
}