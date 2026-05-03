<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class EliminarNegocioJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries   = 1;
    public int $timeout = 300;

    public function __construct(
        public readonly string $idNegocio,
        public readonly string $nombreNegocio,
        public readonly string $rootUsuarioId,
    ) {}

    public function handle(): void
    {
            Log::channel('daily')->info('JOB VERSION: DINAMICO - ' . Carbon::now()); // ← agrega esta línea

        $inicio = Carbon::now();
        $log    = [];
        $log[]  = "═══════════════════════════════════════════════════════";
        $log[]  = "  REPORTE DE ELIMINACIÓN DE NEGOCIO — ArrowX / CloudLabs";
        $log[]  = "═══════════════════════════════════════════════════════";
        $log[]  = "  Negocio   : {$this->nombreNegocio}";
        $log[]  = "  ID        : {$this->idNegocio}";
        $log[]  = "  Ejecutado : {$inicio->toDateTimeString()}";
        $log[]  = "  Root user : {$this->rootUsuarioId}";
        $log[]  = "───────────────────────────────────────────────────────";
        $log[]  = "";

        $totalEliminados = 0;

        // ── Tablas que NO deben tocarse nunca ──────────────────────────
        $excluidas = ['migrations', 'sessions', 'jobs', 'failed_jobs', 'password_resets', 'cache'];

        // ── Obtener dinámicamente todas las tablas con columna id_negocio ──
        $baseDatos = DB::connection()->getDatabaseName();

        $tablasConColumna = DB::select("
            SELECT c.TABLE_NAME
            FROM information_schema.COLUMNS c
            WHERE c.TABLE_SCHEMA = ?
              AND c.COLUMN_NAME  = 'id_negocio'
            ORDER BY c.TABLE_NAME
        ", [$baseDatos]);

        $tablasEncontradas = collect($tablasConColumna)
            ->pluck('TABLE_NAME')
            ->filter(fn($t) => !in_array($t, $excluidas))
            ->values()
            ->toArray();

        // ── Ordenar: hijos (tienen FK hacia otra tabla) primero, ──────────
        //    la tabla 'negocios' siempre al final                ──────────
        $ordenadas = $this->ordenarPorDependencias($tablasEncontradas, $baseDatos);

        $log[] = "  Tablas detectadas con 'id_negocio': " . count($ordenadas);
        $log[] = "";

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        try {
            foreach ($ordenadas as $tabla) {
                $count = DB::table($tabla)
                    ->where('id_negocio', $this->idNegocio)
                    ->count();

                if ($count === 0) {
                    $log[] = "  [VACÍA] {$tabla}";
                    continue;
                }

                DB::table($tabla)
                    ->where('id_negocio', $this->idNegocio)
                    ->delete();

                $totalEliminados += $count;
                $log[] = "  [OK]    {$tabla} — {$count} registros eliminados";
            }

            $log[] = "  [INFO]  sessions — se limpiarán automáticamente por expiración";

        } finally {
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        }

        $fin      = Carbon::now();
        $duracion = number_format($inicio->diffInMilliseconds($fin) / 1000, 3);

        $log[] = "";
        $log[] = "───────────────────────────────────────────────────────";
        $log[] = "  RESUMEN";
        $log[] = "───────────────────────────────────────────────────────";
        $log[] = "  Total registros eliminados : {$totalEliminados}";
        $log[] = "  Duración                   : {$duracion}s";
        $log[] = "  Finalizado                 : {$fin->toDateTimeString()}";
        $log[] = "═══════════════════════════════════════════════════════";
        $log[] = "";

        $contenido     = implode("\n", $log);
        $nombreArchivo = 'deletion_' . $this->idNegocio . '_' . $inicio->format('Ymd_His') . '.log';

        Storage::disk('local')->put("logs/negocios/{$nombreArchivo}", $contenido);

        Log::channel('daily')->info(
            "Negocio eliminado: {$this->nombreNegocio} ({$this->idNegocio}) — {$totalEliminados} registros"
        );
    }

    /**
     * Ordena las tablas poniendo los "hijos" (referenciados por FK) primero
     * y la tabla 'negocios' siempre al final.
     */
    private function ordenarPorDependencias(array $tablas, string $baseDatos): array
    {
        // Obtener todas las FK entre estas tablas
        $fks = DB::select("
            SELECT TABLE_NAME, REFERENCED_TABLE_NAME
            FROM information_schema.KEY_COLUMN_USAGE
            WHERE TABLE_SCHEMA            = ?
              AND REFERENCED_TABLE_SCHEMA = ?
              AND REFERENCED_TABLE_NAME   IS NOT NULL
              AND TABLE_NAME IN ('" . implode("','", $tablas) . "')
        ", [$baseDatos, $baseDatos]);

        // Construir grafo de dependencias: tabla → tablas de las que depende
        $grafo     = array_fill_keys($tablas, []);
        $inDegree  = array_fill_keys($tablas, 0);

        foreach ($fks as $fk) {
            $hijo  = $fk->TABLE_NAME;
            $padre = $fk->REFERENCED_TABLE_NAME;

            if (isset($grafo[$hijo]) && isset($grafo[$padre])) {
                if (!in_array($padre, $grafo[$hijo])) {
                    $grafo[$hijo][] = $padre;
                    $inDegree[$padre]++;
                }
            }
        }

        // Topological sort (Kahn) — los que tienen menor in-degree van primero
        $cola     = [];
        $resultado = [];

        foreach ($inDegree as $tabla => $grado) {
            if ($grado === 0) {
                $cola[] = $tabla;
            }
        }

        while (!empty($cola)) {
            $actual     = array_shift($cola);
            $resultado[] = $actual;

            foreach ($grafo as $hijo => $padres) {
                if (in_array($actual, $padres)) {
                    $inDegree[$hijo]--;
                    if ($inDegree[$hijo] === 0) {
                        $cola[] = $hijo;
                    }
                }
            }
        }

        // Cualquier tabla que no quedó ordenada (ciclos) va al final
        foreach ($tablas as $tabla) {
            if (!in_array($tabla, $resultado)) {
                $resultado[] = $tabla;
            }
        }

        // 'negocios' siempre al final pase lo que pase
        $resultado = array_filter($resultado, fn($t) => $t !== 'negocios');
        $resultado = array_values($resultado);
        $resultado[] = 'negocios';

        return $resultado;
    }

    public function failed(\Throwable $e): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $log   = [];
        $log[] = "═══════════════════════════════════════════════════════";
        $log[] = "  ERROR EN ELIMINACIÓN DE NEGOCIO";
        $log[] = "═══════════════════════════════════════════════════════";
        $log[] = "  Negocio : {$this->nombreNegocio} ({$this->idNegocio})";
        $log[] = "  Error   : {$e->getMessage()}";
        $log[] = "  Fecha   : " . Carbon::now()->toDateTimeString();
        $log[] = "═══════════════════════════════════════════════════════";

        $nombreArchivo = 'deletion_ERROR_' . $this->idNegocio . '_' . Carbon::now()->format('Ymd_His') . '.log';
        Storage::disk('local')->put("logs/negocios/{$nombreArchivo}", implode("\n", $log));

        Log::error("Fallo al eliminar negocio {$this->idNegocio}: {$e->getMessage()}");
    }
}