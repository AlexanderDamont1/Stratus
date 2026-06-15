<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\CatalogService;
use App\Models\Enlace;
use App\Models\Venta;
use App\Models\Pedido;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
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
            idNegocio:     $idNegocio,
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
            'biciStats', 'stockVendedores', 'piezasBajas',
            'personal', 'catalogo', 'enlace',
        ));
    }

    // ─── ENDPOINT PRINCIPAL ───────────────────────────────────────────────────
    // GET /admin/dashboard/stats?periodo=7d
    // GET /admin/dashboard/stats?desde=YYYY-MM-DD&hasta=YYYY-MM-DD
    public function stats(Request $request)
    {
        $user      = auth()->user();
        $idNegocio = $user->id_negocio;

        [$desde, $hasta] = $this->resolvePeriod($request);
        $desdeTs = $desde->copy()->startOfDay();
        $hastaTs = $hasta->copy()->endOfDay();

        // ── KPIs base ────────────────────────────────────────────────────────
        $ventasBase = Venta::where('id_negocio', $idNegocio)
            ->whereBetween('created_at', [$desdeTs, $hastaTs]);

        $totalVentas    = (clone $ventasBase)->count();
        $totalIngresos  = (float)(clone $ventasBase)->sum('total');
        $ticketProm     = $totalVentas > 0 ? round($totalIngresos / $totalVentas) : 0;
        $clientesUnicos = (clone $ventasBase)->distinct('id_cliente')->count('id_cliente');
        $descuentos     = (float)(clone $ventasBase)->sum('descuento_total');

        // ── Gráfica por día ───────────────────────────────────────────────────
        $diasPeriodo = max(1, $desde->diffInDays($hasta) + 1);
        $fmt         = $diasPeriodo === 1 ? 'H:00' : ($diasPeriodo <= 31 ? 'd/m' : 'M y');
        $graficaDias = collect(range(0, $diasPeriodo - 1))->map(function ($offset) use ($idNegocio, $desde, $fmt) {
            $dia = $desde->copy()->addDays($offset);
            $row = DB::table('ventas')
                ->where('id_negocio', $idNegocio)
                ->whereDate('created_at', $dia)
                ->selectRaw('COUNT(*) as ventas, COALESCE(SUM(total),0) as ingresos')
                ->first();
            return [
                'label'    => $dia->translatedFormat($fmt),
                'ventas'   => (int)$row->ventas,
                'ingresos' => (float)$row->ingresos,
            ];
        });

        // ── Pedidos ───────────────────────────────────────────────────────────
        $pedidoStats    = CatalogService::getPedidoStats($idNegocio);
        $pedidosActivos = $pedidoStats['pendientes'] + $pedidoStats['en_proceso'];
        $conversion     = ($totalVentas + $pedidosActivos) > 0
            ? round($totalVentas / ($totalVentas + $pedidosActivos), 2) : 0;

        $pedidosRecientes = CatalogService::getPedidosRecientesByNegocio($idNegocio, 6)
            ->map(fn($p) => [
                'id'      => $p->id_pedido,
                'estado'  => match((int)$p->status) { 1 => 'pendiente', 2 => 'proceso', default => 'completado' },
                'monto'   => (float)($p->total ?? 0),
                'cliente' => $p->usuario?->nombre_usuario ?? 'Sin cliente',
                'items'   => $p->items?->count() ?? 0,
                'fecha'   => $p->created_at?->diffForHumans() ?? '—',
            ]);

        // ── OTs activas ───────────────────────────────────────────────────────
        $otsActivas = $this->getOtsActivas($idNegocio);

        // ── Ingresos por vendedor/sucursal ────────────────────────────────────
        $ingresosVendedor = DB::table('ventas as v')
            ->join('usuarios as u', 'v.id_usuario', '=', 'u.id_usuario')
            ->where('v.id_negocio', $idNegocio)
            ->whereBetween('v.created_at', [$desdeTs, $hastaTs])
            ->selectRaw('u.id_usuario, u.nombre_usuario,
                COUNT(*) as total_ventas,
                COALESCE(SUM(v.total),0) as total_ingresos,
                COALESCE(SUM(v.descuento_total),0) as total_descuentos')
            ->groupBy('u.id_usuario', 'u.nombre_usuario')
            ->orderByDesc('total_ingresos')
            ->get()
            ->map(fn($r) => [
                'nombre'     => $r->nombre_usuario,
                'id_usuario' => $r->id_usuario,
                'ventas'     => (int)$r->total_ventas,
                'monto'      => (float)$r->total_ingresos,
                'descuentos' => (float)$r->total_descuentos,
                'ticket'     => $r->total_ventas > 0 ? round($r->total_ingresos / $r->total_ventas) : 0,
            ]);

        // ── Métodos de pago ───────────────────────────────────────────────────
        $metodosPago = DB::table('venta_pagos as vp')
            ->join('ventas as v', 'vp.id_venta', '=', 'v.id_venta')
            ->where('v.id_negocio', $idNegocio)
            ->whereBetween('v.created_at', [$desdeTs, $hastaTs])
            ->selectRaw('vp.metodo, COUNT(*) as usos, COALESCE(SUM(vp.monto),0) as monto_total')
            ->groupBy('vp.metodo')
            ->orderByDesc('monto_total')
            ->get()
            ->map(fn($r) => [
                'metodo' => $r->metodo,
                'usos'   => (int)$r->usos,
                'monto'  => (float)$r->monto_total,
            ]);

        // ── Top modelos vendidos ──────────────────────────────────────────────
        $topModelos = DB::table('detalle_venta as dv')
            ->join('ventas as v', 'dv.id_venta', '=', 'v.id_venta')
            ->join('bicicletas as b', 'dv.num_serie', '=', 'b.num_serie')
            ->join('modelos as m', 'b.id_modelo', '=', 'm.id_modelo')
            ->where('v.id_negocio', $idNegocio)
            ->whereBetween('v.created_at', [$desdeTs, $hastaTs])
            ->whereNotNull('dv.num_serie')
            ->selectRaw('m.id_modelo, m.nombre_modelo,
                COUNT(*) as unidades,
                COALESCE(SUM(dv.precio_unitario * dv.cantidad),0) as ingresos')
            ->groupBy('m.id_modelo', 'm.nombre_modelo')
            ->orderByDesc('unidades')
            ->limit(8)
            ->get()
            ->map(fn($r) => [
                'id_modelo' => $r->id_modelo,
                'nombre'    => $r->nombre_modelo,
                'unidades'  => (int)$r->unidades,
                'ingresos'  => (float)$r->ingresos,
            ]);

        // ── Detalle por modelo: colores y voltajes ────────────────────────────
        $topModeloIds   = $topModelos->take(5)->pluck('id_modelo')->toArray();
        $detalleModelos = [];

        if (!empty($topModeloIds)) {
            $coloresVendidos = DB::table('detalle_venta as dv')
                ->join('ventas as v', 'dv.id_venta', '=', 'v.id_venta')
                ->join('bicicletas as b', 'dv.num_serie', '=', 'b.num_serie')
                ->join('colores as c', 'b.id_color', '=', 'c.id_color')
                ->where('v.id_negocio', $idNegocio)
                ->whereBetween('v.created_at', [$desdeTs, $hastaTs])
                ->whereIn('b.id_modelo', $topModeloIds)
                ->selectRaw('b.id_modelo, c.color, COUNT(*) as cnt')
                ->groupBy('b.id_modelo', 'c.color')
                ->orderByDesc('cnt')
                ->get()
                ->groupBy('id_modelo');

            $voltajesVendidos = DB::table('detalle_venta as dv')
                ->join('ventas as v', 'dv.id_venta', '=', 'v.id_venta')
                ->join('bicicletas as b', 'dv.num_serie', '=', 'b.num_serie')
                ->join('voltajes as vt', 'b.id_voltaje', '=', 'vt.id_voltaje')
                ->where('v.id_negocio', $idNegocio)
                ->whereBetween('v.created_at', [$desdeTs, $hastaTs])
                ->whereIn('b.id_modelo', $topModeloIds)
                ->selectRaw('b.id_modelo, vt.voltaje, COUNT(*) as cnt')
                ->groupBy('b.id_modelo', 'vt.voltaje')
                ->orderByDesc('cnt')
                ->get()
                ->groupBy('id_modelo');

            foreach ($topModeloIds as $idModelo) {
                $detalleModelos[$idModelo] = [
                    'colores'  => collect($coloresVendidos->get($idModelo, []))
                        ->map(fn($r) => ['color' => $r->color, 'cnt' => (int)$r->cnt])
                        ->values(),
                    'voltajes' => collect($voltajesVendidos->get($idModelo, []))
                        ->map(fn($r) => ['voltaje' => $r->voltaje, 'cnt' => (int)$r->cnt])
                        ->values(),
                ];
            }
        }

        // ── Accesorios más vendidos ───────────────────────────────────────────
        $topAccesorios = DB::table('detalle_venta as dv')
            ->join('ventas as v', 'dv.id_venta', '=', 'v.id_venta')
            ->join('productos as p', 'dv.id_producto', '=', 'p.id_producto')
            ->where('v.id_negocio', $idNegocio)
            ->whereBetween('v.created_at', [$desdeTs, $hastaTs])
            ->whereNull('dv.num_serie')
            ->selectRaw('p.nombre_producto,
                SUM(dv.cantidad) as unidades,
                COALESCE(SUM(dv.precio_unitario * dv.cantidad),0) as ingresos')
            ->groupBy('p.nombre_producto')
            ->orderByDesc('unidades')
            ->limit(6)
            ->get()
            ->map(fn($r) => [
                'nombre'   => $r->nombre_producto,
                'unidades' => (int)$r->unidades,
                'ingresos' => (float)$r->ingresos,
            ]);

        // ── Horas pico (para heatmap) ─────────────────────────────────────────
        $horasPico = DB::table('ventas')
            ->where('id_negocio', $idNegocio)
            ->whereBetween('created_at', [$desdeTs, $hastaTs])
            ->selectRaw('HOUR(created_at) as hora, COUNT(*) as cnt')
            ->groupBy('hora')
            ->orderBy('hora')
            ->get()
            ->map(fn($r) => ['hora' => (int)$r->hora, 'cnt' => (int)$r->cnt]);

        // ── Clientes nuevos vs recurrentes ────────────────────────────────────
        $clientesEnPeriodo  = (clone $ventasBase)->distinct('id_cliente')->pluck('id_cliente');
        $clientesNuevos     = DB::table('ventas')
            ->where('id_negocio', $idNegocio)
            ->whereIn('id_cliente', $clientesEnPeriodo)
            ->select('id_cliente', DB::raw('MIN(created_at) as primera_compra'))
            ->groupBy('id_cliente')
            ->havingRaw('primera_compra BETWEEN ? AND ?', [$desdeTs, $hastaTs])
            ->count();
        $clientesRecurrentes = max(0, $clientesUnicos - $clientesNuevos);

        // ── Ventas hoy (siempre) ──────────────────────────────────────────────
        $ventasHoy = (int) DB::table('ventas')
            ->where('id_negocio', $idNegocio)
            ->whereDate('created_at', Carbon::today())
            ->count();

        // ═════════════════════════════════════════════════════════════════════
        // ESTADÍSTICAS CALCULADAS EN BACKEND
        // ═════════════════════════════════════════════════════════════════════

        // ── 1. Regresión lineal sobre ingresos diarios ────────────────────────
        $ingresosArr = $graficaDias->pluck('ingresos')->map(fn($v) => (float)$v)->values()->toArray();
        $n           = count($ingresosArr);
        $regression  = ['slope' => 0, 'intercept' => 0, 'r2' => 0, 'equation' => '', 'prediction' => 0, 'line' => [], 'scatter' => []];

        if ($n >= 2) {
            $xs    = range(0, $n - 1);
            $sumX  = array_sum($xs);
            $sumY  = array_sum($ingresosArr);
            $sumXY = 0;
            $sumX2 = 0;
            foreach ($xs as $i) {
                $sumXY += $i * $ingresosArr[$i];
                $sumX2 += $i * $i;
            }

            $denom     = ($n * $sumX2 - $sumX * $sumX);
            $slope     = $denom ? ($n * $sumXY - $sumX * $sumY) / $denom : 0;
            $intercept = ($sumY - $slope * $sumX) / $n;

            $meanY = $sumY / $n;
            $ssTot = 0;
            $ssRes = 0;
            foreach ($ingresosArr as $i => $y) {
                $ssTot += ($y - $meanY) ** 2;
                $ssRes += ($y - ($slope * $i + $intercept)) ** 2;
            }
            $r2 = $ssTot > 0 ? 1 - ($ssRes / $ssTot) : 0;

            $regression = [
                'slope'      => round($slope, 2),
                'intercept'  => round($intercept, 2),
                'r2'         => round($r2, 4),
                'equation'   => 'y = ' . round($slope, 2) . 'x + ' . number_format($intercept, 0, '.', ','),
                'prediction' => round(max(0, $slope * ($n - 1 + 7) + $intercept), 2),
                'line'       => array_map(fn($i) => round($slope * $i + $intercept, 2), $xs),
                'scatter'    => array_map(fn($i) => ['x' => $i, 'y' => $ingresosArr[$i]], $xs),
            ];
        }

        // ── 2. Media móvil 7 días ─────────────────────────────────────────────
        $window        = 7;
        $movingAverage = [];
        foreach ($ingresosArr as $i => $_) {
            $start           = max(0, $i - $window + 1);
            $slice           = array_slice($ingresosArr, $start, $i - $start + 1);
            $movingAverage[] = round(array_sum($slice) / count($slice), 2);
        }

        // ── 3. Heatmap: ventas por día de semana × hora ───────────────────────
        // DAYOFWEEK en MySQL: 1=Dom … 7=Sáb → ajustamos a 0=Lun … 6=Dom
        $heatmapRaw = DB::table('ventas')
            ->where('id_negocio', $idNegocio)
            ->whereBetween('created_at', [$desdeTs, $hastaTs])
            ->selectRaw('MOD(DAYOFWEEK(created_at) + 5, 7) AS dow, HOUR(created_at) AS hora, COUNT(*) AS cnt')
            ->groupByRaw('dow, hora')
            ->get();

        $matrixRaw = array_fill(0, 7, array_fill(0, 24, 0));
        foreach ($heatmapRaw as $row) {
            $dow = (int)$row->dow;
            $h   = (int)$row->hora;
            if ($dow >= 0 && $dow < 7 && $h >= 0 && $h < 24) {
                $matrixRaw[$dow][$h] = (int)$row->cnt;
            }
        }

        $maxHeatmap = max(array_map('max', $matrixRaw)) ?: 1;
        $matrixNorm = array_map(
            fn($row) => array_map(fn($v) => round($v / $maxHeatmap, 3), $row),
            $matrixRaw
        );

        // ─────────────────────────────────────────────────────────────────────

        return response()->json([
            'periodo' => [
                'desde' => $desde->toDateString(),
                'hasta' => $hasta->toDateString(),
                'dias'  => $diasPeriodo,
            ],
            'kpi' => [
                'ventas'          => $totalVentas,
                'ingresos'        => $totalIngresos,
                'ticket'          => $ticketProm,
                'clientes'        => $clientesUnicos,
                'clientes_nuevos' => $clientesNuevos,
                'clientes_rec'    => $clientesRecurrentes,
                'conversion'      => $conversion,
                'ots_activas'     => count($otsActivas),
                'ventas_hoy'      => $ventasHoy,
                'pedidos_activos' => $pedidosActivos,
                'descuentos'      => $descuentos,
            ],
            'grafica'           => $graficaDias,
            'pedidos'           => ['recientes' => $pedidosRecientes, 'stats' => $pedidoStats],
            'ots'               => $otsActivas,
            'ingresos_vendedor' => $ingresosVendedor,
            'metodos_pago'      => $metodosPago,
            'top_modelos'       => $topModelos,
            'detalle_modelos'   => $detalleModelos,
            'top_accesorios'    => $topAccesorios,
            'horas_pico'        => $horasPico,
            'clientes_tipo'     => [
                ['tipo' => 'Nuevos',      'cnt' => $clientesNuevos],
                ['tipo' => 'Recurrentes', 'cnt' => $clientesRecurrentes],
            ],
            // Estadísticas calculadas en backend
            'regression'        => $regression,
            'moving_average'    => $movingAverage,
            'heatmap'           => [
                'matrix'     => $matrixNorm,
                'matrix_raw' => $matrixRaw,
                'max'        => $maxHeatmap,
            ],
        ]);
    }

    // ─── HELPERS PRIVADOS ─────────────────────────────────────────────────────
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
}