<?php

namespace App\Http\Controllers;

use App\Models\ModeloVoltaje;
use App\Models\Modelo;
use App\Models\Voltaje;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Services\CatalogService;

class ModeloVoltajeController extends Controller
{
    public function modeloVoltaje()
    {
        // Relaciones paginadas (necesitamos paginado para tabla)
        $relaciones = ModeloVoltaje::with(['modelo','voltaje'])->paginate(10);

        // Modelos y voltajes para selects: desde cache
        $modelos = CatalogService::getModelos();
        $voltajes = CatalogService::getAllVoltajes();

        return view('gestor.vehiculos.modelo_voltaje', compact(
            'relaciones',
            'modelos',
            'voltajes'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_modelo' => 'required|exists:modelos,id_modelo',
            'id_voltaje' => 'required|exists:voltajes,id_voltaje'
        ]);

        $rel = ModeloVoltaje::create([
            'id_mvoltaje' => 'MV'.Str::random(13),
            'id_modelo'   => $request->id_modelo,
            'id_voltaje'  => $request->id_voltaje
        ]);

        // Invalidar cache del modelo afectado (voltajes y colores por modelo)
        CatalogService::invalidateModelo($request->id_modelo);
        CatalogService::incrementVersion();

        return redirect()->back()->with('success','Relación creada correctamente');
    }

    public function destroy($id)
    {
        $relacion = ModeloVoltaje::findOrFail($id);
        $idModelo = $relacion->id_modelo;
        $relacion->delete();

        CatalogService::invalidateModelo($idModelo);
        CatalogService::incrementVersion();

        return redirect()->back()->with('success','Relación eliminada correctamente');
    }

   public function voltajesPorModelo(string $id_modelo)
{
    return response()->json(
        CatalogService::getVoltajesByModelo($id_modelo)->values()
    );
}
}