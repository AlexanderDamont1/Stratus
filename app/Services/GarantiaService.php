<?php

namespace App\Services;

use App\Models\BicicletaGarantia;
use App\Models\GarantiaComponenteDef;
use App\Models\GarantiaReclamo;
use App\Models\GarantiaReemplazo;
use App\Models\MarcaGarantiaConfig;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class GarantiaService
{
    // ────────────────────────────────────────────────────────────────────────
    // 1. GENERACIÓN AL VENDER
    // ────────────────────────────────────────────────────────────────────────

    public function generarGarantiasParaVenta(
        string $numSerie,
        string $idMarca,
        string $idNegocio,
        Carbon $fechaVenta
    ): bool {
        $marcaConfig = MarcaGarantiaConfig::where('id_marca', $idMarca)
            ->where('id_negocio', $idNegocio)
            ->where('activa', true)
            ->first();

        if (!$marcaConfig) return false;

        $defs = GarantiaComponenteDef::where('id_marca_garantia', $marcaConfig->id_marca_garantia)
            ->where('activo', true)
            ->where('excluido', false)
            ->get();

        if ($defs->isEmpty()) return false;

        $fechaInicio = $fechaVenta->copy()->startOfDay();

        foreach ($defs as $def) {
            BicicletaGarantia::create([
                'id_negocio'       => $idNegocio,
                'num_serie'        => $numSerie,
                'id_garantia_def'  => $def->getKey(),
                'clave_componente' => $def->clave_componente,
                'fecha_inicio'     => $fechaInicio->toDateString(),
                'fecha_expiracion' => $fechaInicio->copy()->addMonths($def->duracion_meses)->toDateString(),
                'estado'           => 'vigente',
            ]);
        }

        return true;
    }

    // ────────────────────────────────────────────────────────────────────────
    // 2. MAPA VISUAL
    // ────────────────────────────────────────────────────────────────────────

    public function getMapaGarantia(string $numSerie, string $idNegocio): Collection
    {
        return BicicletaGarantia::where('num_serie', $numSerie)
            ->where('id_negocio', $idNegocio)
            ->get()
            ->map(fn($g) => [
                'clave_componente' => $g->clave_componente,
                'estado_visual'    => $g->estado_visual,
                'color_mapa'       => $g->color_mapa,
                'dias_restantes'   => $g->dias_restantes,
                'porcentaje_vida'  => $g->porcentaje_vida,
                'fecha_expiracion' => $g->fecha_expiracion->format('d/m/Y'),
                'num_serie_comp'   => $g->num_serie_componente,
            ]);
    }

    // ────────────────────────────────────────────────────────────────────────
    // 3. VALIDAR GARANTÍA PARA RECLAMO
    // ────────────────────────────────────────────────────────────────────────

    public function validarGarantia(
        string $numSerie,
        string $claveComponente,
        string $idNegocio
    ): ?BicicletaGarantia {
        $garantia = BicicletaGarantia::where('num_serie', $numSerie)
            ->where('clave_componente', $claveComponente)
            ->where('id_negocio', $idNegocio)
            ->where('estado', 'vigente')
            ->first();

        if (!$garantia) return null;
        if ($garantia->dias_restantes < 0) return null;

        return $garantia;
    }

    // ────────────────────────────────────────────────────────────────────────
    // 4. REEMPLAZO DE COMPONENTE
    // ────────────────────────────────────────────────────────────────────────

    public function procesarReemplazo(
        string $idReclamo,
        ?string $numSerieNuevoComponente,
        string $notas = ''
    ): BicicletaGarantia {
        return DB::transaction(function () use ($idReclamo, $numSerieNuevoComponente, $notas) {
            $reclamo = GarantiaReclamo::with([
                'bicicletaGarantia.garantiaDef.marcaGarantiaConfig', // eager load hasta la config de marca
            ])->findOrFail($idReclamo);

            $garantiaAnterior = $reclamo->bicicletaGarantia;
            $def              = $garantiaAnterior->garantiaDef;

            // ── Política: ahora viene de la marca, no del negocio ────────────
            $marcaConfig = $def?->marcaGarantiaConfig;
            $politica    = $marcaConfig?->politicaEfectiva()  ?? 'mini';
            $miniDias    = $marcaConfig?->miniDiasEfectivos() ?? 7;
            // ────────────────────────────────────────────────────────────────

            $hoy = now()->toDateString();

            $nuevaExpiracion = match ($politica) {
                'heredar' => Carbon::parse($garantiaAnterior->fecha_expiracion)->toDateString(),
                'nueva'   => now()->addMonths($def->duracion_meses)->toDateString(),
                'mini'    => now()->addDays($miniDias)->toDateString(),
            };

            $garantiaNueva = BicicletaGarantia::create([
                'id_negocio'           => $garantiaAnterior->id_negocio,
                'num_serie'            => $garantiaAnterior->num_serie,
                'id_garantia_def'      => $garantiaAnterior->id_garantia_def,
                'clave_componente'     => $garantiaAnterior->clave_componente,
                'fecha_inicio'         => $hoy,
                'fecha_expiracion'     => $nuevaExpiracion,
                'num_serie_componente' => $numSerieNuevoComponente,
                'estado'               => 'vigente',
            ]);

            $garantiaAnterior->update([
                'estado'             => 'reemplazada',
                'id_reemplazada_por' => $garantiaNueva->id_bicicleta_garantia,
            ]);

            GarantiaReemplazo::create([
                'id_negocio'                 => $reclamo->id_negocio,
                'id_reclamo'                 => $idReclamo,
                'id_garantia_anterior'       => $garantiaAnterior->id_bicicleta_garantia,
                'id_garantia_nueva'          => $garantiaNueva->id_bicicleta_garantia,
                'num_serie_nuevo_componente' => $numSerieNuevoComponente,
                'politica_aplicada'          => $politica,
                'fecha_reemplazo'            => $hoy,
                'notas'                      => $notas,
            ]);

            $reclamo->update([
                'requiere_reemplazo' => true,
                'estado'             => 'finalizado',
            ]);

            Log::info('Reemplazo de componente procesado', [
                'reclamo'           => $idReclamo,
                'garantia_anterior' => $garantiaAnterior->id_bicicleta_garantia,
                'garantia_nueva'    => $garantiaNueva->id_bicicleta_garantia,
                'politica'          => $politica,
                'marca_config'      => $marcaConfig?->id_marca_garantia,
            ]);

            return $garantiaNueva;
        });
    }

    // ────────────────────────────────────────────────────────────────────────
    // 5. INVALIDACIÓN COMPLETA
    // ────────────────────────────────────────────────────────────────────────

    public function invalidarTodasLasGarantias(string $numSerie, string $idNegocio): void
    {
        BicicletaGarantia::where('num_serie', $numSerie)
            ->where('id_negocio', $idNegocio)
            ->where('estado', 'vigente')
            ->update(['estado' => 'invalidada']);

        Log::warning('Garantías invalidadas por reemplazo fuera de mantenimiento', [
            'num_serie' => $numSerie,
        ]);
    }
}