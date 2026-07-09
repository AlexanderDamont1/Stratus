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
use App\Models\Personal;
use App\Models\Usuario;
use App\Models\Venta;
use App\Models\Producto;
use App\Models\Inventario;
use App\Models\BicicletaMovimiento;
use App\Models\PiezaCatalogo;
use App\Models\PiezaMovimiento;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Models\BicicletaGarantia;
use App\Models\GarantiaReclamo;

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
        'piezas'     => 300,
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
        return (int) Cache::get(self::getVersionKey($idNegocio), 0);
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

    /**
     * Ahora es public para que StockService y ReparacionService
     * puedan usarlo directamente sin duplicar el helper.
     */
    public static function remember(string $key, int $ttl, callable $callback, ?string $idNegocio = null)
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
            fn() => Marca::where('id_negocio', $idNegocio)
                ->select('id_marca', 'nombre_marca')
                ->orderBy('nombre_marca')
                ->get(),
            $idNegocio
        );

        return $withSelectFormato ? $marcas->pluck('nombre_marca', 'id_marca') : $marcas;
    }

    public static function getMarcasPublicas(bool $withSelectFormato = false)
    {
        $marcas = self::remember(
            'marcas:publicas',
            self::CACHE_TTL['marcas'],
            fn() =>
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
            fn() => Modelo::where('id_negocio', $idNegocio)
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
            fn() => Modelo::where('id_marca', $idMarca)
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
        $modelos = self::remember(
            'modelos:publicos',
            self::CACHE_TTL['modelos'],
            fn() => Modelo::whereNull('id_negocio')
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

    public static function getVoltajesByNegocio(string $idNegocio, bool $withSelectFormato = false, bool $paginar = false, int $page = 1)
    {
        if ($paginar) {
            return self::remember(
                "voltajes:negocio:{$idNegocio}:page:{$page}",
                self::CACHE_TTL['voltajes'],
                fn() => Voltaje::where('id_negocio', $idNegocio)
                    ->select('id_voltaje', 'voltaje')
                    ->orderBy('voltaje')
                    ->paginate(20, ['*'], 'page', $page),
                $idNegocio
            );
        }

        $voltajes = self::remember(
            "voltajes:negocio:{$idNegocio}",
            self::CACHE_TTL['voltajes'],
            fn() => Voltaje::where('id_negocio', $idNegocio)
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

    public static function getAllVoltajes(bool $withSelectFormato = false, bool $paginar = false, int $page = 1)
    {
        if ($paginar) {
            return self::remember(
                "voltajes:publicos:page:{$page}",
                self::CACHE_TTL['voltajes'],
                fn() => Voltaje::whereNull('id_negocio')
                    ->select('id_voltaje', 'voltaje')
                    ->orderBy('voltaje')
                    ->paginate(20, ['*'], 'page', $page)
            );
        }

        $voltajes = self::remember(
            'voltajes:publicos',
            self::CACHE_TTL['voltajes'],
            fn() => Voltaje::whereNull('id_negocio')
                ->select('id_voltaje', 'voltaje')
                ->orderBy('voltaje')
                ->get()
        );

        return $withSelectFormato ? $voltajes->pluck('voltaje', 'id_voltaje') : $voltajes;
    }

    public static function getAllVoltajesPaginados(int $page = 1): LengthAwarePaginator
    {
        return self::remember(
            "voltajes:publicos:page:{$page}",
            self::CACHE_TTL['voltajes'],
            fn() => Voltaje::whereNull('id_negocio')
                ->select('id_voltaje', 'voltaje')
                ->orderBy('voltaje')
                ->paginate(20, ['*'], 'page', $page)
        );
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
        self::incrementVersion($idNegocio ?? null);
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
            fn() => Color::where('id_negocio', $idNegocio)
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
        $colores = self::remember(
            'colores:publicos',
            self::CACHE_TTL['colores'],
            fn() => Color::whereNull('id_negocio')
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
        self::incrementVersion($idNegocio ?? null);
    }

    // ─── NEGOCIOS ───────────────────────────────────────────────────────────

    public static function getNegocios(bool $withSelectFormato = false)
    {
        $negocios = self::remember(
            'negocios',
            self::CACHE_TTL['negocios'],
            fn() => Negocio::select('id_negocio', 'nombre_negocio')
                ->orderBy('nombre_negocio')
                ->get()
        );

        return $withSelectFormato ? $negocios->pluck('nombre_negocio', 'id_negocio') : $negocios;
    }

    public static function getNegocioById(string $idNegocio): ?Negocio
    {
        return self::remember(
            "negocio:{$idNegocio}",
            self::CACHE_TTL['negocios'],
            fn() => Negocio::find($idNegocio),
            $idNegocio
        );
    }

    public static function invalidateNegocio(string $idNegocio): void
    {
        $globalV = self::getVersion();
        $tenantV = self::getVersion($idNegocio);

        Cache::forget(self::key("negocio:{$idNegocio}") . ":v{$tenantV}");
        Cache::forget(self::key('negocios') . ":v{$globalV}");

        self::incrementVersion($idNegocio);
    }

    // ─── BICICLETAS ─────────────────────────────────────────────────────────

    public static function getBicicletasPaginadas(string $idNegocio, int $page = 1): LengthAwarePaginator
    {
        return self::remember(
            "bicicletas:negocio:{$idNegocio}:page:{$page}",
            self::CACHE_TTL['bicicletas'],
            fn() => Bicicleta::where('id_negocio', $idNegocio)
                ->with(['modelo', 'voltaje', 'color'])
                ->orderByDesc('updated_at')
                ->paginate(10, ['*'], 'page', $page),
            $idNegocio
        );
    }

    public static function getBicicletaBySerie(string $numSerie, ?string $idNegocio = null): ?Bicicleta
    {
        return self::remember(
            "bicicleta:serie:{$numSerie}",
            self::CACHE_TTL['bicicletas'],
            fn() => Bicicleta::with(['modelo.marca', 'voltaje', 'color'])
                ->where('num_serie', $numSerie)
                ->when($idNegocio, fn($q) => $q->where('id_negocio', $idNegocio))
                ->first(),
            $idNegocio
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
        Cache::forget(self::key("bicicleta:garantia:{$numSerie}") . ":v{$version}"); // ← añadir
        Cache::forget(self::key("stats:bicicletas:{$idNegocio}") . ":v{$version}");
        Cache::forget(self::key("stock:vendedores:negocio:{$idNegocio}") . ":v{$version}");
        foreach ([1, 2, 3] as $p) {
            Cache::forget(self::key("bicicletas:negocio:{$idNegocio}:page:{$p}") . ":v{$version}");
        }
    }

    public static function getBicicletasPorUsuarioPaginadas(string $idNegocio, string $idUsuario, int $page = 1, ?string $search = null): LengthAwarePaginator
    {
        if (!empty($search)) {
            return self::queryBicicletasUsuario($idNegocio, $idUsuario, $search)
                ->paginate(10, ['*'], 'page', $page);
        }

        return self::remember(
            "bicicletas:user:{$idUsuario}:negocio:{$idNegocio}:page:{$page}",
            self::CACHE_TTL['bicicletas'],
            fn() => self::queryBicicletasUsuario($idNegocio, $idUsuario, null)
                ->paginate(10, ['*'], 'page', $page),
            $idNegocio
        );
    }

    private static function queryBicicletasUsuario(string $idNegocio, string $idUsuario, ?string $search)
    {
        $query = Bicicleta::where('id_negocio', $idNegocio)
            ->where('id_usuario', $idUsuario)
            ->with(['modelo', 'voltaje', 'color']);

        if ($search) {
            $query->where(
                fn($q) => $q
                    ->where('num_serie', 'like', "%{$search}%")
                    ->orWhere('status', 'like', "%{$search}%")
            );
        }

        return $query->orderByDesc('created_at');
    }

    public static function invalidateBicicletasPorUsuario(string $idUsuario, string $idNegocio): void
    {
        $version = self::getVersion($idNegocio);
        foreach ([1, 2, 3] as $p) {
            Cache::forget(self::key("bicicletas:user:{$idUsuario}:negocio:{$idNegocio}:page:{$p}") . ":v{$version}");
        }
    }

    public static function getBicicletasByCliente(string $idCliente, string $idNegocio)
    {
        return self::remember(
            "bicicletas:cliente:{$idCliente}:negocio:{$idNegocio}",
            300,
            fn() => Bicicleta::with(['modelo', 'color'])
                ->where('id_cliente', $idCliente)
                ->where('id_negocio', $idNegocio)
                ->where('status', '!=', 'danada')
                ->get(),
            $idNegocio
        );
    }

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

        return self::remember(
            $cacheKey,
            self::CACHE_TTL['bicicletas'],
            fn() => self::querySeccion($idNegocio, $idUsuario, $page),
            $idNegocio
        );
    }

    private static function querySeccion(string $idNegocio, ?string $idUsuario, int $page): array
    {
        $paginador = Bicicleta::with(['modelo', 'voltaje', 'color'])
            ->where('id_negocio', $idNegocio)
            ->where('status', '!=', 2)
            ->when($idUsuario, fn($q) => $q->where('id_usuario', $idUsuario), fn($q) => $q->whereNull('id_usuario'))
            ->orderByDesc('updated_at')
            ->paginate(5, ['*'], 'page', $page);

        return [
            'data'         => $paginador->items(),
            'current_page' => $paginador->currentPage(),
            'last_page'    => $paginador->lastPage(),
            'total'        => $paginador->total(),
        ];
    }

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

    public static function getPedidosRecientesByNegocio(string $idNegocio, int $limit = 10): \Illuminate\Support\Collection
    {
        return self::remember(
            "pedidos:recientes:negocio:{$idNegocio}:limit{$limit}",
            self::CACHE_TTL['pedidos'],
            fn() => Pedido::with([
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

    public static function invalidatePedido(string $idPedido, string $idNegocio): void
    {
        $tenantV = self::getVersion($idNegocio);
        $globalV = self::getVersion();

        Cache::forget(self::key("pedido:{$idPedido}") . ":v{$globalV}");
        Cache::forget(self::key("pedido:{$idPedido}") . ":v{$tenantV}");
        Cache::forget(self::key("stats:pedidos:negocio:{$idNegocio}") . ":v{$tenantV}");

        foreach ([5, 10, 15, 20] as $limit) {
            Cache::forget(self::key("pedidos:recientes:negocio:{$idNegocio}:limit{$limit}") . ":v{$tenantV}");
        }
    }

    // ─── USUARIOS ───────────────────────────────────────────────────────────

    public static function getUserById(string $idUsuario, string $idNegocio): ?Usuario
    {
        return self::remember(
            "usuario:{$idUsuario}",
            self::CACHE_TTL['usuarios'],
            fn() => Usuario::where('id_negocio', $idNegocio)->find($idUsuario),
            $idNegocio
        );
    }

    public static function getUserWithNegocio(string $idUsuario, string $idNegocio): ?Usuario
    {
        return self::remember(
            "usuario:negocio:{$idUsuario}",
            self::CACHE_TTL['usuarios'],
            fn() => Usuario::with('negocio')->where('id_negocio', $idNegocio)->find($idUsuario),
            $idNegocio
        );
    }

    public static function invalidateUsuario(string $idUsuario, string $idNegocio): void
    {
        $version = self::getVersion($idNegocio);
        Cache::forget(self::key("usuario:{$idUsuario}") . ":v{$version}");
        Cache::forget(self::key("usuario:negocio:{$idUsuario}") . ":v{$version}");
    }

    // ─── STATS GLOBALES ─────────────────────────────────────────────────────

    public static function getGlobalStats(): array
    {
        return self::remember('stats:global', self::CACHE_TTL['stats'], fn() => [
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
            fn() => Bicicleta::where('id_negocio', $idNegocio)
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
        $productos = self::remember(
            'productos',
            self::CACHE_TTL['productos'],
            fn() => Producto::with('negocio')->orderBy('nombre_producto')->get()
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
            fn() => Producto::where('id_negocio', $idNegocio)->orderBy('nombre_producto')->get(),
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

        self::incrementVersion($idNegocio);
    }

    // ─── CATÁLOGO COMPLETO ───────────────────────────────────────────────────

    public static function getCatalogoCompleto(string $idNegocio): \Illuminate\Support\Collection
    {
        return self::remember(
            "catalogo:completo:{$idNegocio}",
            self::CACHE_TTL['marcas'],
            fn() => Marca::where('id_negocio', $idNegocio)
                ->withCount('modelos')
                ->with(['modelos' => function ($q) use ($idNegocio) {
                    $q->where('id_negocio', $idNegocio)
                        ->with([
                            'colores'  => fn($q) => $q->where('id_negocio', $idNegocio),
                            'voltajes' => fn($q) => $q->wherePivot('id_negocio', $idNegocio)->orderBy('voltaje'),
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
            fn() => Producto::where('id_negocio', $idNegocio)
                ->when($idUsuario, fn($q) => $q->where('id_usuario', $idUsuario))
                ->with([
                    'productoModelo.modelo.marca',
                    'productoModelo.modelo.colores' => fn($q) => $q->where('id_negocio', $idNegocio),
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
            fn() => Usuario::where('id_negocio', $idNegocio)
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

    public static function getInventarioByNegocio(string $idNegocio)
    {
        return self::remember(
            "inventario:negocio:{$idNegocio}",
            self::CACHE_TTL['productos'],
            fn() => Inventario::with([
                    'productoModelo.producto:id_producto,nombre_producto,precio',
                    'sucursal:id_usuario,nombre_usuario',
                ])
                ->where('id_negocio', $idNegocio)
                ->select('id_inventario', 'id_producto_modelo', 'id_producto', 'id_negocio', 'id_usuario', 'cantidad', 'stock_minimo')
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
            fn() => Inventario::with(['productoModelo.producto:id_producto,nombre_producto,precio'])
                ->where('id_negocio', $idNegocio)
                ->where('id_usuario', $idUsuario)
                ->select('id_inventario', 'id_producto_modelo', 'id_producto', 'id_negocio', 'id_usuario', 'cantidad', 'stock_minimo')
                ->get(),
            $idNegocio
        );
    }

    public static function invalidateInventario(string $idNegocio, ?string $idUsuario = null): void
    {
        $version = self::getVersion($idNegocio);

        Cache::forget(self::key("inventario:negocio:{$idNegocio}") . ":v{$version}");

        if ($idUsuario) {
            Cache::forget(self::key("inventario:sucursal:{$idNegocio}:{$idUsuario}") . ":v{$version}");
        }

        self::incrementVersion($idNegocio);
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
            fn() => Bicicleta::whereIn('id_modelo', $idModelos)
                ->where('id_negocio', $idNegocio)
                ->where('status', 1)
                ->when($idUsuario, fn($q) => $q->where('id_usuario', $idUsuario))
                ->select('id_modelo', 'id_color')
                ->distinct()
                ->with('color')
                ->get()
                ->groupBy('id_modelo'),
            $idNegocio
        );
    }

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
            fn() => BicicletaMovimiento::with('usuario')
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
            fn() => BicicletaMovimiento::with('usuario')
                ->where('id_negocio', $idNegocio)
                ->orderByDesc('fecha_movimiento')
                ->limit($limit)
                ->get(),
            $idNegocio
        );
    }

    public static function invalidateMovimientos(string $numSerie, string $idNegocio): void
    {
        $version = self::getVersion($idNegocio);
        Cache::forget(self::key("movimientos:serie:{$numSerie}") . ":v{$version}");
        Cache::forget(self::key("movimientos:recientes:negocio:{$idNegocio}:limit20") . ":v{$version}");
        self::incrementVersion($idNegocio);
    }

    // ─── VENTAS ─────────────────────────────────────────────────────────────

    public static function getVentasByVendedor(string $idNegocio, string $idUsuario, int $page = 1): LengthAwarePaginator
    {
        return self::remember(
            "ventas:vendedor:{$idUsuario}:negocio:{$idNegocio}:page:{$page}",
            300,
            fn() => Venta::with([
                'cliente',
                'detalles.producto',
                'detalles.bicicleta.modelo',
                'detalles.bicicleta.color',
            ])
                ->where('id_negocio', $idNegocio)
                ->where('id_usuario', $idUsuario)   // ← directo, sin whereHas
                ->latest()
                ->paginate(15, ['*'], 'page', $page),
            $idNegocio
        );
    }

    public static function invalidateVentasByVendedor(string $idNegocio, string $idUsuario): void
    {
        $version = self::getVersion($idNegocio);
        foreach ([1, 2, 3] as $p) {
            Cache::forget(self::key("ventas:vendedor:{$idUsuario}:negocio:{$idNegocio}:page:{$p}") . ":v{$version}");
        }
    }

    // ─── CONFIG NEGOCIO ──────────────────────────────────────────────────────

    public static function getConfigNegocio(string $idNegocio): array
    {
        return self::remember(
            "config:negocio:{$idNegocio}",
            self::CACHE_TTL['negocios'],
            function () use ($idNegocio) {
                $definiciones = \App\Models\NegocioConfig::where('activo', true)->get();

                $valores = \App\Models\NegocioConfigValor::where('id_negocio', $idNegocio)
                    ->pluck('valor', 'id_ncf');

                $config = [];
                foreach ($definiciones as $def) {
                    $valor = $valores[$def->id_ncf] ?? $def->valor_default;

                    $config[$def->clave] = $def->tipo === 'checkbox_multi'
                        ? json_decode($valor, true)
                        : $valor;
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
        self::incrementVersion($idNegocio);
    }

    // ─── PIEZAS (lecturas) ───────────────────────────────────────────────────

    /**
     * Lista paginada de piezas con filtros.
     * Las búsquedas libres van directo a BD (resultados demasiado variables).
     */
    public static function getPiezasPaginadas(
        string  $idNegocio,
        int     $page = 1,
        ?string $busqueda = null,
        ?string $categoria = null,
        bool    $soloStockBajo = false
    ): LengthAwarePaginator {
        if ($busqueda) {
            return self::queryPiezas($idNegocio, $busqueda, $categoria, $soloStockBajo)
                ->paginate(20, ['*'], 'page', $page);
        }

        $key = "piezas:lista:{$idNegocio}:cat:" . ($categoria ?? 'todas')
            . ":bajo:" . ($soloStockBajo ? '1' : '0')
            . ":p{$page}";

        return self::remember(
            $key,
            self::CACHE_TTL['piezas'],
            fn() =>
            self::queryPiezas($idNegocio, null, $categoria, $soloStockBajo)
                ->paginate(20, ['*'], 'page', $page),
            $idNegocio
        );
    }

    private static function queryPiezas(
        string  $idNegocio,
        ?string $busqueda,
        ?string $categoria,
        bool    $soloStockBajo
    ) {
        return PiezaCatalogo::where('id_negocio', $idNegocio)
            ->where('activo', true)
            ->when($busqueda, fn($q) => $q->where(
                fn($q2) =>
                $q2->where('nombre', 'like', "%{$busqueda}%")
                    ->orWhere('clave', 'like', "%{$busqueda}%")
            ))
            ->when($categoria, fn($q) => $q->where('categoria', $categoria))
            ->when($soloStockBajo, fn($q) => $q->whereColumn('stock_actual', '<=', 'stock_minimo'))
            ->orderBy('nombre');
    }

    public static function getPiezaById(string $idPieza, string $idNegocio): ?PiezaCatalogo
    {
        return self::remember(
            "pieza:{$idPieza}",
            self::CACHE_TTL['piezas'],
            fn() => PiezaCatalogo::where('id_negocio', $idNegocio)
                ->with('movimientos.usuario')
                ->find($idPieza),
            $idNegocio
        );
    }

    public static function getPiezasByNegocio(string $idNegocio)
    {
        return self::remember(
            "piezas:negocio:{$idNegocio}",
            self::CACHE_TTL['piezas'],
            fn() => PiezaCatalogo::where('id_negocio', $idNegocio)
                ->orderBy('nombre')
                ->get(),
            $idNegocio
        );
    }

    public static function getCategoriasPiezas(string $idNegocio): array
    {
        return self::remember(
            "piezas:categorias:{$idNegocio}",
            self::CACHE_TTL['piezas'],
            fn() => PiezaCatalogo::where('id_negocio', $idNegocio)
                ->whereNotNull('categoria')
                ->distinct()
                ->orderBy('categoria')
                ->pluck('categoria')
                ->toArray(),
            $idNegocio
        );
    }


    public static function buscarPiezasParaDiagnostico(
        string  $idNegocio,
        string  $busqueda,
        ?string $idModelo = null
    ): array {
        $piezas = PiezaCatalogo::where('id_negocio', $idNegocio)
            ->where('activo', true)
            ->where(
                fn($q) =>
                $q->where('nombre', 'like', "%{$busqueda}%")
                    ->orWhere('clave', 'like', "%{$busqueda}%")
            )
            ->orderBy('nombre')
            ->limit(15)
            ->get();

        return $piezas->map(function ($p) use ($idModelo) {
            $compatible = $idModelo ? $p->esCompatibleCon($idModelo) : true;
            return [
                'id_pieza'     => $p->id_pieza,
                'nombre'       => $p->nombre,
                'clave'        => $p->clave,
                'categoria'    => $p->categoria,
                'precio_venta' => (float) $p->precio_venta,
                'stock_actual' => $p->stock_actual,
                'stock_bajo'   => $p->stockBajo(),
                'compatible'   => $compatible,
            ];
        })
            ->sortByDesc('compatible')
            ->values()
            ->toArray();
    }


    public static function getModelosParaSelectorPiezas(string $idNegocio): array
    {
        return self::getModelosByNegocio($idNegocio)
            ->map(fn($m) => [
                'id_modelo'     => $m->id_modelo,
                'nombre_modelo' => $m->nombre_modelo,
            ])
            ->toArray();
    }


    public static function getHistorialPieza(
        string $idPieza,
        int    $page = 1
    ): LengthAwarePaginator {
        return PiezaMovimiento::with('usuario')
            ->where('id_pieza', $idPieza)
            ->orderByDesc('created_at')
            ->paginate(20, ['*'], 'page', $page);
    }

    public static function invalidatePiezas(string $idNegocio): void
    {
        self::incrementVersion($idNegocio);
    }


    // ─── GARANTÍAS INDEX ────────────────────────────────────────────────────

    public static function getGarantiasIndex(string $idNegocio, int $page = 1): array
    {
        return self::remember(
            "garantias:index:negocio:{$idNegocio}:page:{$page}",
            900,
            function () use ($idNegocio, $page) {
                $garantiaSub = BicicletaGarantia::selectRaw("
                        num_serie,
                        SUBSTRING_INDEX(
                            GROUP_CONCAT(estado ORDER BY FIELD(estado, 'vigente', 'por_vencer', 'expirada')),
                            ',', 1
                        ) AS status_garantia
                    ")
                    ->where('id_negocio', $idNegocio)
                    ->whereNull('id_reemplazada_por')
                    ->groupBy('num_serie');

                $bicicletas = Bicicleta::with(['modelo', 'marca', 'color', 'voltaje'])
                    ->where('bicicletas.id_negocio', $idNegocio)
                    ->where('bicicletas.status', 2)
                    ->leftJoinSub($garantiaSub, 'g', 'g.num_serie', '=', 'bicicletas.num_serie')
                    ->addSelect('bicicletas.*', 'g.status_garantia')
                    ->latest('bicicletas.created_at')
                    ->paginate(15, ['*'], 'page', $page);

                $ultimaGarantia = BicicletaGarantia::with('bicicleta.modelo')
                    ->where('id_negocio', $idNegocio)
                    ->latest()
                    ->first();

                $statsGarantias = BicicletaGarantia::where('id_negocio', $idNegocio)
                    ->selectRaw("
                        COUNT(*) as consultas,
                        SUM(estado = 'vigente') as activas,
                        SUM(id_reemplazada_por IS NOT NULL) as reemplazos
                    ")
                    ->first();

                $stats = [
                    'activas'    => (int) $statsGarantias->activas,
                    'consultas'  => (int) $statsGarantias->consultas,
                    'reclamos'   => GarantiaReclamo::where('id_negocio', $idNegocio)->count(),
                    'reemplazos' => (int) $statsGarantias->reemplazos,
                ];

                return compact('bicicletas', 'ultimaGarantia', 'stats');
            },
            $idNegocio
        );
    }

    public static function invalidateGarantiasIndex(string $idNegocio): void
    {
        $version = self::getVersion($idNegocio);
        foreach (range(1, 5) as $p) {
            Cache::forget(self::key("garantias:index:negocio:{$idNegocio}:page:{$p}") . ":v{$version}");
        }
    }

    public static function getBicicletaParaGarantia(string $numSerie, string $idNegocio): ?Bicicleta
    {
        return self::remember(
            "bicicleta:garantia:{$numSerie}",
            self::CACHE_TTL['bicicletas'],
            fn() => Bicicleta::with(['modelo.marca', 'voltaje', 'color', 'cliente'])
                ->where('num_serie', $numSerie)
                ->first(),
            $idNegocio
        );
    }

    public static function invalidateBicicletaGarantia(string $numSerie, string $idNegocio): void
    {
        $version = self::getVersion($idNegocio);
        Cache::forget(self::key("bicicleta:garantia:{$numSerie}") . ":v{$version}");
    }

    public static function getClienteByNumSerie(string $numSerie, string $idNegocio): ?\App\Models\Cliente
    {
        return self::remember(
            "cliente:serie:{$numSerie}",
            300,
            fn() => \App\Models\Venta::where('id_negocio', $idNegocio)
                ->whereHas('detalles', fn($q) => $q->where('num_serie', $numSerie))
                ->with('cliente')
                ->latest()
                ->first()
                ?->cliente,
            $idNegocio
        );
    }


    public static function getPersonalByNegocio(string $idNegocio): array
    {
        return self::remember(
            "personal:negocio:{$idNegocio}",
            self::CACHE_TTL['usuarios'],
            function () use ($idNegocio) {
                return Personal::with('sucursal:id_usuario,nombre_usuario')
                    ->deNegocio($idNegocio)
                    ->orderBy('nombre')
                    ->get()
                    ->groupBy('nombre')
                    ->map(function ($grupo) {
                        $primero = $grupo->first();
                        return [
                            'id_personal' => $primero->id_personal,
                            'nombre'      => $primero->nombre,
                            'activo'      => (bool) $primero->activo,
                            'sucursales'  => $grupo->map(fn($p) => [
                                'id_usuario'     => $p->id_usuario,
                                'nombre_usuario' => $p->sucursal->nombre_usuario ?? '—',
                            ])->values()->toArray(),
                        ];
                    })
                    ->values()
                    ->toArray();
            },
            $idNegocio
        );
    }

    public static function getPersonalItem(string $nombre, string $idNegocio): array
    {
        // Cache corto — solo se usa justo después de un write, dura lo suficiente
        // para que el response JSON lo reciba el cliente sin re-consultar
        return self::remember(
            "personal:item:" . md5($nombre) . ":{$idNegocio}",
            60, // 1 minuto — solo para absorber el response inmediato post-write
            function () use ($nombre, $idNegocio) {
                $registros = Personal::with('sucursal:id_usuario,nombre_usuario')
                    ->deNegocio($idNegocio)
                    ->where('nombre', $nombre)
                    ->get();

                if ($registros->isEmpty()) return [];

                $primero = $registros->first();
                return [
                    'id_personal' => $primero->id_personal,
                    'nombre'      => $primero->nombre,
                    'activo'      => (bool) $primero->activo,
                    'sucursales'  => $registros->map(fn($p) => [
                        'id_usuario'     => $p->id_usuario,
                        'nombre_usuario' => $p->sucursal->nombre_usuario ?? '—',
                    ])->values()->toArray(),
                ];
            },
            $idNegocio
        );
    }

    public static function invalidatePersonal(string $idNegocio): void
    {
        // Incrementar versión invalida getPersonalByNegocio y getPersonalItem
        // a la vez, porque ambos usan remember() con la versión del tenant
        self::incrementVersion($idNegocio);
    }

    // ─── ESTADÍSTICAS DIARIAS (DASHBOARD) ────────────────────────────────────────

    public static function getDashboardStats(string $idNegocio, string $desde, string $hasta): ?array
    {
        $cacheKey = "dashboard:stats:{$idNegocio}:{$desde}:{$hasta}";
        $version  = self::getVersion($idNegocio);
        $fullKey  = self::key($cacheKey) . ":v{$version}";

        // TTL dinámico: si el periodo incluye hoy, cache corto (5 min)
        // Si es histórico puro, cache largo (1 hora)
        $incluyeHoy = $hasta >= now()->toDateString();
        $ttl        = $incluyeHoy ? 300 : 3600;

        return Cache::remember($fullKey, $ttl, function () use ($idNegocio, $desde, $hasta) {
            $filas = \App\Models\EstadisticaDiaria::where('id_negocio', $idNegocio)
                ->whereBetween('fecha', [$desde, $hasta])
                ->orderBy('fecha')
                ->get();

            if ($filas->isEmpty()) return null;

            return [
                'filas'  => $filas->toArray(),
                'cached' => now()->toDateTimeString(),
            ];
        });
    }

    public static function invalidateDashboardStats(string $idNegocio, string $fecha): void
    {
        $version = self::getVersion($idNegocio);

       
        Cache::forget(self::key("dashboard:stats:{$idNegocio}:{$fecha}:{$fecha}") . ":v{$version}");
        self::incrementVersion($idNegocio);
    }
   
    public static function getGraficaHoyPorHora(string $idNegocio, string $fecha): \Illuminate\Support\Collection
    {
        return self::remember(
            "dashboard:grafica_hora:{$idNegocio}:{$fecha}",
            60, // 1 minuto — "hoy" cambia rápido pero no necesitas tiempo real exacto
            fn() => collect(range(0, 23))->map(function ($hora) use ($idNegocio, $fecha) {
                $row = \Illuminate\Support\Facades\DB::table('ventas')
                    ->where('id_negocio', $idNegocio)
                    ->whereDate('created_at', $fecha)
                    ->whereRaw('HOUR(created_at) = ?', [$hora])
                    ->selectRaw('COUNT(*) as ventas, COALESCE(SUM(total),0) as ingresos')
                    ->first();
                return [
                    'label'    => str_pad($hora, 2, '0', STR_PAD_LEFT) . ':00',
                    'ventas'   => (int)   $row->ventas,
                    'ingresos' => (float) $row->ingresos,
                ];
            }),
            $idNegocio
        );
    }

    public static function getSucursalesPorHora(string $idNegocio, string $fecha): array
    {
        return self::remember(
            "dashboard:sucursales_hora:{$idNegocio}:{$fecha}",
            60,
            function () use ($idNegocio, $fecha) {
                $raw = DB::table('ventas as v')
                    ->join('usuarios as u', 'v.id_usuario', '=', 'u.id_usuario')
                    ->where('v.id_negocio', $idNegocio)
                    ->whereDate('v.created_at', $fecha)
                    ->selectRaw('u.nombre_usuario, HOUR(v.created_at) as hora, COUNT(*) as ventas, COALESCE(SUM(v.total),0) as ingresos')
                    ->groupBy('u.nombre_usuario', 'hora')
                    ->orderBy('hora')
                    ->get();

                $map = [];
                foreach ($raw as $row) {
                    $key = str_pad($row->hora, 2, '0', STR_PAD_LEFT) . ':00';
                    $map[$row->nombre_usuario][$key] = [
                        'ventas'   => (int)   $row->ventas,
                        'ingresos' => (float) $row->ingresos,
                    ];
                }
                return $map;
            },
            $idNegocio
        );
    }

    public static function getPersonalPorHora(string $idNegocio, string $fecha): array
    {
        return self::remember(
            "dashboard:personal_hora:{$idNegocio}:{$fecha}",
            60,
            function () use ($idNegocio, $fecha) {
                $raw = DB::table('ventas as v')
                    ->join('personal as p', 'v.id_personal', '=', 'p.id_personal')
                    ->where('v.id_negocio', $idNegocio)
                    ->whereDate('v.created_at', $fecha)
                    ->selectRaw('p.nombre, HOUR(v.created_at) as hora, COUNT(*) as ventas, COALESCE(SUM(v.total),0) as ingresos')
                    ->groupBy('p.id_personal', 'p.nombre', 'hora')
                    ->orderBy('hora')
                    ->get();

                $map = [];
                foreach ($raw as $row) {
                    $key = str_pad($row->hora, 2, '0', STR_PAD_LEFT) . ':00';
                    $map[$row->nombre][$key] = [
                        'ventas'   => (int)   $row->ventas,
                        'ingresos' => (float) $row->ingresos,
                    ];
                }
                return $map;
            },
            $idNegocio
        );
    }

    public static function getFilasHistoricas(string $idNegocio, string $desde, string $hasta): \Illuminate\Support\Collection
    {
        // Rango histórico puro (no incluye hoy) → TTL largo, casi nunca cambia
        return self::remember(
            "dashboard:filas_historicas:{$idNegocio}:{$desde}:{$hasta}",
            3600,
            fn() => \App\Models\EstadisticaDiaria::where('id_negocio', $idNegocio)
                ->whereBetween('fecha', [$desde, $hasta])
                ->orderBy('fecha')
                ->get()
                ->keyBy(fn($f) => $f->fecha->toDateString()),
            $idNegocio
        );
    }

    public static function getVentasHoy(string $idNegocio, ?string $idSucursal = null): int
    {
        $key = $idSucursal
            ? "dashboard:ventas_hoy:{$idNegocio}:{$idSucursal}:" . now()->toDateString()
            : "dashboard:ventas_hoy:{$idNegocio}:" . now()->toDateString();

        return self::remember(
            $key,
            30,
            fn() => (int) DB::table('ventas')
                ->where('id_negocio', $idNegocio)
                ->when($idSucursal, fn($q) => $q->where('id_usuario', $idSucursal))
                ->whereDate('created_at', now())
                ->count(),
            $idNegocio
        );
    }

    public static function getOtsActivas(string $idNegocio, ?string $idSucursal = null): array
    {
        $key = $idSucursal
            ? "dashboard:ots_activas:{$idNegocio}:{$idSucursal}"
            : "dashboard:ots_activas:{$idNegocio}";

        return self::remember(
            $key,
            60,
            fn() => \App\Models\Reparaciones::where('id_negocio', $idNegocio)
                ->when($idSucursal, fn($q) => $q->where('id_usuario', $idSucursal))
                ->whereIn('estado', ['recibida', 'diagnostico', 'cotizacion_enviada', 'en_proceso'])
                ->with('bicicleta.modelo')
                ->orderByDesc('created_at')
                ->limit(8)
                ->get()
                ->map(fn($ot) => [
                    'id'     => $ot->id_reparacion,
                    'tipo'   => $ot->tipo ?? '—',
                    'estado' => $ot->estado,
                    'modelo' => $ot->bicicleta?->modelo?->nombre_modelo ?? $ot->unidad_descripcion ?? '—',
                    'dias'   => $ot->created_at?->diffInDays(now()) ?? 0,
                ])
                ->toArray(),
            $idNegocio
        );
    }

    public static function getFeedVentasRecientes(string $idNegocio, int $limit = 15, ?string $idSucursal = null): \Illuminate\Support\Collection
    {
        $key = $idSucursal
            ? "dashboard:feed_ventas:{$idNegocio}:{$idSucursal}:limit{$limit}"
            : "dashboard:feed_ventas:{$idNegocio}:limit{$limit}";

        return self::remember(
            $key,
            30,
            fn() => \App\Models\Venta::with('usuario:id_usuario,nombre_usuario')
                ->where('id_negocio', $idNegocio)
                ->when($idSucursal, fn($q) => $q->where('id_usuario', $idSucursal))
                ->latest('created_at')
                ->limit($limit)
                ->get(['id_venta', 'id_usuario', 'total', 'created_at'])
                ->map(fn ($v) => [
                    'id_venta' => $v->id_venta,
                    'monto'    => (float) $v->total,
                    'sucursal' => $v->usuario->nombre_usuario ?? 'Sucursal',
                    'hora'     => $v->created_at->format('H:i'),
                    'fecha'    => $v->created_at->toIso8601String(),
                ]),
            $idNegocio
        );
    }

    public static function getTopModelosPorSucursal(string $idNegocio, string $desde, string $hasta, string $idSucursal): array
    {
        return self::remember(
            "dashboard:top_modelos_suc:{$idNegocio}:{$idSucursal}:{$desde}:{$hasta}",
            (($hasta >= now()->toDateString()) ? 300 : 3600),
            fn() => DB::table('detalle_venta as dv')
                ->join('ventas as v',    'dv.id_venta',  '=', 'v.id_venta')
                ->join('bicicletas as b','dv.num_serie',  '=', 'b.num_serie')
                ->join('modelos as m',   'b.id_modelo',   '=', 'm.id_modelo')
                ->where('v.id_negocio', $idNegocio)
                ->where('v.id_usuario', $idSucursal)
                ->whereBetween('v.created_at', ["{$desde} 00:00:00", "{$hasta} 23:59:59"])
                ->whereNotNull('dv.num_serie')
                ->selectRaw('m.id_modelo, m.nombre_modelo, COUNT(*) as unidades, COALESCE(SUM(dv.precio_unitario * dv.cantidad),0) as ingresos')
                ->groupBy('m.id_modelo', 'm.nombre_modelo')
                ->orderByDesc('unidades')
                ->limit(8)
                ->get()
                ->map(fn($r) => [
                    'id_modelo' => $r->id_modelo,
                    'nombre'    => $r->nombre_modelo,
                    'unidades'  => (int)   $r->unidades,
                    'ingresos'  => (float) $r->ingresos,
                ])
                ->toArray(),
            $idNegocio
        );
    }

    public static function getBicicletasSinMovimiento(string $idNegocio, int $diasUmbral = 45, ?string $idSucursal = null): array
    {
        $key = $idSucursal
            ? "dashboard:rotacion_inventario:{$idNegocio}:{$idSucursal}:{$diasUmbral}"
            : "dashboard:rotacion_inventario:{$idNegocio}:{$diasUmbral}";

        return self::remember(
            $key,
            3600,
            function () use ($idNegocio, $diasUmbral, $idSucursal) {
                $fechaLimite = now()->subDays($diasUmbral);

                $bicis = Bicicleta::where('id_negocio', $idNegocio)
                    ->where('status', 1)
                    ->when($idSucursal, fn($q) => $q->where('id_usuario', $idSucursal))
                    ->where('created_at', '<=', $fechaLimite)
                    ->with(['modelo', 'color', 'voltaje'])
                    ->orderBy('created_at')
                    ->get();

                return [
                    'total_estancadas' => $bicis->count(),
                    'dias_umbral'      => $diasUmbral,
                    'bicicletas'       => $bicis->map(fn($b) => [
                        'num_serie' => $b->num_serie,
                        'modelo'    => $b->modelo?->nombre_modelo ?? '—',
                        'color'     => $b->color?->color ?? '—',
                        'voltaje'   => $b->voltaje?->voltaje ?? '—',
                        'dias'      => $b->created_at->diffInDays(now()),
                    ])->toArray(),
                    'por_modelo' => $bicis->groupBy(fn($b) => $b->modelo?->nombre_modelo ?? 'Sin modelo')
                        ->map(fn($grupo, $nombre) => [
                            'modelo'        => $nombre,
                            'cantidad'      => $grupo->count(),
                            'dias_promedio' => round($grupo->avg(fn($b) => $b->created_at->diffInDays(now()))),
                        ])
                        ->sortByDesc('cantidad')
                        ->values()
                        ->toArray(),
                ];
            },
            $idNegocio
        );
    }

    public static function getMargenPorSucursal(string $idNegocio, string $desde, string $hasta): array
    {
        return self::remember(
            "dashboard:margen_sucursal:{$idNegocio}:{$desde}:{$hasta}",
            3600,
            function () use ($idNegocio, $desde, $hasta) {
                $ingresos = DB::table('ventas as v')
                    ->join('usuarios as u', 'v.id_usuario', '=', 'u.id_usuario')
                    ->where('v.id_negocio', $idNegocio)
                    ->whereBetween('v.created_at', ["{$desde} 00:00:00", "{$hasta} 23:59:59"])
                    ->selectRaw('u.id_usuario, u.nombre_usuario, SUM(v.total) as ingresos')
                    ->groupBy('u.id_usuario', 'u.nombre_usuario')
                    ->get();

                // TODO: enchufar aquí la query de gastos_sucursal cuando esté confirmada
                return $ingresos->map(fn($r) => [
                    'id_usuario'    => $r->id_usuario,
                    'nombre'        => $r->nombre_usuario,
                    'ingresos'      => (float) $r->ingresos,
                    'gastos'        => 0.0,
                    'margen'        => (float) $r->ingresos,
                    'margen_pct'    => 100.0,
                    'limite_gasto'  => null,
                    'excede_limite' => false,
                ])->sortByDesc('margen')->values()->toArray();
            },
            $idNegocio
        );
    }

    
}
