<?php

namespace App\Http\Controllers;

use App\Models\Modelo;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ModeloController extends Controller
{
    public function index()
    {
        $modelos = Modelo::orderBy('nombre_modelo')->paginate(15);

        return view('gestor.Vehiculos.modelo.index', compact('modelos'));
    }

    public function create()
    {
        return view('gestor.Vehiculos.modelo.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre_modelo' => 'required|string|max:255|unique:modelos,nombre_modelo',
        ]);

        Modelo::create([
            'id_modelo'     => Str::upper(Str::random(10)),
            'nombre_modelo' => $request->nombre_modelo,
        ]);

        return redirect()
            ->route('gestor.vehiculos.modelos.index') // 👈 PLURAL
            ->with('success', 'Modelo creado correctamente.');
    }

    public function edit(Modelo $modelo)
    {
        return view('gestor.Vehiculos.modelo.edit', compact('modelo'));
    }

    public function update(Request $request, Modelo $modelo)
    {
        $request->validate([
            'nombre_modelo' => 'required|string|max:255|unique:modelos,nombre_modelo,' . $modelo->id_modelo . ',id_modelo',
        ]);

        $modelo->update([
            'nombre_modelo' => $request->nombre_modelo
        ]);

        return redirect()
            ->route('gestor.vehiculos.modelos.index') // 👈 PLURAL
            ->with('success', 'Modelo actualizado correctamente.');
    }

    public function destroy(Modelo $modelo)
    {
        if ($modelo->colores()->exists()) {
            return redirect()
                ->route('gestor.vehiculos.modelos.index') // 👈 PLURAL
                ->with('error', 'No se puede eliminar: tiene colores asociados.');
        }

        $modelo->delete();

        return redirect()
            ->route('gestor.vehiculos.modelos.index') // 👈 PLURAL
            ->with('success', 'Modelo eliminado correctamente.');
    }
}