<?php

namespace App\Services;

use App\Models\Modulo;
use App\Models\NegocioModuloRol;
use Illuminate\Support\Facades\Cache;

class ModuloService
{
    const CACHE_TTL    = 3600;
    const CACHE_PREFIX = 'modulos:';

    // ─── VERSIÓN — reutiliza la versión por tenant de CatalogService ─────────

    protected static function getVersion(string $idNegocio): int
    {
        return (int) Cache::get('catalog:version:' . $idNegocio, 1);
    }

    protected static function key(string $key): string
    {
        return self::CACHE_PREFIX . $key;
    }

    // ─── CONSULTA PRINCIPAL ──────────────────────────────────────────────────

    /**
     * ¿Tiene este negocio+rol acceso al módulo?
     * Único método que usan controllers, middlewares y vistas.
     */
    public static function tiene(string $idNegocio, int $idRol, string $idModulo): bool
    {
        $modulos = self::getModulosNegocio($idNegocio);
        return isset($modulos[$idModulo][$idRol]) && $modulos[$idModulo][$idRol] === true;
    }

    // ─── CACHE ───────────────────────────────────────────────────────────────

    public static function getModulosNegocio(string $idNegocio): array
    {
        $version = self::getVersion($idNegocio);

        return Cache::remember(
            self::key("negocio:{$idNegocio}") . ":v{$version}",
            self::CACHE_TTL,
            function () use ($idNegocio) {
                $rows = NegocioModuloRol::where('id_negocio', $idNegocio)->get();

                $mapa = [];
                foreach ($rows as $row) {
                    $mapa[$row->id_modulo][$row->id_rol] = (bool) $row->activo;
                }
                return $mapa;
            }
        );
    }

    public static function invalidate(string $idNegocio): void
    {
        $version = self::getVersion($idNegocio);
        Cache::forget(self::key("negocio:{$idNegocio}") . ":v{$version}");
    }

    // ─── GESTIÓN (solo Root) ─────────────────────────────────────────────────

    public static function toggle(
        string $idNegocio,
        string $idModulo,
        int    $idRol,
        bool   $activo
    ): void {
        NegocioModuloRol::updateOrCreate(
            [
                'id_negocio' => $idNegocio,
                'id_modulo'  => $idModulo,
                'id_rol'     => $idRol,
            ],
            ['activo' => $activo]
        );

        // ← Hook: sincroniza visibilidad de configs cuando cambia el rol Admin
        if ($idRol === 1) {
            self::sincronizarConfigs($idNegocio, $idModulo);
        }

        self::invalidate($idNegocio);
    }

    /**
     * Activa o desactiva las NegocioConfig del módulo
     * según si el admin (rol 1) tiene acceso activo.
     * Solo afecta configs que pertenecen al grupo del módulo.
     */
    protected static function sincronizarConfigs(string $idNegocio, string $idModulo): void
    {
        // Mapa módulo → clave(s) de negocio_config que controla
        $configsPorModulo = [
            'reparaciones' => ['reparaciones.sucursal_puede_reparar'],
            'garantias'    => ['garantias.sucursal_puede_gestionar'],
        ];

        if (!isset($configsPorModulo[$idModulo])) {
            return;
        }

        $adminTieneAcceso = NegocioModuloRol::where('id_negocio', $idNegocio)
            ->where('id_modulo', $idModulo)
            ->where('id_rol', 1)
            ->where('activo', true)
            ->exists();

        \App\Models\NegocioConfig::whereIn('clave', $configsPorModulo[$idModulo])
            ->update(['activo' => $adminTieneAcceso]);
    }
    
    /**
     * Activa un plan completo de golpe.
     * Ejemplo: ModuloService::activarPlan($id, ['tracking' => [1, 2]])
     */
    public static function activarPlan(string $idNegocio, array $modulos): void
    {
        foreach ($modulos as $idModulo => $roles) {
            foreach ($roles as $idRol) {
                self::toggle($idNegocio, $idModulo, $idRol, true);
            }
        }
    }

    // ─── HELPER PARA PANEL ROOT ───────────────────────────────────────────────

    public static function getEstadoCompleto(string $idNegocio): array
    {
        $todosLosModulos = Modulo::all();
        $activos         = self::getModulosNegocio($idNegocio);

        $resultado = [];
        foreach ($todosLosModulos as $modulo) {
            // ✅ Roles específicos por módulo, no todos siempre
            $roles  = self::getRolesDeModulo($modulo->id_modulo);
            $porRol = [];

            foreach ($roles as $idRol => $nombre) {
                $porRol[$idRol] = [
                    'nombre' => $nombre,
                    'activo' => $activos[$modulo->id_modulo][$idRol] ?? false,
                ];
            }

            $resultado[] = [
                'modulo' => $modulo,
                'roles'  => $porRol,
            ];
        }

        return $resultado;
    }

    // ─── ROLES POR MÓDULO ────────────────────────────────────────────────────────
    protected static array $rolesPorModulo = [
        'tracking'     => [1 => 'Admin'],
        'pedidos'      => [1 => 'Admin'],
        'reparaciones' => [1 => 'Admin', 2 => 'Vendedor'], // ← nuevo
    ];

    protected static function getRolesDeModulo(string $idModulo): array
    {
        return self::$rolesPorModulo[$idModulo] ?? [1 => 'Admin', 2 => 'Vendedor', 5 => 'Gestor'];
    }
}