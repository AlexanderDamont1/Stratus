<?php

namespace App\Http\Controllers;

use App\Models\Color;
use App\Models\Modelo;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Services\CatalogService;

class ColorController extends Controller
{
    public function index()
    {
        // Paginación de colores (seguimos necesitando DB para paginado)
        $colores = Color::with('modelo')->orderBy('color')->paginate(15);

        // Modelos para selects: desde cache (Redis)
        $modelos = CatalogService::getModelos();

        return view('gestor.Vehiculos.color.index', compact('colores', 'modelos'));
    }

    public function create()
    {
        // Usar cache para el select de modelos
        $modelos = CatalogService::getModelos();
        return view('gestor.Vehiculos.color.create', compact('modelos'));
    }

   public function store(Request $request)
{
    $request->validate([
        'color'     => 'required|string|max:100',
        'id_modelo' => 'required|exists:modelos,id_modelo',
    ]);

    $color = Color::create([
        'id_modelo' => $request->id_modelo,
        'color'     => $request->color,
    ]);

    // Invalidar cache del modelo al que pertenece el color
    CatalogService::invalidateModelo($request->id_modelo);
    CatalogService::incrementVersion();

    return redirect()->route('gestor.vehiculos.colores.index')
        ->with('success', 'Color creado correctamente.');
}

    public function edit(Color $color)
    {
        // Modelos para select (cache)
        $modelos = CatalogService::getModelos();
        // Aseguramos retornar la vista "edit" correcta (antes devolvías index)
        return view('gestor.vehiculos.colores.edit', compact('color', 'modelos'));
    }

    public function update(Request $request, Color $color)
    {
        $request->validate([
            'color'     => 'required|string|max:255|unique:colores,color,' . $color->id_color . ',id_color',
            'id_modelo' => 'required|exists:modelos,id_modelo',
        ]);

        $oldModeloId = $color->id_modelo;

        $color->update([
            'id_modelo' => $request->id_modelo,
            'color'     => $request->color,
        ]);

        // Si se cambió de modelo, invalidamos ambos modelos
        CatalogService::invalidateModelo($oldModeloId);
        CatalogService::invalidateModelo($request->id_modelo);
        CatalogService::incrementVersion();

        return redirect()->route('gestor.vehiculos.colores.index')
            ->with('success', 'Color actualizado correctamente.');
    }

    
}