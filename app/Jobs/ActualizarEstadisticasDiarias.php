<?php

namespace App\Jobs;

use App\Models\EstadisticaDiaria;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ActualizarEstadisticasDiarias implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries   = 3;
    public int $timeout = 60;

    public function __construct(
        public readonly string $idNegocio,
        public readonly string $fecha,   // Y-m-d
    ) {}

    public function handle(): void
    {
        $fecha     = Carbon::parse($this->fecha);
        $desdeTs   = $fecha->copy()->startOfDay();
        $hastaTs   = $fecha->copy()->endOfDay();
        $idNegocio = $this->idNegocio;

        // ── Ventas base ───────────────────────────────────────────────────
        $base = DB::table('ventas')
            ->where('id_negocio', $idNegocio)
            ->whereBetween('created_at', [$desdeTs, $hastaTs]);

        $ventasCount    = (clone $base)->count();
        $ingresosTotal  = (float)(clone $base)->sum('total');
        $descuentosTotal= (float)(clone $base)->sum('descuento_total');
        $ticketPromedio = $ventasCount > 0 ? round($ingresosTotal / $ventasCount, 2) : 0;

        // ── Clientes nuevos vs recurrentes ────────────────────────────────
        $clientesEnPeriodo = (clone $base)->distinct('id_cliente')->pluck('id_cliente');

        $clientesNuevos = DB::table('ventas')
            ->where('id_negocio', $idNegocio)
            ->whereIn('id_cliente', $clientesEnPeriodo)
            ->select('id_cliente', DB::raw('MIN(created_at) as primera_compra'))
            ->groupBy('id_cliente')
            ->havingRaw('primera_compra BETWEEN ? AND ?', [$desdeTs, $hastaTs])
            ->count();

        $clientesRec = max(0, $clientesEnPeriodo->count() - $clientesNuevos);

        // ── Modelo top ────────────────────────────────────────────────────
        $modeloTop = DB::table('detalle_venta as dv')
            ->join('ventas as v',    'dv.id_venta',  '=', 'v.id_venta')
            ->join('bicicletas as b','dv.num_serie',  '=', 'b.num_serie')
            ->join('modelos as m',   'b.id_modelo',   '=', 'm.id_modelo')
            ->where('v.id_negocio', $idNegocio)
            ->whereBetween('v.created_at', [$desdeTs, $hastaTs])
            ->whereNotNull('dv.num_serie')
            ->selectRaw('m.id_modelo, m.nombre_modelo, COUNT(*) as unidades')
            ->groupBy('m.id_modelo', 'm.nombre_modelo')
            ->orderByDesc('unidades')
            ->first();

        // ── Configuración más vendida (modelo + color + voltaje) ──────────
        $configTop = DB::table('detalle_venta as dv')
            ->join('ventas as v',    'dv.id_venta',  '=', 'v.id_venta')
            ->join('bicicletas as b','dv.num_serie',  '=', 'b.num_serie')
            ->join('modelos as m',   'b.id_modelo',   '=', 'm.id_modelo')
            ->join('colores as c',   'b.id_color',    '=', 'c.id_color')
            ->join('voltajes as vt', 'b.id_voltaje',  '=', 'vt.id_voltaje')
            ->where('v.id_negocio', $idNegocio)
            ->whereBetween('v.created_at', [$desdeTs, $hastaTs])
            ->whereNotNull('dv.num_serie')
            ->selectRaw("
                CONCAT(m.nombre_modelo, ' · ', SUBSTRING_INDEX(c.color, '|', 1), ' · ', vt.voltaje, 'V') as config,
                COUNT(*) as unidades
            ")
            ->groupBy('config')
            ->orderByDesc('unidades')
            ->first();

        // ── Accesorio top ─────────────────────────────────────────────────
        $accesorioTop = DB::table('detalle_venta as dv')
            ->join('ventas as v',   'dv.id_venta',   '=', 'v.id_venta')
            ->join('productos as p','dv.id_producto', '=', 'p.id_producto')
            ->where('v.id_negocio', $idNegocio)
            ->whereBetween('v.created_at', [$desdeTs, $hastaTs])
            ->whereNull('dv.num_serie')
            ->where('p.tipo', '1')
            ->selectRaw('p.nombre_producto, SUM(dv.cantidad) as unidades')
            ->groupBy('p.nombre_producto')
            ->orderByDesc('unidades')
            ->first();

        // ── Combo más frecuente (bici + accesorio en misma venta) ─────────
        $comboTop = DB::table('detalle_venta as dv1')
            ->join('detalle_venta as dv2', 'dv1.id_venta', '=', 'dv2.id_venta')
            ->join('ventas as v',          'dv1.id_venta', '=', 'v.id_venta')
            ->join('modelos as m',         function ($j) {
                $j->join('bicicletas as b', 'dv1.num_serie', '=', 'b.num_serie')
                  ->on('b.id_modelo', '=', 'm.id_modelo');
            })
            ->join('productos as p', 'dv2.id_producto', '=', 'p.id_producto')
            ->where('v.id_negocio', $idNegocio)
            ->whereBetween('v.created_at', [$desdeTs, $hastaTs])
            ->whereNotNull('dv1.num_serie')   // dv1 = bici
            ->whereNull('dv2.num_serie')       // dv2 = accesorio
            ->where('p.tipo', '1')
            ->selectRaw("CONCAT(m.nombre_modelo, ' + ', p.nombre_producto) as combo, COUNT(*) as uds")
            ->groupBy('combo')
            ->orderByDesc('uds')
            ->first();

        // ── Cupón top ─────────────────────────────────────────────────────
        $cuponesData = DB::table('cupon_usos as cu')
            ->join('cupones as c', 'cu.id_cupon', '=', 'c.id_cupon')
            ->where('cu.id_negocio', $idNegocio)
            ->whereBetween('cu.created_at', [$desdeTs, $hastaTs])
            ->selectRaw('c.codigo, COUNT(*) as usos, SUM(cu.descuento_aplicado) as total_descuento')
            ->groupBy('c.codigo')
            ->orderByDesc('usos')
            ->get();

        $cuponTop       = $cuponesData->first();
        $cuponesUsados  = $cuponesData->sum('usos');
        $cuponesDescuento = $cuponesData->sum('total_descuento');

        // ── Horas pico ────────────────────────────────────────────────────
        $horasPico = DB::table('ventas')
            ->where('id_negocio', $idNegocio)
            ->whereBetween('created_at', [$desdeTs, $hastaTs])
            ->selectRaw('HOUR(created_at) as hora, COUNT(*) as cnt')
            ->groupBy('hora')
            ->orderBy('hora')
            ->get()
            ->map(fn($r) => ['hora' => (int)$r->hora, 'cnt' => (int)$r->cnt])
            ->toArray();

        // ── Métodos de pago ───────────────────────────────────────────────
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
            ])
            ->toArray();

        // ── Sucursales ────────────────────────────────────────────────────
        $sucursalesRaw = DB::table('ventas as v')
            ->join('usuarios as u', 'v.id_usuario', '=', 'u.id_usuario')
            ->where('v.id_negocio', $idNegocio)
            ->whereBetween('v.created_at', [$desdeTs, $hastaTs])
            ->selectRaw('
                u.id_usuario,
                u.nombre_usuario,
                COUNT(*) as ventas_count,
                COALESCE(SUM(v.total), 0) as ingresos_total,
                COALESCE(SUM(v.descuento_total), 0) as descuentos_total
            ')
            ->groupBy('u.id_usuario', 'u.nombre_usuario')
            ->orderByDesc('ingresos_total')
            ->get();

        // Unidades de bicis por sucursal
        $bicisPorSucursal = DB::table('detalle_venta as dv')
            ->join('ventas as v', 'dv.id_venta', '=', 'v.id_venta')
            ->where('v.id_negocio', $idNegocio)
            ->whereBetween('v.created_at', [$desdeTs, $hastaTs])
            ->whereNotNull('dv.num_serie')
            ->selectRaw('v.id_usuario, COUNT(*) as unidades_bicis')
            ->groupBy('v.id_usuario')
            ->get()
            ->keyBy('id_usuario');

        $accesoriosPorSucursal = DB::table('detalle_venta as dv')
            ->join('ventas as v',   'dv.id_venta',   '=', 'v.id_venta')
            ->join('productos as p','dv.id_producto', '=', 'p.id_producto')
            ->where('v.id_negocio', $idNegocio)
            ->whereBetween('v.created_at', [$desdeTs, $hastaTs])
            ->whereNull('dv.num_serie')
            ->where('p.tipo', '1')
            ->selectRaw('v.id_usuario, SUM(dv.cantidad) as unidades_accesorios')
            ->groupBy('v.id_usuario')
            ->get()
            ->keyBy('id_usuario');

        // Vendedor top por sucursal
        $vendedorTopPorSucursal = DB::table('ventas as v')
            ->join('personal as p', 'v.id_personal', '=', 'p.id_personal')
            ->where('v.id_negocio', $idNegocio)
            ->whereBetween('v.created_at', [$desdeTs, $hastaTs])
            ->selectRaw('
                v.id_usuario,
                p.id_personal,
                p.nombre,
                COUNT(*) as ventas,
                COALESCE(SUM(v.total), 0) as ingresos
            ')
            ->groupBy('v.id_usuario', 'p.id_personal', 'p.nombre')
            ->orderByDesc('ingresos')
            ->get()
            ->groupBy('id_usuario')
            ->map(fn($rows) => $rows->first());

        // Horas pico por sucursal
        $horasPorSucursal = DB::table('ventas')
            ->where('id_negocio', $idNegocio)
            ->whereBetween('created_at', [$desdeTs, $hastaTs])
            ->selectRaw('id_usuario, HOUR(created_at) as hora, COUNT(*) as cnt')
            ->groupBy('id_usuario', 'hora')
            ->orderBy('hora')
            ->get()
            ->groupBy('id_usuario');

        $sucursalData = $sucursalesRaw->map(function ($row) use (
            $bicisPorSucursal,
            $accesoriosPorSucursal,
            $vendedorTopPorSucursal,
            $horasPorSucursal,
            $ventasCount,
        ) {
            $vendedorTop = $vendedorTopPorSucursal[$row->id_usuario] ?? null;
            $horasSuc    = $horasPorSucursal[$row->id_usuario] ?? collect();

            return [
                'id_usuario'           => $row->id_usuario,
                'nombre'               => $row->nombre_usuario,
                'ventas_count'         => (int)$row->ventas_count,
                'ingresos_total'       => (float)$row->ingresos_total,
                'ticket_promedio'      => $row->ventas_count > 0
                                            ? round($row->ingresos_total / $row->ventas_count, 2)
                                            : 0,
                'unidades_bicis'       => (int)($bicisPorSucursal[$row->id_usuario]->unidades_bicis ?? 0),
                'unidades_accesorios'  => (int)($accesoriosPorSucursal[$row->id_usuario]->unidades_accesorios ?? 0),
                'descuentos_total'     => (float)$row->descuentos_total,
                'aporte_pct'           => $ventasCount > 0
                                            ? round($row->ventas_count / $ventasCount * 100, 1)
                                            : 0,
                'vendedor_top' => $vendedorTop ? [
                    'id_personal' => $vendedorTop->id_personal,
                    'nombre'      => $vendedorTop->nombre,
                    'ventas'      => (int)$vendedorTop->ventas,
                    'ingresos'    => (float)$vendedorTop->ingresos,
                ] : null,
                'horas_pico' => $horasSuc->map(fn($h) => [
                    'hora' => (int)$h->hora,
                    'cnt'  => (int)$h->cnt,
                ])->values()->toArray(),
            ];
        })->values()->toArray();

        
        $existente = EstadisticaDiaria::where('id_negocio', $idNegocio)
            ->where('fecha', $this->fecha)
            ->first();

        $datos = [
            'ventas_count'         => $ventasCount,
            'ingresos_total'       => $ingresosTotal,
            'ticket_promedio'      => $ticketPromedio,
            'descuentos_total'     => $descuentosTotal,
            'clientes_nuevos'      => $clientesNuevos,
            'clientes_rec'         => $clientesRec,
            'modelo_top_id'        => $modeloTop?->id_modelo,
            'modelo_top_nombre'    => $modeloTop?->nombre_modelo,
            'modelo_top_unidades'  => (int)($modeloTop?->unidades ?? 0),
            'config_top'           => $configTop?->config,
            'config_top_unidades'  => (int)($configTop?->unidades ?? 0),
            'accesorio_top_nombre' => $accesorioTop?->nombre_producto,
            'accesorio_top_uds'    => (int)($accesorioTop?->unidades ?? 0),
            'combo_top'            => $comboTop?->combo,
            'combo_top_uds'        => (int)($comboTop?->uds ?? 0),
            'cupones_usados'       => (int)$cuponesUsados,
            'cupones_descuento'    => (float)$cuponesDescuento,
            'cupon_top_codigo'     => $cuponTop?->codigo,
            'cupon_top_usos'       => (int)($cuponTop?->usos ?? 0),
            'sucursal_data'        => $sucursalData,
            'horas_pico'           => $horasPico,
            'metodos_pago'         => $metodosPago,
        ];

        if ($existente) {
            $existente->update($datos);
        } else {
            EstadisticaDiaria::create(array_merge($datos, [
                'id_estadistica' => EstadisticaDiaria::generarId($fecha),
                'id_negocio'     => $idNegocio,
                'fecha'          => $this->fecha,
            ]));
        }

        Log::info('EstadisticasDiarias: recalculado', [
            'id_negocio' => $idNegocio,
            'fecha'      => $this->fecha,
            'ventas'     => $ventasCount,
        ]);
        \App\Services\CatalogService::invalidateDashboardStats($idNegocio);
    }

    public function failed(\Throwable $e): void
    {
        Log::error('EstadisticasDiarias: Job falló', [
            'id_negocio' => $this->idNegocio,
            'fecha'      => $this->fecha,
            'error'      => $e->getMessage(),
        ]);
    }
}