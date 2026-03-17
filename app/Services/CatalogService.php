<?php

namespace App\Services;

use App\Models\Modelo;
use App\Models\Negocio;
use App\Models\Color;
use App\Models\Voltaje;
use App\Models\ModeloVoltaje;
use App\Models\Bicicleta;
use App\Models\Pedido;
use App\Models\User;
use App\Models\Producto;
use Illuminate\Support\Facades\Cache;
use Illuminate\Pagination\LengthAwarePaginator;

class CatalogService
{
    const CACHE_TTL = [
        'modelos'    => 86400, // 24h
        'negocios'   => 3600,  // 1h
        'voltajes'   => 7200,  // 2h
        'colores'    => 7200,
        'bicicletas' => 3600,   // 10m
        'pedidos'    => 3600,
        'usuarios'   => 3600,
        'stats'      => 1800,  // 30m
        'search'     => 3600, 
        'productos'  => 3600,  // 1h
    ];

    const CACHE_PREFIX = 'catalog:';
    const CACHE_VERSION_KEY = self::CACHE_PREFIX . 'version';

    // ─── VERSIÓN GLOBAL ─────────────────────────────────────────────────────
    
    public static function getVersion(): int
    {
        return (int) Cache::get(self::CACHE_VERSION_KEY, 1);
    }

    public static function incrementVersion(): void
    {
        Cache::increment(self::CACHE_VERSION_KEY);
    }

    // ─── UTILIDADES ─────────────────────────────────────────────────────────

    protected static function key(string $key): string
    {
        return self::CACHE_PREFIX . $key;
    }

    protected static function remember(string $key, int $ttl, callable $callback, ?int $version = null)
    {
        $version = $version ?? self::getVersion();
        return Cache::remember(self::key($key) . ":v{$version}", $ttl, $callback);
    }

    // ─── MODELOS ────────────────────────────────────────────────────────────

    public static function getModelos(bool $withSelectFormato = false)
    {
        $modelos = self::remember('modelos', self::CACHE_TTL['modelos'], function () {
            return Modelo::select('id_modelo', 'nombre_modelo')
                ->orderBy('nombre_modelo')
                ->get();
        });

        return $withSelectFormato ? $modelos->pluck('nombre_modelo', 'id_modelo') : $modelos;
    }

    public static function getModeloById(string $idModelo): ?Modelo
    {
        return self::remember("modelo:{$idModelo}", self::CACHE_TTL['modelos'], function () use ($idModelo) {
            return Modelo::find($idModelo);
        });
    }

    // ─── VOLTAJES ───────────────────────────────────────────────────────────

    public static function getVoltajesByModelo(string $idModelo, bool $withSelectFormato = false)
    {
        $voltajes = self::remember("voltajes:modelo:{$idModelo}", self::CACHE_TTL['voltajes'], function () use ($idModelo) {
            return ModeloVoltaje::where('id_modelo', $idModelo)
                ->join('voltajes', 'modelo_voltaje.id_voltaje', '=', 'voltajes.id_voltaje')
                ->select('voltajes.id_voltaje', 'voltajes.voltaje')
                ->orderBy('voltajes.voltaje')
                ->get();
        });

        return $withSelectFormato ? $voltajes->pluck('voltaje', 'id_voltaje') : $voltajes;
    }

    public static function getAllVoltajes(bool $withSelectFormato = false)
    {
        $voltajes = self::remember('voltajes:all', self::CACHE_TTL['voltajes'], function () {
            return Voltaje::select('id_voltaje', 'voltaje')->orderBy('voltaje')->get();
        });

        return $withSelectFormato ? $voltajes->pluck('voltaje', 'id_voltaje') : $voltajes;
    }

    public static function getVoltajeById(string $id): ?Voltaje
    {
        return self::remember("voltaje:{$id}", self::CACHE_TTL['voltajes'], function () use ($id) {
            return Voltaje::find($id);
        });
    }

    // ─── COLORES ────────────────────────────────────────────────────────────

    public static function getColoresByModelo(string $idModelo, bool $withSelectFormato = false)
    {
        $colores = self::remember("colores:modelo:{$idModelo}", self::CACHE_TTL['colores'], function () use ($idModelo) {
            return Color::where('id_modelo', $idModelo)
                ->select('id_color', 'color')
                ->orderBy('color')
                ->get();
        });

        return $withSelectFormato ? $colores->pluck('color', 'id_color') : $colores;
    }

    public static function getAllColores(bool $withSelectFormato = false)
    {
        $colores = self::remember('colores:all', self::CACHE_TTL['colores'], function () {
            return Color::select('id_color', 'color')->orderBy('color')->get();
        });

        return $withSelectFormato ? $colores->pluck('color', 'id_color') : $colores;
    }

    public static function getColorById(string $id): ?Color
    {
        return self::remember("color:{$id}", self::CACHE_TTL['colores'], function () use ($id) {
            return Color::find($id);
        });
    }

    // ─── NEGOCIOS ───────────────────────────────────────────────────────────

    public static function getNegocios(bool $withSelectFormato = false)
    {
        $negocios = self::remember('negocios', self::CACHE_TTL['negocios'], function () {
            return Negocio::select('id_negocio', 'nombre_negocio')
                ->orderBy('nombre_negocio')
                ->get();
        });

        return $withSelectFormato ? $negocios->pluck('nombre_negocio', 'id_negocio') : $negocios;
    }

    public static function getNegocioById(string $idNegocio): ?Negocio
    {
        return self::remember("negocio:{$idNegocio}", self::CACHE_TTL['negocios'], function () use ($idNegocio) {
            return Negocio::find($idNegocio);
        });
    }

    // ─── BICICLETAS ─────────────────────────────────────────────────────────

    /**
     * Búsqueda paginada de bicicletas por negocio (sin cachear la paginación completa)
     */
    public static function getBicicletasPaginadas(string $idNegocio, int $page = 1, ?string $search = null): LengthAwarePaginator
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
     * Obtener una bicicleta por número de serie (cacheada)
     */
    public static function getBicicletaBySerie(string $numSerie): ?Bicicleta
    {
        return self::remember("bicicleta:serie:{$numSerie}", self::CACHE_TTL['bicicletas'], function () use ($numSerie) {
            return Bicicleta::with(['modelo', 'voltaje', 'color'])->where('num_serie', $numSerie)->first();
        });
    }

    /**
     * Stats de bicicletas por negocio
     */
    public static function getBicicletaStats(string $idNegocio): array
    {
        return self::remember("stats:bicicletas:{$idNegocio}", self::CACHE_TTL['stats'], function () use ($idNegocio) {
            return [
                'total'    => Bicicleta::where('id_negocio', $idNegocio)->count(),
                'en_stock' => Bicicleta::where('id_negocio', $idNegocio)->where('status', 'STOCK')->count(),
                'vendidas' => Bicicleta::where('id_negocio', $idNegocio)->where('status', 'VENDIDA')->count(),
                'apartadas'=> Bicicleta::where('id_negocio', $idNegocio)->where('status', 'APARTADA')->count(),
            ];
        });
    }

    // ─── PEDIDOS ────────────────────────────────────────────────────────────

    /**
     * Obtener un pedido con relaciones básicas
     */
    public static function getPedidoById(string $idPedido): ?Pedido
    {
        return self::remember("pedido:{$idPedido}", self::CACHE_TTL['pedidos'], function () use ($idPedido) {
            return Pedido::with(['usuario', 'negocio', 'items.modelo', 'items.voltaje', 'items.color', 'bicicletas'])
                ->find($idPedido);
        });
    }

    /**
     * Pedidos recientes por negocio (con paginación, pero cacheamos solo IDs y luego modelos)
     */
    public static function getPedidosRecientesByNegocio(string $idNegocio, int $limit = 10): array
    {
        $ids = self::remember("pedidos:recientes:negocio:{$idNegocio}:limit{$limit}", self::CACHE_TTL['pedidos'], function () use ($idNegocio, $limit) {
            return Pedido::where('id_negocio', $idNegocio)
                ->orderByDesc('created_at')
                ->limit($limit)
                ->pluck('id_pedido')
                ->toArray();
        });

        return array_map(fn($id) => self::getPedidoById($id), $ids);
    }

    /**
     * Stats de pedidos (pendientes, completados, etc.) por negocio
     */
    public static function getPedidoStats(string $idNegocio): array
    {
        return self::remember("stats:pedidos:negocio:{$idNegocio}", self::CACHE_TTL['stats'], function () use ($idNegocio) {
            return [
                'pendientes' => Pedido::where('id_negocio', $idNegocio)->where('status', 1)->count(),
                'en_proceso' => Pedido::where('id_negocio', $idNegocio)->where('status', 2)->count(),
                'completados'=> Pedido::where('id_negocio', $idNegocio)->where('status', 3)->count(),
                'total'      => Pedido::where('id_negocio', $idNegocio)->count(),
            ];
        });
    }

    // ─── USUARIOS ───────────────────────────────────────────────────────────

    public static function getUserById(string $idUsuario): ?User
    {
        return self::remember("usuario:{$idUsuario}", self::CACHE_TTL['usuarios'], function () use ($idUsuario) {
            return User::find($idUsuario);
        });
    }

    public static function getUserWithNegocio(string $idUsuario): ?User
    {
        return self::remember("usuario:negocio:{$idUsuario}", self::CACHE_TTL['usuarios'], function () use ($idUsuario) {
            return User::with('negocio')->find($idUsuario);
        });
    }

    // ─── STATS GLOBALES ─────────────────────────────────────────────────────

    public static function getGlobalStats(): array
    {
        return self::remember('stats:global', self::CACHE_TTL['stats'], function () {
            return [
                'total_modelos'       => Modelo::count(),
                'total_negocios'      => Negocio::count(),
                'total_colores'       => Color::count(),
                'total_voltajes'      => Voltaje::count(),
                'total_bicicletas'    => Bicicleta::count(),
                'total_pedidos'       => Pedido::count(),
                'total_usuarios'      => User::count(),
                'relaciones_voltajes' => ModeloVoltaje::count(),
            ];
        });
    }

    // ─── INVALIDACIÓN ───────────────────────────────────────────────────────

    public static function clearCache(?string $key = null): void
    {
        if ($key) {
            Cache::forget(self::key($key) . ':v' . self::getVersion());
            return;
        }

        // Incrementar versión global: invalida todo lo que tenga versión
        self::incrementVersion();
    }

    public static function invalidateModelo(string $idModelo): void
    {
        $version = self::getVersion();
        Cache::forget(self::key("modelo:{$idModelo}") . ":v{$version}");
        Cache::forget(self::key("voltajes:modelo:{$idModelo}") . ":v{$version}");
        Cache::forget(self::key("colores:modelo:{$idModelo}") . ":v{$version}");
    }

    public static function invalidateBicicleta(string $numSerie, string $idNegocio): void
    {
        $version = self::getVersion();
        Cache::forget(self::key("bicicleta:serie:{$numSerie}") . ":v{$version}");
        Cache::forget(self::key("stats:bicicletas:{$idNegocio}") . ":v{$version}");
    }

    public static function invalidatePedido(string $idPedido, string $idNegocio): void
    {
        $version = self::getVersion();
        Cache::forget(self::key("pedido:{$idPedido}") . ":v{$version}");
        Cache::forget(self::key("stats:pedidos:negocio:{$idNegocio}") . ":v{$version}");
        // También limpiar listados recientes si los tienes
        for ($limit = 5; $limit <= 20; $limit += 5) {
            Cache::forget(self::key("pedidos:recientes:negocio:{$idNegocio}:limit{$limit}") . ":v{$version}");
        }
    }

    public static function invalidateUsuario(string $idUsuario): void
    {
        $version = self::getVersion();
        Cache::forget(self::key("usuario:{$idUsuario}") . ":v{$version}");
        Cache::forget(self::key("usuario:negocio:{$idUsuario}") . ":v{$version}");
    }

    public static function invalidateNegocio(string $idNegocio): void
    {
        $version = self::getVersion();
        Cache::forget(self::key("negocio:{$idNegocio}") . ":v{$version}");
        Cache::forget(self::key("stats:bicicletas:{$idNegocio}") . ":v{$version}");
        Cache::forget(self::key("stats:pedidos:negocio:{$idNegocio}") . ":v{$version}");
        // Limpiar listados de pedidos recientes
        for ($limit = 5; $limit <= 20; $limit += 5) {
            Cache::forget(self::key("pedidos:recientes:negocio:{$idNegocio}:limit{$limit}") . ":v{$version}");
        }
    }

    // ─── MÉTODOS ADICIONALES PARA BÚSQUEDA (opcional) ───────────────────────

    /**
     * Búsqueda de bicicletas con resultados cacheados (útil para autocompletado)
     */
    public static function searchBicicletas(string $query, string $idNegocio, int $limit = 5): array
    {
        $cacheKey = "search:bicicletas:{$idNegocio}:" . md5($query) . ":limit{$limit}";
        return self::remember($cacheKey, self::CACHE_TTL['search'], function () use ($query, $idNegocio, $limit) {
            return Bicicleta::where('id_negocio', $idNegocio)
                ->where('num_serie', 'like', "%{$query}%")
                ->limit($limit)
                ->get(['num_serie', 'status'])
                ->toArray();
        });
    }

    // ─── PRODUCTOS ─────────────────────────────────────────────────────

public static function getProductos(bool $withSelectFormato = false)
{
    $productos = self::remember('productos', self::CACHE_TTL['productos'], function () {
        return Producto::with('negocio')->orderBy('nombre_producto')->get();
    });

    return $withSelectFormato ? $productos->pluck('nombre_producto', 'id_producto') : $productos;
}

public static function getProductoById(string $idProducto): ?Producto
{
    return self::remember("producto:{$idProducto}", self::CACHE_TTL['productos'], function () use ($idProducto) {
        return Producto::with('negocio')->find($idProducto);
    });
}

public static function getProductosByNegocio(string $idNegocio, bool $withSelectFormato = false)
{
    $productos = self::remember("productos:negocio:{$idNegocio}", self::CACHE_TTL['productos'], function () use ($idNegocio) {
        return Producto::where('id_negocio', $idNegocio)
            ->orderBy('nombre_producto')
            ->get();
    });

    return $withSelectFormato ? $productos->pluck('nombre_producto', 'id_producto') : $productos;
}

public static function invalidateProducto(string $idProducto, string $idNegocio): void
{
    $version = self::getVersion();
    Cache::forget(self::key("producto:{$idProducto}") . ":v{$version}");
    Cache::forget(self::key('productos') . ":v{$version}");
    Cache::forget(self::key("productos:negocio:{$idNegocio}") . ":v{$version}");
}

public static function invalidateVoltajes(): void
{
    $version = self::getVersion();
    // Invalida la lista global de voltajes
    Cache::forget(self::key('voltajes:all') . ":v{$version}");

}
}