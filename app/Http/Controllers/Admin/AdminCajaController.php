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

/**
 * AdminCajaController (administrador — rol 1)
 *
 * El admin gestiona cajas de TODAS sus sucursales.
 * Operaciones exclusivas del admin:
 *   - Crear / activar / desactivar cajas por sucursal
 *   - Retiros de efectivo
 *   - Ajustes contables
 *   - Cierre forzado de sesión abierta
 *   - Vista consolidada de todas las sucursales
 *   - Historial completo por sucursal
 */
class AdminCajaController extends Controller
{
    public function __construct()
    {
        $this->middleware('administrador');
    }

    /* ----------------------------------------------------------
     | INDEX — vista consolidada de todas las sucursales
     ---------------------------------------------------------- */

    public function index()
    {
        $user = auth()->user();

        // Todos los vendedores (sucursales) de este negocio
        $sucursales = Usuario::where('id_negocio', $user->id_negocio)
            ->where('id_rol', 2)
            ->orderBy('nombre_usuario')
            ->get();

        // Cajas con su sesión activa (si existe)
        $cajas = Caja::with(['sesionActiva'])
            ->where('id_negocio', $user->id_negocio)
            ->get()
            ->keyBy('id_usuario');

        // Para cada sucursal, snapshot rápido si hay sesión activa
        $snapshots = collect();
        foreach ($cajas as $caja) {
            $sesion = $caja->sesionActiva;
            if ($sesion) {
                $snapshots[$caja->id_usuario] = CajaService::calcularSnapshot($sesion, 'parcial');
            }
        }

        return view('administrador.cajas.index', compact('sucursales', 'cajas', 'snapshots'));
    }

    /* ----------------------------------------------------------
     | SHOW — detalle de la caja de una sucursal
     ---------------------------------------------------------- */

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

        return view('administrador.cajas.show', compact('sucursal', 'caja', 'sesion', 'snapshot', 'historial'));
    }

    /* ----------------------------------------------------------
     | CREAR CAJA — asignar caja a sucursal
     ---------------------------------------------------------- */

    public function store(Request $request)
    {
        $request->validate([
            'id_usuario' => 'required|exists:usuarios,id_usuario',
            'nombre'     => 'nullable|string|max:80',
        ]);

        $user = auth()->user();

        // Verificar que el vendedor pertenece a este negocio
        $vendedor = Usuario::where('id_negocio', $user->id_negocio)
            ->where('id_usuario', $request->id_usuario)
            ->where('id_rol', 2)
            ->firstOrFail();

        // Una sola caja por sucursal
        if (Caja::where('id_negocio', $user->id_negocio)->where('id_usuario', $vendedor->id_usuario)->exists()) {
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

        return back()->with('success', 'Caja creada para la sucursal de ' . $vendedor->nombre_usuario . '.');
    }

    /* ----------------------------------------------------------
     | RETIRO — solo admin, quita efectivo de la caja
     ---------------------------------------------------------- */

    public function retiro(Request $request, string $idUsuario)
    {
        $request->validate([
            'monto'      => 'required|numeric|min:0.01|max:999999.99',
            'concepto'   => 'required|string|max:200',
            'referencia' => 'nullable|string|max:120',
        ]);

        $user   = auth()->user();
        $sesion = $this->_sesionActivaDeVendedor($idUsuario, $user->id_negocio);

        if (!$sesion) {
            return back()->with('error', 'No hay sesión activa en esta caja.');
        }

        try {
            $movimiento = CajaService::registrarRetiro(
                sesion:     $sesion,
                idUsuario:  $user->id_usuario,
                monto:      (float) $request->monto,
                concepto:   $request->concepto,
                referencia: $request->referencia,
            );

            Log::info('Admin: retiro de caja', [
                'id_movimiento' => $movimiento->id_movimiento,
                'id_sesion'     => $sesion->id_sesion,
                'monto'         => $request->monto,
                'concepto'      => $request->concepto,
                'admin'         => $user->id_usuario,
                'sucursal'      => $idUsuario,
            ]);

            return back()->with('success', 'Retiro de $' . number_format($request->monto, 2) . ' registrado.');

        } catch (\Throwable $e) {
            return back()->with('error', 'Error al registrar el retiro: ' . $e->getMessage());
        }
    }

    /* ----------------------------------------------------------
     | INGRESO MANUAL — admin registra entrada de efectivo
     ---------------------------------------------------------- */

    public function ingreso(Request $request, string $idUsuario)
    {
        $request->validate([
            'monto'      => 'required|numeric|min:0.01|max:999999.99',
            'id_metodo'  => 'nullable|exists:metodos_pago,id_metodo',
            'concepto'   => 'required|string|max:200',
            'referencia' => 'nullable|string|max:120',
        ]);

        $user   = auth()->user();
        $sesion = $this->_sesionActivaDeVendedor($idUsuario, $user->id_negocio);

        if (!$sesion) {
            return back()->with('error', 'No hay sesión activa en esta caja.');
        }

        try {
            $movimiento = CajaService::registrarIngreso(
                sesion:     $sesion,
                idUsuario:  $user->id_usuario,
                monto:      (float) $request->monto,
                idMetodo:   $request->id_metodo,
                concepto:   $request->concepto,
                referencia: $request->referencia,
            );

            Log::info('Admin: ingreso manual a caja', [
                'id_movimiento' => $movimiento->id_movimiento,
                'id_sesion'     => $sesion->id_sesion,
                'monto'         => $request->monto,
                'admin'         => $user->id_usuario,
            ]);

            return back()->with('success', 'Ingreso de $' . number_format($request->monto, 2) . ' registrado.');

        } catch (\Throwable $e) {
            return back()->with('error', 'Error al registrar el ingreso: ' . $e->getMessage());
        }
    }

    /* ----------------------------------------------------------
     | AJUSTE CONTABLE — solo admin, corrección con justificación
     ---------------------------------------------------------- */

    public function ajuste(Request $request, string $idUsuario)
    {
        $request->validate([
            'monto'      => 'required|numeric|min:0.01|max:999999.99',
            'es_entrada' => 'required|boolean',
            'concepto'   => 'required|string|max:200',
        ]);

        $user   = auth()->user();
        $sesion = $this->_sesionActivaDeVendedor($idUsuario, $user->id_negocio);

        if (!$sesion) {
            return back()->with('error', 'No hay sesión activa en esta caja.');
        }

        try {
            $movimiento = CajaService::registrarAjuste(
                sesion:     $sesion,
                idUsuario:  $user->id_usuario,
                monto:      (float) $request->monto,
                esEntrada:  (bool) $request->es_entrada,
                concepto:   $request->concepto,
            );

            Log::info('Admin: ajuste contable en caja', [
                'id_movimiento' => $movimiento->id_movimiento,
                'id_sesion'     => $sesion->id_sesion,
                'monto'         => $request->monto,
                'es_entrada'    => $request->es_entrada,
                'concepto'      => $request->concepto,
                'admin'         => $user->id_usuario,
            ]);

            return back()->with('success', 'Ajuste registrado correctamente.');

        } catch (\Throwable $e) {
            return back()->with('error', 'Error al registrar el ajuste: ' . $e->getMessage());
        }
    }

    /* ----------------------------------------------------------
     | CIERRE FORZADO — admin cierra la sesión de una sucursal
     ---------------------------------------------------------- */

    public function cerrarForzado(Request $request, string $idUsuario)
    {
        $request->validate([
            'notas' => 'nullable|string|max:500',
        ]);

        $user   = auth()->user();
        $sesion = $this->_sesionActivaDeVendedor($idUsuario, $user->id_negocio);

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

            Log::warning('Admin: cierre forzado de sesión', [
                'id_sesion'  => $sesion->id_sesion,
                'admin'      => $user->id_usuario,
                'sucursal'   => $idUsuario,
            ]);

            return redirect()
                ->route('admin.cajas.corte.pdf', $corte->id_corte)
                ->with('success', 'Sesión cerrada forzosamente.');

        } catch (\Throwable $e) {
            return back()->with('error', 'Error al cerrar la sesión: ' . $e->getMessage());
        }
    }

    /* ----------------------------------------------------------
     | INGRESO MANUAL — por el vendedor con "anuencia" del admin
     | (el vendedor puede hacer esto también; ver CajaController)
     ---------------------------------------------------------- */

    public function ingresoVendedor(Request $request)
    {
        $request->validate([
            'monto'      => 'required|numeric|min:0.01',
            'id_metodo'  => 'nullable|exists:metodos_pago,id_metodo',
            'concepto'   => 'required|string|max:200',
            'referencia' => 'nullable|string|max:120',
        ]);

        $user = auth()->user();
        if ($user->id_rol !== 2) abort(403);

        $caja   = CajaService::cajaDeUsuario($user->id_usuario, $user->id_negocio);
        $sesion = $caja ? CajaService::sesionActiva($caja->id_caja) : null;

        if (!$sesion) {
            return back()->with('error', 'No hay sesión de caja abierta.');
        }

        try {
            CajaService::registrarIngreso(
                sesion:     $sesion,
                idUsuario:  $user->id_usuario,
                monto:      (float) $request->monto,
                idMetodo:   $request->id_metodo,
                concepto:   $request->concepto,
                referencia: $request->referencia,
            );

            Log::info('Vendedor: ingreso manual a caja', [
                'id_sesion'  => $sesion->id_sesion,
                'monto'      => $request->monto,
                'id_usuario' => $user->id_usuario,
            ]);

            return back()->with('success', 'Ingreso registrado correctamente.');

        } catch (\Throwable $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    /* ----------------------------------------------------------
     | PDF — corte (admin puede ver cualquier corte de su negocio)
     ---------------------------------------------------------- */

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

    /* ----------------------------------------------------------
     | HELPER privado
     ---------------------------------------------------------- */

    private function _sesionActivaDeVendedor(string $idUsuario, string $idNegocio): ?object
    {
        $caja = Caja::where('id_negocio', $idNegocio)
            ->where('id_usuario', $idUsuario)
            ->where('activa', true)
            ->first();

        return $caja ? CajaService::sesionActiva($caja->id_caja) : null;
    }
}