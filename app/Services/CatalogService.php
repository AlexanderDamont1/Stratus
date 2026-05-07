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
use App\Models\Venta;
use App\Models\Producto;
use App\Models\Inventario;
use App\Models\BicicletaMovimiento;
use Illuminate\Support\Facades\Cache;
use Illuminate\Pagination\LengthAwarePaginator;

class CatalogService
{
    const CACHE_TTL = [
        'marcas'     => 86400,
        'modelos'    => 86400,
        'negocios'   => 3600,
        'voltajes'   => 7200,
        'colores'    => 7200,
        'bicicletas' => 3600,
        'pedidos'    => 3600,
        'usuarios'   => 3600,
        'stats'      => 300,
        'search'     => 3600,
        'productos'  => 3600,
    ];

    const CACHE_PREFIX = 'catalog:';

    // ─── VERSIÓN: GLOBAL Y POR TENANT ───────────────────────────────────────

    protected static function getVersionKey(?string $idNegocio = null): string
    {
        return $idNegocio
            ? self::CACHE_PREFIX . "version:{$idNegocio}"
            : self::CACHE_PREFIX . 'version';
    }

    public static function getVersion(?string $idNegocio = null): int
    {
        return (int) Cache::get(self::getVersionKey($idNegocio), 1);
    }

    public static function incrementVersion(?string $idNegocio = null): void
    {
        Cache::increment(self::getVersionKey($idNegocio));
    }

    // ─── UTILIDADES ─────────────────────────────────────────────────────────

    protected static function key(string $key): string
    {
        return self::CACHE_PREFIX . $key;
    }

    protected static function remember(string $key, int $ttl, callable $callback, ?string $idNegocio = null)
    {
        $version = self::getVersion($idNegocio);
        return Cache::remember(self::key($key) . ":v{$version}", $ttl, $callback);
    }

    // ─── MARCAS ─────────────────────────────────────────────────────────────

    public static function getMarcasByNegocio(string $idNegocio, bool $withSelectFormato = false)
    {
        $marcas = self::remember(
            "marcas:negocio:{$idNegocio}",
            self::CACHE_TTL['marcas'],
            fn () => Marca::where('id_negocio', $idNegocio)
                ->select('id_marca', 'nombre_marca')
                ->orderBy('nombre_marca')
                ->get(),
            $idNegocio
        );

        return $withSelectFormato ? $marcas->pluck('nombre_marca', 'id_marca') : $marcas;
    }

    public static function getMarcasPublicas(bool $withSelectFormato = false)
    {
        $marcas = self::remember('marcas:publicas', self::CACHE_TTL['marcas'], fn () =>
            Marca::whereNull('id_negocio')
                ->select('id_marca', 'nombre_marca')
                ->orderBy('nombre_marca')
                ->get()
        );

        return $withSelectFormato ? $marcas->pluck('nombre_marca', 'id_marca') : $marcas;
    }

    public static function getMarcaById(string $idMarca, ?string $idNegocio = null): ?Marca
    {
        return self::remember(
            "marca:{$idMarca}",
            self::CACHE_TTL['marcas'],
            function () use ($idMarca, $idNegocio) {
                $query = Marca::with('negocio');
                if ($idNegocio) {
                    $query->where('id_negocio', $idNegocio);
                }
                return $query->find($idMarca);
            },
            $idNegocio
        );
    }

    public static function invalidateMarca(string $idMarca, ?string $idNegocio = null): void
    {
        $globalV = self::getVersion();
        $tenantV = self::getVersion($idNegocio);

        Cache::forget(self::key("marca:{$idMarca}") . ":v{$globalV}");

        if ($idNegocio) {
            Cache::forget(self::key("marca:{$idMarca}") . ":v{$tenantV}");
            Cache::forget(self::key("marcas:negocio:{$idNegocio}") . ":v{$tenantV}");
            Cache::forget(self::key("modelos:negocio:{$idNegocio}") . ":v{$tenantV}");
        } else {
            Cache::forget(self::key('marcas:publicas') . ":v{$globalV}");
        }
    }

    // ─── MODELOS ────────────────────────────────────────────────────────────

    public static function getModelosByNegocio(string $idNegocio, bool $withSelectFormato = false)
    {
        $modelos = self::remember(
            "modelos:negocio:{$idNegocio}",
            self::CACHE_TTL['modelos'],
            fn () => Modelo::where('id_negocio', $idNegocio)
                ->with('marca')
                ->select('id_modelo', 'id_marca', 'id_negocio', 'nombre_modelo')
                ->orderBy('nombre_modelo')
                ->get(),
            $idNegocio
        );

        return $withSelectFormato ? $modelos->pluck('nombre_modelo', 'id_modelo') : $modelos;
    }

    public static function getModelosByMarca(string $idMarca, string $idNegocio, bool $withSelectFormato = false)
    {
        $modelos = self::remember(
            "modelos:marca:{$idMarca}:negocio:{$idNegocio}",
            self::CACHE_TTL['modelos'],
            fn () => Modelo::where('id_marca', $idMarca)
                ->where('id_negocio', $idNegocio)
                ->select('id_modelo', 'id_marca', 'nombre_modelo')
                ->orderBy('nombre_modelo')
                ->get(),
            $idNegocio
        );

        return $withSelectFormato ? $modelos->pluck('nombre_modelo', 'id_modelo') : $modelos;
    }

    public static function getModelos(bool $withSelectFormato = false)
    {
        $modelos = self::remember('modelos:publicos', self::CACHE_TTL['modelos'],
            fn () => Modelo::whereNull('id_negocio')
                ->select('id_modelo', 'nombre_modelo')
                ->orderBy('nombre_modelo')
                ->get()
        );

        return $withSelectFormato ? $modelos->pluck('nombre_modelo', 'id_modelo') : $modelos;
    }

    public static function getModeloById(string $idModelo, ?string $idNegocio = null): ?Modelo
    {
        return self::remember(
            "modelo:{$idModelo}",
            self::CACHE_TTL['modelos'],
            function () use ($idModelo, $idNegocio) {
                $query = Modelo::with(['marca', 'negocio']);
                if ($idNegocio) {
                    $query->where('id_negocio', $idNegocio);
                }
                return $query->find($idModelo);
            },
            $idNegocio
        );
    }

    public static function invalidateModelo(string $idModelo, ?string $idNegocio = null): void
    {
        $globalV = self::getVersion();
        $tenantV = self::getVersion($idNegocio);

        Cache::forget(self::key("modelo:{$idModelo}") . ":v{$globalV}");
        Cache::forget(self::key("voltajes:modelo:{$idModelo}") . ":v{$globalV}");
        Cache::forget(self::key("colores:modelo:{$idModelo}") . ":v{$globalV}");

        if ($idNegocio) {
            Cache::forget(self::key("modelos:negocio:{$idNegocio}") . ":v{$tenantV}");
            Cache::forget(self::key("voltajes:negocio:{$idNegocio}") . ":v{$tenantV}");
            Cache::forget(self::key("colores:negocio:{$idNegocio}") . ":v{$tenantV}");
            Cache::forget(self::key("voltajes:modelo:{$idModelo}:negocio:{$idNegocio}") . ":v{$tenantV}");
            Cache::forget(self::key("colores:modelo:{$idModelo}:negocio:{$idNegocio}") . ":v{$tenantV}");
        } else {
            Cache::forget(self::key('modelos:publicos') . ":v{$globalV}");
        }
    }

    // ─── VOLTAJES ───────────────────────────────────────────────────────────

    public static function getVoltajesByNegocio(string $idNegocio, bool $withSelectFormato = false)
    {
        $voltajes = self::remember(
            "voltajes:negocio:{$idNegocio}",
            self::CACHE_TTL['voltajes'],
            fn () => Voltaje::where('id_negocio', $idNegocio)
                ->select('id_voltaje', 'voltaje')
                ->orderBy('voltaje')
                ->get(),
            $idNegocio
        );

        return $withSelectFormato ? $voltajes->pluck('voltaje', 'id_voltaje') : $voltajes;
    }

    public static function getVoltajesByModelo(string $idModelo, ?string $idNegocio = null, bool $withSelectFormato = false)
    {
        $cacheKey = $idNegocio
            ? "voltajes:modelo:{$idModelo}:negocio:{$idNegocio}"
            : "voltajes:modelo:{$idModelo}";

        $voltajes = self::remember(
            $cacheKey,
            self::CACHE_TTL['voltajes'],
            function () use ($idModelo, $idNegocio) {
                $query = ModeloVoltaje::where('modelo_voltaje.id_modelo', $idModelo)
                    ->join('voltajes', 'modelo_voltaje.id_voltaje', '=', 'voltajes.id_voltaje')
                    ->select('voltajes.id_voltaje', 'voltajes.voltaje');

                if ($idNegocio) {
                    $query->where('modelo_voltaje.id_negocio', $idNegocio);
                } else {
                    $query->whereNull('modelo_voltaje.id_negocio');
                }

                return $query->orderBy('voltajes.voltaje')->get();
            },
            $idNegocio
        );

        return $withSelectFormato ? $voltajes->pluck('voltaje', 'id_voltaje') : $voltajes;
    }

    public static function getAllVoltajes(bool $withSelectFormato = false)
    {
        $voltajes = self::remember('voltajes:publicos', self::CACHE_TTL['voltajes'],
            fn () => Voltaje::whereNull('id_negocio')
                ->select('id_voltaje', 'voltaje')
                ->orderBy('voltaje')
                ->get()
        );

        return $withSelectFormato ? $voltajes->pluck('voltaje', 'id_voltaje') : $voltajes;
    }

    public static function getVoltajeById(string $id, ?string $idNegocio = null): ?Voltaje
    {
        return self::remember(
            "voltaje:{$id}",
            self::CACHE_TTL['voltajes'],
            function () use ($id, $idNegocio) {
                $query = Voltaje::query();
                if ($idNegocio) {
                    $query->where('id_negocio', $idNegocio);
                }
                return $query->find($id);
            },
            $idNegocio
        );
    }

    public static function invalidateVoltaje(string $idVoltaje, ?string $idNegocio = null): void
    {
        if ($idNegocio) {
            self::incrementVersion($idNegocio);
        } else {
            self::incrementVersion();
        }
    }

    public static function invalidateVoltajes(): void
    {
        self::incrementVersion();
    }

    // ─── COLORES ────────────────────────────────────────────────────────────

    public static function getColoresByNegocio(string $idNegocio, bool $withSelectFormato = false)
    {
        $colores = self::remember(
            "colores:negocio:{$idNegocio}",
            self::CACHE_TTL['colores'],
            fn () => Color::where('id_negocio', $idNegocio)
                ->select('id_color', 'id_modelo', 'color')
                ->orderBy('color')
                ->get(),
            $idNegocio
        );

        return $withSelectFormato ? $colores->pluck('color', 'id_color') : $colores;
    }

    public static function getColoresByModelo(string $idModelo, ?string $idNegocio = null, bool $withSelectFormato = false)
    {
        $cacheKey = $idNegocio
            ? "colores:modelo:{$idModelo}:negocio:{$idNegocio}"
            : "colores:modelo:{$idModelo}";

        $colores = self::remember(
            $cacheKey,
            self::CACHE_TTL['colores'],
            function () use ($idModelo, $idNegocio) {
                $query = Color::where('id_modelo', $idModelo)->select('id_color', 'color');

                if ($idNegocio) {
                    $query->where('id_negocio', $idNegocio);
                } else {
                    $query->whereNull('id_negocio');
                }

                return $query->orderBy('color')->get();
            },
            $idNegocio
        );

        return $withSelectFormato ? $colores->pluck('color', 'id_color') : $colores;
    }

    public static function getAllColores(bool $withSelectFormato = false)
    {
        $colores = self::remember('colores:publicos', self::CACHE_TTL['colores'],
            fn () => Color::whereNull('id_negocio')
                ->select('id_color', 'color')
                ->orderBy('color')
                ->get()
        );

        return $withSelectFormato ? $colores->pluck('color', 'id_color') : $colores;
    }

    public static function getColorById(string $id, ?string $idNegocio = null): ?Color
    {
        return self::remember(
            "color:{$id}",
            self::CACHE_TTL['colores'],
            function () use ($id, $idNegocio) {
                $query = Color::query();
                if ($idNegocio) {
                    $query->where('id_negocio', $idNegocio);
                }
                return $query->find($id);
            },
            $idNegocio
        );
    }

    public static function invalidateColor(string $idColor, string $idModelo, ?string $idNegocio = null): void
    {
        if ($idNegocio) {
            self::incrementVersion($idNegocio);
        } else {
            self::incrementVersion();
        }
    }

    // ─── NEGOCIOS ───────────────────────────────────────────────────────────

    public static function getNegocios(bool $withSelectFormato = false)
    {
        $negocios = self::remember('negocios', self::CACHE_TTL['negocios'],
            fn () => Negocio::select('id_negocio', 'nombre_negocio')
                ->orderBy('nombre_negocio')
                ->get()
        );

        return $withSelectFormato ? $negocios->pluck('nombre_negocio', 'id_negocio') : $negocios;
    }

    public static function getNegocioById(string $idNegocio): ?Negocio
    {
        return self::remember("negocio:{$idNegocio}", self::CACHE_TTL['negocios'],
            fn () => Negocio::find($idNegocio)
        );
    }

    // ─── BICICLETAS ─────────────────────────────────────────────────────────

    public static function getBicicletasPaginadas(string $idNegocio): LengthAwarePaginator
    {
        return Bicicleta::where('id_negocio', $idNegocio)
            ->with(['modelo', 'voltaje', 'color'])
            ->orderByDesc('updated_at')
            ->paginate(10);
    }

    /**
     * FIX: Ahora pasa $idNegocio al cuarto param de remember() para que use
     * la versión del tenant, igual que hace invalidateBicicleta().
     */
    public static function getBicicletaBySerie(string $numSerie, ?string $idNegocio = null): ?Bicicleta
    {
        return self::remember(
            "bicicleta:serie:{$numSerie}",
            self::CACHE_TTL['bicicletas'],
            fn () => Bicicleta::with(['modelo.marca', 'voltaje', 'color'])
                ->where('num_serie', $numSerie)
                ->when($idNegocio, fn($q) => $q->where('id_negocio', $idNegocio))
                ->first(),
            $idNegocio  // ← FIX: antes faltaba este argumento
        );
    }

    public static function getBicicletaStats(string $idNegocio): array
    {
        return self::remember(
            "stats:bicicletas:{$idNegocio}",
            self::CACHE_TTL['stats'],
            function () use ($idNegocio) {
                $stats = Bicicleta::where('id_negocio', $idNegocio)
                    ->selectRaw("
                        COUNT(*) as total,
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
            },
            $idNegocio
        );
    }

    public static function invalidateBicicleta(string $numSerie, string $idNegocio): void
    {
        $version = self::getVersion($idNegocio);
        Cache::forget(self::key("bicicleta:serie:{$numSerie}") . ":v{$version}");
        Cache::forget(self::key("stats:bicicletas:{$idNegocio}") . ":v{$version}");
        Cache::forget(self::key("stock:vendedores:negocio:{$idNegocio}") . ":v{$version}");
    }

    public static function getBicicletasPorUsuarioPaginadas(string $idNegocio, string $idUsuario, int $page = 1, ?string $search = null): LengthAwarePaginator
    {
        $usarCache = ($page === 1 && empty($search));

        if ($usarCache) {
            return self::remember(
                "bicicletas:user:{$idUsuario}:negocio:{$idNegocio}:page:1",
                self::CACHE_TTL['bicicletas'],
                fn () => self::queryBicicletasUsuario($idNegocio, $idUsuario, null)->paginate(10, ['*'], 'page', 1),
                $idNegocio
            );
        }

        return self::queryBicicletasUsuario($idNegocio, $idUsuario, $search)->paginate(10, ['*'], 'page', $page);
    }

    private static function queryBicicletasUsuario(string $idNegocio, string $idUsuario, ?string $search)
    {
        $query = Bicicleta::where('id_negocio', $idNegocio)
            ->where('id_usuario', $idUsuario)
            ->with(['modelo', 'voltaje', 'color']);

        if ($search) {
            $query->where(fn ($q) => $q
                ->where('num_serie', 'like', "%{$search}%")
                ->orWhere('status', 'like', "%{$search}%")
            );
        }

        return $query->orderByDesc('created_at');
    }

    public static function invalidateBicicletasPorUsuario(string $idUsuario, string $idNegocio): void
    {
        $version = self::getVersion($idNegocio);
        Cache::forget(self::key("bicicletas:user:{$idUsuario}:negocio:{$idNegocio}:page:1") . ":v{$version}");
    }

    public static function getBicicletasByCliente(string $idCliente, string $idNegocio)
    {
        return self::remember(
            "bicicletas:cliente:{$idCliente}:negocio:{$idNegocio}",
            300,
            fn () => Bicicleta::with(['modelo', 'color'])
                ->where('id_cliente', $idCliente)
                ->where('id_negocio', $idNegocio)
                ->where('status', '!=', 'danada')
                ->get(),
            $idNegocio
        );
    }

    /**
     * FIX: Ahora borra la clave con la versión actual antes de que cambie,
     * en lugar de intentar borrar con una versión que ya no existe.
     */
    public static function invalidateBicicletasByCliente(string $idCliente, string $idNegocio): void
    {
        $version = self::getVersion($idNegocio);
        Cache::forget(self::key("bicicletas:cliente:{$idCliente}:negocio:{$idNegocio}") . ":v{$version}");
    }

    public static function getStockPorVendedores(string $idNegocio): array
    {
        return self::remember(
            "stock:vendedores:negocio:{$idNegocio}",
            self::CACHE_TTL['bicicletas'],
            function () use ($idNegocio) {
                $vendedores = Usuario::where('id_negocio', $idNegocio)
                    ->where('id_rol', 2)
                    ->select('id_usuario', 'nombre_usuario', 'correo')
                    ->orderBy('nombre_usuario')
                    ->get();

                $totales = Bicicleta::where('id_negocio', $idNegocio)
                    ->selectRaw('id_usuario, COUNT(*) as total')
                    ->groupBy('id_usuario')
                    ->pluck('total', 'id_usuario');

                $sinAsignar = (int) ($totales[null] ?? 0);

                $resultado = [];
                foreach ($vendedores as $vendedor) {
                    $resultado[] = [
                        'vendedor'   => $vendedor,
                        'total'      => (int) ($totales[$vendedor->id_usuario] ?? 0),
                        'bicicletas' => null,
                    ];
                }
                $resultado[] = ['vendedor' => null, 'total' => $sinAsignar, 'bicicletas' => null];

                return $resultado;
            },
            $idNegocio
        );
    }

    public static function getBicicletasSeccion(string $idNegocio, ?string $idUsuario, int $page): array
    {
        $cacheKey = $idUsuario
            ? "seccion:vendedor:{$idUsuario}:negocio:{$idNegocio}:page:{$page}"
            : "seccion:sin_asignar:negocio:{$idNegocio}:page:{$page}";

        if ($page === 1) {
            return self::remember(
                $cacheKey,
                self::CACHE_TTL['bicicletas'],
                fn () => self::querySeccion($idNegocio, $idUsuario, 1),
                $idNegocio
            );
        }

        return self::querySeccion($idNegocio, $idUsuario, $page);
    }

    private static function querySeccion(string $idNegocio, ?string $idUsuario, int $page): array
    {
        $paginador = Bicicleta::with(['modelo', 'voltaje', 'color'])
            ->where('id_negocio', $idNegocio)
            ->where('status', '!=', 2)
            ->when($idUsuario, fn ($q) => $q->where('id_usuario', $idUsuario), fn ($q) => $q->whereNull('id_usuario'))
            ->orderByDesc('updated_at')
            ->paginate(5, ['*'], 'page', $page);

        return [
            'data'         => $paginador->items(),
            'current_page' => $paginador->currentPage(),
            'last_page'    => $paginador->lastPage(),
            'total'        => $paginador->total(),
        ];
    }

    /**
     * FIX: Borra la clave con la versión actual (antes de que se incremente).
     */
    public static function invalidateSeccion(?string $idUsuario, string $idNegocio): void
    {
        $version  = self::getVersion($idNegocio);
        $cacheKey = $idUsuario
            ? "seccion:vendedor:{$idUsuario}:negocio:{$idNegocio}:page:1"
            : "seccion:sin_asignar:negocio:{$idNegocio}:page:1";
        Cache::forget(self::key($cacheKey) . ":v{$version}");
    }

    public static function invalidateStockVendedores(string $idNegocio): void
    {
        $version = self::getVersion($idNegocio);
        Cache::forget(self::key("stock:vendedores:negocio:{$idNegocio}") . ":v{$version}");
    }

    // ─── PEDIDOS ────────────────────────────────────────────────────────────

    public static function getPedidoById(string $idPedido, ?string $idNegocio = null): ?Pedido
    {
        return self::remember(
            "pedido:{$idPedido}",
            self::CACHE_TTL['pedidos'],
            function () use ($idPedido, $idNegocio) {
                $query = Pedido::with(['usuario', 'negocio', 'items.modelo', 'items.voltaje', 'items.color', 'bicicletas']);
                if ($idNegocio) {
                    $query->where('id_negocio', $idNegocio);
                }
                return $query->find($idPedido);
            },
            $idNegocio
        );
    }

    /**
     * FIX: Cachea el resultado completo directamente en lugar de hacer
     * N llamadas individuales a getPedidoById() (N roundtrips a Redis).
     */
    public static function getPedidosRecientesByNegocio(string $idNegocio, int $limit = 10): \Illuminate\Support\Collection
    {
        return self::remember(
            "pedidos:recientes:negocio:{$idNegocio}:limit{$limit}",
            self::CACHE_TTL['pedidos'],
            fn () => Pedido::with([
                    'usuario',
                    'negocio',
                    'items.modelo',
                    'items.voltaje',
                    'items.color',
                    'bicicletas',
                ])
                ->where('id_negocio', $idNegocio)
                ->orderByDesc('created_at')
                ->limit($limit)
                ->get(),
            $idNegocio
        );
    }

    public static function getPedidoStats(string $idNegocio): array
    {
        return self::remember(
            "stats:pedidos:negocio:{$idNegocio}",
            self::CACHE_TTL['stats'],
            function () use ($idNegocio) {
                $stats = Pedido::where('id_negocio', $idNegocio)
                    ->selectRaw("
                        COUNT(*) as total,
                        SUM(status = 1) as pendientes,
                        SUM(status = 2) as en_proceso,
                        SUM(status = 3) as completados
                    ")
                    ->first();

                return [
                    'pendientes'  => (int) $stats->pendientes,
                    'en_proceso'  => (int) $stats->en_proceso,
                    'completados' => (int) $stats->completados,
                    'total'       => (int) $stats->total,
                ];
            },
            $idNegocio
        );
    }

    /**
     * FIX: Reemplaza el loop con límites hardcodeados por incrementVersion(),
     * que invalida todos los pedidos recientes sin importar el $limit usado.
     */
    public static function invalidatePedido(string $idPedido, string $idNegocio): void
    {
        $globalV = self::getVersion();
        $tenantV = self::getVersion($idNegocio);

        Cache::forget(self::key("pedido:{$idPedido}") . ":v{$globalV}");
        Cache::forget(self::key("stats:pedidos:negocio:{$idNegocio}") . ":v{$tenantV}");

        // FIX: en lugar de iterar límites hardcodeados (5,10,15,20),
        // incrementamos la versión del tenant para invalidar todas las
        // variantes de pedidos:recientes sin importar el $limit.
        self::incrementVersion($idNegocio);
    }

    // ─── USUARIOS ───────────────────────────────────────────────────────────

    public static function getUserById(string $idUsuario, ?string $idNegocio = null): ?Usuario
    {
        return self::remember(
            "usuario:{$idUsuario}",
            self::CACHE_TTL['usuarios'],
            function () use ($idUsuario, $idNegocio) {
                $query = Usuario::query();
                if ($idNegocio) {
                    $query->where('id_negocio', $idNegocio);
                }
                return $query->find($idUsuario);
            },
            $idNegocio
        );
    }

    public static function getUserWithNegocio(string $idUsuario, ?string $idNegocio = null): ?Usuario
    {
        return self::remember(
            "usuario:negocio:{$idUsuario}",
            self::CACHE_TTL['usuarios'],
            function () use ($idUsuario, $idNegocio) {
                $query = Usuario::with('negocio');
                if ($idNegocio) {
                    $query->where('id_negocio', $idNegocio);
                }
                return $query->find($idUsuario);
            },
            $idNegocio
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
        self::incrementVersion($idNegocio);

        $globalV = self::getVersion();
        Cache::forget(self::key("negocio:{$idNegocio}") . ":v{$globalV}");
        Cache::forget(self::key('negocios') . ":v{$globalV}");
    }

    // ─── STATS GLOBALES ─────────────────────────────────────────────────────

    public static function getGlobalStats(): array
    {
        return self::remember('stats:global', self::CACHE_TTL['stats'], fn () => [
            'total_marcas'        => Marca::count(),
            'total_modelos'       => Modelo::count(),
            'total_negocios'      => Negocio::count(),
            'total_colores'       => Color::count(),
            'total_voltajes'      => Voltaje::count(),
            'total_bicicletas'    => Bicicleta::count(),
            'total_pedidos'       => Pedido::count(),
            'total_usuarios'      => Usuario::count(),
            'relaciones_voltajes' => ModeloVoltaje::count(),
        ], null);
    }

    // ─── BÚSQUEDA ───────────────────────────────────────────────────────────

    public static function searchBicicletas(string $query, string $idNegocio, int $limit = 5): array
    {
        return self::remember(
            "search:bicicletas:{$idNegocio}:" . md5($query) . ":limit{$limit}",
            self::CACHE_TTL['search'],
            fn () => Bicicleta::where('id_negocio', $idNegocio)
                ->where('num_serie', 'like', "%{$query}%")
                ->limit($limit)
                ->get(['num_serie', 'status'])
                ->toArray(),
            $idNegocio
        );
    }

    // ─── PRODUCTOS ──────────────────────────────────────────────────────────

    public static function getProductos(bool $withSelectFormato = false)
    {
        $productos = self::remember('productos', self::CACHE_TTL['productos'],
            fn () => Producto::with('negocio')->orderBy('nombre_producto')->get()
        );

        return $withSelectFormato ? $productos->pluck('nombre_producto', 'id_producto') : $productos;
    }

    public static function getProductoById(string $idProducto, ?string $idNegocio = null): ?Producto
    {
        return self::remember(
            "producto:{$idProducto}",
            self::CACHE_TTL['productos'],
            function () use ($idProducto, $idNegocio) {
                $query = Producto::with('negocio');
                if ($idNegocio) {
                    $query->where('id_negocio', $idNegocio);
                }
                return $query->find($idProducto);
            },
            $idNegocio
        );
    }

    public static function getProductosByNegocio(string $idNegocio, bool $withSelectFormato = false)
    {
        $productos = self::remember(
            "productos:negocio:{$idNegocio}",
            self::CACHE_TTL['productos'],
            fn () => Producto::where('id_negocio', $idNegocio)->orderBy('nombre_producto')->get(),
            $idNegocio
        );

        return $withSelectFormato ? $productos->pluck('nombre_producto', 'id_producto') : $productos;
    }

    public static function invalidateProducto(string $idProducto, string $idNegocio): void
    {
        $globalV = self::getVersion();
        $tenantV = self::getVersion($idNegocio);

        Cache::forget(self::key("producto:{$idProducto}") . ":v{$globalV}");
        Cache::forget(self::key('productos') . ":v{$globalV}");
        Cache::forget(self::key("productos:negocio:{$idNegocio}") . ":v{$tenantV}");
    }

    // ─── CATÁLOGO COMPLETO ───────────────────────────────────────────────────

    public static function getCatalogoCompleto(string $idNegocio): \Illuminate\Support\Collection
    {
        return self::remember(
            "catalogo:completo:{$idNegocio}",
            self::CACHE_TTL['marcas'],
            fn () => Marca::where('id_negocio', $idNegocio)
                ->withCount('modelos')
                ->with(['modelos' => function ($q) use ($idNegocio) {
                    $q->where('id_negocio', $idNegocio)
                      ->with([
                          'colores'  => fn ($q) => $q->where('id_negocio', $idNegocio),
                          'voltajes' => fn ($q) => $q->wherePivot('id_negocio', $idNegocio)->orderBy('voltaje'),
                      ])
                      ->orderBy('updated_at');
                }])
                ->orderBy('updated_at')
                ->get(),
            $idNegocio
        );
    }

    public static function invalidateCatalogoCompleto(string $idNegocio): void
    {
        $version = self::getVersion($idNegocio);
        Cache::forget(self::key("catalogo:completo:{$idNegocio}") . ":v{$version}");
    }

    // ─── PRODUCTOS CON RELACIONES ────────────────────────────────────────────

    public static function getProductosConRelaciones(string $idNegocio, ?string $idUsuario = null)
    {
        $cacheKey = $idUsuario
            ? "productos:relaciones:negocio:{$idNegocio}:usuario:{$idUsuario}"
            : "productos:relaciones:negocio:{$idNegocio}";

        return self::remember(
            $cacheKey,
            self::CACHE_TTL['productos'],
            fn () => Producto::where('id_negocio', $idNegocio)
                ->when($idUsuario, fn ($q) => $q->where('id_usuario', $idUsuario))
                ->with([
                    'productoModelo.modelo.colores' => fn ($q) => $q->where('id_negocio', $idNegocio),
                    'productoModelo.voltaje',
                ])
                ->get(),
            $idNegocio
        );
    }

    public static function getSucursalesByNegocio(string $idNegocio)
    {
        return self::remember(
            "sucursales:negocio:{$idNegocio}",
            self::CACHE_TTL['usuarios'],
            fn () => Usuario::where('id_negocio', $idNegocio)
                ->where('id_rol', 2)
                ->get(['id_usuario', 'nombre_usuario']),
            $idNegocio
        );
    }

    public static function invalidateProductosConRelaciones(string $idNegocio, ?string $idUsuario = null): void
    {
        $version = self::getVersion($idNegocio);
        Cache::forget(self::key("productos:relaciones:negocio:{$idNegocio}") . ":v{$version}");
        if ($idUsuario) {
            Cache::forget(self::key("productos:relaciones:negocio:{$idNegocio}:usuario:{$idUsuario}") . ":v{$version}");
        }
    }

    // ─── CACHÉ GENERAL ──────────────────────────────────────────────────────

    public static function clearCache(?string $idNegocio = null): void
    {
        self::incrementVersion($idNegocio);
    }

    // ─── INVENTARIO ─────────────────────────────────────────────────────────

    /**
     * FIX: El método original obtenía la versión DESPUÉS de que el caller
     * ya la había incrementado en otro método (como invalidateInventario en
     * VentaController), haciendo que el forget() apuntara a una clave que
     * ya no existe (versión+1). Ahora borra con la versión ACTUAL y luego
     * incrementa para que el próximo remember() genere una clave nueva.
     */
    public static function invalidateInventario(string $idNegocio, ?string $idUsuario = null): void
    {
        $version = self::getVersion($idNegocio);

        Cache::forget(self::key("inventario:negocio:{$idNegocio}") . ":v{$version}");

        if ($idUsuario) {
            Cache::forget(self::key("inventario:sucursal:{$idNegocio}:{$idUsuario}") . ":v{$version}");
        }

        // Incrementar DESPUÉS del forget para que la próxima lectura
        // genere una clave nueva y vaya a DB.
        self::incrementVersion($idNegocio);
    }

    public static function getInventarioByNegocio(string $idNegocio)
    {
        return self::remember(
            "inventario:negocio:{$idNegocio}",
            self::CACHE_TTL['productos'],
            fn () => Inventario::with(['productoModelo.producto', 'sucursal'])
                ->where('id_negocio', $idNegocio)
                ->orderBy('id_usuario')
                ->get(),
            $idNegocio
        );
    }

    public static function getInventarioBySucursal(string $idNegocio, string $idUsuario)
    {
        return self::remember(
            "inventario:sucursal:{$idNegocio}:{$idUsuario}",
            self::CACHE_TTL['productos'],
            fn () => Inventario::with(['productoModelo.producto'])
                ->where('id_negocio', $idNegocio)
                ->where('id_usuario', $idUsuario)
                ->get(),
            $idNegocio
        );
    }

    // ─── COLORES EN STOCK POR MODELO ────────────────────────────────────────

    public static function getColoresEnStockPorModelos(array $idModelos, string $idNegocio, ?string $idUsuario = null): \Illuminate\Support\Collection
    {
        $cacheKey = $idUsuario
            ? "colores:stock:negocio:{$idNegocio}:usuario:{$idUsuario}"
            : "colores:stock:negocio:{$idNegocio}";

        return self::remember(
            $cacheKey,
            self::CACHE_TTL['colores'],
            fn () => Bicicleta::whereIn('id_modelo', $idModelos)
                ->where('id_negocio', $idNegocio)
                ->where('status', 1)
                ->when($idUsuario, fn ($q) => $q->where('id_usuario', $idUsuario))
                ->select('id_modelo', 'id_color')
                ->distinct()
                ->with('color')
                ->get()
                ->groupBy('id_modelo'),
            $idNegocio
        );
    }

    /**
     * FIX: Mismo patrón — forget con versión actual, luego incrementar.
     */
    public static function invalidateColoresEnStock(string $idNegocio, ?string $idUsuario = null): void
    {
        $version = self::getVersion($idNegocio);
        Cache::forget(self::key("colores:stock:negocio:{$idNegocio}") . ":v{$version}");
        if ($idUsuario) {
            Cache::forget(self::key("colores:stock:negocio:{$idNegocio}:usuario:{$idUsuario}") . ":v{$version}");
        }
        self::incrementVersion($idNegocio);
    }

    public static function invalidateSucursales(string $idNegocio): void
    {
        $version = self::getVersion($idNegocio);
        Cache::forget(self::key("sucursales:negocio:{$idNegocio}") . ":v{$version}");
    }

    // ─── MOVIMIENTOS DE BICICLETAS ───────────────────────────────────────────

    public static function getHistorialMovimientos(string $numSerie, string $idNegocio): \Illuminate\Support\Collection
    {
        return self::remember(
            "movimientos:serie:{$numSerie}",
            600,
            fn () => BicicletaMovimiento::with('usuario')
                ->where('num_serie', $numSerie)
                ->where('id_negocio', $idNegocio)
                ->orderBy('fecha_movimiento', 'asc')
                ->get(),
            $idNegocio
        );
    }

    public static function getMovimientosRecientes(string $idNegocio, int $limit = 20): \Illuminate\Support\Collection
    {
        return self::remember(
            "movimientos:recientes:negocio:{$idNegocio}:limit{$limit}",
            300,
            fn () => BicicletaMovimiento::with('usuario')
                ->where('id_negocio', $idNegocio)
                ->orderByDesc('fecha_movimiento')
                ->limit($limit)
                ->get(),
            $idNegocio
        );
    }

    /**
     * FIX: Mismo patrón — forget primero, incrementar después.
     */
    public static function invalidateMovimientos(string $numSerie, string $idNegocio): void
    {
        $version = self::getVersion($idNegocio);
        Cache::forget(self::key("movimientos:serie:{$numSerie}") . ":v{$version}");
        Cache::forget(self::key("movimientos:recientes:negocio:{$idNegocio}:limit20") . ":v{$version}");
        self::incrementVersion($idNegocio);
    }

    // ─── VENTAS ─────────────────────────────────────────────────────────────

    public static function getVentasByVendedor(string $idNegocio, string $idUsuario): LengthAwarePaginator
    {
        return self::remember(
            "ventas:vendedor:{$idUsuario}:negocio:{$idNegocio}:page:1",
            300,
            fn () => self::queryVentas($idNegocio, $idUsuario)
                ->paginate(15, ['*'], 'page', 1),
            $idNegocio
        );
    }

    private static function queryVentas(string $idNegocio, string $idUsuario)
    {
        return Venta::with([
                'cliente',
                'detalles.producto',
                'detalles.bicicleta.modelo',
                'detalles.bicicleta.color',
            ])
            ->where('id_negocio', $idNegocio)
            ->whereHas('detalles.producto', fn($q) => $q->where('id_usuario', $idUsuario))
            ->latest();
    }

    public static function invalidateVentasByVendedor(string $idNegocio, string $idUsuario): void
    {
        $version = self::getVersion($idNegocio);
        Cache::forget(self::key("ventas:vendedor:{$idUsuario}:negocio:{$idNegocio}:page:1") . ":v{$version}");
    }

    public static function getConfigNegocio(string $idNegocio): \App\Models\NegocioConfig
    {
        return self::remember(
            "config:negocio:{$idNegocio}",
            self::CACHE_TTL['negocios'],
            function () use ($idNegocio) {
                $config = \App\Models\NegocioConfig::where('id_negocio', $idNegocio)->first();

                if (!$config) {
                    // updateOrCreate como safety net ante race conditions concurrentes.
                    // Si dos procesos llegan aquí al mismo tiempo, el segundo hará
                    // un UPDATE sin romper nada, gracias al unique constraint en id_negocio.
                    $config = \App\Models\NegocioConfig::updateOrCreate(
                        ['id_negocio' => $idNegocio],
                        ['entrega_comprobante' => 'ticket']
                    );
                }

                return $config;
            },
            $idNegocio
        );
    }

    public static function invalidateConfigNegocio(string $idNegocio): void
    {
        $version = self::getVersion($idNegocio);
        Cache::forget(self::key("config:negocio:{$idNegocio}") . ":v{$version}");
    }
}