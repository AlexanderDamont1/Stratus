<?php

namespace App\Services;

use App\Models\Marca;
use App\Models\Modelo;
use App\Models\Negocio;
use App\Models\Color;
use App\Models\Voltaje;
use App\Models\ModeloVoltaje;
use App\Models\Bicicleta;
use App\Models\Pedido;
use App\Models\Usuario;
use App\Models\Producto;
use Illuminate\Support\Facades\Cache;
use Illuminate\Pagination\LengthAwarePaginator;

class CatalogService
{
    const CACHE_TTL = [
        'marcas'     => 86400, // 24h
        'modelos'    => 86400, // 24h
        'negocios'   => 3600,  // 1h
        'voltajes'   => 7200,  // 2h
        'colores'    => 7200,
        'bicicletas' => 3600,
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

    // ─── MARCAS ─────────────────────────────────────────────────────────────

    /**
     * Marcas de un negocio específico (rol 1)
     */
    public static function getMarcasByNegocio(string $idNegocio, bool $withSelectFormato = false)
    {
        $marcas = self::remember("marcas:negocio:{$idNegocio}", self::CACHE_TTL['marcas'], function () use ($idNegocio) {
            return Marca::where('id_negocio', $idNegocio)
                ->select('id_marca', 'nombre_marca')
                ->orderBy('nombre_marca')
                ->get();
        });

        return $withSelectFormato ? $marcas->pluck('nombre_marca', 'id_marca') : $marcas;
    }

    /**
     * Marcas públicas del rol 5 (Ecobici) — visibles para todos
     */
    public static function getMarcasPublicas(bool $withSelectFormato = false)
    {
        $marcas = self::remember('marcas:publicas', self::CACHE_TTL['marcas'], function () {
            return Marca::whereNull('id_negocio')
                ->select('id_marca', 'nombre_marca')
                ->orderBy('nombre_marca')
                ->get();
        });

        return $withSelectFormato ? $marcas->pluck('nombre_marca', 'id_marca') : $marcas;
    }

    public static function getMarcaById(string $idMarca): ?Marca
    {
        return self::remember("marca:{$idMarca}", self::CACHE_TTL['marcas'], function () use ($idMarca) {
            return Marca::with('negocio')->find($idMarca);
        });
    }

    public static function invalidateMarca(string $idMarca, ?string $idNegocio = null): void
    {
        $version = self::getVersion();
        Cache::forget(self::key("marca:{$idMarca}") . ":v{$version}");

        if ($idNegocio) {
            Cache::forget(self::key("marcas:negocio:{$idNegocio}") . ":v{$version}");
            // También invalidar modelos del negocio ya que dependen de la marca
            Cache::forget(self::key("modelos:negocio:{$idNegocio}") . ":v{$version}");
        } else {
            // Marca pública
            Cache::forget(self::key('marcas:publicas') . ":v{$version}");
        }
    }

    // ─── MODELOS ────────────────────────────────────────────────────────────

    /**
     * Modelos de un negocio específico (rol 1) — multi-tenant
     */
    public static function getModelosByNegocio(string $idNegocio, bool $withSelectFormato = false)
    {
        $modelos = self::remember("modelos:negocio:{$idNegocio}", self::CACHE_TTL['modelos'], function () use ($idNegocio) {
            return Modelo::where('id_negocio', $idNegocio)
                ->with('marca')
                ->select('id_modelo', 'id_marca', 'id_negocio', 'nombre_modelo')
                ->orderBy('nombre_modelo')
                ->get();
        });

        return $withSelectFormato ? $modelos->pluck('nombre_modelo', 'id_modelo') : $modelos;
    }

    /**
     * Modelos públicos del rol 5 — sin id_negocio
     */
    public static function getModelos(bool $withSelectFormato = false)
    {
        $modelos = self::remember('modelos:publicos', self::CACHE_TTL['modelos'], function () {
            return Modelo::whereNull('id_negocio')
                ->select('id_modelo', 'nombre_modelo')
                ->orderBy('nombre_modelo')
                ->get();
        });

        return $withSelectFormato ? $modelos->pluck('nombre_modelo', 'id_modelo') : $modelos;
    }

    public static function getModeloById(string $idModelo): ?Modelo
    {
        return self::remember("modelo:{$idModelo}", self::CACHE_TTL['modelos'], function () use ($idModelo) {
            return Modelo::with(['marca', 'negocio'])->find($idModelo);
        });
    }

    public static function invalidateModelo(string $idModelo, ?string $idNegocio = null): void
    {
        $version = self::getVersion();
        Cache::forget(self::key("modelo:{$idModelo}") . ":v{$version}");
        Cache::forget(self::key("voltajes:modelo:{$idModelo}") . ":v{$version}");
        Cache::forget(self::key("colores:modelo:{$idModelo}") . ":v{$version}");

        if ($idNegocio) {
            Cache::forget(self::key("modelos:negocio:{$idNegocio}") . ":v{$version}");
            Cache::forget(self::key("voltajes:negocio:{$idNegocio}") . ":v{$version}");
            Cache::forget(self::key("colores:negocio:{$idNegocio}") . ":v{$version}");
        } else {
            Cache::forget(self::key('modelos:publicos') . ":v{$version}");
        }
    }

    // ─── VOLTAJES ───────────────────────────────────────────────────────────

    /**
     * Voltajes de un negocio específico (rol 1) — multi-tenant
     */
    public static function getVoltajesByNegocio(string $idNegocio, bool $withSelectFormato = false)
    {
        $voltajes = self::remember("voltajes:negocio:{$idNegocio}", self::CACHE_TTL['voltajes'], function () use ($idNegocio) {
            return Voltaje::where('id_negocio', $idNegocio)
                ->select('id_voltaje', 'voltaje')
                ->orderBy('voltaje')
                ->get();
        });

        return $withSelectFormato ? $voltajes->pluck('voltaje', 'id_voltaje') : $voltajes;
    }

    /**
     * Voltajes por modelo filtrados por negocio
     */
    public static function getVoltajesByModelo(string $idModelo, ?string $idNegocio = null, bool $withSelectFormato = false)
    {
        $cacheKey = $idNegocio
            ? "voltajes:modelo:{$idModelo}:negocio:{$idNegocio}"
            : "voltajes:modelo:{$idModelo}";

        $voltajes = self::remember($cacheKey, self::CACHE_TTL['voltajes'], function () use ($idModelo, $idNegocio) {
            $query = ModeloVoltaje::where('modelo_voltaje.id_modelo', $idModelo)
                ->join('voltajes', 'modelo_voltaje.id_voltaje', '=', 'voltajes.id_voltaje')
                ->select('voltajes.id_voltaje', 'voltajes.voltaje');

            if ($idNegocio) {
                $query->where('modelo_voltaje.id_negocio', $idNegocio);
            } else {
                $query->whereNull('modelo_voltaje.id_negocio');
            }

            return $query->orderBy('voltajes.voltaje')->get();
        });

        return $withSelectFormato ? $voltajes->pluck('voltaje', 'id_voltaje') : $voltajes;
    }

    /**
     * Todos los voltajes públicos (rol 5)
     */
    public static function getAllVoltajes(bool $withSelectFormato = false)
    {
        $voltajes = self::remember('voltajes:publicos', self::CACHE_TTL['voltajes'], function () {
            return Voltaje::whereNull('id_negocio')
                ->select('id_voltaje', 'voltaje')
                ->orderBy('voltaje')
                ->get();
        });

        return $withSelectFormato ? $voltajes->pluck('voltaje', 'id_voltaje') : $voltajes;
    }

    public static function getVoltajeById(string $id): ?Voltaje
    {
        return self::remember("voltaje:{$id}", self::CACHE_TTL['voltajes'], function () use ($id) {
            return Voltaje::find($id);
        });
    }

    public static function invalidateVoltaje(string $idVoltaje, ?string $idNegocio = null): void
    {
        $version = self::getVersion();
        Cache::forget(self::key("voltaje:{$idVoltaje}") . ":v{$version}");

        if ($idNegocio) {
            Cache::forget(self::key("voltajes:negocio:{$idNegocio}") . ":v{$version}");
        } else {
            Cache::forget(self::key('voltajes:publicos') . ":v{$version}");
        }
    }

    // Mantener compatibilidad con código existente que llama invalidateVoltajes()
    public static function invalidateVoltajes(): void
    {
        $version = self::getVersion();
        Cache::forget(self::key('voltajes:publicos') . ":v{$version}");
    }

    // ─── COLORES ────────────────────────────────────────────────────────────

    /**
     * Colores de un negocio específico (rol 1) — multi-tenant
     */
    public static function getColoresByNegocio(string $idNegocio, bool $withSelectFormato = false)
    {
        $colores = self::remember("colores:negocio:{$idNegocio}", self::CACHE_TTL['colores'], function () use ($idNegocio) {
            return Color::where('id_negocio', $idNegocio)
                ->select('id_color', 'id_modelo', 'color')
                ->orderBy('color')
                ->get();
        });

        return $withSelectFormato ? $colores->pluck('color', 'id_color') : $colores;
    }

    /**
     * Colores por modelo filtrados por negocio
     */
    public static function getColoresByModelo(string $idModelo, ?string $idNegocio = null, bool $withSelectFormato = false)
    {
        $cacheKey = $idNegocio
            ? "colores:modelo:{$idModelo}:negocio:{$idNegocio}"
            : "colores:modelo:{$idModelo}";

        $colores = self::remember($cacheKey, self::CACHE_TTL['colores'], function () use ($idModelo, $idNegocio) {
            $query = Color::where('id_modelo', $idModelo)
                ->select('id_color', 'color');

            if ($idNegocio) {
                $query->where('id_negocio', $idNegocio);
            } else {
                $query->whereNull('id_negocio');
            }

            return $query->orderBy('color')->get();
        });

        return $withSelectFormato ? $colores->pluck('color', 'id_color') : $colores;
    }

    /**
     * Todos los colores públicos (rol 5)
     */
    public static function getAllColores(bool $withSelectFormato = false)
    {
        $colores = self::remember('colores:publicos', self::CACHE_TTL['colores'], function () {
            return Color::whereNull('id_negocio')
                ->select('id_color', 'color')
                ->orderBy('color')
                ->get();
        });

        return $withSelectFormato ? $colores->pluck('color', 'id_color') : $colores;
    }

    public static function getColorById(string $id): ?Color
    {
        return self::remember("color:{$id}", self::CACHE_TTL['colores'], function () use ($id) {
            return Color::find($id);
        });
    }

    public static function invalidateColor(string $idColor, string $idModelo, ?string $idNegocio = null): void
    {
        $version = self::getVersion();
        Cache::forget(self::key("color:{$idColor}") . ":v{$version}");

        if ($idNegocio) {
            Cache::forget(self::key("colores:modelo:{$idModelo}:negocio:{$idNegocio}") . ":v{$version}");
            Cache::forget(self::key("colores:negocio:{$idNegocio}") . ":v{$version}");
        } else {
            Cache::forget(self::key("colores:modelo:{$idModelo}") . ":v{$version}");
            Cache::forget(self::key('colores:publicos') . ":v{$version}");
        }
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

    public static function getBicicletaBySerie(string $numSerie): ?Bicicleta
    {
        return self::remember("bicicleta:serie:{$numSerie}", self::CACHE_TTL['bicicletas'], function () use ($numSerie) {
            return Bicicleta::with(['modelo', 'voltaje', 'color'])->where('num_serie', $numSerie)->first();
        });
    }

    public static function getBicicletaStats(string $idNegocio): array
    {
        return self::remember("stats:bicicletas:{$idNegocio}", self::CACHE_TTL['stats'], function () use ($idNegocio) {
            $stats = Bicicleta::where('id_negocio', $idNegocio)
                ->selectRaw("
                    COUNT(*)        as total,
                    SUM(status = 1) as en_stock,
                    SUM(status = 2) as vendidas,
                    SUM(status = 3) as en_reparacion
                ")
                ->first();

            return [
                'total'         => (int) $stats->total,
                'en_stock'      => (int) $stats->en_stock,
                'vendidas'      => (int) $stats->vendidas,
                'en_reparacion' => (int) $stats->en_reparacion,
            ];
        });
    }

    public static function invalidateBicicleta(string $numSerie, string $idNegocio): void
    {
        $version = self::getVersion();
        Cache::forget(self::key("bicicleta:serie:{$numSerie}") . ":v{$version}");
        Cache::forget(self::key("stats:bicicletas:{$idNegocio}") . ":v{$version}");
        Cache::forget(self::key("stock:vendedores:negocio:{$idNegocio}") . ":v{$version}");
    }

    public static function getBicicletasPorUsuarioPaginadas(
        string $idNegocio,
        string $idUsuario,
        int $page = 1,
        ?string $search = null
    ): LengthAwarePaginator {
        $usarCache = ($page === 1 && empty($search));

        if ($usarCache) {
            $cacheKey = "bicicletas:user:{$idUsuario}:negocio:{$idNegocio}:page:1";
            return self::remember(
                $cacheKey,
                self::CACHE_TTL['bicicletas'],
                fn() => self::queryBicicletasUsuario($idNegocio, $idUsuario, null)
                    ->paginate(10, ['*'], 'page', 1)
            );
        }

        return self::queryBicicletasUsuario($idNegocio, $idUsuario, $search)
            ->paginate(10, ['*'], 'page', $page);
    }

    private static function queryBicicletasUsuario(string $idNegocio, string $idUsuario, ?string $search)
    {
        $query = Bicicleta::where('id_negocio', $idNegocio)
            ->where('id_usuario', $idUsuario)
            ->with(['modelo', 'voltaje', 'color']);

        if ($search) {
            $query->where(fn($q) => $q
                ->where('num_serie', 'like', "%{$search}%")
                ->orWhere('status', 'like', "%{$search}%")
            );
        }

        return $query->orderByDesc('created_at');
    }

    public static function invalidateBicicletasPorUsuario(string $idUsuario, string $idNegocio): void
    {
        $version = self::getVersion();
        Cache::forget(self::key("bicicletas:user:{$idUsuario}:negocio:{$idNegocio}:page:1") . ":v{$version}");
    }

    public static function getBicicletasByCliente(string $idCliente)
    {
        return self::remember("bicicletas:cliente:{$idCliente}", 300, fn() =>
            Bicicleta::with(['modelo', 'color'])
                ->where('id_cliente', $idCliente)
                ->where('status', '!=', 'danada')
                ->get()
        );
    }

    public static function invalidateBicicletasByCliente(string $idCliente): void
    {
        $version = self::getVersion();
        Cache::forget(self::key("bicicletas:cliente:{$idCliente}") . ":v{$version}");
    }

    public static function getStockPorVendedores(string $idNegocio): array
    {
        return self::remember("stock:vendedores:negocio:{$idNegocio}", self::CACHE_TTL['bicicletas'], function () use ($idNegocio) {
            $vendedores = Usuario::where('id_negocio', $idNegocio)
                ->where('id_rol', 2)
                ->select('id_usuario', 'nombre_usuario', 'correo')
                ->orderBy('nombre_usuario')
                ->get();

            $resultado = [];

            foreach ($vendedores as $vendedor) {
                $resultado[] = [
                    'vendedor'   => $vendedor,
                    'bicicletas' => Bicicleta::with(['modelo', 'voltaje', 'color'])
                        ->where('id_negocio', $idNegocio)
                        ->where('id_usuario', $vendedor->id_usuario)
                        ->orderByDesc('created_at')
                        ->get(),
                ];
            }

            $resultado[] = [
                'vendedor'   => null,
                'bicicletas' => Bicicleta::with(['modelo', 'voltaje', 'color'])
                    ->where('id_negocio', $idNegocio)
                    ->whereNull('id_usuario')
                    ->orderByDesc('created_at')
                    ->get(),
            ];

            return $resultado;
        });
    }

    public static function invalidateStockVendedores(string $idNegocio): void
    {
        $version = self::getVersion();
        Cache::forget(self::key("stock:vendedores:negocio:{$idNegocio}") . ":v{$version}");
    }

    // ─── PEDIDOS ────────────────────────────────────────────────────────────

    public static function getPedidoById(string $idPedido): ?Pedido
    {
        return self::remember("pedido:{$idPedido}", self::CACHE_TTL['pedidos'], function () use ($idPedido) {
            return Pedido::with(['usuario', 'negocio', 'items.modelo', 'items.voltaje', 'items.color', 'bicicletas'])
                ->find($idPedido);
        });
    }

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

    public static function getPedidoStats(string $idNegocio): array
    {
        return self::remember("stats:pedidos:negocio:{$idNegocio}", self::CACHE_TTL['stats'], function () use ($idNegocio) {
            return [
                'pendientes'  => Pedido::where('id_negocio', $idNegocio)->where('status', 1)->count(),
                'en_proceso'  => Pedido::where('id_negocio', $idNegocio)->where('status', 2)->count(),
                'completados' => Pedido::where('id_negocio', $idNegocio)->where('status', 3)->count(),
                'total'       => Pedido::where('id_negocio', $idNegocio)->count(),
            ];
        });
    }

    public static function invalidatePedido(string $idPedido, string $idNegocio): void
    {
        $version = self::getVersion();
        Cache::forget(self::key("pedido:{$idPedido}") . ":v{$version}");
        Cache::forget(self::key("stats:pedidos:negocio:{$idNegocio}") . ":v{$version}");
        for ($limit = 5; $limit <= 20; $limit += 5) {
            Cache::forget(self::key("pedidos:recientes:negocio:{$idNegocio}:limit{$limit}") . ":v{$version}");
        }
    }

    // ─── USUARIOS ───────────────────────────────────────────────────────────

    public static function getUserById(string $idUsuario): ?Usuario
    {
        return self::remember("usuario:{$idUsuario}", self::CACHE_TTL['usuarios'], fn() =>
            Usuario::find($idUsuario)
        );
    }

    public static function getUserWithNegocio(string $idUsuario): ?Usuario
    {
        return self::remember("usuario:negocio:{$idUsuario}", self::CACHE_TTL['usuarios'], fn() =>
            Usuario::with('negocio')->find($idUsuario)
        );
    }

    public static function invalidateUsuario(string $idUsuario): void
    {
        $version = self::getVersion();
        Cache::forget(self::key("usuario:{$idUsuario}") . ":v{$version}");
        Cache::forget(self::key("usuario:negocio:{$idUsuario}") . ":v{$version}");
    }

    // ─── NEGOCIOS INVALIDACIÓN ───────────────────────────────────────────────

    public static function invalidateNegocio(string $idNegocio): void
    {
        $version = self::getVersion();
        Cache::forget(self::key("negocio:{$idNegocio}") . ":v{$version}");
        Cache::forget(self::key("stats:bicicletas:{$idNegocio}") . ":v{$version}");
        Cache::forget(self::key("stats:pedidos:negocio:{$idNegocio}") . ":v{$version}");
        Cache::forget(self::key("marcas:negocio:{$idNegocio}") . ":v{$version}");
        Cache::forget(self::key("modelos:negocio:{$idNegocio}") . ":v{$version}");
        Cache::forget(self::key("colores:negocio:{$idNegocio}") . ":v{$version}");
        Cache::forget(self::key("voltajes:negocio:{$idNegocio}") . ":v{$version}");
        for ($limit = 5; $limit <= 20; $limit += 5) {
            Cache::forget(self::key("pedidos:recientes:negocio:{$idNegocio}:limit{$limit}") . ":v{$version}");
        }
    }

    // ─── STATS GLOBALES ─────────────────────────────────────────────────────

    public static function getGlobalStats(): array
    {
        return self::remember('stats:global', self::CACHE_TTL['stats'], function () {
            return [
                'total_marcas'        => Marca::count(),
                'total_modelos'       => Modelo::count(),
                'total_negocios'      => Negocio::count(),
                'total_colores'       => Color::count(),
                'total_voltajes'      => Voltaje::count(),
                'total_bicicletas'    => Bicicleta::count(),
                'total_pedidos'       => Pedido::count(),
                'total_usuarios'      => Usuario::count(),
                'relaciones_voltajes' => ModeloVoltaje::count(),
            ];
        });
    }

    // ─── BÚSQUEDA ────────────────────────────────────────────────────────────

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

    // ─── PRODUCTOS ──────────────────────────────────────────────────────────

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

    // ─── CACHÉ GENERAL ──────────────────────────────────────────────────────

    public static function clearCache(?string $key = null): void
    {
        if ($key) {
            Cache::forget(self::key($key) . ':v' . self::getVersion());
            return;
        }
        self::incrementVersion();
    }

    // En CatalogService, agrega este método:

public static function getCatalogoCompleto(string $idNegocio): \Illuminate\Support\Collection
{
    return self::remember("catalogo:completo:{$idNegocio}", self::CACHE_TTL['marcas'], function () use ($idNegocio) {
        return Marca::where('id_negocio', $idNegocio)
            ->withCount('modelos')
            ->with(['modelos' => function ($q) use ($idNegocio) {
                $q->where('id_negocio', $idNegocio)
                  ->with([
                      'colores'  => fn($q) => $q->where('id_negocio', $idNegocio),
                      'voltajes' => fn($q) => $q->wherePivot('id_negocio', $idNegocio)
                                               ->orderBy('voltaje'),
                  ])
                  ->orderBy('nombre_modelo');
            }])
            ->orderBy('nombre_marca')
            ->get();
    });
}

public static function invalidateCatalogoCompleto(string $idNegocio): void
{
    $version = self::getVersion();
    Cache::forget(self::key("catalogo:completo:{$idNegocio}") . ":v{$version}");
}
}