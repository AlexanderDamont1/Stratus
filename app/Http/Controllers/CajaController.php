<?php

namespace App\Http\Controllers;

use App\Models\Caja;
use App\Models\CajaSesion;
use App\Models\CajaCorte;
use App\Services\CajaService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * CajaController (vendedor — rol 2)
 *
 * Operaciones permitidas al vendedor:
 *   - Ver estado actual de su caja
 *   - Abrir sesión (con fondo inicial)
 *   - Corte parcial (libre, sin restricción)
 *   - Cierre de sesión (libre — sin flujo de autorización, solo log)
 *
 * Operaciones vedadas (ver AdminCajaController):
 *   - Retiros
 *   - Ajustes
 *   - Anular / editar movimientos
 */
class CajaController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (auth()->user()?->id_rol !== 2) {
                abort(403);
            }
            return $next($request);
        });
    }

    /* ----------------------------------------------------------
     | INDEX — estado actual de la caja
     ---------------------------------------------------------- */

    public function index()
    {
        $user  = auth()->user();
        $caja  = CajaService::cajaDeUsuario($user->id_usuario, $user->id_negocio);

        if (!$caja) {
            // El admin aún no ha creado la caja para esta sucursal
            return view('vendedor.caja.sin-caja');
        }

        $sesion    = CajaService::sesionActiva($caja->id_caja);
        $snapshot  = $sesion ? CajaService::calcularSnapshot($sesion, 'parcial') : null;

        // Historial de las últimas 10 sesiones para referencia
        $historial = CajaSesion::where('id_caja', $caja->id_caja)
            ->orderByDesc('abierta_at')
            ->limit(10)
            ->get();

        return view('vendedor.caja.index', compact('caja', 'sesion', 'snapshot', 'historial'));
    }

    /* ----------------------------------------------------------
     | ABRIR SESIÓN
     ---------------------------------------------------------- */

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
                'id_negocio' => $user->id_negocio,
                'fondo'      => $request->fondo_inicial,
            ]);

            return redirect()
                ->route('caja.index')
                ->with('success', 'Caja abierta correctamente con fondo de $' . number_format($request->fondo_inicial, 2) . '.');

        } catch (\Throwable $e) {
            Log::error('Error al abrir caja', [
                'mensaje'    => $e->getMessage(),
                'id_usuario' => $user->id_usuario,
            ]);
            return back()->with('error', 'No se pudo abrir la caja: ' . $e->getMessage());
        }
    }

    /* ----------------------------------------------------------
     | CORTE PARCIAL — snapshot sin cerrar
     ---------------------------------------------------------- */

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

    /* ----------------------------------------------------------
     | CIERRE DE SESIÓN
     ---------------------------------------------------------- */

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
                sesion:          $sesion,
                idUsuario:       $user->id_usuario,
                montoDeclarado:  $request->filled('monto_declarado') ? (float) $request->monto_declarado : null,
                motivo:          'vendedor',
                notas:           $request->notas,
            );

            Log::info('Caja cerrada por vendedor', [
                'id_sesion'   => $sesion->id_sesion,
                'id_usuario'  => $user->id_usuario,
                'diferencia'  => $sesion->fresh()->diferencia,
            ]);

            // Redirige directo al PDF del corte de cierre
            return redirect()
                ->route('caja.corte.pdf', $corte->id_corte)
                ->with('success', 'Caja cerrada correctamente.');

        } catch (\Throwable $e) {
            Log::error('Error al cerrar caja', ['mensaje' => $e->getMessage()]);
            return back()->with('error', 'Error al cerrar la caja: ' . $e->getMessage());
        }
    }

    /* ----------------------------------------------------------
     | PDF — corte (parcial o cierre)
     ---------------------------------------------------------- */

    public function cortePdf(string $idCorte)
    {
        $user  = auth()->user();

        $corte = CajaCorte::with(['sesion.caja', 'usuario'])
            ->where('id_negocio', $user->id_negocio)
            ->findOrFail($idCorte);

        // Verificar que el corte pertenece a la sucursal del vendedor
        if ($corte->sesion->caja->id_usuario !== $user->id_usuario) {
            abort(403);
        }

        $pdf = Pdf::loadView('vendedor.caja.corte-pdf', [
            'corte'   => $corte,
            'sesion'  => $corte->sesion,
            'negocio' => $corte->sesion->caja->negocio ?? null,
            'snapshot'=> $corte->snapshot,
            'fecha'   => now()->format('d/m/Y H:i'),
        ])->setPaper([0, 0, 226.77, 800], 'portrait'); // ~80mm ticket

        return response()->make($pdf->output(), 200, [
            'Content-Type'        => 'application/pdf',
            'Content-Disposition' => 'inline; filename="corte-' . $idCorte . '.pdf"',
        ]);
    }
}