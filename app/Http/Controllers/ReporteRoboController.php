<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\ReporteRobo;
use App\Services\CatalogService;
use App\Services\RoboService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ReporteRoboController extends Controller
{

    public function index()
    {
        $user = auth()->user();
        if ($user->id_rol != 2) abort(404);

        return view('vendedor.robo.index');
    }

    public function buscar(Request $request)
    {
        $user = auth()->user();
        if (!in_array($user->id_rol, [1, 2])) abort(404);

        $numSerie = strtoupper(trim($request->get('num_serie', '')));

        if (!$numSerie) {
            return response()->json(['ok' => false, 'mensaje' => 'Indica un número de serie.'], 422);
        }

        // Buscar en toda la DB — cross-tenant, sin filtro de negocio
        $bici = CatalogService::getBicicletaBySerie($numSerie);

        if (!$bici) {
            return response()->json(['ok' => false, 'mensaje' => 'Vehículo no encontrado en el sistema ArrowX.'], 404);
        }

        // 1. Ya tiene un reporte de robo activo → no se puede levantar otro
        if (RoboService::estaReportada($numSerie)) {
            $reporte = RoboService::getReporteActivo($numSerie);
            return response()->json([
                'ok'      => false,
                'error'   => 'ya_reportada',
                'mensaje' => 'Este vehículo ya tiene un reporte de robo activo.',
                'folio'   => $reporte?->id_reporte,
            ], 409);
        }

        // 2. Solo se puede reportar como robada una unidad vendida (status 2)
        if ($bici->status != 2) {
            return response()->json([
                'ok'      => false,
                'error'   => 'no_vendida',
                'mensaje' => 'Esta unidad no se puede registrar como robada porque todavía no ha sido vendida a un cliente.',
            ], 422);
        }

        // Buscar la venta para datos de compra
        $venta = \App\Models\Venta::with(['negocio'])
            ->whereHas('detalles', fn($q) => $q->where('num_serie', $numSerie))
            ->latest()
            ->first();

        $cliente = \App\Models\Cliente::find($bici->id_cliente ?? $venta?->id_cliente);

        return response()->json([
            'ok'      => true,
            'bici'    => [
                'num_serie' => $bici->num_serie,
                'marca'     => $bici->modelo?->marca?->nombre_marca ?? '—',
                'modelo'    => $bici->modelo?->nombre_modelo ?? '—',
                'voltaje'   => $bici->voltaje?->voltaje ?? '—',
                'color'     => $bici->color?->color ?? '—',
            ],
            'cliente' => $cliente ? [
                'id_cliente'     => $cliente->id_cliente,
                'nombre'         => $cliente->nombre_cliente . ' ' . $cliente->apellido1,
                'telefono'       => $cliente->telefono,
                'correo'         => $cliente->correo,
            ] : null,
            'compra'  => $venta ? [
                'negocio' => $venta->negocio?->nombre_negocio ?? '—',
                'fecha'   => $venta->created_at->format('d/m/Y'),
            ] : null,
        ]);
    }

    // ── POST /robo/reportar — levantar reporte ───────────────────────────
    public function reportar(Request $request)
    {
        $user = auth()->user();
        if (!in_array($user->id_rol, [1, 2])) abort(404);

        $request->validate([
            'num_serie'  => 'required|string|exists:bicicletas,num_serie',
            'id_cliente' => 'required|string|exists:clientes,id_cliente',
            'notas'      => 'nullable|string|max:500',
        ]);

        $numSerie = strtoupper(trim($request->num_serie));

        // Verificar que no esté ya reportada
        if (RoboService::estaReportada($numSerie)) {
            $reporte = RoboService::getReporteActivo($numSerie);
            return response()->json([
                'ok'      => false,
                'mensaje' => 'Este vehículo ya tiene un reporte de robo activo.',
                'folio'   => $reporte?->id_reporte,
            ], 409);
        }

        // Obtener negocio origen (donde se vendió)
        $venta = \App\Models\Venta::whereHas('detalles', fn($q) => $q->where('num_serie', $numSerie))
            ->latest()
            ->first();

        $idNegocioOrigen = $venta?->id_negocio ?? $user->id_negocio;

        try {
            $reporte = RoboService::levantarReporte(
                numSerie:          $numSerie,
                idNegocioOrigen:   $idNegocioOrigen,
                idNegocioReporta:  $user->id_negocio,
                idCliente:         $request->id_cliente,
                notas:             $request->notas,
            );

            return response()->json([
                'ok'      => true,
                'mensaje' => 'Reporte levantado. Se envió un correo al cliente para confirmar.',
                'folio'   => $reporte->id_reporte,
            ]);

        } catch (\Exception $e) {
            Log::error('Error al levantar reporte de robo', [
                'error'    => $e->getMessage(),
                'serie'    => $numSerie,
                'negocio'  => $user->id_negocio,
            ]);
            return response()->json(['ok' => false, 'mensaje' => 'Error al registrar el reporte.'], 500);
        }
    }

    // ── GET /robo/confirmar/{token} — cliente confirma por link ─────────
    public function confirmar(string $token)
    {
        $reporte = RoboService::confirmarReporte($token);

        if (!$reporte) {
            return view('confirmacion', [
                'exito'   => false,
                'mensaje' => 'El enlace no es válido o ha expirado. Contacta a la sucursal.',
            ]);
        }

        return view('confirmacion', [
            'exito'   => true,
            'mensaje' => 'Tu reporte de robo ha sido confirmado. Te notificaremos si tu vehículo es encontrado.',
            'folio'   => $reporte->id_reporte,
            'serie'   => $reporte->num_serie,
        ]);
    }

    public function verificar(string $numSerie)
    {
        $user = auth()->user();
        if (!in_array($user->id_rol, [1, 2])) abort(403);

        $numSerie = strtoupper(trim($numSerie));

        if (!RoboService::estaReportada($numSerie)) {
            return response()->json(['reportada' => false]);
        }

        $reporte = RoboService::getReporteActivo($numSerie);

        // Si está confirmado y lo detecta otra sucursal → marcar encontrado
        if ($reporte?->esConfirmado()) {
            RoboService::marcarEncontrado($numSerie, $user->id_negocio, $user->id_usuario);
            $reporte->refresh();
        }

        return response()->json([
            'reportada' => true,
            'estado'    => $reporte?->estado,
            'folio'     => $reporte?->id_reporte,
            'cliente'   => $reporte?->cliente?->nombre_cliente . ' ' . $reporte?->cliente?->apellido1,
            'desde'     => $reporte?->created_at->format('d/m/Y'),
            'serie'     => $numSerie,
        ]);
    }

    public function custodia()
    {
        $user = auth()->user();
        if (!in_array($user->id_rol, [1, 2])) abort(403);

        $vehiculos = RoboService::getEnCustodia($user->id_negocio);

        return response()->json(['ok' => true, 'data' => $vehiculos]);
    }

    public function entregar(string $id)
    {
        $user = auth()->user();
        if (!in_array($user->id_rol, [1, 2])) abort(403);

        $reporte = RoboService::cerrarReporte($id, $user->id_negocio);

        if (!$reporte) {
            return response()->json([
                'ok'      => false,
                'mensaje' => 'Reporte no encontrado o no corresponde a esta sucursal.',
            ], 404);
        }

        return response()->json([
            'ok'      => true,
            'mensaje' => 'Vehículo entregado al dueño. Reporte cerrado.',
        ]);
    }
}