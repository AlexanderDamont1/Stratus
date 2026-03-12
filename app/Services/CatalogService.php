<?php

namespace App\Services;

use App\Models\Modelo;
use App\Models\Negocio;
use App\Models\Color;
use App\Models\Voltaje;
use App\Models\ModeloVoltaje;
use Illuminate\Support\Facades\Cache;

class CatalogService
{
    const CACHE_TTL = [
        'modelos'    => 86400,  // 24 horas
        'negocios'   => 3600,   // 1 hora
        'voltajes'   => 7200,   // 2 horas
        'colores'    => 7200,   // 2 horas
        'relaciones' => 3600,   // 1 hora
        'search'     => 600,    // 10 minutos (era 1 hora — riesgo de crecer sin control)
    ];

    const CACHE_PREFIX = 'catalog:';

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

        return $withSelectFormato
            ? $modelos->pluck('nombre_modelo', 'id_modelo')
            : $modelos;
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

        return $withSelectFormato
            ? $negocios->pluck('nombre_negocio', 'id_negocio')
            : $negocios;
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

        return $withSelectFormato
            ? $voltajes->pluck('voltaje', 'id_voltaje')
            : $voltajes;
    }

    public static function getAllVoltajes(bool $withSelectFormato = false)
    {
        $voltajes = Cache::remember(
            self::CACHE_PREFIX . 'voltajes:all',
            self::CACHE_TTL['voltajes'],
            fn() => Voltaje::select('id_voltaje', 'voltaje')
                ->orderBy('voltaje')
                ->get()
        );

        return $withSelectFormato
            ? $voltajes->pluck('voltaje', 'id_voltaje')
            : $voltajes;
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

        return $withSelectFormato
            ? $colores->pluck('color', 'id_color')
            : $colores;
    }

    public static function getAllColores(bool $withSelectFormato = false)
    {
        $colores = Cache::remember(
            self::CACHE_PREFIX . 'colores:all',
            self::CACHE_TTL['colores'],
            fn() => Color::select('id_color', 'color')
                ->orderBy('color')
                ->get()
        );

        return $withSelectFormato
            ? $colores->pluck('color', 'id_color')
            : $colores;
    }

    // ─── MODELO COMPLETO ──────────────────────────────────────────────────────
    //
    // CORRECCIÓN: ya no envuelve en un tercer Cache::remember.
    // Cada método hijo ya cachea por separado; aquí solo los agrupamos.
    // Evitamos duplicar los mismos datos tres veces en Redis.

    public static function getModeloCompleto(string $idModelo): array
    {
        return [
            'modelo'   => self::getModeloById($idModelo),
            'voltajes' => self::getVoltajesByModelo($idModelo),
            'colores'  => self::getColoresByModelo($idModelo),
        ];
    }

    // ─── STATS ────────────────────────────────────────────────────────────────

    public static function getStats(): array
    {
        return Cache::remember(
            self::CACHE_PREFIX . 'stats',
            1800,
            fn() => [
                'total_modelos'      => Modelo::count(),
                'total_negocios'     => Negocio::count(),
                'total_colores'      => Color::count(),
                'total_voltajes'     => Voltaje::count(),
                'relaciones_voltajes' => ModeloVoltaje::count(),
            ]
        );
    }

    // ─── BÚSQUEDA ─────────────────────────────────────────────────────────────
    //
    // CORRECCIÓN: TTL reducido a 10 min para evitar acumulación ilimitada de
    // keys md5 en Redis. Considera añadir un comando artisan que limpie
    // 'catalog:search:*' periódicamente si el volumen de búsquedas es alto.

    public static function searchModelos(string $search)
    {
        return Cache::remember(
            self::CACHE_PREFIX . 'search:modelos:' . md5($search),
            self::CACHE_TTL['search'],
            fn() => Modelo::where('nombre_modelo', 'like', "%{$search}%")
                ->select('id_modelo', 'nombre_modelo')
                ->orderBy('nombre_modelo')
                ->get()
        );
    }

    // ─── WARMUP ───────────────────────────────────────────────────────────────

    public static function warmup(): void
    {
        self::getModelos();
        self::getNegocios();
        self::getAllVoltajes();
        self::getAllColores();
        self::getStats();

        foreach (self::getModelos() as $modelo) {
            self::getVoltajesByModelo($modelo->id_modelo);
            self::getColoresByModelo($modelo->id_modelo);
        }
    }

    // ─── INVALIDACIÓN ─────────────────────────────────────────────────────────
    //
    // CORRECCIÓN: clearCache ya no intenta usar Cache::tags() porque los
    // Cache::remember() del servicio no guardan con tags — el flush no
    // habría borrado nada. Ahora limpia las keys conocidas explícitamente.

    public static function clearCache(?string $specific = null): void
    {
        if ($specific) {
            Cache::forget(self::CACHE_PREFIX . $specific);
            return;
        }

        $keys = [
            'modelos',
            'negocios',
            'voltajes:all',
            'colores:all',
            'stats',
        ];

        foreach ($keys as $key) {
            Cache::forget(self::CACHE_PREFIX . $key);
        }

        // ── NUEVO: limpiar keys por modelo ──
        foreach (self::getModelos() as $modelo) {
            self::invalidateModelo($modelo->id_modelo);
        }
    }

    public static function invalidateModelo(string $idModelo): void
    {
        $keys = [
            "modelo:{$idModelo}",
            "voltajes:modelo:{$idModelo}",
            "colores:modelo:{$idModelo}",
        ];

        foreach ($keys as $key) {
            Cache::forget(self::CACHE_PREFIX . $key);
        }

        // invalidar listas generales
        Cache::forget(self::CACHE_PREFIX . 'voltajes:all');
        Cache::forget(self::CACHE_PREFIX . 'colores:all');
        Cache::forget(self::CACHE_PREFIX . 'stats');
    }

    // ─── VERSIÓN ──────────────────────────────────────────────────────────────
    //
    // NOTA: La propiedad estática $version fue eliminada. En PHP-FPM cada
    // request tiene su propio proceso — la estática no persiste entre requests
    // y daba una falsa sensación de "cache en memoria". La versión se lee
    // siempre desde Redis directamente.

    public static function getVersion(): int
    {
        return (int) Cache::get(self::CACHE_PREFIX . 'version', 1);
    }

    public static function incrementVersion(): void
    {
        Cache::increment(self::CACHE_PREFIX . 'version');
    }

    
}
