<?php

namespace App\Http\Controllers;

use App\Models\Caja;
use App\Models\CajaSesion;
use App\Models\CajaCorte;
use App\Services\CajaService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CajaController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (auth()->user()?->id_rol !== 2) abort(403);
            return $next($request);
        });
    }

    /* ── INDEX ──────────────────────────────────────────────── */

    public function index()
    {
        $user  = auth()->user();
        $caja  = CajaService::cajaDeUsuario($user->id_usuario, $user->id_negocio);

        if (!$caja) {
            return view('vendedor.caja.sin-caja');
        }

        $sesion   = CajaService::sesionActiva($caja->id_caja);
        $snapshot = $sesion ? CajaService::calcularSnapshot($sesion, 'parcial') : null;

        $historial = CajaSesion::where('id_caja', $caja->id_caja)
            ->orderByDesc('abierta_at')
            ->limit(10)
            ->get();

        // ← NUEVO
        $gastoSemana = CajaService::getGastoSemanaActual($user->id_negocio, $user->id_usuario);
        $limiteGasto = CajaService::getLimiteSucursal($user->id_negocio, $user->id_usuario)?->limite_semanal;

        return view('vendedor.caja.index', compact(
            'caja', 'sesion', 'snapshot', 'historial', 'gastoSemana', 'limiteGasto'
        ));
    }
    /* ── ABRIR ──────────────────────────────────────────────── */

    public function abrir(Request $request)
    {
        $request->validate([
            'fondo_inicial' => 'required|numeric|min:0|max:999999.99',
        ]);

        $user = auth()->user();
        $caja = CajaService::cajaDeUsuario($user->id_usuario, $user->id_negocio);

        if (!$caja) {
            return back()->with('error', 'No tienes una caja asignada. Contacta al administrador.');
        }

        try {
            $sesion = CajaService::abrirSesion(
                $caja,
                $user->id_usuario,
                (float) $request->fondo_inicial
            );

            Log::info('Caja abierta por vendedor', [
                'id_sesion'  => $sesion->id_sesion,
                'id_usuario' => $user->id_usuario,
                'fondo'      => $request->fondo_inicial,
            ]);

            return redirect()
                ->route('caja.index')
                ->with('success', 'Caja abierta con fondo de $' . number_format($request->fondo_inicial, 2) . '.');

        } catch (\Throwable $e) {
            Log::error('Error al abrir caja', [
                'mensaje'    => $e->getMessage(),
                'id_usuario' => $user->id_usuario,
            ]);
            return back()->with('error', 'No se pudo abrir la caja: ' . $e->getMessage());
        }
    }

    /* ── CORTE PARCIAL ──────────────────────────────────────── */

    public function corteParcial()
    {
        $user   = auth()->user();
        $caja   = CajaService::cajaDeUsuario($user->id_usuario, $user->id_negocio);
        $sesion = $caja ? CajaService::sesionActiva($caja->id_caja) : null;

        if (!$sesion) {
            return back()->with('error', 'No hay sesión de caja abierta.');
        }

        try {
            $corte = CajaService::corteParcial($sesion, $user->id_usuario);

            return redirect()
                ->route('caja.corte.pdf', $corte->id_corte)
                ->with('success', 'Corte parcial generado.');

        } catch (\Throwable $e) {
            Log::error('Error en corte parcial', ['mensaje' => $e->getMessage()]);
            return back()->with('error', 'Error al generar el corte: ' . $e->getMessage());
        }
    }

    /* ── CERRAR ─────────────────────────────────────────────── */

    public function cerrar(Request $request)
    {
        $request->validate([
            'monto_declarado' => 'nullable|numeric|min:0|max:9999999.99',
            'notas'           => 'nullable|string|max:500',
        ]);

        $user   = auth()->user();
        $caja   = CajaService::cajaDeUsuario($user->id_usuario, $user->id_negocio);
        $sesion = $caja ? CajaService::sesionActiva($caja->id_caja) : null;

        if (!$sesion) {
            return back()->with('error', 'No hay sesión de caja abierta.');
        }

        try {
            $corte = CajaService::cerrarSesion(
                sesion:         $sesion,
                idUsuario:      $user->id_usuario,
                montoDeclarado: $request->filled('monto_declarado')
                                    ? (float) $request->monto_declarado
                                    : null,
                motivo:         'vendedor',
                notas:          $request->notas,
            );

            Log::info('Caja cerrada por vendedor', [
                'id_sesion'  => $sesion->id_sesion,
                'id_usuario' => $user->id_usuario,
            ]);

            return redirect()
                ->route('caja.corte.pdf', $corte->id_corte)
                ->with('success', 'Caja cerrada correctamente.');

        } catch (\Throwable $e) {
            Log::error('Error al cerrar caja', ['mensaje' => $e->getMessage()]);
            return back()->with('error', 'Error al cerrar la caja: ' . $e->getMessage());
        }
    }

    /* ── PDF ────────────────────────────────────────────────── */

    public function cortePdf(string $idCorte)
    {
        $user  = auth()->user();

        $corte = CajaCorte::with(['sesion.caja', 'usuario'])
            ->where('id_negocio', $user->id_negocio)
            ->findOrFail($idCorte);

        if ($corte->sesion->caja->id_usuario !== $user->id_usuario) {
            abort(403);
        }

        $pdf = Pdf::loadView('vendedor.caja.corte-pdf', [
            'corte'    => $corte,
            'sesion'   => $corte->sesion,
            'snapshot' => $corte->snapshot,
            'fecha'    => now()->format('d/m/Y H:i'),
        ])->setPaper([0, 0, 226.77, 800], 'portrait');

        return response()->make($pdf->output(), 200, [
            'Content-Type'        => 'application/pdf',
            'Content-Disposition' => 'inline; filename="corte-' . $idCorte . '.pdf"',
        ]);
    }

    public function ingreso(Request $request)
    {
        $request->validate([
            'monto'      => 'required|numeric|min:0.01|max:999999.99',
            'metodo'     => 'required|string|max:50',
            'concepto'   => 'required|string|max:200',
            'referencia' => 'nullable|string|max:120',
        ]);

        $user   = auth()->user();
        $caja   = CajaService::cajaDeUsuario($user->id_usuario, $user->id_negocio);
        $sesion = $caja ? CajaService::sesionActiva($caja->id_caja) : null;

        if (!$sesion) {
            return back()->with('error', 'No hay sesión de caja abierta.');
        }

        $metodosEfectivo = ['efectivo'];
        $metodo = $request->metodo;
        $esEfectivo = in_array($metodo, $metodosEfectivo);

        try {
            CajaService::registrarIngreso(
                sesion:       $sesion,
                idUsuario:    $user->id_usuario,
                monto:        (float) $request->monto,
                metodo:       $metodo,
                metodoLabel:  ucfirst($metodo),
                esEfectivo:   $esEfectivo,
                concepto:     $request->concepto,
                referencia:   $request->referencia,
            );

            return back()->with('success', 'Ingreso registrado correctamente.');
        } catch (\Throwable $e) {
            return back()->with('error', 'Error al registrar ingreso: ' . $e->getMessage());
        }
    }

    public function gasto(Request $request)
    {
        $request->validate([
            'monto'      => 'required|numeric|min:0.01|max:999999.99',
            'motivo'     => 'required|string|max:100',
            'referencia' => 'nullable|string|max:120',
            'notas'      => 'nullable|string|max:500',
            'fecha_gasto'=> 'nullable|date',
        ]);

        $user   = auth()->user();
        $caja   = CajaService::cajaDeUsuario($user->id_usuario, $user->id_negocio);
        $sesion = $caja ? CajaService::sesionActiva($caja->id_caja) : null;

        try {
            $resultado = CajaService::registrarGasto(
                idNegocio:         $user->id_negocio,
                idUsuario:         $user->id_usuario,
                idUsuarioRegistro: $user->id_usuario,
                monto:             (float) $request->monto,
                motivo:            $request->motivo,
                referencia:        $request->referencia,
                notas:             $request->notas,
                fechaGasto:        $request->fecha_gasto,
                idSesion:          $sesion?->id_sesion,
            );

            if ($resultado['excede_limite']) {
                return back()->with('success', 'Gasto registrado correctamente.')
                    ->with('limite_excedido', true)
                    ->with('limite_excedido_data', [
                        'total_semana' => $resultado['total_semana'],
                        'limite'       => $resultado['limite'],
                    ]);
            }

            return back()->with('success', 'Gasto registrado correctamente.');
        } catch (\Throwable $e) {
            Log::error('Error al registrar gasto', ['mensaje' => $e->getMessage()]);
            return back()->with('error', 'Error al registrar gasto: ' . $e->getMessage());
        }
    }
}