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
use App\Models\Inventario;
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
        'stats'      => 1800,
        'search'     => 3600,
        'productos'  => 3600,
    ];

    const CACHE_PREFIX = 'catalog:';

    // ─── VERSIÓN: GLOBAL Y POR TENANT ───────────────────────────────────────
    // Datos globales (sin negocio) usan version global.
    // Datos de tenant usan version:{idNegocio} → un bump solo afecta a ese negocio.

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

    /**
     * @param string        $key        Clave lógica del cache
     * @param int           $ttl        Segundos de vida
     * @param callable      $callback   Query a ejecutar en caso de miss
     * @param string|null   $idNegocio  Si se pasa → versión por tenant; null → versión global
     */
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
            $idNegocio  // ← versión por tenant
        );

        return $withSelectFormato ? $marcas->pluck('nombre_marca', 'id_marca') : $marcas;
    }

    public static function getMarcasPublicas(bool $withSelectFormato = false)
    {
        // Sin negocio → versión global
        $marcas = self::remember('marcas:publicas', self::CACHE_TTL['marcas'], fn () =>
            Marca::whereNull('id_negocio')
                ->select('id_marca', 'nombre_marca')
                ->orderBy('nombre_marca')
                ->get()
        );

        return $withSelectFormato ? $marcas->pluck('nombre_marca', 'id_marca') : $marcas;
    }

    public static function getMarcaById(string $idMarca): ?Marca
    {
        // Sin negocio en firma → versión global; invalidateMarca hace forget explícito
        return self::remember("marca:{$idMarca}", self::CACHE_TTL['marcas'],
            fn () => Marca::with('negocio')->find($idMarca)
        );
    }

    public static function invalidateMarca(string $idMarca, ?string $idNegocio = null): void
    {
        $version = self::getVersion($idNegocio);
        Cache::forget(self::key("marca:{$idMarca}") . ":v" . self::getVersion()); // global key
        Cache::forget(self::key("marca:{$idMarca}") . ":v{$version}");

        if ($idNegocio) {
            Cache::forget(self::key("marcas:negocio:{$idNegocio}") . ":v{$version}");
            Cache::forget(self::key("modelos:negocio:{$idNegocio}") . ":v{$version}");
        } else {
            Cache::forget(self::key('marcas:publicas') . ":v" . self::getVersion());
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
        // Públicos → versión global
        $modelos = self::remember('modelos:publicos', self::CACHE_TTL['modelos'],
            fn () => Modelo::whereNull('id_negocio')
                ->select('id_modelo', 'nombre_modelo')
                ->orderBy('nombre_modelo')
                ->get()
        );

        return $withSelectFormato ? $modelos->pluck('nombre_modelo', 'id_modelo') : $modelos;
    }

    public static function getModeloById(string $idModelo): ?Modelo
    {
        // Sin negocio en firma → versión global; invalidateModelo hace forget explícito
        return self::remember("modelo:{$idModelo}", self::CACHE_TTL['modelos'],
            fn () => Modelo::with(['marca', 'negocio'])->find($idModelo)
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
            $idNegocio  // null si es público → versión global
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

    public static function getVoltajeById(string $id): ?Voltaje
    {
        return self::remember("voltaje:{$id}", self::CACHE_TTL['voltajes'],
            fn () => Voltaje::find($id)
        );
    }

    /**
     * ✅ CORREGIDO: bump por tenant → solo afecta al negocio que editó el voltaje.
     * El voltaje vive embebido en bicicletas, secciones, productos, etc.
     * No es posible invalidar quirúrgicamente todas esas caches sin conocer
     * cada num_serie afectada, por eso se hace bump de versión del tenant.
     */
    public static function invalidateVoltaje(string $idVoltaje, ?string $idNegocio = null): void
    {
        if ($idNegocio) {
            // Dato de tenant → bump solo de ese negocio
            self::incrementVersion($idNegocio);
        } else {
            // Dato público → bump global
            self::incrementVersion();
        }
    }

    public static function invalidateVoltajes(): void
    {
        // Para datos públicos globales
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

    public static function getColorById(string $id): ?Color
    {
        return self::remember("color:{$id}", self::CACHE_TTL['colores'],
            fn () => Color::find($id)
        );
    }

    /**
     * ✅ CORREGIDO: bump por tenant → igual que voltaje.
     * El color vive embebido en decenas de caches (bicicletas, secciones, productos, catálogo).
     */
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
        // Meta-dato global → versión global
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
        // Paginación directa sin cache (se usa solo en rol 5)
        return Bicicleta::where('id_negocio', $idNegocio)
            ->with(['modelo', 'voltaje', 'color'])
            ->orderByDesc('updated_at')
            ->paginate(10);
    }

    /**
     * @param string|null $idNegocio  Pasar siempre que esté disponible para usar versión por tenant.
     */
    public static function getBicicletaBySerie(string $numSerie, ?string $idNegocio = null): ?Bicicleta
    {
        return self::remember(
            "bicicleta:serie:{$numSerie}",
            self::CACHE_TTL['bicicletas'],
            fn () => Bicicleta::with(['modelo', 'voltaje', 'color'])->where('num_serie', $numSerie)->first(),
            $idNegocio  // ← tenant version cuando esté disponible
        );
    }

    public static function getBicicletaStats(string $idNegocio): array
    {
        return self::remember(
            "stats:bicicletas:{$idNegocio}",
            self::CACHE_TTL['stats'],
            function () use ($idNegocio) {
                $stats = Bicicleta::where('id_negocio', $idNegocio)
                    ->selectRaw("COUNT(*) as total, SUM(status = 1) as en_stock, SUM(status = 2) as vendidas, SUM(status = 3) as en_reparacion")
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

    public static function getBicicletasByCliente(string $idCliente)
    {
        // Sin negocio en firma → versión global
        return self::remember("bicicletas:cliente:{$idCliente}", 300,
            fn () => Bicicleta::with(['modelo', 'color'])
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

                $sinAsignar = $totales->get(null,
                    Bicicleta::where('id_negocio', $idNegocio)->whereNull('id_usuario')->count()
                );

                $resultado = [];
                foreach ($vendedores as $vendedor) {
                    $resultado[] = [
                        'vendedor'   => $vendedor,
                        'total'      => (int) ($totales[$vendedor->id_usuario] ?? 0),
                        'bicicletas' => null,
                    ];
                }
                $resultado[] = ['vendedor' => null, 'total' => (int) $sinAsignar, 'bicicletas' => null];

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

    public static function getPedidoById(string $idPedido): ?Pedido
    {
        // Sin negocio en firma → versión global; invalidatePedido hace forget explícito
        return self::remember("pedido:{$idPedido}", self::CACHE_TTL['pedidos'],
            fn () => Pedido::with(['usuario', 'negocio', 'items.modelo', 'items.voltaje', 'items.color', 'bicicletas'])->find($idPedido)
        );
    }

    public static function getPedidosRecientesByNegocio(string $idNegocio, int $limit = 10): array
    {
        $ids = self::remember(
            "pedidos:recientes:negocio:{$idNegocio}:limit{$limit}",
            self::CACHE_TTL['pedidos'],
            fn () => Pedido::where('id_negocio', $idNegocio)
                ->orderByDesc('created_at')
                ->limit($limit)
                ->pluck('id_pedido')
                ->toArray(),
            $idNegocio
        );

        return array_map(fn ($id) => self::getPedidoById($id), $ids);
    }

    public static function getPedidoStats(string $idNegocio): array
    {
        return self::remember(
            "stats:pedidos:negocio:{$idNegocio}",
            self::CACHE_TTL['stats'],
            fn () => [
                'pendientes'  => Pedido::where('id_negocio', $idNegocio)->where('status', 1)->count(),
                'en_proceso'  => Pedido::where('id_negocio', $idNegocio)->where('status', 2)->count(),
                'completados' => Pedido::where('id_negocio', $idNegocio)->where('status', 3)->count(),
                'total'       => Pedido::where('id_negocio', $idNegocio)->count(),
            ],
            $idNegocio
        );
    }

    public static function invalidatePedido(string $idPedido, string $idNegocio): void
    {
        $globalV = self::getVersion();      // pedido:{id} usa versión global
        $tenantV = self::getVersion($idNegocio);

        Cache::forget(self::key("pedido:{$idPedido}") . ":v{$globalV}");
        Cache::forget(self::key("stats:pedidos:negocio:{$idNegocio}") . ":v{$tenantV}");

        for ($limit = 5; $limit <= 20; $limit += 5) {
            Cache::forget(self::key("pedidos:recientes:negocio:{$idNegocio}:limit{$limit}") . ":v{$tenantV}");
        }
    }

    // ─── USUARIOS ───────────────────────────────────────────────────────────

    public static function getUserById(string $idUsuario): ?Usuario
    {
        return self::remember("usuario:{$idUsuario}", self::CACHE_TTL['usuarios'],
            fn () => Usuario::find($idUsuario)
        );
    }

    public static function getUserWithNegocio(string $idUsuario): ?Usuario
    {
        return self::remember("usuario:negocio:{$idUsuario}", self::CACHE_TTL['usuarios'],
            fn () => Usuario::with('negocio')->find($idUsuario)
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
        // Bump completo del tenant → limpia todo lo de ese negocio de una vez
        self::incrementVersion($idNegocio);

        // Limpia también claves globales relacionadas al negocio
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
        ]);
        // versión global (sin idNegocio)
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
        // Global → versión global
        $productos = self::remember('productos', self::CACHE_TTL['productos'],
            fn () => Producto::with('negocio')->orderBy('nombre_producto')->get()
        );

        return $withSelectFormato ? $productos->pluck('nombre_producto', 'id_producto') : $productos;
    }

    public static function getProductoById(string $idProducto): ?Producto
    {
        return self::remember("producto:{$idProducto}", self::CACHE_TTL['productos'],
            fn () => Producto::with('negocio')->find($idProducto)
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

    /**
     * Limpia todo el cache de un tenant específico o el global si no se pasa negocio.
     */
    public static function clearCache(?string $idNegocio = null): void
    {
        self::incrementVersion($idNegocio);
    }

    // ─── INVENTARIO ─────────────────────────────────────────────────────────

    public static function invalidateInventario(string $idNegocio, ?string $idUsuario = null): void
    {
        $version = self::getVersion($idNegocio);

        // Cache general del admin
        Cache::forget(self::key("inventario:negocio:{$idNegocio}") . ":v{$version}");
        Cache::forget(self::key("inventario:sucursales:{$idNegocio}") . ":v{$version}");

        // ✅ Cache específico de la sucursal — este era el que nunca se limpiaba
        if ($idUsuario) {
            Cache::forget(self::key("inventario:sucursal:{$idNegocio}:{$idUsuario}") . ":v{$version}");
        }
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

    public static function invalidateColoresEnStock(string $idNegocio, ?string $idUsuario = null): void
    {
        $version = self::getVersion($idNegocio);
        Cache::forget(self::key("colores:stock:negocio:{$idNegocio}") . ":v{$version}");
        if ($idUsuario) {
            Cache::forget(self::key("colores:stock:negocio:{$idNegocio}:usuario:{$idUsuario}") . ":v{$version}");
        }
    }

    public static function invalidateSucursales(string $idNegocio): void
    {
        $version = self::getVersion($idNegocio);
        Cache::forget(self::key("sucursales:negocio:{$idNegocio}") . ":v{$version}");
    }
}