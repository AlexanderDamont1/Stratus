<?php

namespace App\Services;

use App\Models\Caja;
use App\Models\CajaSesion;
use App\Models\CajaMovimiento;
use App\Models\CajaCorte;
use App\Models\Venta;
use App\Models\VentaPago;
use App\Models\CajaGasto;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CajaService
{
    /* ══════════════════════════════════════════════════════════
     | HELPERS
     ══════════════════════════════════════════════════════════ */

    public static function generarId(string $prefix): string
    {
        return strtoupper($prefix)
            . now()->format('ymd')
            . strtoupper(substr(bin2hex(random_bytes(3)), 0, 6));
    }

    /* ══════════════════════════════════════════════════════════
     | CAJA
     ══════════════════════════════════════════════════════════ */

    public static function cajaDeUsuario(string $idUsuario, string $idNegocio): ?Caja
    {
        return Caja::where('id_negocio', $idNegocio)
            ->where('id_usuario', $idUsuario)
            ->where('activa', true)
            ->first();
    }

    /* ══════════════════════════════════════════════════════════
     | SESIÓN — apertura
     ══════════════════════════════════════════════════════════ */

    public static function abrirSesion(
        Caja $caja,
        string $idUsuario,
        float $fondoInicial
    ): CajaSesion {
        return DB::transaction(function () use ($caja, $idUsuario, $fondoInicial) {

            $sesionAnterior = self::sesionActiva($caja->id_caja);
            if ($sesionAnterior) {
                self::_autocerrarSesion($sesionAnterior, $idUsuario);
                Log::warning('CajaService: auto-cierre de sesión anterior', [
                    'id_sesion'   => $sesionAnterior->id_sesion,
                    'id_caja'     => $caja->id_caja,
                    'cerrada_por' => $idUsuario,
                ]);
            }

            $sesion = CajaSesion::create([
                'id_sesion'           => self::generarId('SES'),
                'id_caja'             => $caja->id_caja,
                'id_negocio'          => $caja->id_negocio,
                'id_usuario_apertura' => $idUsuario,
                'fondo_inicial'       => $fondoInicial,
                'estado'              => 'abierta',
                'abierta_at'          => now(),
            ]);

            self::_registrarMovimiento($sesion, $idUsuario, [
                'tipo'        => 'apertura',
                'monto'       => $fondoInicial,
                'es_entrada'  => true,
                'concepto'    => 'Fondo inicial de apertura',
            ]);

            return $sesion;
        });
    }

    /* ══════════════════════════════════════════════════════════
     | SESIÓN — cierre
     ══════════════════════════════════════════════════════════ */

    public static function cerrarSesion(
        CajaSesion $sesion,
        string $idUsuario,
        ?float $montoDeclarado,
        string $motivo = 'vendedor',
        ?string $notas = null
    ): CajaCorte {
        return DB::transaction(function () use ($sesion, $idUsuario, $montoDeclarado, $motivo, $notas) {

            $snapshot     = self::calcularSnapshot($sesion, 'cierre');
            $montoSistema = $snapshot['totales']['total_sistema'];
            $declarado    = $montoDeclarado ?? $montoSistema;
            $diferencia   = round($montoSistema - $declarado, 2);

            $sesion->update([
                'id_usuario_cierre'      => $idUsuario,
                'monto_cierre_declarado' => $declarado,
                'monto_cierre_sistema'   => $montoSistema,
                'diferencia'             => $diferencia,
                'estado'                 => 'cerrada',
                'motivo_cierre'          => $motivo,
                'notas_cierre'           => $notas,
                'cerrada_at'             => now(),
            ]);

            $corte = CajaCorte::create([
                'id_corte'   => self::generarId('COR'),
                'id_sesion'  => $sesion->id_sesion,
                'id_negocio' => $sesion->id_negocio,
                'id_usuario' => $idUsuario,
                'snapshot'   => $snapshot,
            ]);

            Log::info('CajaService: sesión cerrada', [
                'id_sesion'  => $sesion->id_sesion,
                'motivo'     => $motivo,
                'diferencia' => $diferencia,
                'por'        => $idUsuario,
            ]);

            return $corte;
        });
    }

    /* ══════════════════════════════════════════════════════════
     | CORTE PARCIAL
     ══════════════════════════════════════════════════════════ */

    public static function corteParcial(CajaSesion $sesion, string $idUsuario): CajaCorte
    {
        $snapshot = self::calcularSnapshot($sesion, 'parcial');

        $corte = CajaCorte::create([
            'id_corte'   => self::generarId('COR'),
            'id_sesion'  => $sesion->id_sesion,
            'id_negocio' => $sesion->id_negocio,
            'id_usuario' => $idUsuario,
            'snapshot'   => $snapshot,
        ]);

        Log::info('CajaService: corte parcial', [
            'id_sesion' => $sesion->id_sesion,
            'id_corte'  => $corte->id_corte,
            'total'     => $snapshot['totales']['total_sistema'],
            'por'       => $idUsuario,
        ]);

        return $corte;
    }

    /* ══════════════════════════════════════════════════════════
     | MOVIMIENTOS
     ══════════════════════════════════════════════════════════ */

    public static function registrarIngreso(
        CajaSesion $sesion,
        string $idUsuario,
        float $monto,
        ?string $metodo,
        ?string $metodoLabel,
        bool $esEfectivo,
        string $concepto,
        ?string $referencia = null
    ): CajaMovimiento {
        return self::_registrarMovimiento($sesion, $idUsuario, [
            'tipo'         => 'ingreso_manual',
            'monto'        => $monto,
            'es_entrada'   => true,
            'metodo'       => $metodo,
            'metodo_label' => $metodoLabel,
            'es_efectivo'  => $esEfectivo,
            'concepto'     => $concepto,
            'referencia'   => $referencia,
        ]);
    }

    public static function registrarRetiro(
        CajaSesion $sesion,
        string $idUsuario,
        float $monto,
        string $concepto,
        ?string $referencia = null
    ): CajaMovimiento {
        return self::_registrarMovimiento($sesion, $idUsuario, [
            'tipo'        => 'retiro',
            'monto'       => $monto,
            'es_entrada'  => false,
            'concepto'    => $concepto,
            'referencia'  => $referencia,
        ]);
    }

    public static function registrarAjuste(
        CajaSesion $sesion,
        string $idUsuario,
        float $monto,
        bool $esEntrada,
        string $concepto
    ): CajaMovimiento {
        return self::_registrarMovimiento($sesion, $idUsuario, [
            'tipo'        => 'ajuste',
            'monto'       => abs($monto),
            'es_entrada'  => $esEntrada,
            'concepto'    => $concepto,
        ]);
    }

    /* ══════════════════════════════════════════════════════════
     | INTEGRACIÓN VENTAS
     ══════════════════════════════════════════════════════════ */

    public static function registrarVenta(
        Venta $venta,
        string $idUsuario,
        string $idNegocio
    ): ?array {
        $caja = self::cajaDeUsuario($idUsuario, $idNegocio);
        if (!$caja) {
            Log::warning('CajaService: venta sin caja configurada', [
                'id_venta'   => $venta->id_venta,
                'id_usuario' => $idUsuario,
            ]);
            return null;
        }

        $sesion = self::sesionActiva($caja->id_caja);
        if (!$sesion) {
            Log::info('CajaService: venta sin sesión activa — no se registra en caja', [
                'id_venta' => $venta->id_venta,
                'id_caja'  => $caja->id_caja,
            ]);
            return null;
        }

        $pagos = VentaPago::where('id_venta', $venta->id_venta)->get();

        foreach ($pagos as $pago) {
            self::_registrarMovimiento($sesion, $idUsuario, [
                'tipo'         => 'venta',
                'monto'        => $pago->monto,
                'es_entrada'   => true,
                'id_venta'     => $venta->id_venta,
                'metodo'       => $pago->metodo,
                'metodo_label' => $pago->label,
                'es_efectivo'  => $pago->es_efectivo,
                'concepto'     => 'Venta ' . $venta->id_venta,
                'referencia'   => $pago->referencia,
            ]);
        }

        return self::calcularSnapshot($sesion, 'parcial');
    }

    /* ══════════════════════════════════════════════════════════
     | CONSULTAS
     ══════════════════════════════════════════════════════════ */

    public static function sesionActiva(string $idCaja): ?CajaSesion
    {
        return CajaSesion::where('id_caja', $idCaja)
            ->where('estado', 'abierta')
            ->latest('abierta_at')
            ->first();
    }

    public static function calcularSnapshot(CajaSesion $sesion, string $tipo): array
    {
        $movimientos = CajaMovimiento::where('id_sesion', $sesion->id_sesion)->get();

        $ventasTotal   = $movimientos->where('tipo', 'venta')->where('es_entrada', true)->sum('monto');
        $ingresosTotal = $movimientos->where('tipo', 'ingreso_manual')->where('es_entrada', true)->sum('monto');
        $retirosTotal  = $movimientos->where('tipo', 'retiro')->where('es_entrada', false)->sum('monto');
        $ajustesPos    = $movimientos->where('tipo', 'ajuste')->where('es_entrada', true)->sum('monto');
        $ajustesNeg    = $movimientos->where('tipo', 'ajuste')->where('es_entrada', false)->sum('monto');
        $ajustesNeto   = round($ajustesPos - $ajustesNeg, 2);

        $totalSistema = round(
            $sesion->fondo_inicial + $ventasTotal + $ingresosTotal - $retirosTotal + $ajustesNeto,
            2
        );

        $porMetodo = $movimientos
            ->whereIn('tipo', ['venta', 'ingreso_manual'])
            ->where('es_entrada', true)
            ->filter(fn($m) => !empty($m->metodo))
            ->groupBy('metodo')
            ->map(function ($grupo) {
                $primero = $grupo->first();
                return [
                    'metodo'      => $primero->metodo,
                    'label'       => $primero->metodo_label ?? ucfirst($primero->metodo),
                    'es_efectivo' => (bool) $primero->es_efectivo,
                    'total'       => round($grupo->sum('monto'), 2),
                ];
            })
            ->values()
            ->toArray();

        $usuario = \App\Models\Usuario::find($sesion->id_usuario_apertura);

        $movimientosDetalle = $movimientos
            ->sortBy('created_at')
            ->values()
            ->map(fn($m) => [
                'tipo'       => $m->tipo,
                'label'      => $m->label_tipo,
                'monto'      => (float) $m->monto,
                'es_entrada' => (bool) $m->es_entrada,
                'concepto'   => $m->concepto,
                'fecha'      => $m->created_at,
            ])
            ->toArray();

        $gastosSesion = CajaGasto::where('id_sesion', $sesion->id_sesion)
            ->orderBy('fecha_gasto')
            ->get();

        $gastosDetalle = $gastosSesion
            ->map(fn($g) => [
                'motivo'     => $g->motivo,
                'monto'      => (float) $g->monto,
                'referencia' => $g->referencia,
                'fecha'      => $g->fecha_gasto,
            ])
            ->values()
            ->toArray();

        return [
            'tipo'   => $tipo,
            'sesion' => [
                'id_sesion'     => $sesion->id_sesion,
                'abierta_at'    => $sesion->abierta_at,
                'fondo_inicial' => (float) $sesion->fondo_inicial,
            ],
            'totales' => [
                'ingresos_ventas'   => round($ventasTotal, 2),
                'ingresos_manuales' => round($ingresosTotal, 2),
                'retiros'           => round($retirosTotal, 2),
                'ajustes_neto'      => $ajustesNeto,
                'total_sistema'     => $totalSistema,
            ],
            'por_metodo'   => $porMetodo,
            'ventas_count' => $movimientos->where('tipo', 'venta')
                ->pluck('id_venta')->unique()->count(),
            'movimientos'  => $movimientosDetalle,
            'gastos'       => $gastosDetalle,
            'gastos_total' => (float) $gastosSesion->sum('monto'),
            'corte_at'     => now()->toISOString(),
            'usuario'      => [
                'id_usuario'     => $sesion->id_usuario_apertura,
                'nombre_usuario' => $usuario?->nombre_usuario ?? '—',
            ],
        ];
    }

    /* ══════════════════════════════════════════════════════════
     | PRIVADOS
     ══════════════════════════════════════════════════════════ */

    private static function _registrarMovimiento(
        CajaSesion $sesion,
        string $idUsuario,
        array $datos
    ): CajaMovimiento {
        $usuario = \App\Models\Usuario::find($idUsuario);

        return CajaMovimiento::create([
            'id_movimiento' => self::generarId('MOV'),
            'id_sesion'     => $sesion->id_sesion,
            'id_negocio'    => $sesion->id_negocio,
            'id_usuario'    => $idUsuario,
            'id_venta'      => $datos['id_venta']      ?? null,
            'metodo'        => $datos['metodo']        ?? null,
            'metodo_label'  => $datos['metodo_label']  ?? null,
            'es_efectivo'   => $datos['es_efectivo']   ?? false,
            'tipo'          => $datos['tipo'],
            'monto'         => $datos['monto'],
            'es_entrada'    => $datos['es_entrada'],
            'concepto'      => $datos['concepto']      ?? null,
            'referencia'    => $datos['referencia']    ?? null,
            'origen_rol'    => $usuario?->id_rol       ?? 2,
        ]);
    }

    private static function _autocerrarSesion(
        CajaSesion $sesion,
        string $idUsuario
    ): void {
        $snapshot     = self::calcularSnapshot($sesion, 'cierre');
        $montoSistema = $snapshot['totales']['total_sistema'];

        $sesion->update([
            'id_usuario_cierre'      => $idUsuario,
            'monto_cierre_sistema'   => $montoSistema,
            'monto_cierre_declarado' => null,
            'diferencia'             => null,
            'estado'                 => 'auto_cerrada',
            'motivo_cierre'          => 'sistema',
            'notas_cierre'           => 'Cierre automático por apertura de nueva sesión',
            'cerrada_at'             => now(),
        ]);

        CajaCorte::create([
            'id_corte'   => self::generarId('COR'),
            'id_sesion'  => $sesion->id_sesion,
            'id_negocio' => $sesion->id_negocio,
            'id_usuario' => $idUsuario,
            'snapshot'   => $snapshot,
        ]);
    }

    public static function registrarGasto(
        string $idNegocio,
        string $idUsuario,          // sucursal
        string $idUsuarioRegistro,  // quién lo captura
        float  $monto,
        string $motivo,
        ?string $referencia = null,
        ?string $notas = null,
        ?string $fechaGasto = null,
        ?string $idSesion = null,
    ): array {
        $fecha = $fechaGasto ?? now()->toDateString();

        // Límite único de la sucursal, configurado por el admin
        $limiteConfig = \App\Models\GastoLimite::where('id_negocio', $idNegocio)
            ->where('id_usuario', $idUsuario)
            ->value('limite_semanal');

        $gasto = \App\Models\CajaGasto::create([
            'id_sesion'            => $idSesion,
            'id_negocio'           => $idNegocio,
            'id_usuario'           => $idUsuario,
            'motivo'               => $motivo,
            'monto'                => $monto,
            'limite'               => $limiteConfig, // snapshot histórico del límite vigente al momento del gasto
            'referencia'           => $referencia,
            'notas'                => $notas,
            'fecha_gasto'          => $fecha,
            'id_usuario_registro'  => $idUsuarioRegistro,
        ]);

        \App\Services\CatalogService::incrementVersion($idNegocio);

        $excedeLimite  = false;
        $totalSemana   = 0.0;

        if ($limiteConfig !== null) {
            // Suma TODOS los gastos de la sucursal en la semana en curso (lunes–domingo), sin importar motivo
            $totalSemana = (float) \App\Models\CajaGasto::where('id_negocio', $idNegocio)
                ->where('id_usuario', $idUsuario)
                ->whereBetween('fecha_gasto', [now()->startOfWeek()->toDateString(), now()->endOfWeek()->toDateString()])
                ->sum('monto');

            $excedeLimite = $totalSemana > $limiteConfig;
        }

        return [
            'gasto'         => $gasto,
            'excede_limite' => $excedeLimite,
            'total_semana'  => $totalSemana,
            'limite'        => $limiteConfig !== null ? (float) $limiteConfig : null,
        ];
    }


    public static function setLimiteGasto(
        string $idNegocio,
        string $idUsuario,
        float  $limiteSemanal,
        string $idUsuarioAdmin,
    ): \App\Models\GastoLimite {
        return \App\Models\GastoLimite::updateOrCreate(
            [
                'id_negocio' => $idNegocio,
                'id_usuario' => $idUsuario,
            ],
            [
                'limite_semanal'   => $limiteSemanal,
                'id_usuario_admin' => $idUsuarioAdmin,
            ]
        );
    }

    
    public static function eliminarLimiteGasto(string $idNegocio, string $idUsuario): void
    {
        \App\Models\GastoLimite::where('id_negocio', $idNegocio)
            ->where('id_usuario', $idUsuario)
            ->delete();
    }

    
    public static function getLimiteSucursal(string $idNegocio, string $idUsuario): ?\App\Models\GastoLimite
    {
        return \App\Models\GastoLimite::where('id_negocio', $idNegocio)
            ->where('id_usuario', $idUsuario)
            ->first();
    }

    
    public static function gastosPorSucursal(string $idNegocio, string $desde, string $hasta): \Illuminate\Support\Collection
    {
        return \App\Models\CajaGasto::where('id_negocio', $idNegocio)
            ->whereBetween('fecha_gasto', [$desde, $hasta])
            ->selectRaw('id_usuario, SUM(monto) as total_gastos')
            ->groupBy('id_usuario')
            ->pluck('total_gastos', 'id_usuario');
    }

   
    public static function gastosPorMotivo(string $idNegocio, string $idUsuario, string $desde, string $hasta): \Illuminate\Support\Collection
    {
        return \App\Models\CajaGasto::where('id_negocio', $idNegocio)
            ->where('id_usuario', $idUsuario)
            ->whereBetween('fecha_gasto', [$desde, $hasta])
            ->selectRaw('motivo, COUNT(*) as cnt, SUM(monto) as total')
            ->groupBy('motivo')
            ->orderByDesc('total')
            ->get();
    }

    public static function getGastoSemanaActual(string $idNegocio, string $idUsuario): float
    {
        return (float) \App\Models\CajaGasto::where('id_negocio', $idNegocio)
            ->where('id_usuario', $idUsuario)
            ->whereBetween('fecha_gasto', [now()->startOfWeek()->toDateString(), now()->endOfWeek()->toDateString()])
            ->sum('monto');
    }

    public static function gastosPorMotivoNegocio(string $idNegocio, string $desde, string $hasta): \Illuminate\Support\Collection
    {
        return \App\Models\CajaGasto::where('id_negocio', $idNegocio)
            ->whereBetween('fecha_gasto', [$desde, $hasta])
            ->selectRaw('motivo, COUNT(*) as cnt, SUM(monto) as total')
            ->groupBy('motivo')
            ->orderByDesc('total')
            ->get();
    }
}
