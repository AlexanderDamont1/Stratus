<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MetodoPago;
use Illuminate\Http\Request;

class MetodoPagoController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $metodos = MetodoPago::deNegocio($user->id_negocio)->get();

        return view('administrador.metodos_pago.index', compact('metodos'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'nombre'              => 'required|string|max:60',
            'es_efectivo'         => 'boolean',
            'requiere_referencia' => 'boolean',
        ]);

        // Solo un efectivo
        if ($request->boolean('es_efectivo')) {

            $yaExiste = MetodoPago::deNegocio($user->id_negocio)
                ->where('es_efectivo', true)
                ->exists();

            if ($yaExiste) {

                return response()->json([
                    'message' => 'Ya existe un método de efectivo.',
                    'errors' => [
                        'es_efectivo' => [
                            'Ya existe un método de efectivo.'
                        ]
                    ]
                ], 422);
            }
        }

        $orden = MetodoPago::deNegocio($user->id_negocio)->count();

        $metodo = MetodoPago::create([
            'id_negocio'          => $user->id_negocio,
            'nombre'              => $request->nombre,
            'es_efectivo'         => $request->boolean('es_efectivo'),
            'requiere_referencia' => $request->boolean('requiere_referencia'),
            'activo'              => true,
            'orden'               => $orden,
        ]);

        return response()->json([
            'ok'      => true,
            'message' => 'Método creado.',
            'metodo'  => $metodo,
        ]);
    }

    public function update(Request $request, string $id)
    {
        $user = auth()->user();

        $metodo = MetodoPago::deNegocio($user->id_negocio)
            ->findOrFail($id);

        $request->validate([
            'nombre'              => 'required|string|max:60',
            'es_efectivo'         => 'boolean',
            'requiere_referencia' => 'boolean',
            'activo'              => 'boolean',
        ]);

        // Solo un efectivo
        if (
            $request->boolean('es_efectivo')
            && !$metodo->es_efectivo
        ) {

            $yaExiste = MetodoPago::deNegocio($user->id_negocio)
                ->where('es_efectivo', true)
                ->where('id_metodo', '!=', $id)
                ->exists();

            if ($yaExiste) {

                return response()->json([
                    'message' => 'Ya existe un método de efectivo.',
                    'errors' => [
                        'es_efectivo' => [
                            'Ya existe un método de efectivo.'
                        ]
                    ]
                ], 422);
            }
        }

        $metodo->update([
            'nombre'              => $request->nombre,
            'es_efectivo'         => $request->boolean('es_efectivo'),
            'requiere_referencia' => $request->boolean('requiere_referencia'),
            'activo'              => $request->boolean('activo', $metodo->activo),
        ]);

        return response()->json([
            'ok'      => true,
            'message' => 'Método actualizado.',
            'metodo'  => $metodo->fresh(),
        ]);
    }

    public function reordenar(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'orden' => 'required|array'
        ]);

        foreach ($request->orden as $pos => $id) {

            MetodoPago::deNegocio($user->id_negocio)
                ->where('id_metodo', $id)
                ->update([
                    'orden' => $pos
                ]);
        }

        return response()->json([
            'ok' => true
        ]);
    }

    public function destroy(Request $request, string $id)
{
    $user = auth()->user();

    $metodo = MetodoPago::deNegocio($user->id_negocio)
        ->findOrFail($id);

    // Eliminación REAL
    if ($request->boolean('eliminar')) {

        $metodo->delete();

        return response()->json([
            'ok' => true,
            'message' => 'Método eliminado.'
        ]);
    }

    // Toggle activo/inactivo
    $metodo->update([
        'activo' => !$metodo->activo
    ]);

    return response()->json([
        'ok'      => true,
        'message' => $metodo->activo
            ? 'Método activado.'
            : 'Método desactivado.',
        'metodo'  => $metodo->fresh(),
    ]);
}
}