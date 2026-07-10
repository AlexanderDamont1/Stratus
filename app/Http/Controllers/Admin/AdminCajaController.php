<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Caja;
use App\Models\CajaSesion;
use App\Models\CajaCorte;
use App\Models\Usuario;
use App\Services\CajaService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AdminCajaController extends Controller
{
    public function __construct()
    {
        $this->middleware('administrador');
    }

    /* ── INDEX ──────────────────────────────────────────────── */
    public function index()
    {
        $user = auth()->user();

        $sucursales = Usuario::where('id_negocio', $user->id_negocio)
            ->where('id_rol', 2)
            ->orderBy('nombre_usuario')
            ->get();

        $cajas = Caja::with(['sesionActiva'])
            ->where('id_negocio', $user->id_negocio)
            ->get()
            ->keyBy('id_usuario');

        $snapshots     = collect();
        $gastosSemana  = collect();
        $limitesConfig = collect();
        $sucSinCaja    = 0;

        foreach ($sucursales as $suc) {
            $caja = $cajas->get($suc->id_usuario);

            if (!$caja) {
                $sucSinCaja++;
            } elseif ($caja->sesionActiva) {
                $snapshots[$suc->id_usuario] = CajaService::calcularSnapshot($caja->sesionActiva, 'parcial');
            }

            $gastosSemana[$suc->id_usuario]  = CajaService::getGastoSemanaActual($user->id_negocio, $suc->id_usuario);
            $limitesConfig[$suc->id_usuario] = CajaService::getLimiteSucursal($user->id_negocio, $suc->id_usuario)?->limite_semanal;
        }

        return view('administrador.cajas.index', compact(
            'sucursales', 'cajas', 'snapshots', 'sucSinCaja', 'gastosSemana', 'limitesConfig'
        ));
    }

    /* ── SHOW ───────────────────────────────────────────────── */

    public function show(string $idUsuario)
    {
        $user = auth()->user();

        $sucursal = Usuario::where('id_negocio', $user->id_negocio)
            ->where('id_usuario', $idUsuario)
            ->where('id_rol', 2)
            ->firstOrFail();

        $caja = Caja::where('id_negocio', $user->id_negocio)
            ->where('id_usuario', $idUsuario)
            ->first();

        $sesion   = $caja ? CajaService::sesionActiva($caja->id_caja) : null;
        $snapshot = $sesion ? CajaService::calcularSnapshot($sesion, 'parcial') : null;

        $historial = $caja
            ? CajaSesion::where('id_caja', $caja->id_caja)
                ->orderByDesc('abierta_at')
                ->paginate(20)
            : collect();

        return view('administrador.cajas.show',
            compact('sucursal', 'caja', 'sesion', 'snapshot', 'historial'));
    }

    /* ── STORE — crear caja ─────────────────────────────────── */

    public function store(Request $request)
    {
        $request->validate([
            'id_usuario' => 'required|exists:usuarios,id_usuario',
            'nombre'     => 'nullable|string|max:80',
        ]);

        $user = auth()->user();

        $vendedor = Usuario::where('id_negocio', $user->id_negocio)
            ->where('id_usuario', $request->id_usuario)
            ->where('id_rol', 2)
            ->firstOrFail();

        if (Caja::where('id_negocio', $user->id_negocio)
                ->where('id_usuario', $vendedor->id_usuario)
                ->exists()) {
            return back()->with('error', 'Esta sucursal ya tiene una caja asignada.');
        }

        Caja::create([
            'id_caja'    => CajaService::generarId('CAJ'),
            'id_negocio' => $user->id_negocio,
            'id_usuario' => $vendedor->id_usuario,
            'nombre'     => $request->nombre ?? 'Caja principal',
            'activa'     => true,
        ]);

        Log::info('Admin: caja creada', [
            'id_negocio'  => $user->id_negocio,
            'id_vendedor' => $vendedor->id_usuario,
            'creada_por'  => $user->id_usuario,
        ]);

        return back()->with('success', 'Caja creada para ' . $vendedor->nombre_usuario . '.');
    }

    /* ── RETIRO ─────────────────────────────────────────────── */

    public function retiro(Request $request, string $idUsuario)
    {
        $request->validate([
            'monto'      => 'required|numeric|min:0.01|max:999999.99',
            'concepto'   => 'required|string|max:200',
            'referencia' => 'nullable|string|max:120',
        ]);

        $user   = auth()->user();
        $sesion = $this->_sesionActiva($idUsuario, $user->id_negocio);

        if (!$sesion) {
            return back()->with('error', 'No hay sesión activa en esta caja.');
        }

        try {
            $mov = CajaService::registrarRetiro(
                sesion:     $sesion,
                idUsuario:  $user->id_usuario,
                monto:      (float) $request->monto,
                concepto:   $request->concepto,
                referencia: $request->referencia,
            );

            Log::info('Admin: retiro de caja', [
                'id_movimiento' => $mov->id_movimiento,
                'monto'         => $request->monto,
                'admin'         => $user->id_usuario,
            ]);

            return back()->with('success', 'Retiro de $' . number_format($request->monto, 2) . ' registrado.');

        } catch (\Throwable $e) {
            return back()->with('error', 'Error al registrar el retiro: ' . $e->getMessage());
        }
    }

    /* ── INGRESO ────────────────────────────────────────────── */

    public function ingreso(Request $request, string $idUsuario)
    {
        $request->validate([
            'monto'        => 'required|numeric|min:0.01|max:999999.99',
            'metodo'       => 'nullable|string|max:40',
            'metodo_label' => 'nullable|string|max:80',
            'es_efectivo'  => 'nullable|boolean',
            'concepto'     => 'required|string|max:200',
            'referencia'   => 'nullable|string|max:120',
        ]);

        $user   = auth()->user();
        $sesion = $this->_sesionActiva($idUsuario, $user->id_negocio);

        if (!$sesion) {
            return back()->with('error', 'No hay sesión activa en esta caja.');
        }

        try {
            $mov = CajaService::registrarIngreso(
                sesion:       $sesion,
                idUsuario:    $user->id_usuario,
                monto:        (float) $request->monto,
                metodo:       $request->metodo       ?? null,
                metodoLabel:  $request->metodo_label ?? null,
                esEfectivo:   (bool) ($request->es_efectivo ?? false),
                concepto:     $request->concepto,
                referencia:   $request->referencia,
            );

            Log::info('Admin: ingreso a caja', [
                'id_movimiento' => $mov->id_movimiento,
                'monto'         => $request->monto,
                'admin'         => $user->id_usuario,
            ]);

            return back()->with('success', 'Ingreso de $' . number_format($request->monto, 2) . ' registrado.');

        } catch (\Throwable $e) {
            return back()->with('error', 'Error al registrar el ingreso: ' . $e->getMessage());
        }
    }

    /* ── AJUSTE ─────────────────────────────────────────────── */

    public function ajuste(Request $request, string $idUsuario)
    {
        $request->validate([
            'monto'      => 'required|numeric|min:0.01|max:999999.99',
            'es_entrada' => 'required|boolean',
            'concepto'   => 'required|string|max:200',
        ]);

        $user   = auth()->user();
        $sesion = $this->_sesionActiva($idUsuario, $user->id_negocio);

        if (!$sesion) {
            return back()->with('error', 'No hay sesión activa en esta caja.');
        }

        try {
            $mov = CajaService::registrarAjuste(
                sesion:    $sesion,
                idUsuario: $user->id_usuario,
                monto:     (float) $request->monto,
                esEntrada: (bool) $request->es_entrada,
                concepto:  $request->concepto,
            );

            Log::info('Admin: ajuste contable', [
                'id_movimiento' => $mov->id_movimiento,
                'monto'         => $request->monto,
                'es_entrada'    => $request->es_entrada,
                'admin'         => $user->id_usuario,
            ]);

            return back()->with('success', 'Ajuste registrado correctamente.');

        } catch (\Throwable $e) {
            return back()->with('error', 'Error al registrar el ajuste: ' . $e->getMessage());
        }
    }

    /* ── CIERRE FORZADO ─────────────────────────────────────── */

    public function cerrarForzado(Request $request, string $idUsuario)
    {
        $request->validate([
            'notas' => 'nullable|string|max:500',
        ]);

        $user   = auth()->user();
        $sesion = $this->_sesionActiva($idUsuario, $user->id_negocio);

        if (!$sesion) {
            return back()->with('error', 'No hay sesión activa en esta caja.');
        }

        try {
            $corte = CajaService::cerrarSesion(
                sesion:         $sesion,
                idUsuario:      $user->id_usuario,
                montoDeclarado: null,
                motivo:         'admin',
                notas:          $request->notas ?? 'Cierre forzado por administrador',
            );

            Log::warning('Admin: cierre forzado', [
                'id_sesion' => $sesion->id_sesion,
                'admin'     => $user->id_usuario,
                'sucursal'  => $idUsuario,
            ]);

            return redirect()
                ->route('admin.cajas.corte.pdf', $corte->id_corte)
                ->with('success', 'Sesión cerrada forzosamente.');

        } catch (\Throwable $e) {
            return back()->with('error', 'Error al cerrar la sesión: ' . $e->getMessage());
        }
    }

    /* ── PDF ────────────────────────────────────────────────── */

    public function cortePdf(string $idCorte)
    {
        $user  = auth()->user();

        $corte = CajaCorte::with(['sesion.caja', 'usuario'])
            ->where('id_negocio', $user->id_negocio)
            ->findOrFail($idCorte);

        $pdf = Pdf::loadView('administrador.cajas.corte-pdf', [
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

    /* ── HELPER ─────────────────────────────────────────────── */

    private function _sesionActiva(string $idUsuario, string $idNegocio): ?CajaSesion
    {
        $caja = Caja::where('id_negocio', $idNegocio)
            ->where('id_usuario', $idUsuario)
            ->where('activa', true)
            ->first();

        return $caja ? CajaService::sesionActiva($caja->id_caja) : null;
    }

    public function limites(string $idUsuario)
    {
        $user = auth()->user();

        $limite = CajaService::getLimiteSucursal($user->id_negocio, $idUsuario);

        return response()->json([
            'limite_semanal' => $limite?->limite_semanal,
        ]);
    }

    public function guardarLimite(Request $request, string $idUsuario)
    {
        $request->validate([
            'limite_semanal' => 'required|numeric|min:0|max:999999.99',
        ]);

        $user = auth()->user();

        try {
            CajaService::setLimiteGasto(
                idNegocio:      $user->id_negocio,
                idUsuario:      $idUsuario,
                limiteSemanal:  (float) $request->limite_semanal,
                idUsuarioAdmin: $user->id_usuario,
            );

            return back()->with('success', 'Límite de gasto guardado correctamente.');
        } catch (\Throwable $e) {
            return back()->with('error', 'Error al guardar el límite: ' . $e->getMessage());
        }
    }

    public function eliminarLimite(string $idUsuario)
    {
        $user = auth()->user();

        CajaService::eliminarLimiteGasto($user->id_negocio, $idUsuario);

        return back()->with('success', 'Límite eliminado. Esta sucursal queda sin restricción.');
    }
}