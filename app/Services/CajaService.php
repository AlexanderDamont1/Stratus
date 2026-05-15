<?php

namespace App\Services;

use App\Models\Caja;
use App\Models\CajaSesion;
use App\Models\CajaMovimiento;
use App\Models\CajaCorte;
use App\Models\Venta;
use App\Models\VentaPago;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * CajaService
 *
 * Lógica de negocio para el módulo de caja registradora.
 * Toda operación queda trazada en caja_movimientos (log inmutable).
 *
 * Convenciones:
 *  - monto siempre positivo en BD; es_entrada define la dirección.
 *  - Los IDs siguen el patrón CAJ/SES/MOV/COR + timestamp + random.
 *  - El servicio no lanza excepciones propias: usa los de Laravel/DB.
 */
class CajaService
{
    /* ==========================================================
     | HELPERS — generadores de ID
     ========================================================== */

    public static function generarId(string $prefix): string
    {
        return strtoupper($prefix) . now()->format('ymd') . strtoupper(substr(bin2hex(random_bytes(3)), 0, 6));
    }

    /* ==========================================================
     | CAJA — obtener o crear la caja de una sucursal
     ========================================================== */

    /**
     * Devuelve la caja activa de la sucursal.
     * El admin la crea explícitamente; el vendedor solo la usa.
     */
    public static function cajaDeUsuario(string $idUsuario, string $idNegocio): ?Caja
    {
        return Caja::where('id_negocio', $idNegocio)
            ->where('id_usuario', $idUsuario)
            ->where('activa', true)
            ->first();
    }

    /* ==========================================================
     | SESIÓN — apertura
     ========================================================== */

    /**
     * Abre una nueva sesión de caja.
     *
     * Si hay una sesión abierta anterior, la auto-cierra primero
     * (Ciclo de vida B: la caja nunca bloquea, registra y avisa).
     *
     * @param  Caja   $caja
     * @param  string $idUsuario   Vendedor que abre
     * @param  float  $fondoInicial
     * @return CajaSesion
     */
    public static function abrirSesion(Caja $caja, string $idUsuario, float $fondoInicial): CajaSesion
    {
        return DB::transaction(function () use ($caja, $idUsuario, $fondoInicial) {

            // Auto-cierre de sesión abierta anterior
            $sesionAnterior = self::sesionActiva($caja->id_caja);
            if ($sesionAnterior) {
                self::_autocerrarSesion($sesionAnterior, $idUsuario);

                Log::warning('CajaService: auto-cierre de sesión anterior', [
                    'id_sesion'   => $sesionAnterior->id_sesion,
                    'id_caja'     => $caja->id_caja,
                    'id_negocio'  => $caja->id_negocio,
                    'cerrada_por' => $idUsuario,
                ]);
            }

            // Nueva sesión
            $sesion = CajaSesion::create([
                'id_sesion'           => self::generarId('SES'),
                'id_caja'             => $caja->id_caja,
                'id_negocio'          => $caja->id_negocio,
                'id_usuario_apertura' => $idUsuario,
                'fondo_inicial'       => $fondoInicial,
                'estado'              => 'abierta',
                'abierta_at'          => now(),
            ]);

            // Movimiento de apertura (trazabilidad)
            self::_registrarMovimiento($sesion, $idUsuario, [
                'tipo'      => 'apertura',
                'monto'     => $fondoInicial,
                'es_entrada'=> true,
                'concepto'  => 'Fondo inicial de apertura',
            ]);

            return $sesion;
        });
    }

    /* ==========================================================
     | SESIÓN — cierre
     ========================================================== */

    /**
     * Cierra la sesión activa.
     * Calcula diferencia: sistema - declarado.
     * Genera el snapshot final de cierre.
     *
     * @param  CajaSesion $sesion
     * @param  string     $idUsuario          Quién cierra
     * @param  float|null $montoDeclarado     Lo que el operador dice que hay
     * @param  string     $motivo             'vendedor' | 'admin'
     * @param  string|null $notas
     * @return CajaCorte  El corte de cierre con el PDF snapshot
     */
    public static function cerrarSesion(
        CajaSesion $sesion,
        string $idUsuario,
        ?float $montoDeclarado,
        string $motivo = 'vendedor',
        ?string $notas = null
    ): CajaCorte {
        return DB::transaction(function () use ($sesion, $idUsuario, $montoDeclarado, $motivo, $notas) {

            $snapshot       = self::calcularSnapshot($sesion, 'cierre');
            $montoSistema   = $snapshot['totales']['total_sistema'];
            $declarado      = $montoDeclarado ?? $montoSistema;
            $diferencia     = round($montoSistema - $declarado, 2);

            $sesion->update([
                'id_usuario_cierre'       => $idUsuario,
                'monto_cierre_declarado'  => $declarado,
                'monto_cierre_sistema'    => $montoSistema,
                'diferencia'              => $diferencia,
                'estado'                  => 'cerrada',
                'motivo_cierre'           => $motivo,
                'notas_cierre'            => $notas,
                'cerrada_at'              => now(),
            ]);

            // Corte final (tipo 'cierre') — snapshot inmutable para PDF
            $corte = CajaCorte::create([
                'id_corte'   => self::generarId('COR'),
                'id_sesion'  => $sesion->id_sesion,
                'id_negocio' => $sesion->id_negocio,
                'id_usuario' => $idUsuario,
                'snapshot'   => $snapshot,
            ]);

            Log::info('CajaService: sesión cerrada', [
                'id_sesion'   => $sesion->id_sesion,
                'motivo'      => $motivo,
                'diferencia'  => $diferencia,
                'cerrada_por' => $idUsuario,
            ]);

            return $corte;
        });
    }

    /* ==========================================================
     | CORTE PARCIAL — snapshot sin cerrar
     ========================================================== */

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
            'id_sesion'   => $sesion->id_sesion,
            'id_corte'    => $corte->id_corte,
            'total'       => $snapshot['totales']['total_sistema'],
            'por'         => $idUsuario,
        ]);

        return $corte;
    }

    /* ==========================================================
     | MOVIMIENTOS — ingreso manual y retiro
     ========================================================== */

    /**
     * Registra un ingreso manual (solo vendedor o admin, según política).
     */
    public static function registrarIngreso(
        CajaSesion $sesion,
        string $idUsuario,
        float $monto,
        ?string $idMetodo,
        string $concepto,
        ?string $referencia = null
    ): CajaMovimiento {
        return self::_registrarMovimiento($sesion, $idUsuario, [
            'tipo'       => 'ingreso_manual',
            'monto'      => $monto,
            'es_entrada' => true,
            'id_metodo'  => $idMetodo,
            'concepto'   => $concepto,
            'referencia' => $referencia,
        ]);
    }

    /**
     * Registra un retiro de efectivo (solo admin).
     */
    public static function registrarRetiro(
        CajaSesion $sesion,
        string $idUsuario,
        float $monto,
        string $concepto,
        ?string $referencia = null
    ): CajaMovimiento {
        return self::_registrarMovimiento($sesion, $idUsuario, [
            'tipo'       => 'retiro',
            'monto'      => $monto,
            'es_entrada' => false,
            'concepto'   => $concepto,
            'referencia' => $referencia,
        ]);
    }

    /**
     * Registra un ajuste contable (solo admin, con justificación obligatoria).
     */
    public static function registrarAjuste(
        CajaSesion $sesion,
        string $idUsuario,
        float $monto,
        bool $esEntrada,
        string $concepto
    ): CajaMovimiento {
        return self::_registrarMovimiento($sesion, $idUsuario, [
            'tipo'       => 'ajuste',
            'monto'      => abs($monto),
            'es_entrada' => $esEntrada,
            'concepto'   => $concepto,
        ]);
    }

    /* ==========================================================
     | INTEGRACIÓN CON VENTAS
     | Llamar desde VentaController::store() post-commit
     ========================================================== */

    /**
     * Registra los pagos de una venta como movimientos de caja.
     * Crea un movimiento por cada VentaPago (desglosado por método).
     * Si no hay sesión activa para la sucursal, no falla — solo loguea.
     * (Ciclo de vida B: ventas siempre se registran, caja puede no estar abierta)
     */
    public static function registrarVenta(Venta $venta, string $idUsuario, string $idNegocio): void
    {
        $caja = self::cajaDeUsuario($idUsuario, $idNegocio);
        if (!$caja) {
            Log::warning('CajaService: venta sin caja configurada', [
                'id_venta'   => $venta->id_venta,
                'id_usuario' => $idUsuario,
            ]);
            return;
        }

        $sesion = self::sesionActiva($caja->id_caja);
        if (!$sesion) {
            Log::info('CajaService: venta sin sesión activa — no se registra en caja', [
                'id_venta'  => $venta->id_venta,
                'id_caja'   => $caja->id_caja,
            ]);
            return;
        }

        $pagos = VentaPago::where('id_venta', $venta->id_venta)->get();

        foreach ($pagos as $pago) {
            self::_registrarMovimiento($sesion, $idUsuario, [
                'tipo'       => 'venta',
                'monto'      => $pago->monto,
                'es_entrada' => true,
                'id_venta'   => $venta->id_venta,
                'id_metodo'  => $pago->id_metodo,
                'concepto'   => 'Venta ' . $venta->id_venta,
                'referencia' => $pago->referencia,
            ]);
        }
    }

    /* ==========================================================
     | CONSULTAS
     ========================================================== */

    public static function sesionActiva(string $idCaja): ?CajaSesion
    {
        return CajaSesion::where('id_caja', $idCaja)
            ->where('estado', 'abierta')
            ->latest('abierta_at')
            ->first();
    }

    /**
     * Calcula el snapshot de una sesión para corte parcial o cierre.
     * Agrupa movimientos por tipo y por método de pago.
     */
    public static function calcularSnapshot(CajaSesion $sesion, string $tipo): array
    {
        $movimientos = CajaMovimiento::with('metodo')
            ->where('id_sesion', $sesion->id_sesion)
            ->get();

        $ventasTotal   = $movimientos->where('tipo', 'venta')->where('es_entrada', true)->sum('monto');
        $ingresosTotal = $movimientos->where('tipo', 'ingreso_manual')->where('es_entrada', true)->sum('monto');
        $retirosTotal  = $movimientos->where('tipo', 'retiro')->where('es_entrada', false)->sum('monto');
        $ajustesPos    = $movimientos->where('tipo', 'ajuste')->where('es_entrada', true)->sum('monto');
        $ajustesNeg    = $movimientos->where('tipo', 'ajuste')->where('es_entrada', false)->sum('monto');
        $ajustesNeto   = round($ajustesPos - $ajustesNeg, 2);

        $totalSistema  = round(
            $sesion->fondo_inicial + $ventasTotal + $ingresosTotal - $retirosTotal + $ajustesNeto,
            2
        );

        // Desglose por método de pago (solo movimientos de tipo venta e ingreso_manual)
        $porMetodo = $movimientos
            ->whereIn('tipo', ['venta', 'ingreso_manual'])
            ->where('es_entrada', true)
            ->groupBy('id_metodo')
            ->map(function ($grupo) {
                $metodo = $grupo->first()->metodo;
                return [
                    'id_metodo'  => $grupo->first()->id_metodo,
                    'nombre'     => $metodo?->nombre ?? 'Sin método',
                    'es_efectivo'=> (bool) ($metodo?->es_efectivo ?? false),
                    'total'      => round($grupo->sum('monto'), 2),
                ];
            })
            ->values()
            ->toArray();

        $usuario = \App\Models\Usuario::find($sesion->id_usuario_apertura);

        return [
            'tipo'       => $tipo,
            'sesion'     => [
                'id_sesion'    => $sesion->id_sesion,
                'abierta_at'   => $sesion->abierta_at,
                'fondo_inicial'=> (float) $sesion->fondo_inicial,
            ],
            'totales'    => [
                'ingresos_ventas'   => round($ventasTotal, 2),
                'ingresos_manuales' => round($ingresosTotal, 2),
                'retiros'           => round($retirosTotal, 2),
                'ajustes_neto'      => $ajustesNeto,
                'total_sistema'     => $totalSistema,
            ],
            'por_metodo'    => $porMetodo,
            'ventas_count'  => $movimientos->where('tipo', 'venta')->pluck('id_venta')->unique()->count(),
            'corte_at'      => now()->toISOString(),
            'usuario'       => [
                'id_usuario'     => $sesion->id_usuario_apertura,
                'nombre_usuario' => $usuario?->nombre_usuario ?? '—',
            ],
        ];
    }

    /* ==========================================================
     | PRIVADOS
     ========================================================== */

    private static function _registrarMovimiento(CajaSesion $sesion, string $idUsuario, array $datos): CajaMovimiento
    {
        $usuario = \App\Models\Usuario::find($idUsuario);

        return CajaMovimiento::create([
            'id_movimiento' => self::generarId('MOV'),
            'id_sesion'     => $sesion->id_sesion,
            'id_negocio'    => $sesion->id_negocio,
            'id_usuario'    => $idUsuario,
            'id_venta'      => $datos['id_venta']  ?? null,
            'id_metodo'     => $datos['id_metodo'] ?? null,
            'tipo'          => $datos['tipo'],
            'monto'         => $datos['monto'],
            'es_entrada'    => $datos['es_entrada'],
            'concepto'      => $datos['concepto']   ?? null,
            'referencia'    => $datos['referencia'] ?? null,
            'origen_rol'    => $usuario?->id_rol ?? 2,
        ]);
    }

    private static function _autocerrarSesion(CajaSesion $sesion, string $idUsuario): void
    {
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
}