<?php

namespace App\Http\Controllers\Root;

use App\Http\Controllers\Controller;
use App\Models\NegocioConfig;
use Illuminate\Http\Request;

class RootConfigController extends Controller
{
    public function index()
    {
        $definiciones = NegocioConfig::orderBy('grupo')->orderBy('orden')->get();
        return view('root.config', compact('definiciones'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'clave'         => 'required|string|unique:negocio_config,clave|alpha_dash',
            'nombre'        => 'required|string|max:100',
            'descripcion'   => 'nullable|string|max:255',
            'icono'         => 'nullable|string',
            'tipo'          => 'required|in:radio,checkbox_multi,toggle,texto,numero',
            'opciones'      => 'nullable|string',
            'valor_default' => 'required|string',
            'grupo'         => 'required|string|max:50',
            'orden'         => 'required|integer|min:0',
        ]);

        NegocioConfig::create([
            'clave'         => $request->clave,
            'nombre'        => $request->nombre,
            'descripcion'   => $request->descripcion,
            'icono'         => $request->icono,
            'tipo'          => $request->tipo,
            'opciones'      => $request->opciones ? json_decode($request->opciones) : null,
            'valor_default' => $request->valor_default,
            'grupo'         => $request->grupo,
            'orden'         => $request->orden,
            'activo'        => true,
        ]);

        return back()->with('success', 'Configuración creada correctamente.');
    }

    public function update(Request $request, string $id)
    {
        $def = NegocioConfig::findOrFail($id);

        $request->validate([
            'nombre'        => 'required|string|max:100',
            'descripcion'   => 'nullable|string|max:255',
            'icono'         => 'nullable|string',
            'opciones'      => 'nullable|string',
            'valor_default' => 'required|string',
            'grupo'         => 'required|string|max:50',
            'orden'         => 'required|integer|min:0',
        ]);

        $def->update([
            'nombre'        => $request->nombre,
            'descripcion'   => $request->descripcion,
            'icono'         => $request->icono,
            'opciones'      => $request->opciones ? json_decode($request->opciones) : null,
            'valor_default' => $request->valor_default,
            'grupo'         => $request->grupo,
            'orden'         => $request->orden,
        ]);

        return back()->with('success', 'Configuración actualizada.');
    }

    public function toggle(string $id)
    {
        $def = NegocioConfig::findOrFail($id);
        $def->update(['activo' => !$def->activo]);
        return response()->json(['activo' => (bool) $def->activo]);
    }


    public function destroy(string $id)
    {
        NegocioConfig::findOrFail($id)->delete();
        return back()->with('success', 'Configuración eliminada.');
    }
}