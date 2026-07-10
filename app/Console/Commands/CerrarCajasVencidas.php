<?php

namespace App\Console\Commands;

use App\Models\CajaSesion;
use App\Services\CajaService;
use App\Services\CatalogService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CerrarCajasVencidas extends Command
{
    protected $signature   = 'cajas:cerrar-vencidas';
    protected $description = 'Cierra automáticamente las sesiones de caja que quedaron abiertas al fin del día (11pm)';

    public function handle(): void
    {
        $sesiones = CajaSesion::where('estado', 'abierta')->get();

        if ($sesiones->isEmpty()) {
            $this->info('No hay sesiones abiertas.');
            return;
        }

        foreach ($sesiones as $sesion) {
            $corte = CajaService::cerrarSesionFinDeDia($sesion);
            CatalogService::incrementVersion($sesion->id_negocio);

            $this->info("✓ Sesión {$sesion->id_sesion} cerrada automáticamente (corte {$corte->id_corte}).");
        }

        Log::warning('CerrarCajasVencidas: cierre automático de fin de día ejecutado', [
            'total_sesiones' => $sesiones->count(),
            'ids_sesion'     => $sesiones->pluck('id_sesion')->toArray(),
        ]);
    }
}
