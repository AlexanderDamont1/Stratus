<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Voltaje;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class VoltajeController extends Controller
{
    public function index()
    {
        $voltajes = Voltaje::orderBy('voltaje')->paginate(15);
        return view('gestor.vehiculos.voltaje.index', compact('voltajes'));
    }

    public function create()
    {
        return view('gestor.vehiculos.voltaje.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'voltaje' => 'required|string|max:10|unique:voltajes,voltaje',
        ]);

        Voltaje::create([
            'id_voltaje' => Str::upper(Str::random(8)),
            'voltaje'    => $request->voltaje,
        ]);

        return redirect()->route('gestor.vehiculos.voltajes.index')
            ->with('success', 'Voltaje creado correctamente.');
    }

    public function edit(Voltaje $voltaje)
    {
        return view('gestor.vehiculos.voltaje.edit', compact('voltaje'));
    }

    public function update(Request $request, Voltaje $voltaje)
    {
        $request->validate([
            'voltaje' => 'required|string|max:10|unique:voltajes,voltaje,' . $voltaje->id_voltaje . ',id_voltaje',
        ]);

        $voltaje->update(['voltaje' => $request->voltaje]);

        return redirect()->route('gestor.vehiculos.voltajes.index')
            ->with('success', 'Voltaje actualizado correctamente.');
    }

    public function destroy(Voltaje $voltaje)
    {
        $voltaje->delete();

        return redirect()->route('gestor.vehiculos.voltajes.index')
            ->with('success', 'Voltaje eliminado correctamente.');
    }
}