<?php

namespace App\Http\Controllers;

use App\Models\Modelo;
use Illuminate\Http\Request;
use App\Services\CatalogService;

class ModeloController extends Controller
{
    public function index()
    {
        $modelosPag = Modelo::orderBy('nombre_modelo')->paginate(15);
        $modelosAll = CatalogService::getModelos();

        return view('gestor.Vehiculos.modelo.index', [
            'modelos' => $modelosPag,
            'modelosAll' => $modelosAll
        ]);
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

        $modelo = Modelo::create([
            'nombre_modelo' => $request->nombre_modelo,
        ]);

        // Invalidar lista global de modelos
        CatalogService::clearCache('modelos');
        // Invalidar posibles cachés del modelo (aunque es nuevo, por consistencia)
        CatalogService::invalidateModelo($modelo->id_modelo);

        return redirect()
            ->route('gestor.vehiculos.modelos.index')
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

        // Invalidar lista global y cachés del modelo
        CatalogService::clearCache('modelos');
        CatalogService::invalidateModelo($modelo->id_modelo);

        return redirect()
            ->route('gestor.vehiculos.modelos.index')
            ->with('success', 'Modelo actualizado correctamente.');
    }

    public function destroy(Modelo $modelo)
    {
        if ($modelo->colores()->exists()) {
            return redirect()
                ->route('gestor.vehiculos.modelos.index')
                ->with('error', 'No se puede eliminar: tiene colores asociados.');
        }

        $idModelo = $modelo->id_modelo;
        $modelo->delete();

        CatalogService::clearCache('modelos');
        CatalogService::invalidateModelo($idModelo);

        return redirect()
            ->route('gestor.vehiculos.modelos.index')
            ->with('success', 'Modelo eliminado correctamente.');
    }
}