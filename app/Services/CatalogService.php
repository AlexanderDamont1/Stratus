<?php

namespace App\Services;

use App\Models\Modelo;
use App\Models\Negocio;
use App\Models\Color;
use App\Models\Voltaje;
use App\Models\ModeloVoltaje;
use App\Models\Bicicleta;
use Illuminate\Support\Facades\Cache;

class CatalogService
{
    const CACHE_TTL = [
        'modelos'    => 86400,
        'negocios'   => 3600,
        'voltajes'   => 7200,
        'colores'    => 7200,
        'search'     => 600,
    ];

    const CACHE_PREFIX = 'catalog:';

    // ─── BICICLETAS ───────────────────────────────────────────────────────────

    /**
     * Sin cache — paginación no es serializable en Redis de forma confiable.
     */
    public static function getBicicletasPaginadas(string $idNegocio, int $page = 1, ?string $search = null)
    {
        $query = Bicicleta::where('id_negocio', $idNegocio)
            ->with(['modelo', 'voltaje', 'color']);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('num_serie', 'like', "%{$search}%")
                  ->orWhere('status', 'like', "%{$search}%");
            });
        }

        return $query->orderByDesc('created_at')->paginate(10);
    }

    /**
     * Stats cacheadas — solo arrays simples, se serializan sin problema.
     */
    public static function getBicicletaStats(string $idNegocio)
    {
        return Cache::remember(
            self::CACHE_PREFIX . "stats:bicicletas:{$idNegocio}",
            600,
            function () use ($idNegocio) {
                return [
                    'total'    => Bicicleta::where('id_negocio', $idNegocio)->count(),
                    'en_stock' => Bicicleta::where('id_negocio', $idNegocio)->where('status', 'STOCK')->count(),
                ];
            }
        );
    }

    /**
     * Solo limpia stats — la paginación ya no se cachea.
     */
    public static function clearBicicletaCache(string $idNegocio): void
    {
        Cache::forget(self::CACHE_PREFIX . "stats:bicicletas:{$idNegocio}");
        self::incrementVersion();
    }

    // ─── MODELOS ──────────────────────────────────────────────────────────────

    public static function getModelos(bool $withSelectFormato = false)
    {
        $modelos = Cache::remember(
            self::CACHE_PREFIX . 'modelos',
            self::CACHE_TTL['modelos'],
            fn() => Modelo::select('id_modelo', 'nombre_modelo')
                ->orderBy('nombre_modelo')
                ->get()
        );

        return $withSelectFormato ? $modelos->pluck('nombre_modelo', 'id_modelo') : $modelos;
    }

    public static function getModeloById(string $idModelo): ?Modelo
    {
        return Cache::remember(
            self::CACHE_PREFIX . "modelo:{$idModelo}",
            self::CACHE_TTL['modelos'],
            fn() => Modelo::find($idModelo)
        );
    }

    // ─── NEGOCIOS ─────────────────────────────────────────────────────────────

    public static function getNegocios(bool $withSelectFormato = false)
    {
        $negocios = Cache::remember(
            self::CACHE_PREFIX . 'negocios',
            self::CACHE_TTL['negocios'],
            fn() => Negocio::select('id_negocio', 'nombre_negocio')
                ->orderBy('nombre_negocio')
                ->get()
        );

        return $withSelectFormato ? $negocios->pluck('nombre_negocio', 'id_negocio') : $negocios;
    }

    // ─── VOLTAJES ─────────────────────────────────────────────────────────────

    public static function getVoltajesByModelo(string $idModelo, bool $withSelectFormato = false)
    {
        $voltajes = Cache::remember(
            self::CACHE_PREFIX . "voltajes:modelo:{$idModelo}",
            self::CACHE_TTL['voltajes'],
            fn() => ModeloVoltaje::where('id_modelo', $idModelo)
                ->join('voltajes', 'modelo_voltaje.id_voltaje', '=', 'voltajes.id_voltaje')
                ->select('voltajes.id_voltaje', 'voltajes.voltaje')
                ->orderBy('voltajes.voltaje')
                ->get()
        );

        return $withSelectFormato ? $voltajes->pluck('voltaje', 'id_voltaje') : $voltajes;
    }

    public static function getAllVoltajes(bool $withSelectFormato = false)
    {
        $voltajes = Cache::remember(
            self::CACHE_PREFIX . 'voltajes:all',
            self::CACHE_TTL['voltajes'],
            fn() => Voltaje::select('id_voltaje', 'voltaje')->orderBy('voltaje')->get()
        );

        return $withSelectFormato ? $voltajes->pluck('voltaje', 'id_voltaje') : $voltajes;
    }

    // ─── COLORES ──────────────────────────────────────────────────────────────

    public static function getColoresByModelo(string $idModelo, bool $withSelectFormato = false)
    {
        $colores = Cache::remember(
            self::CACHE_PREFIX . "colores:modelo:{$idModelo}",
            self::CACHE_TTL['colores'],
            fn() => Color::where('id_modelo', $idModelo)
                ->select('id_color', 'color')
                ->orderBy('color')
                ->get()
        );

        return $withSelectFormato ? $colores->pluck('color', 'id_color') : $colores;
    }

    public static function getAllColores(bool $withSelectFormato = false)
    {
        $colores = Cache::remember(
            self::CACHE_PREFIX . 'colores:all',
            self::CACHE_TTL['colores'],
            fn() => Color::select('id_color', 'color')->orderBy('color')->get()
        );

        return $withSelectFormato ? $colores->pluck('color', 'id_color') : $colores;
    }

    // ─── STATS GLOBALES ───────────────────────────────────────────────────────

    public static function getStats(): array
    {
        return Cache::remember(
            self::CACHE_PREFIX . 'stats',
            1800,
            fn() => [
                'total_modelos'       => Modelo::count(),
                'total_negocios'      => Negocio::count(),
                'total_colores'       => Color::count(),
                'total_voltajes'      => Voltaje::count(),
                'relaciones_voltajes' => ModeloVoltaje::count(),
            ]
        );
    }

    /**
         * Cachear un objeto Voltaje individual
         */
        public static function getVoltajeById(string $id): ?Voltaje
        {
            return Cache::remember(
                self::CACHE_PREFIX . "voltaje:{$id}",
                self::CACHE_TTL['voltajes'],
                fn() => Voltaje::find($id)
            );
        }

        /**
         * Cachear un objeto Color individual
         */
        public static function getColorById(string $id): ?Color
        {
            return Cache::remember(
                self::CACHE_PREFIX . "color:{$id}",
                self::CACHE_TTL['colores'],
                fn() => Color::find($id)
            );
        }

    // ─── INVALIDACIÓN ─────────────────────────────────────────────────────────

    public static function clearCache(?string $specific = null): void
    {
        if ($specific) {
            Cache::forget(self::CACHE_PREFIX . $specific);
            return;
        }

        $keys = ['modelos', 'negocios', 'voltajes:all', 'colores:all', 'stats'];
        foreach ($keys as $key) {
            Cache::forget(self::CACHE_PREFIX . $key);
        }

        foreach (self::getModelos() as $modelo) {
            self::invalidateModelo($modelo->id_modelo);
        }
    }

    public static function invalidateModelo(string $idModelo): void
    {
        Cache::forget(self::CACHE_PREFIX . "modelo:{$idModelo}");
        Cache::forget(self::CACHE_PREFIX . "voltajes:modelo:{$idModelo}");
        Cache::forget(self::CACHE_PREFIX . "colores:modelo:{$idModelo}");
    }

    public static function getVersion(): int
    {
        return (int) Cache::get(self::CACHE_PREFIX . 'version', 1);
    }

    public static function incrementVersion(): void
    {
        Cache::increment(self::CACHE_PREFIX . 'version');
    }
}