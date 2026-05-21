<?php
// app/Http/Controllers/Sucursal/OtController.php

namespace App\Http\Controllers\Sucursal;

use App\Http\Controllers\Controller;
use App\Services\OtService;
use App\Services\CatalogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OtController extends Controller
{
    // ─── Vista principal ──────────────────────────────────────────────────────

    public function index(Request $request)
    {
        $user   = Auth::user();
        $estado = $request->query('estado');
        $page   = (int) $request->query('page', 1);

        $ots = OtService::listarOts($user->id_negocio, $user->id_usuario, $page, $estado);

        if ($request->wantsJson()) {
            return response()->json(['ok' => true, 'data' => $ots]);
        }

        return view('vendedor.reparaciones.index', compact('ots', 'estado'));
    }

    // ─── Detalle de OT ───────────────────────────────────────────────────────

    public function show(string $idOt, Request $request)
    {
        $user = Auth::user();
        $ot   = OtService::getOt($idOt, $user->id_negocio);

        abort_if(!$ot, 404);

        if ($request->wantsJson()) {
            return response()->json(['ok' => true, 'data' => $ot]);
        }

        return view('vendedor.reparaciones.show', compact('ot'));
    }

    // ─── Buscar bicicleta por num_serie (autocomplete al crear OT) ───────────

    public function buscarBicicleta(Request $request)
    {
        $request->validate(['num_serie' => 'required|string|max:100']);

        $user = Auth::user();
        $data = OtService::buscarBicicleta($request->num_serie, $user->id_negocio);

        return response()->json([
            'ok'   => (bool) $data,
            'data' => $data,
        ]);
    }

    public function create()
    {
        return view('vendedor.reparaciones.create');
    }

    // ─── Crear OT manual ─────────────────────────────────────────────────────

    public function store(Request $request)
    {
        $validated = $request->validate([
            'num_serie'          => 'nullable|string|max:100',
            'bici_descripcion'   => 'nullable|string|max:150|required_without:num_serie',
            'id_cliente'         => 'nullable|string|max:36',
            'cliente_nombre'     => 'nullable|string|max:120',
            'cliente_email'      => 'nullable|email|max:150',
            'id_verificada'      => 'boolean',
            'tipo'               => 'required|in:reparacion,garantia,mantenimiento',
            'problema_reportado' => 'required|string|max:1000',
            'notas_internas'     => 'nullable|string|max:1000',
        ]);
    
        $user = Auth::user();
    
        $ot = OtService::crearOt($validated, $user->id_negocio, $user->id_usuario);
    
        return response()->json([
            'ok'      => true,
            'id_ot'   => $ot->id_ot,
            'mensaje' => "Orden {$ot->id_ot} creada correctamente.",
        ], 201);
    }
 

    // ─── Avanzar estado ──────────────────────────────────────────────────────

    public function avanzarEstado(Request $request, string $idOt)
    {
        $request->validate([
            'estado' => 'required|in:diagnostico,esperando_aprobacion,en_proceso,mandado_fabrica,lista,entregada,cancelada',
            'nota'   => 'nullable|string|max:500',
        ]);

        $user = Auth::user();

        $ot = OtService::avanzarEstado(
            $idOt,
            $request->estado,
            $user->id_usuario,
            $user->id_negocio,
            $request->nota
        );

        return response()->json([
            'ok'      => true,
            'estado'  => $ot->estado,
            'mensaje' => "Estado actualizado a {$ot->estado}.",
        ]);
    }

    // ─── Actualizar piezas de una OT ─────────────────────────────────────────

    public function actualizarPiezas(Request $request, string $idOt)
    {
        $request->validate([
            'piezas'                   => 'required|array',
            'piezas.*.id_pieza'        => 'nullable|integer',
            'piezas.*.descripcion'     => 'nullable|string|max:150',
            'piezas.*.cantidad'        => 'required|integer|min:1',
            'piezas.*.precio_unitario' => 'required|numeric|min:0',
            'piezas.*.es_garantia'     => 'boolean',
        ]);

        $user = Auth::user();
        $ot   = OtService::getOt($idOt, $user->id_negocio);
        abort_if(!$ot, 404);

        OtService::sincronizarPiezas($ot, $request->piezas);
        OtService::invalidateOt($idOt, $user->id_negocio);

        return response()->json([
            'ok'             => true,
            'costo_piezas'   => $ot->fresh()->costo_piezas,
            'costo_total'    => $ot->fresh()->costo_total,
        ]);
    }
}