<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EstadisticaDiaria;
use Illuminate\Http\Request;
use App\Services\CatalogService;
use App\Models\Enlace;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    // ─── SWITCH PARA MOCK (cambia a true para ver datos falsos) ──────────
    private $usarMock = true;   // ← Pon true para pruebas, false para reales

    // ─── VISTA PRINCIPAL ─────────────────────────────────────────────────────
    public function index(Request $request)
    {
        $user      = auth()->user();
        $idNegocio = $user->id_negocio;

        $biciStats       = CatalogService::getBicicletaStats($idNegocio);
        $stockVendedores = collect(CatalogService::getStockPorVendedores($idNegocio))
            ->map(fn($v) => [
                'nombre' => $v['vendedor']?->nombre_usuario ?? 'Sin asignar',
                'total'  => $v['total'],
            ]);

        $piezasBajas = CatalogService::getPiezasPaginadas(
            idNegocio: $idNegocio,
            soloStockBajo: true
        )->getCollection()->map(fn($p) => [
            'id_pieza' => $p->id_pieza,
            'nombre'   => $p->nombre,
            'stock'    => $p->stock_actual,
            'minimo'   => $p->stock_minimo,
            'clave'    => $p->clave,
            'cat'      => $p->categoria ?? '—',
        ]);

        $personal = collect(CatalogService::getPersonalByNegocio($idNegocio))
            ->map(fn($p) => [
                'nombre'   => $p['nombre'],
                'sucursal' => $p['sucursales'][0]['nombre_usuario'] ?? '—',
                'activo'   => $p['activo'],
            ]);

        $catalogo = (function () {
            $g = CatalogService::getGlobalStats();
            return [
                ['label' => 'Marcas',   'count' => $g['total_marcas']],
                ['label' => 'Modelos',  'count' => $g['total_modelos']],
                ['label' => 'Colores',  'count' => $g['total_colores']],
                ['label' => 'Voltajes', 'count' => $g['total_voltajes']],
            ];
        })();

        $enlace = Enlace::where('id_usuario1', $user->id_usuario)
            ->with('usuarioDestino:id_usuario,nombre_usuario,correo')
            ->orderBy('created_at', 'desc')
            ->first();

        return view('administrador.dashboard', compact(
            'biciStats',
            'stockVendedores',
            'piezasBajas',
            'personal',
            'catalogo',
            'enlace',
        ));
    }

    // ─── STATS (AJAX) — con soporte para mock ──────────────────────────────
    public function stats(Request $request)
    {
        if ($this->usarMock) {
            return $this->mockStats($request);
        }

        // ─── CÓDIGO REAL (sin cambios) ──────────────────────────────────────
        $user      = auth()->user();
        $idNegocio = $user->id_negocio;

        [$desde, $hasta] = $this->resolvePeriod($request);
        $diasPeriodo     = max(1, $desde->diffInDays($hasta) + 1);

        $cached = CatalogService::getDashboardStats(
            $idNegocio,
            $desde->toDateString(),
            $hasta->toDateString(),
        );

        $filas = $cached
            ? collect($cached['filas'])->map(fn($f) => (object) $f)
            : collect();

        $ventasCount     = $filas->sum('ventas_count');
        $ingresosTotal   = (float) $filas->sum('ingresos_total');
        $descuentosTotal = (float) $filas->sum('descuentos_total');
        $ticketPromedio  = $ventasCount > 0 ? round($ingresosTotal / $ventasCount, 2) : 0;
        $clientesNuevos  = $filas->sum('clientes_nuevos');
        $clientesRec     = $filas->sum('clientes_rec');
        $cuponesUsados   = $filas->sum('cupones_usados');
        $cuponesDescuento= (float) $filas->sum('cupones_descuento');

        $modeloTop = $filas
            ->whereNotNull('modelo_top_nombre')
            ->groupBy('modelo_top_nombre')
            ->map(fn($g) => ['nombre' => $g->first()->modelo_top_nombre, 'unidades' => $g->sum('modelo_top_unidades')])
            ->sortByDesc('unidades')
            ->first();

        $configTop = $filas
            ->whereNotNull('config_top')
            ->groupBy('config_top')
            ->map(fn($g) => ['config' => $g->first()->config_top, 'unidades' => $g->sum('config_top_unidades')])
            ->sortByDesc('unidades')
            ->first();

        $accesorioTop = $filas
            ->whereNotNull('accesorio_top_nombre')
            ->groupBy('accesorio_top_nombre')
            ->map(fn($g) => ['nombre' => $g->first()->accesorio_top_nombre, 'uds' => $g->sum('accesorio_top_uds')])
            ->sortByDesc('uds')
            ->first();

        $comboTop = $filas
            ->whereNotNull('combo_top')
            ->groupBy('combo_top')
            ->map(fn($g) => ['combo' => $g->first()->combo_top, 'uds' => $g->sum('combo_top_uds')])
            ->sortByDesc('uds')
            ->first();

        $cuponTop = $filas
            ->whereNotNull('cupon_top_codigo')
            ->groupBy('cupon_top_codigo')
            ->map(fn($g) => ['codigo' => $g->first()->cupon_top_codigo, 'usos' => $g->sum('cupon_top_usos')])
            ->sortByDesc('usos')
            ->first();

        $metodosPago = $filas
            ->flatMap(fn($f) => $f->metodos_pago ?? [])
            ->groupBy('metodo')
            ->map(fn($g, $metodo) => [
                'metodo' => $metodo,
                'usos'   => $g->sum('usos'),
                'monto'  => (float) $g->sum('monto'),
            ])
            ->sortByDesc('monto')
            ->values();

        $horasPico = $filas
            ->flatMap(fn($f) => $f->horas_pico ?? [])
            ->groupBy('hora')
            ->map(fn($g, $hora) => ['hora' => (int)$hora, 'cnt' => $g->sum('cnt')])
            ->sortBy('hora')
            ->values();

        $sucursalesAgregadas = $filas
            ->flatMap(fn($f) => $f->sucursal_data ?? [])
            ->groupBy('id_usuario')
            ->map(function ($g, $idUsuario) {
                $ventasSuc   = $g->sum('ventas_count');
                $ingresosSuc = (float) $g->sum('ingresos_total');
                return [
                    'id_usuario'          => $idUsuario,
                    'nombre'              => $g->first()['nombre'],
                    'ventas_count'        => $ventasSuc,
                    'ingresos_total'      => $ingresosSuc,
                    'ticket_promedio'     => $ventasSuc > 0 ? round($ingresosSuc / $ventasSuc, 2) : 0,
                    'unidades_bicis'      => $g->sum('unidades_bicis'),
                    'unidades_accesorios' => $g->sum('unidades_accesorios'),
                    'descuentos_total'    => (float) $g->sum('descuentos_total'),
                    'aporte_pct'          => 0,
                ];
            })
            ->values();

        $sucursalesAgregadas = $sucursalesAgregadas->map(function ($s) use ($ventasCount) {
            $s['aporte_pct'] = $ventasCount > 0
                ? round($s['ventas_count'] / $ventasCount * 100, 1)
                : 0;
            return $s;
        })->sortByDesc('ingresos_total')->values();

        if ($diasPeriodo === 1) {
            $fechas = collect(range(0, 23))
                ->map(fn($h) => str_pad($h, 2, '0', STR_PAD_LEFT) . ':00')
                ->toArray();

            $graficaDias = collect(range(0, 23))->map(function ($hora) use ($idNegocio, $desde) {
                $row = DB::table('ventas')
                    ->where('id_negocio', $idNegocio)
                    ->whereDate('created_at', $desde)
                    ->whereRaw('HOUR(created_at) = ?', [$hora])
                    ->selectRaw('COUNT(*) as ventas, COALESCE(SUM(total),0) as ingresos')
                    ->first();
                return [
                    'label'    => str_pad($hora, 2, '0', STR_PAD_LEFT) . ':00',
                    'ventas'   => (int)   $row->ventas,
                    'ingresos' => (float) $row->ingresos,
                ];
            });

            $ayer = $desde->copy()->subDay();
            $graficaAnterior = collect(range(0, 23))->map(function ($hora) use ($idNegocio, $ayer) {
                $row = DB::table('ventas')
                    ->where('id_negocio', $idNegocio)
                    ->whereDate('created_at', $ayer)
                    ->whereRaw('HOUR(created_at) = ?', [$hora])
                    ->selectRaw('COUNT(*) as ventas, COALESCE(SUM(total),0) as ingresos')
                    ->first();
                return [
                    'ventas'   => (int)   $row->ventas,
                    'ingresos' => (float) $row->ingresos,
                ];
            });

            $graficaSucursales = $this->sucursalesPorHora($idNegocio, $desde, $fechas);
            $ventasPersonal    = $this->personalPorHora($idNegocio, $desde, $fechas);

        } else {
            $fechas = collect(range(0, $diasPeriodo - 1))
                ->map(fn($i) => $desde->copy()->addDays($i)->toDateString())
                ->toArray();

            $fmt        = $diasPeriodo <= 31 ? 'd/m' : 'M y';
            $filasIndex = $filas->keyBy(fn($f) => $f->fecha->toDateString());

            $graficaDias = collect($fechas)->map(function ($fecha) use ($filasIndex, $fmt) {
                $fila = $filasIndex[$fecha] ?? null;
                return [
                    'label'    => Carbon::parse($fecha)->translatedFormat($fmt),
                    'ventas'   => (int)   ($fila?->ventas_count   ?? 0),
                    'ingresos' => (float) ($fila?->ingresos_total ?? 0),
                ];
            });

            $desdeAnt   = $desde->copy()->subDays($diasPeriodo);
            $hastaAnt   = $hasta->copy()->subDays($diasPeriodo);
            $filasAnt   = EstadisticaDiaria::where('id_negocio', $idNegocio)
                ->whereBetween('fecha', [$desdeAnt->toDateString(), $hastaAnt->toDateString()])
                ->orderBy('fecha')
                ->get()
                ->keyBy(fn($f) => $f->fecha->toDateString());

            $fechasAnt = collect(range(0, $diasPeriodo - 1))
                ->map(fn($i) => $desdeAnt->copy()->addDays($i)->toDateString());

            $graficaAnterior = $fechasAnt->map(function ($fecha) use ($filasAnt) {
                $fila = $filasAnt[$fecha] ?? null;
                return [
                    'ventas'   => (int)   ($fila?->ventas_count   ?? 0),
                    'ingresos' => (float) ($fila?->ingresos_total ?? 0),
                ];
            });

            $graficaSucursales = $this->sucursalesPorDia($filas, $fechas, $fmt);
            $ventasPersonal    = $this->personalPorDia($idNegocio, $desde, $hasta, $fechas, $fmt);
        }

        $pedidoStats      = CatalogService::getPedidoStats($idNegocio);
        $pedidosRecientes = CatalogService::getPedidosRecientesByNegocio($idNegocio, 6)
            ->map(fn($p) => [
                'id'      => $p->id_pedido,
                'estado'  => match ((int)$p->status) {
                    1 => 'pendiente',
                    2 => 'proceso',
                    default => 'completado'
                },
                'monto'   => (float)($p->total ?? 0),
                'cliente' => $p->usuario?->nombre_usuario ?? 'Sin cliente',
                'items'   => $p->items?->count() ?? 0,
                'fecha'   => $p->created_at?->diffForHumans() ?? '—',
            ]);

        $otsActivas = $this->getOtsActivas($idNegocio);

        $ventasHoy = (int) DB::table('ventas')
            ->where('id_negocio', $idNegocio)
            ->whereDate('created_at', Carbon::today())
            ->count();

        return response()->json([
            'ventas_personal'    => $ventasPersonal,
            'periodo' => [
                'desde' => $desde->toDateString(),
                'hasta' => $hasta->toDateString(),
                'dias'  => $diasPeriodo,
            ],
            'kpi' => [
                'ventas'           => $ventasCount,
                'ingresos'         => $ingresosTotal,
                'ticket'           => $ticketPromedio,
                'clientes'         => $clientesNuevos + $clientesRec,
                'clientes_nuevos'  => $clientesNuevos,
                'clientes_rec'     => $clientesRec,
                'ots_activas'      => count($otsActivas),
                'ventas_hoy'       => $ventasHoy,
                'descuentos'       => $descuentosTotal,
                'cupones_usados'   => $cuponesUsados,
                'cupones_descuento'=> $cuponesDescuento,
            ],
            'tops' => [
                'modelo'    => $modeloTop,
                'config'    => $configTop,
                'accesorio' => $accesorioTop,
                'combo'     => $comboTop,
                'cupon'     => $cuponTop,
            ],
            'grafica'            => $graficaDias,
            'grafica_anterior'   => $graficaAnterior,
            'grafica_sucursales' => $graficaSucursales,
            'sucursales'         => $sucursalesAgregadas,
            'pedidos'            => ['recientes' => $pedidosRecientes, 'stats' => $pedidoStats],
            'ots'                => $otsActivas,
            'metodos_pago'       => $metodosPago,
            'horas_pico'         => $horasPico,
            'clientes_tipo'      => [
                ['tipo' => 'Nuevos',      'cnt' => $clientesNuevos],
                ['tipo' => 'Recurrentes', 'cnt' => $clientesRec],
            ],
        ]);
    }

    // ─── DETALLE (AJAX) — con soporte para mock ──────────────────────────
    public function detalle(Request $request)
    {
        if ($this->usarMock) {
            return $this->mockDetalle($request);
        }

        // ─── CÓDIGO REAL ──────────────────────────────────────────────────────
        $user      = auth()->user();
        $idNegocio = $user->id_negocio;
        $tipo      = $request->input('tipo');

        [$desde, $hasta] = $this->resolvePeriod($request);
        $desdeTs = $desde->copy()->startOfDay();
        $hastaTs = $hasta->copy()->endOfDay();

        $data = match ($tipo) {

            'top_modelos' => DB::table('detalle_venta as dv')
                ->join('ventas as v',    'dv.id_venta',  '=', 'v.id_venta')
                ->join('bicicletas as b','dv.num_serie',  '=', 'b.num_serie')
                ->join('modelos as m',   'b.id_modelo',   '=', 'm.id_modelo')
                ->where('v.id_negocio', $idNegocio)
                ->whereBetween('v.created_at', [$desdeTs, $hastaTs])
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
                ]),

            'detalle_modelo' => (function () use ($request, $idNegocio, $desdeTs, $hastaTs) {
                $idModelo = $request->input('id_modelo');

                $colores = DB::table('detalle_venta as dv')
                    ->join('ventas as v',    'dv.id_venta', '=', 'v.id_venta')
                    ->join('bicicletas as b','dv.num_serie', '=', 'b.num_serie')
                    ->join('colores as c',   'b.id_color',  '=', 'c.id_color')
                    ->where('v.id_negocio', $idNegocio)
                    ->whereBetween('v.created_at', [$desdeTs, $hastaTs])
                    ->where('b.id_modelo', $idModelo)
                    ->selectRaw('c.color, COUNT(*) as cnt')
                    ->groupBy('c.color')
                    ->orderByDesc('cnt')
                    ->get()
                    ->map(fn($r) => ['color' => $r->color, 'cnt' => (int)$r->cnt]);

                $voltajes = DB::table('detalle_venta as dv')
                    ->join('ventas as v',    'dv.id_venta',  '=', 'v.id_venta')
                    ->join('bicicletas as b','dv.num_serie',  '=', 'b.num_serie')
                    ->join('voltajes as vt', 'b.id_voltaje', '=', 'vt.id_voltaje')
                    ->where('v.id_negocio', $idNegocio)
                    ->whereBetween('v.created_at', [$desdeTs, $hastaTs])
                    ->where('b.id_modelo', $idModelo)
                    ->selectRaw('vt.voltaje, COUNT(*) as cnt')
                    ->groupBy('vt.voltaje')
                    ->orderByDesc('cnt')
                    ->get()
                    ->map(fn($r) => ['voltaje' => $r->voltaje, 'cnt' => (int)$r->cnt]);

                return ['colores' => $colores, 'voltajes' => $voltajes];
            })(),

            'top_accesorios' => DB::table('detalle_venta as dv')
                ->join('ventas as v',   'dv.id_venta',   '=', 'v.id_venta')
                ->join('productos as p','dv.id_producto', '=', 'p.id_producto')
                ->where('v.id_negocio', $idNegocio)
                ->whereBetween('v.created_at', [$desdeTs, $hastaTs])
                ->whereNull('dv.num_serie')
                ->where('p.tipo', '1')
                ->selectRaw('p.id_producto, p.nombre_producto, SUM(dv.cantidad) as unidades, COALESCE(SUM(dv.precio_unitario * dv.cantidad),0) as ingresos')
                ->groupBy('p.id_producto', 'p.nombre_producto')
                ->orderByDesc('unidades')
                ->limit(8)
                ->get()
                ->map(fn($r) => [
                    'nombre'   => $r->nombre_producto,
                    'unidades' => (int)   $r->unidades,
                    'ingresos' => (float) $r->ingresos,
                ]),

            'combos' => DB::table('detalle_venta as dv1')
                ->join('detalle_venta as dv2', 'dv1.id_venta', '=', 'dv2.id_venta')
                ->join('ventas as v',          'dv1.id_venta', '=', 'v.id_venta')
                ->join('bicicletas as b',      'dv1.num_serie','=', 'b.num_serie')
                ->join('modelos as m',         'b.id_modelo',  '=', 'm.id_modelo')
                ->join('productos as p',       'dv2.id_producto','=','p.id_producto')
                ->where('v.id_negocio', $idNegocio)
                ->whereBetween('v.created_at', [$desdeTs, $hastaTs])
                ->whereNotNull('dv1.num_serie')
                ->whereNull('dv2.num_serie')
                ->where('p.tipo', '1')
                ->selectRaw("CONCAT(m.nombre_modelo, ' + ', p.nombre_producto) as combo, COUNT(*) as uds")
                ->groupBy('combo')
                ->orderByDesc('uds')
                ->limit(8)
                ->get()
                ->map(fn($r) => ['combo' => $r->combo, 'uds' => (int)$r->uds]),

            'clientes' => (function () use ($idNegocio, $desdeTs, $hastaTs) {
                $top = DB::table('ventas as v')
                    ->join('clientes as c', 'v.id_cliente', '=', 'c.id_cliente')
                    ->where('v.id_negocio', $idNegocio)
                    ->whereBetween('v.created_at', [$desdeTs, $hastaTs])
                    ->selectRaw('c.nombre_cliente, c.apellido1, COUNT(*) as compras, SUM(v.total) as total_gastado')
                    ->groupBy('c.id_cliente', 'c.nombre_cliente', 'c.apellido1')
                    ->orderByDesc('total_gastado')
                    ->limit(8)
                    ->get()
                    ->map(fn($r) => [
                        'nombre'        => $r->nombre_cliente . ' ' . $r->apellido1,
                        'compras'       => (int)   $r->compras,
                        'total_gastado' => (float) $r->total_gastado,
                    ]);
                return ['top_clientes' => $top];
            })(),

            'cupones' => DB::table('cupon_usos as cu')
                ->join('cupones as c', 'cu.id_cupon', '=', 'c.id_cupon')
                ->where('cu.id_negocio', $idNegocio)
                ->whereBetween('cu.created_at', [$desdeTs, $hastaTs])
                ->selectRaw('c.codigo, c.nombre, COUNT(*) as usos, SUM(cu.descuento_aplicado) as total_descuento')
                ->groupBy('c.id_cupon', 'c.codigo', 'c.nombre')
                ->orderByDesc('usos')
                ->get()
                ->map(fn($r) => [
                    'codigo'          => $r->codigo,
                    'nombre'          => $r->nombre,
                    'usos'            => (int)   $r->usos,
                    'total_descuento' => (float) $r->total_descuento,
                ]),

            'vendedor_detalle' => (function () use ($request, $idNegocio, $desdeTs, $hastaTs) {
                $idUsuario = $request->input('id_usuario');
                return DB::table('ventas as v')
                    ->join('personal as p', 'v.id_personal', '=', 'p.id_personal')
                    ->where('v.id_negocio', $idNegocio)
                    ->where('v.id_usuario', $idUsuario)
                    ->whereBetween('v.created_at', [$desdeTs, $hastaTs])
                    ->selectRaw('p.nombre, COUNT(*) as ventas, SUM(v.total) as ingresos')
                    ->groupBy('p.id_personal', 'p.nombre')
                    ->orderByDesc('ingresos')
                    ->get()
                    ->map(fn($r) => [
                        'nombre'   => $r->nombre,
                        'ventas'   => (int)   $r->ventas,
                        'ingresos' => (float) $r->ingresos,
                    ]);
            })(),

            default => [],
        };

        return response()->json(['tipo' => $tipo, 'data' => $data]);
    }

    // ─── HELPERS PRIVADOS (sin cambios) ─────────────────────────────────────

    private function sucursalesPorDia($filas, array $fechas, string $fmt): \Illuminate\Support\Collection
    {
        $vendedoresMap = [];

        foreach ($filas as $fila) {
            $fechaStr = $fila->fecha->toDateString();
            foreach ($fila->sucursal_data ?? [] as $suc) {
                $nombre = $suc['nombre'];
                $vendedoresMap[$nombre][$fechaStr] = [
                    'ventas'   => $suc['ventas_count'],
                    'ingresos' => $suc['ingresos_total'],
                ];
            }
        }

        return collect($vendedoresMap)->map(fn($diasData, $nombre) => [
            'nombre'   => $nombre,
            'ventas'   => array_map(fn($f) => $diasData[$f]['ventas']   ?? 0, $fechas),
            'ingresos' => array_map(fn($f) => $diasData[$f]['ingresos'] ?? 0, $fechas),
        ])->values();
    }

    private function sucursalesPorHora(string $idNegocio, Carbon $desde, array $fechas): \Illuminate\Support\Collection
    {
        $raw = DB::table('ventas as v')
            ->join('usuarios as u', 'v.id_usuario', '=', 'u.id_usuario')
            ->where('v.id_negocio', $idNegocio)
            ->whereDate('v.created_at', $desde)
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

        return collect($map)->map(fn($diasData, $nombre) => [
            'nombre'   => $nombre,
            'ventas'   => array_map(fn($f) => $diasData[$f]['ventas']   ?? 0, $fechas),
            'ingresos' => array_map(fn($f) => $diasData[$f]['ingresos'] ?? 0, $fechas),
        ])->values();
    }

    private function personalPorDia(string $idNegocio, Carbon $desde, Carbon $hasta, array $fechas, string $fmt): \Illuminate\Support\Collection
    {
        $raw = DB::table('ventas as v')
            ->join('personal as p', 'v.id_personal', '=', 'p.id_personal')
            ->where('v.id_negocio', $idNegocio)
            ->whereBetween('v.created_at', [$desde->copy()->startOfDay(), $hasta->copy()->endOfDay()])
            ->selectRaw('p.nombre, DATE(v.created_at) as dia, COUNT(*) as ventas, COALESCE(SUM(v.total),0) as ingresos')
            ->groupBy('p.id_personal', 'p.nombre', 'dia')
            ->orderBy('dia')
            ->get();

        $map = [];
        foreach ($raw as $row) {
            $map[$row->nombre][$row->dia] = [
                'ventas'   => (int)   $row->ventas,
                'ingresos' => (float) $row->ingresos,
            ];
        }

        return collect($map)->map(fn($diasData, $nombre) => [
            'nombre'   => $nombre,
            'ventas'   => array_map(fn($f) => $diasData[$f]['ventas']   ?? 0, $fechas),
            'ingresos' => array_map(fn($f) => $diasData[$f]['ingresos'] ?? 0, $fechas),
        ])->values();
    }

    private function personalPorHora(string $idNegocio, Carbon $desde, array $fechas): \Illuminate\Support\Collection
    {
        $raw = DB::table('ventas as v')
            ->join('personal as p', 'v.id_personal', '=', 'p.id_personal')
            ->where('v.id_negocio', $idNegocio)
            ->whereDate('v.created_at', $desde)
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

        return collect($map)->map(fn($diasData, $nombre) => [
            'nombre'   => $nombre,
            'ventas'   => array_map(fn($f) => $diasData[$f]['ventas']   ?? 0, $fechas),
            'ingresos' => array_map(fn($f) => $diasData[$f]['ingresos'] ?? 0, $fechas),
        ])->values();
    }

    private function resolvePeriod(Request $request): array
    {
        $hoy = Carbon::today();
        if ($request->filled('desde') && $request->filled('hasta')) {
            return [Carbon::parse($request->input('desde')), Carbon::parse($request->input('hasta'))];
        }
        return match ($request->input('periodo', '7d')) {
            'today' => [$hoy->copy(), $hoy->copy()],
            '7d'    => [$hoy->copy()->subDays(6), $hoy->copy()],
            '30d'   => [$hoy->copy()->subDays(29), $hoy->copy()],
            'month' => [$hoy->copy()->startOfMonth(), $hoy->copy()->endOfMonth()],
            default => [$hoy->copy()->subDays(6), $hoy->copy()],
        };
    }

    private function getOtsActivas(string $idNegocio): array
    {
        if (!class_exists(\App\Models\OrdenTrabajo::class)) return [];
        return \App\Models\OrdenTrabajo::where('id_negocio', $idNegocio)
            ->whereIn('estatus', ['pendiente', 'en_proceso', 'diagnostico'])
            ->with('bicicleta.modelo')
            ->orderByDesc('created_at')
            ->limit(8)
            ->get()
            ->map(fn($ot) => [
                'id'     => $ot->id_ot ?? $ot->getKey(),
                'tipo'   => $ot->tipo_servicio ?? $ot->descripcion ?? '—',
                'estado' => $ot->estatus ?? 'pendiente',
                'modelo' => $ot->bicicleta?->modelo?->nombre_modelo ?? '—',
                'dias'   => $ot->created_at ? (int)$ot->created_at->diffInDays(now()) : 0,
            ])
            ->toArray();
    }

    // ─── MÉTODOS DE MOCK ──────────────────────────────────────────────────────

    private function mockStats(Request $request)
    {
        // Definir días del periodo (30 días por defecto)
        $dias = 30;
        $hoy = Carbon::today();

        // ── Gráfica de días ──────────────────────────────────────────
        $labels = [];
        $ingresos = [];
        $ventas = [];
        for ($i = $dias - 1; $i >= 0; $i--) {
            $fecha = $hoy->copy()->subDays($i);
            $labels[] = $fecha->format('d/m');
            $ingresos[] = rand(20000, 120000);
            $ventas[] = rand(3, 18);
        }

        // ── Periodo anterior (mock) ──────────────────────────────────
        $graficaAnterior = array_map(fn() => [
            'ventas'   => rand(2, 16),
            'ingresos' => rand(15000, 100000),
        ], range(1, $dias));

        // ── Sucursales para gráfica ──────────────────────────────────
        $sucursalesNombres = ['CDMX Centro', 'Guadalajara', 'Monterrey', 'Querétaro', 'Puebla'];
        $graficaSucursales = [];
        foreach ($sucursalesNombres as $nombre) {
            $graficaSucursales[] = [
                'nombre'   => $nombre,
                'ventas'   => array_map(fn() => rand(1, 12), range(1, $dias)),
                'ingresos' => array_map(fn() => rand(10000, 60000), range(1, $dias)),
            ];
        }

        // ── Ventas por personal (para modal) ────────────────────────
        $ventasPersonal = [
            [
                'nombre'   => 'María López',
                'ventas'   => array_map(fn() => rand(1, 9), range(1, $dias)),
                'ingresos' => array_map(fn() => rand(5000, 40000), range(1, $dias)),
            ],
            [
                'nombre'   => 'Carlos Ruiz',
                'ventas'   => array_map(fn() => rand(1, 7), range(1, $dias)),
                'ingresos' => array_map(fn() => rand(4000, 30000), range(1, $dias)),
            ],
            [
                'nombre'   => 'Ana Torres',
                'ventas'   => array_map(fn() => rand(0, 5), range(1, $dias)),
                'ingresos' => array_map(fn() => rand(0, 20000), range(1, $dias)),
            ],
        ];

        // ── KPIs ──────────────────────────────────────────────────────
        $totalIngresos = array_sum($ingresos);
        $totalVentas = array_sum($ventas);
        $clientesNuevos = rand(5, 15);
        $clientesRec = rand(8, 25);
        $kpi = [
            'ventas'            => $totalVentas,
            'ingresos'          => $totalIngresos,
            'ticket'            => $totalVentas > 0 ? round($totalIngresos / $totalVentas) : 0,
            'clientes'          => $clientesNuevos + $clientesRec,
            'clientes_nuevos'   => $clientesNuevos,
            'clientes_rec'      => $clientesRec,
            'ots_activas'       => rand(2, 7),
            'ventas_hoy'        => rand(5, 20),
            'descuentos'        => rand(500, 8000),
            'cupones_usados'    => rand(1, 12),
            'cupones_descuento' => rand(300, 5000),
        ];

        // ── Tops ──────────────────────────────────────────────────────
        $tops = [
            'modelo'    => ['nombre' => 'Trek FX 3', 'unidades' => rand(6, 22)],
            'config'    => ['config' => 'Batería 48V 20Ah', 'unidades' => rand(4, 14)],
            'accesorio' => ['nombre' => 'Candado U-lock', 'unidades' => rand(5, 18)],
            'combo'     => ['combo' => 'Kit luces + candado', 'uds' => rand(3, 10)],
            'cupon'     => ['codigo' => 'BIENVENIDA10', 'usos' => rand(2, 8)],
        ];

        // ── Sucursales (lista) ──────────────────────────────────────
        $sucursales = [];
        foreach ($sucursalesNombres as $nombre) {
            $ing = rand(60000, 280000);
            $vent = rand(12, 45);
            $sucursales[] = [
                'id_usuario'          => rand(1, 20),
                'nombre'              => $nombre,
                'ventas_count'        => $vent,
                'ingresos_total'      => $ing,
                'ticket_promedio'     => $vent > 0 ? round($ing / $vent) : 0,
                'unidades_bicis'      => rand(5, 30),
                'unidades_accesorios' => rand(8, 55),
                'descuentos_total'    => rand(400, 7000),
                'aporte_pct'          => rand(5, 35),
            ];
        }

        // ── Pedidos recientes ────────────────────────────────────────
        $pedidosRecientes = [];
        $clientes = ['Juan Pérez', 'Ana García', 'Luis Martínez', 'Carla Torres', 'Roberto Cruz', 'Mónica Díaz'];
        $estados = ['pendiente', 'proceso', 'completado', 'pendiente', 'proceso'];
        for ($i = 0; $i < 8; $i++) {
            $pedidosRecientes[] = [
                'id'      => 'PED-' . str_pad($i+1, 4, '0', STR_PAD_LEFT),
                'estado'  => $estados[$i % count($estados)],
                'monto'   => rand(2000, 18000),
                'cliente' => $clientes[$i % count($clientes)],
                'items'   => rand(1, 5),
                'fecha'   => now()->subDays($i)->diffForHumans(),
            ];
        }

        // ── OTs activas ──────────────────────────────────────────────
        $otsActivas = [];
        $tiposOT = ['Mantenimiento', 'Reparación', 'Instalación', 'Diagnóstico'];
        $modelosOT = ['Trek FX 3', 'Giant Escape', 'Specialized Rockhopper'];
        for ($i = 0; $i < 4; $i++) {
            $otsActivas[] = [
                'id'     => 'OT-' . str_pad($i+1, 3, '0', STR_PAD_LEFT),
                'tipo'   => $tiposOT[$i % count($tiposOT)],
                'estado' => ['pendiente', 'en_proceso', 'diagnostico'][$i % 3],
                'modelo' => $modelosOT[$i % count($modelosOT)],
                'dias'   => rand(1, 5),
            ];
        }

        // ── Métodos de pago ──────────────────────────────────────────
        $metodosPago = [
            ['metodo' => 'tarjeta_credito', 'monto' => rand(30000, 90000), 'usos' => rand(10, 30)],
            ['metodo' => 'efectivo',        'monto' => rand(15000, 50000), 'usos' => rand(6, 18)],
            ['metodo' => 'transferencia',   'monto' => rand(5000, 25000),  'usos' => rand(2, 10)],
        ];

        // ── Horas pico ────────────────────────────────────────────────
        $horasPico = [
            ['hora' => 10, 'cnt' => rand(3, 10)],
            ['hora' => 14, 'cnt' => rand(4, 14)],
            ['hora' => 18, 'cnt' => rand(2, 8)],
        ];

        // ── Clientes tipo ─────────────────────────────────────────────
        $clientesTipo = [
            ['tipo' => 'Nuevos',      'cnt' => $kpi['clientes_nuevos']],
            ['tipo' => 'Recurrentes', 'cnt' => $kpi['clientes_rec']],
        ];

        // ── Armar gráfica de días ────────────────────────────────────
        $graficaDias = [];
        foreach ($labels as $idx => $label) {
            $graficaDias[] = [
                'label'    => $label,
                'ventas'   => $ventas[$idx],
                'ingresos' => $ingresos[$idx],
            ];
        }

        // ── Respuesta final ──────────────────────────────────────────
        return response()->json([
            'ventas_personal'    => $ventasPersonal,
            'periodo' => [
                'desde' => $hoy->copy()->subDays($dias-1)->toDateString(),
                'hasta' => $hoy->toDateString(),
                'dias'  => $dias,
            ],
            'kpi' => $kpi,
            'tops' => $tops,
            'grafica'            => $graficaDias,
            'grafica_anterior'   => $graficaAnterior,
            'grafica_sucursales' => $graficaSucursales,
            'sucursales'         => $sucursales,
            'pedidos'            => ['recientes' => $pedidosRecientes, 'stats' => ['pendientes' => rand(2,5), 'proceso' => rand(1,4)]],
            'ots'                => $otsActivas,
            'metodos_pago'       => $metodosPago,
            'horas_pico'         => $horasPico,
            'clientes_tipo'      => $clientesTipo,
        ]);
    }

    private function mockDetalle(Request $request)
    {
        $tipo = $request->input('tipo');
        $data = [];

        switch ($tipo) {
            case 'top_modelos':
                $data = [
                    ['id_modelo' => 1, 'nombre' => 'Trek FX 3',     'unidades' => 15, 'ingresos' => 180000],
                    ['id_modelo' => 2, 'nombre' => 'Giant Escape',  'unidades' => 11, 'ingresos' => 120000],
                    ['id_modelo' => 3, 'nombre' => 'Specialized Rockhopper', 'unidades' => 8, 'ingresos' => 95000],
                    ['id_modelo' => 4, 'nombre' => 'Cannondale Quick', 'unidades' => 6, 'ingresos' => 72000],
                ];
                break;

            case 'detalle_modelo':
                $data = [
                    'colores' => [
                        ['color' => 'Rojo',   'cnt' => rand(4,10)],
                        ['color' => 'Negro',  'cnt' => rand(3,8)],
                        ['color' => 'Blanco', 'cnt' => rand(2,6)],
                    ],
                    'voltajes' => [
                        ['voltaje' => '24V', 'cnt' => rand(2,6)],
                        ['voltaje' => '36V', 'cnt' => rand(4,12)],
                        ['voltaje' => '48V', 'cnt' => rand(5,10)],
                    ],
                ];
                break;

            case 'top_accesorios':
                $data = [
                    ['nombre' => 'Candado U-lock',       'unidades' => 18, 'ingresos' => 54000],
                    ['nombre' => 'Kit de luces LED',     'unidades' => 14, 'ingresos' => 42000],
                    ['nombre' => 'Casco urbano',         'unidades' => 10, 'ingresos' => 30000],
                    ['nombre' => 'Porta teléfono',       'unidades' => 8,  'ingresos' => 16000],
                ];
                break;

            case 'combos':
                $data = [
                    ['combo' => 'Trek FX 3 + Kit luces',         'uds' => 7],
                    ['combo' => 'Giant Escape + Candado U-lock', 'uds' => 5],
                    ['combo' => 'Specialized + Casco',           'uds' => 4],
                ];
                break;

            case 'clientes':
                $data = [
                    'top_clientes' => [
                        ['nombre' => 'Juan Pérez',     'compras' => 8, 'total_gastado' => 45000],
                        ['nombre' => 'Ana García',     'compras' => 6, 'total_gastado' => 32000],
                        ['nombre' => 'Luis Martínez',  'compras' => 5, 'total_gastado' => 28000],
                        ['nombre' => 'Carla Torres',   'compras' => 4, 'total_gastado' => 22000],
                    ],
                ];
                break;

            case 'cupones':
                $data = [
                    ['codigo' => 'BIENVENIDA10', 'nombre' => 'Bienvenida',   'usos' => 8, 'total_descuento' => 2400],
                    ['codigo' => 'VERANO20',     'nombre' => 'Verano 20%',   'usos' => 5, 'total_descuento' => 1800],
                    ['codigo' => 'FLASH15',      'nombre' => 'Flash 15%',    'usos' => 3, 'total_descuento' => 900],
                ];
                break;

            case 'vendedor_detalle':
                $data = [
                    ['nombre' => 'María López', 'ventas' => 28, 'ingresos' => 96000],
                    ['nombre' => 'Carlos Ruiz', 'ventas' => 19, 'ingresos' => 65000],
                ];
                break;

            default:
                $data = [];
        }

        return response()->json(['tipo' => $tipo, 'data' => $data]);
    }
}