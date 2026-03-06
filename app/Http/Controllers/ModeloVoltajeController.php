<?php

namespace App\Http\Controllers;

use App\Models\ModeloVoltaje;
use App\Models\Modelo;
use App\Models\Voltaje;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ModeloVoltajeController extends Controller
{
    public function modeloVoltaje()
    {
        $relaciones = ModeloVoltaje::with(['modelo','voltaje'])->paginate(10); // paginación
        $modelos = Modelo::orderBy('nombre_modelo', 'asc')->get(); // corregido nombre_modelo
        $voltajes = Voltaje::orderBy('voltaje', 'asc')->get();

        return view('gestor.vehiculos.modelo_voltaje', compact(
            'relaciones',
            'modelos',
            'voltajes'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_modelo' => 'required',
            'id_voltaje' => 'required'
        ]);

        ModeloVoltaje::create([
            'id_mvoltaje' => 'MV'.Str::random(13),
            'id_modelo' => $request->id_modelo,
            'id_voltaje' => $request->id_voltaje
        ]);

        return redirect()->back()->with('success','Relación creada correctamente');
    }

    public function destroy($id)
    {
        $relacion = ModeloVoltaje::findOrFail($id);
        $relacion->delete();

        return redirect()->back()->with('success','Relación eliminada correctamente');
    }

public function voltajesPorModelo($id_modelo)
{
    $voltajes = ModeloVoltaje::where('modelo_voltaje.id_modelo', $id_modelo)
        ->join('voltajes', 'modelo_voltaje.id_voltaje', '=', 'voltajes.id_voltaje')
        ->get([
            'modelo_voltaje.id_voltaje',
            'voltajes.voltaje'
        ]);

    return response()->json($voltajes);
}

   
}