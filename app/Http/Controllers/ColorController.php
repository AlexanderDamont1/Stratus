<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Color;
use App\Models\Modelo;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ColorController extends Controller
{
   public function index()
{
    $colores = Color::with('modelo')->orderBy('color')->paginate(15);
    $modelos = Modelo::orderBy('nombre_modelo')->get();

    return view('gestor.Vehiculos.color.index', compact('colores', 'modelos'));
}

    public function create()
    {
        $modelos = Modelo::orderBy('nombre_modelo')->get();
        return view('gestor.Vehiculos.color.create', compact('modelos'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'color'     => 'required|string|max:100',
            'id_modelo' => 'required|exists:modelos,id_modelo',
        ]);

        Color::create([
            'id_color'  => Str::upper(Str::random(10)),
            'id_modelo' => $request->id_modelo,
            'color'     => $request->color,
        ]);

        return redirect()->route('gestor.vehiculos.colores.index')
            ->with('success', 'Color creado correctamente.');
    }

    public function edit(Color $color)
    {
        $modelos = Modelo::orderBy('nombre_modelo')->get();
        return view('gestor.vehiculos.colores.index', compact('color', 'modelos'));
    }

    public function update(Request $request, Color $color)
    {
        $request->validate([
            'color'     => 'required|string|max:255|unique:colores,color,' . $color->id_color . ',id_color',
            'id_modelo' => 'required|exists:modelos,id_modelo',
        ]);

        $color->update([
            'id_modelo' => $request->id_modelo,
            'color'     => $request->color,
        ]);

        return redirect()->route('gestor.vehiculos.colores.index')
            ->with('success', 'Color actualizado correctamente.');
    }

   public function destroy($id)
{
    $color = Color::where('id_color', $id)->firstOrFail();

    $color->delete();

    return redirect()->route('gestor.vehiculos.colores.index')
        ->with('success', 'Color eliminado correctamente.');
}
}