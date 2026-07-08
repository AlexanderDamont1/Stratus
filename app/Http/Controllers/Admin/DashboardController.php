<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EstadisticaDiaria;
use Illuminate\Http\Request;
use App\Services\CatalogService;
use App\Models\Enlace;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\Venta;

class DashboardController extends Controller
{
    // ─── VISTA PRINCIPAL ─────────────────────────────────────────────────────
    public function index(Request $request)
    {
        $user      = auth()->user();
        $idNegocio = $user->id_negocio;

        // Obtener el filtro de sucursal (para el feed de ventas)
        $idSucursal = $request->input('id_sucursal');

        // ── Lista de sucursales disponibles para el selector ──────────────
        $sucursalesDisponibles = CatalogService::getSucursalesByNegocio($idNegocio)
            ->map(fn($s) => ['id_usuario' => $s->id_usuario, 'nombre' => $s->nombre_usuario]);

        // ── Estadísticas básicas ──────────────────────────────────────────
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

        $movimientosRecientes = CatalogService::getMovimientosRecientes($idNegocio, 10)
            ->map(fn($m) => [
                'id'               => $m->id_movimiento,
                'num_serie'        => $m->num_serie,
                'tipo_movimiento'  => $m->tipo_movimiento,
                'origen'           => $m->origen,
                'destino'          => $m->destino,
                'notas'            => $m->notas,
                'fecha_movimiento' => $m->fecha_movimiento->toDateTimeString(),
                'usuario'          => $m->usuario?->nombre_usuario,
            ]);

        // ── Feed de ventas recientes con filtro de sucursal ──────────────
        $feedVentasRecientes = CatalogService::getFeedVentasRecientes($idNegocio, 15, $idSucursal);

        return view('administrador.dashboard', compact(
            'biciStats',
            'stockVendedores',
            'piezasBajas',
            'personal',
            'catalogo',
            'enlace',
            'movimientosRecientes',
            'feedVentasRecientes',
            'sucursalesDisponibles',
        ));
    }

    // ─── STATS (AJAX) — lee de estadisticas_diarias ───────────────────────────
    public function stats(Request $request)
    {
        $user      = auth()->user();
        $idNegocio = $user->id_negocio;
        $idSucursal = $request->input('id_sucursal'); // null = "Todas"

        [$desde, $hasta] = $this->resolvePeriod($request);
        $diasPeriodo     = max(1, $desde->diffInDays($hasta) + 1);

        // ── Leer filas del periodo desde la tabla pre-calculada ───────────────
        $cached = CatalogService::getDashboardStats(
            $idNegocio,
            $desde->toDateString(),
            $hasta->toDateString(),
        );

        $filas = $cached
            ? collect($cached['filas'])->map(fn($f) => (object) $f)
            : collect();

        // ── KPIs agregados — si hay sucursal, extraer de sucursal_data ──────
        if ($idSucursal) {
            $sucData = $filas
                ->flatMap(fn($f) => $f->sucursal_data ?? [])
                ->filter(fn($s) => $s['id_usuario'] == $idSucursal);

            $ventasCount     = $sucData->sum('ventas_count');
            $ingresosTotal   = (float) $sucData->sum('ingresos_total');
            $descuentosTotal = (float) $sucData->sum('descuentos_total');
            $ticketPromedio  = $ventasCount > 0 ? round($ingresosTotal / $ventasCount, 2) : 0;
            // Los siguientes no están en sucursal_data (se calculan globalmente)
            $clientesNuevos  = 0;
            $clientesRec     = 0;
            $cuponesUsados   = 0;
            $cuponesDescuento= 0;
        } else {
            $ventasCount     = $filas->sum('ventas_count');
            $ingresosTotal   = (float) $filas->sum('ingresos_total');
            $descuentosTotal = (float) $filas->sum('descuentos_total');
            $ticketPromedio  = $ventasCount > 0 ? round($ingresosTotal / $ventasCount, 2) : 0;
            $clientesNuevos  = $filas->sum('clientes_nuevos');
            $clientesRec     = $filas->sum('clientes_rec');
            $cuponesUsados   = $filas->sum('cupones_usados');
            $cuponesDescuento= (float) $filas->sum('cupones_descuento');
        }

        // ── Tops del periodo ──────────────────────────────────────────────────
        if ($idSucursal) {
            // Usar método específico para sucursal (en vivo, cacheado)
            $topModelosSuc = CatalogService::getTopModelosPorSucursal(
                $idNegocio,
                $desde->toDateString(),
                $hasta->toDateString(),
                $idSucursal
            );
            $modeloTop = $topModelosSuc[0] ?? null;
            // Los demás tops no están implementados por sucursal aún
            $configTop = null;
            $accesorioTop = null;
            $comboTop = null;
            $cuponTop = null;
        } else {
            // Tops globales (desde las filas diarias)
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
        }

        // ── Métodos de pago (siempre global, no se filtra por sucursal) ────
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

        // ── Horas pico (siempre global) ──────────────────────────────────────
        $horasPico = $filas
            ->flatMap(fn($f) => $f->horas_pico ?? [])
            ->groupBy('hora')
            ->map(fn($g, $hora) => ['hora' => (int)$hora, 'cnt' => $g->sum('cnt')])
            ->sortBy('hora')
            ->values();

        // ── Sucursales agregadas (filtradas si se selecciona una) ──────────
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

        // Recalcular aporte_pct con totales reales del periodo
        $sucursalesAgregadas = $sucursalesAgregadas->map(function ($s) use ($ventasCount) {
            $s['aporte_pct'] = $ventasCount > 0
                ? round($s['ventas_count'] / $ventasCount * 100, 1)
                : 0;
            return $s;
        })->sortByDesc('ingresos_total')->values();

        // Si hay filtro de sucursal, mantener solo esa sucursal en la lista
        if ($idSucursal) {
            $sucursalesAgregadas = $sucursalesAgregadas->filter(fn($s) => $s['id_usuario'] == $idSucursal)->values();
        }

        // ── Gráfica: granularidad hora (hoy) o día (resto) ───────────────────
        if ($diasPeriodo === 1) {
            $fechas = collect(range(0, 23))
                ->map(fn($h) => str_pad($h, 2, '0', STR_PAD_LEFT) . ':00')
                ->toArray();

            $graficaDias = CatalogService::getGraficaHoyPorHora($idNegocio, $desde->toDateString());

            $ayer            = $desde->copy()->subDay();
            $graficaAyerRaw  = CatalogService::getGraficaHoyPorHora($idNegocio, $ayer->toDateString());
            $graficaAnterior = $graficaAyerRaw->map(fn($r) => [
                'ventas'   => $r['ventas'],
                'ingresos' => $r['ingresos'],
            ]);

            $sucursalesMap = CatalogService::getSucursalesPorHora($idNegocio, $desde->toDateString());
            $graficaSucursales = collect($sucursalesMap)->map(fn($diasData, $nombre) => [
                'nombre'   => $nombre,
                'ventas'   => array_map(fn($f) => $diasData[$f]['ventas']   ?? 0, $fechas),
                'ingresos' => array_map(fn($f) => $diasData[$f]['ingresos'] ?? 0, $fechas),
            ])->values();

            $personalMap = CatalogService::getPersonalPorHora($idNegocio, $desde->toDateString());
            $ventasPersonal = collect($personalMap)->map(fn($diasData, $nombre) => [
                'nombre'   => $nombre,
                'ventas'   => array_map(fn($f) => $diasData[$f]['ventas']   ?? 0, $fechas),
                'ingresos' => array_map(fn($f) => $diasData[$f]['ingresos'] ?? 0, $fechas),
            ])->values();

        } else {
            $fechas = collect(range(0, $diasPeriodo - 1))
                ->map(fn($i) => $desde->copy()->addDays($i)->toDateString())
                ->toArray();

            $fmt        = $diasPeriodo <= 31 ? 'd/m' : 'M y';
            $filasIndex = $filas->keyBy(fn($f) => \Carbon\Carbon::parse($f->fecha)->toDateString());

            $graficaDias = collect($fechas)->map(function ($fecha) use ($filasIndex, $fmt) {
                $fila = $filasIndex[$fecha] ?? null;
                return [
                    'label'    => Carbon::parse($fecha)->translatedFormat($fmt),
                    'ventas'   => (int)   ($fila?->ventas_count   ?? 0),
                    'ingresos' => (float) ($fila?->ingresos_total ?? 0),
                ];
            });

            $desdeAnt = $desde->copy()->subDays($diasPeriodo);
            $hastaAnt = $hasta->copy()->subDays($diasPeriodo);
            $filasAnt = CatalogService::getFilasHistoricas(
                $idNegocio,
                $desdeAnt->toDateString(),
                $hastaAnt->toDateString()
            );

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

        // ── Si hay filtro de sucursal, filtrar las series de la gráfica ────
        if ($idSucursal) {
            $nombreSucursal = $sucursalesAgregadas->first()['nombre'] ?? null;
            if ($nombreSucursal) {
                $graficaSucursales = $graficaSucursales->filter(fn($s) => $s['nombre'] === $nombreSucursal)->values();
            } else {
                $graficaSucursales = collect();
            }
        }

        // ── Pedidos y OTs (en vivo, con filtro de sucursal) ─────────────────
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

        $otsActivas = CatalogService::getOtsActivas($idNegocio, $idSucursal);
        $ventasHoy  = CatalogService::getVentasHoy($idNegocio, $idSucursal);

        // ── Nuevos indicadores ──────────────────────────────────────────────────
        $rotacionInventario = CatalogService::getBicicletasSinMovimiento($idNegocio, 45, $idSucursal);
        $margenSucursales   = $idSucursal ? null : CatalogService::getMargenPorSucursal($idNegocio, $desde->toDateString(), $hasta->toDateString());

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
            'rotacion_inventario' => $rotacionInventario,
            'margen_sucursales'   => $margenSucursales,
        ]);
    }

    // ─── DETALLE (AJAX — lazy, solo cuando abre modal) ───────────────────────
    public function detalle(Request $request)
    {
        $user      = auth()->user();
        $idNegocio = $user->id_negocio;
        $tipo      = $request->input('tipo');

        [$desde, $hasta] = $this->resolvePeriod($request);
        $desdeTs = $desde->copy()->startOfDay();
        $hastaTs = $hasta->copy()->endOfDay();

        $extraParams = $request->only(['id_modelo', 'id_usuario', 'id_producto']);

        $data = CatalogService::getDashboardDetalle(
            $idNegocio,
            $tipo,
            $desde->toDateString(),
            $hasta->toDateString(),
            $extraParams,
            function () use ($tipo, $request, $idNegocio, $desdeTs, $hastaTs) {
                return match ($tipo) {

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

                        // ── Modelo x sucursal: qué sucursal vende / factura más de este modelo ──
                        $porSucursal = DB::table('detalle_venta as dv')
                            ->join('ventas as v',    'dv.id_venta', '=', 'v.id_venta')
                            ->join('bicicletas as b','dv.num_serie', '=', 'b.num_serie')
                            ->join('usuarios as u',  'v.id_usuario', '=', 'u.id_usuario')
                            ->where('v.id_negocio', $idNegocio)
                            ->whereBetween('v.created_at', [$desdeTs, $hastaTs])
                            ->where('b.id_modelo', $idModelo)
                            ->selectRaw('u.nombre_usuario as sucursal, COUNT(*) as unidades, COALESCE(SUM(dv.precio_unitario * dv.cantidad),0) as ingresos')
                            ->groupBy('u.nombre_usuario')
                            ->orderByDesc('ingresos')
                            ->get()
                            ->map(fn($r) => [
                                'sucursal' => $r->sucursal,
                                'unidades' => (int)   $r->unidades,
                                'ingresos' => (float) $r->ingresos,
                            ]);

                        return ['colores' => $colores, 'voltajes' => $voltajes, 'por_sucursal' => $porSucursal];
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

                    // ── Accesorio x sucursal: qué sucursal vende / factura más de este accesorio ──
                    'accesorio_x_sucursal' => DB::table('detalle_venta as dv')
                        ->join('ventas as v',    'dv.id_venta', '=', 'v.id_venta')
                        ->join('usuarios as u',  'v.id_usuario', '=', 'u.id_usuario')
                        ->where('v.id_negocio', $idNegocio)
                        ->whereBetween('v.created_at', [$desdeTs, $hastaTs])
                        ->where('dv.id_producto', $request->input('id_producto'))
                        ->whereNull('dv.num_serie')
                        ->selectRaw('u.nombre_usuario as sucursal, SUM(dv.cantidad) as unidades, COALESCE(SUM(dv.precio_unitario * dv.cantidad),0) as ingresos')
                        ->groupBy('u.nombre_usuario')
                        ->orderByDesc('ingresos')
                        ->get()
                        ->map(fn($r) => [
                            'sucursal' => $r->sucursal,
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
            }
        );

        return response()->json(['tipo' => $tipo, 'data' => $data]);
    }

    // ─── HELPERS PRIVADOS ─────────────────────────────────────────────────────
    private function sucursalesPorDia($filas, array $fechas, string $fmt): \Illuminate\Support\Collection
    {
        $vendedoresMap = [];

        foreach ($filas as $fila) {
            $fechaStr = \Carbon\Carbon::parse($fila->fecha)->toDateString();
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
}