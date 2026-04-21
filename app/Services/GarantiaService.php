<?php

namespace App\Services;

use App\Models\BicicletaGarantia;
use App\Models\GarantiaComponenteDef;
use App\Models\GarantiaReclamo;
use App\Models\GarantiaReemplazo;
use App\Models\MarcaGarantiaConfig;
use App\Models\NegocioGarantiaConfig;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class GarantiaService
{
    // ────────────────────────────────────────────────────────────────────────
    // 1. GENERACIÓN AL VENDER
    // ────────────────────────────────────────────────────────────────────────

    /**
     * Crea todas las instancias de garantía para una bicicleta recién vendida.
     * Se llama dentro de la transacción del VentaController.
     */
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

    // ⚠️ Fijar la fecha de inicio UNA vez, fuera del foreach
    // addMonths() muta el objeto Carbon — si lo haces dentro del loop
    // la segunda iteración parte de la fecha ya sumada de la primera.
    $fechaInicio = $fechaVenta->copy()->startOfDay();

    foreach ($defs as $def) {
        BicicletaGarantia::create([
            'id_negocio'       => $idNegocio,
            'num_serie'        => $numSerie,
            // ⚠️ Usa la PK real del modelo GarantiaComponenteDef
            // Revisa con: $def->getKeyName() si no estás seguro
            'id_garantia_def'  => $def->getKey(),
            'clave_componente' => $def->clave_componente,
            'fecha_inicio'     => $fechaInicio->toDateString(),
            // copy() para no mutar $fechaInicio en cada iteración
            'fecha_expiracion' => $fechaInicio->copy()->addMonths($def->duracion_meses)->toDateString(),
            'estado'           => 'vigente',
        ]);
    }

    return true;
}


    // ────────────────────────────────────────────────────────────────────────
    // 2. MAPA VISUAL
    // ────────────────────────────────────────────────────────────────────────

    /**
     * Devuelve el estado visual de cada componente para una bicicleta.
     * Optimizado: una sola query, todo el cálculo en PHP.
     */
    public function getMapaGarantia(string $numSerie, string $idNegocio): Collection
    {
        return BicicletaGarantia::where('num_serie', $numSerie)
            ->where('id_negocio', $idNegocio)
            ->get()
            ->map(fn($g) => [
                'clave_componente'  => $g->clave_componente,
                'estado_visual'     => $g->estado_visual,   // accessor
                'color_mapa'        => $g->color_mapa,      // accessor
                'dias_restantes'    => $g->dias_restantes,  // accessor
                'porcentaje_vida'   => $g->porcentaje_vida, // accessor
                'fecha_expiracion'  => $g->fecha_expiracion->format('d/m/Y'),
                'num_serie_comp'    => $g->num_serie_componente,
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

        // Validación en tiempo real (por_vencer sigue siendo válida para reclamo)
        if ($garantia->dias_restantes < 0) return null;

        return $garantia;
    }

    // ────────────────────────────────────────────────────────────────────────
    // 4. REEMPLAZO DE COMPONENTE
    // Siempre desde un reclamo. Si no hay reclamo → invalidar.
    // ────────────────────────────────────────────────────────────────────────

    public function procesarReemplazo(
        string $idReclamo,
        ?string $numSerieNuevoComponente,
        string $notas = ''
    ): BicicletaGarantia {
        return DB::transaction(function () use ($idReclamo, $numSerieNuevoComponente, $notas) {
            $reclamo = GarantiaReclamo::with('bicicletaGarantia.garantiaDef')
                ->findOrFail($idReclamo);

            $garantiaAnterior = $reclamo->bicicletaGarantia;

            // Política del negocio
            $config = NegocioGarantiaConfig::find($reclamo->id_negocio);
            $politica = $config?->politica_reemplazo ?? 'mini';
            $miniDias = $config?->mini_garantia_dias ?? 7;

            $hoy = now()->toDateString();

            $nuevaExpiracion = match($politica) {
                'heredar' => Carbon::parse($garantiaAnterior->fecha_expiracion)->toDateString(),
                'nueva'   => now()->addMonths(
                                $garantiaAnterior->garantiaDef->duracion_meses
                             )->toDateString(),
                'mini'    => now()->addDays($miniDias)->toDateString(),
            };

            // Crear nueva garantía
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

            // Marcar la anterior como reemplazada
            $garantiaAnterior->update([
                'estado'           => 'reemplazada',
                'id_reemplazada_por' => $garantiaNueva->id_bicicleta_garantia,
            ]);

            // Registrar el reemplazo
            GarantiaReemplazo::create([
                'id_negocio'               => $reclamo->id_negocio,
                'id_reclamo'               => $idReclamo,
                'id_garantia_anterior'     => $garantiaAnterior->id_bicicleta_garantia,
                'id_garantia_nueva'        => $garantiaNueva->id_bicicleta_garantia,
                'num_serie_nuevo_componente' => $numSerieNuevoComponente,
                'politica_aplicada'        => $politica,
                'fecha_reemplazo'          => $hoy,
                'notas'                    => $notas,
            ]);

            // Actualizar reclamo
            $reclamo->update([
                'requiere_reemplazo' => true,
                'estado'             => 'finalizado',
            ]);

            Log::info('Reemplazo de componente procesado', [
                'reclamo'          => $idReclamo,
                'garantia_anterior'=> $garantiaAnterior->id_bicicleta_garantia,
                'garantia_nueva'   => $garantiaNueva->id_bicicleta_garantia,
                'politica'         => $politica,
            ]);

            return $garantiaNueva;
        });
    }

    // ────────────────────────────────────────────────────────────────────────
    // 5. INVALIDACIÓN COMPLETA (reemplazo fuera de mantenimiento)
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