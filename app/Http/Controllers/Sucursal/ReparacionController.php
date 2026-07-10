<?php

namespace App\Http\Controllers\Sucursal;

use App\Http\Controllers\Controller;
use App\Services\CatalogService;
use App\Services\ReparacionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReparacionController extends Controller
{
    // ── Listado ───────────────────────────────────────────────────────────────

    public function index(Request $request)
    {
        $user   = Auth::user();
        $estado = $request->query('estado');
        $page   = (int) $request->query('page', 1);

        $items = ReparacionService::listar($user->id_negocio, $user->id_usuario, $page, $estado);

        if ($request->wantsJson()) {
            return response()->json(['ok' => true, 'data' => $items]);
        }

        return view('vendedor.reparaciones.index', compact('items', 'estado'));
    }

    // ── Detalle ───────────────────────────────────────────────────────────────

    public function show(string $id, Request $request)
    {
        $user = Auth::user();
        $rep  = ReparacionService::get($id, $user->id_negocio);

        abort_if(!$rep, 404);

        if ($request->wantsJson()) {
            return response()->json(['ok' => true, 'data' => $rep]);
        }

        return view('vendedor.reparaciones.show', compact('rep'));
    }

    // ── Crear ─────────────────────────────────────────────────────────────────

    public function create()
    {
        return view('vendedor.reparaciones.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'num_serie'          => 'nullable|string|max:17',
            'unidad_descripcion' => 'nullable|string|max:150',
            'id_cliente'         => 'nullable|string|max:36',
            'cliente_nombre'     => 'nullable|string|max:120',
            'cliente_telefono'   => 'nullable|string|max:20',
            'cliente_email'      => 'nullable|email|max:150',
            'id_verificada'      => 'boolean',
            'tipo'               => 'required|in:reparacion,garantia,mantenimiento',
            'costo_reparacion'   => 'required_if:tipo,reparacion|nullable|numeric|min:0',
            'problema_reportado' => 'required|string|max:1000',
            'notas_internas'     => 'nullable|string|max:1000',
        ]);

        $user = Auth::user();
        $rep  = ReparacionService::crear($validated, $user->id_negocio, $user->id_usuario);

        return response()->json([
            'ok'             => true,
            'id_reparacion'  => $rep->id_reparacion,
            'mensaje'        => "Orden {$rep->id_reparacion} creada correctamente.",
        ], 201);
    }

    // ── Buscar bicicleta ──────────────────────────────────────────────────────

    public function buscarBicicleta(Request $request)
    {
        $request->validate(['num_serie' => 'required|string|max:17']);

        $user = Auth::user();
        $data = ReparacionService::buscarBicicleta($request->num_serie, $user->id_negocio);

        // AÑADIR: unidad existe pero no está vendida
        if (is_array($data) && isset($data['error'])) {
            return response()->json([
                'ok'    => false,
                'error' => $data['error'],   // 'no_vendida'
            ], 422);
        }

        return response()->json([
            'ok'   => (bool) $data,
            'data' => $data,
        ]);
    }

    // ── Diagnóstico + piezas ──────────────────────────────────────────────────

    public function guardarDiagnostico(Request $request, string $id)
    {
        $validated = $request->validate([
            'diagnostico'              => 'required|string|max:2000',
            'costo_mano_obra'          => 'nullable|numeric|min:0',
            'piezas'                   => 'present|array',
            'piezas.*.id_pieza' => 'nullable|string|max:36',
            'piezas.*.descripcion'     => 'nullable|string|max:150',
            'piezas.*.cantidad'        => 'required|integer|min:1',
            'piezas.*.precio_unitario' => 'nullable|numeric|min:0',
        ]);

        $user = Auth::user();
        $rep  = ReparacionService::get($id, $user->id_negocio);

        abort_if(!$rep, 404);

        $rep = ReparacionService::guardarDiagnostico(
            $rep,
            $validated['diagnostico'],
            $validated['piezas'],
            (float) $validated['costo_mano_obra'],
            $user->id_usuario
        );

        return response()->json([
            'ok'          => true,
            'costo_total' => $rep->costo_total,
            'estado'      => $rep->estado,
            'mensaje'     => 'Diagnóstico guardado.',
        ]);
    }

    // ── Enviar cotización ─────────────────────────────────────────────────────

    public function enviarCotizacion(Request $request, string $id)
    {
        $validated = $request->validate([
            'descripcion_trabajo' => 'required|string|max:2000',
        ]);

        $user = Auth::user();
        $rep  = ReparacionService::get($id, $user->id_negocio);

        abort_if(!$rep, 404);
        abort_if($rep->estado !== 'diagnostico', 422,
            'Solo se puede cotizar desde estado diagnóstico.');

        $email = $rep->cliente_email ?? $rep->cliente?->correo;
        abort_if(!$email, 422, 'El cliente no tiene correo registrado.');

        $cotizacion = ReparacionService::enviarCotizacion(
            $rep,
            $validated['descripcion_trabajo'],
            $user->id_usuario
        );

        return response()->json([
            'ok'            => true,
            'id_cotizacion' => $cotizacion->id_cotizacion,
            'expires_at'    => $cotizacion->expires_at->toISOString(),
            'mensaje'       => 'Cotización enviada al cliente.',
        ]);
    }

    // ── Resolución manual (cotización expirada o sin email) ───────────────────

    public function resolverCotizacion(Request $request, string $id)
    {
        $validated = $request->validate([
            'decision'           => 'required|in:aceptar,aceptar_parcial,rechazar,solo_mantenimiento',
            'nota'               => 'nullable|string|max:500',
            'piezas_aceptadas'   => 'required_if:decision,aceptar_parcial|array',
            'piezas_aceptadas.*' => 'integer',
        ]);

        $user = Auth::user();
        $rep  = ReparacionService::get($id, $user->id_negocio);

        abort_if(!$rep, 404);

        $rep = ReparacionService::resolverManualmente(
            $rep,
            $validated['decision'],
            $user->id_usuario,
            $validated['nota'] ?? null,
            $validated['piezas_aceptadas'] ?? []
        );

        return response()->json([
            'ok'      => true,
            'estado'  => $rep->estado,
            'mensaje' => 'Cotización resuelta.',
        ]);
    }

    // ── Cobro ─────────────────────────────────────────────────────────────────

    public function cobrarForm(string $id)
    {
        $user = Auth::user();
        $rep  = ReparacionService::get($id, $user->id_negocio);

        abort_if(!$rep, 404);
        abort_if($rep->estado !== 'lista', 422, 'La OT debe estar lista para poder cobrarse.');
        abort_if($rep->id_venta, 422, 'Esta OT ya fue cobrada.');
        abort_if((float) $rep->costo_total <= 0, 422, 'Esta OT no tiene costo por cobrar.');

        $config         = CatalogService::getConfigNegocio($user->id_negocio);
        $metodosActivos = $config['metodos_pago'] ?? ['efectivo'];

        $todasOpciones = \App\Models\NegocioConfig::where('clave', 'metodos_pago')
            ->where('activo', true)
            ->value('opciones');

        $opcionesMap = collect($todasOpciones ?? [])->keyBy('value');

        $metodos = collect($metodosActivos)->map(function ($value) use ($opcionesMap) {
            $opcion = $opcionesMap[$value] ?? null;
            return [
                'value'               => $value,
                'label'               => $opcion['label']               ?? $value,
                'es_efectivo'         => $opcion['es_efectivo']         ?? false,
                'requiere_referencia' => $opcion['requiere_referencia'] ?? false,
            ];
        })->values();

        return view('vendedor.reparaciones.cobrar', compact('rep', 'metodos'));
    }

    public function cobrar(Request $request, string $id)
    {
        $validated = $request->validate([
            'pagos'              => 'required|array|min:1',
            'pagos.*.metodo'     => 'required|string|max:40',
            'pagos.*.monto'      => 'required|numeric|min:0.01',
            'pagos.*.referencia' => 'nullable|string|max:20',
        ]);

        $user = Auth::user();
        $rep  = ReparacionService::get($id, $user->id_negocio);

        abort_if(!$rep, 404);

        $rep = ReparacionService::cobrar(
            $rep,
            $validated['pagos'],
            $user->id_usuario,
            $user->id_negocio
        );

        return response()->json([
            'ok'       => true,
            'id_venta' => $rep->id_venta,
            'mensaje'  => 'Cobro registrado. OT entregada.',
        ]);
    }

    // ── Avanzar estado ────────────────────────────────────────────────────────

    public function avanzarEstado(Request $request, string $id)
    {
        $validated = $request->validate([
            'estado' => 'required|in:diagnostico,cotizacion_enviada,en_proceso,lista,entregada,cancelada',
            'nota'   => 'nullable|string|max:500',
        ]);

        $user = Auth::user();

        $rep = ReparacionService::avanzarEstado(
            $id,
            $validated['estado'],
            $user->id_usuario,
            $user->id_negocio,
            $validated['nota'] ?? null
        );

        return response()->json([
            'ok'      => true,
            'estado'  => $rep->estado,
            'mensaje' => "Estado actualizado a {$rep->estado}.",
        ]);
    }
}