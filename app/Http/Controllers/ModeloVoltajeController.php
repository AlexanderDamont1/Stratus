<?php

namespace App\Http\Controllers;

use App\Models\ModeloVoltaje;
use App\Models\Modelo;
use App\Models\Voltaje;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Services\CatalogService;
use App\Traits\ResolvesAdminRoute;

class ModeloVoltajeController extends Controller
{
    use ResolvesAdminRoute;
    // ─── INDEX ──────────────────────────────────────────────────────────────

    public function modeloVoltaje()
    {
        $user = auth()->user();

        if (!in_array($user->id_rol, [1, 5])) abort(403);

        if ($user->id_rol === 5) {
            $relaciones = ModeloVoltaje::with(['modelo', 'voltaje'])
                ->whereNull('id_negocio')
                ->paginate(10);

            $modelos  = CatalogService::getModelos();
            $voltajes = CatalogService::getAllVoltajes();

            return view('gestor.Vehiculos.modelo_voltaje', compact('relaciones', 'modelos', 'voltajes'));
        }

        // Rol 1 — solo relaciones de su negocio
        $relaciones = ModeloVoltaje::with(['modelo', 'voltaje'])
            ->where('id_negocio', $user->id_negocio)
            ->paginate(10);

        $modelos  = CatalogService::getModelosByNegocio($user->id_negocio);
        $voltajes = CatalogService::getVoltajesByNegocio($user->id_negocio);

        return view('gestor.Vehiculos.modelo_voltaje', compact('relaciones', 'modelos', 'voltajes'));
    }

    // ─── STORE ──────────────────────────────────────────────────────────────

    public function store(Request $request)
    {
        $user      = auth()->user();
        $idNegocio = $user->id_rol === 1 ? $user->id_negocio : null;

        if (!in_array($user->id_rol, [1, 5])) abort(403);

        $request->validate([
            'id_modelo' => [
                'required',
                Rule::exists('modelos', 'id_modelo')->where('id_negocio', $idNegocio),
            ],
            'id_voltaje' => [
                'required',
                Rule::exists('voltajes', 'id_voltaje')->where('id_negocio', $idNegocio),
            ],
            // Evitar duplicados: mismo modelo + voltaje + negocio
            'id_modelo' => [
                'required',
                Rule::unique('modelo_voltaje', 'id_modelo')
                    ->where('id_voltaje', $request->id_voltaje)
                    ->where('id_negocio', $idNegocio),
            ],
        ]);

        // Generar ID con el mismo patrón que el resto del sistema
        $fecha  = now()->format('ymd');
        $letras = strtoupper(substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 3));
        $nums   = random_int(100, 999);

        ModeloVoltaje::create([
            'id_mvoltaje' => "MV{$fecha}{$letras}{$nums}",
            'id_modelo'   => $request->id_modelo,
            'id_voltaje'  => $request->id_voltaje,
            'id_negocio'  => $idNegocio,
        ]);

        CatalogService::invalidateModelo($request->id_modelo, $idNegocio);
        CatalogService::invalidateVoltaje($request->id_voltaje, $idNegocio);

        return redirect()
            ->route($this->routeByRol('modelo-voltaje'))
            ->with('success', 'Relación creada correctamente.');
    }

    // ─── DESTROY ─────────────────────────────────────────────────────────────

    public function destroy(string $id)
    {
        $user     = auth()->user();
        $relacion = ModeloVoltaje::findOrFail($id);

        if (!in_array($user->id_rol, [1, 5])) abort(403);

        if ($user->id_rol === 1 && $relacion->id_negocio !== $user->id_negocio) abort(403);

        if ($user->id_rol === 5 && !is_null($relacion->id_negocio)) abort(403);

        // Verificar que no haya bicicletas usando esta combinación
        $tieneBicicletas = \App\Models\Bicicleta::where('id_modelo', $relacion->id_modelo)
            ->where('id_voltaje', $relacion->id_voltaje)
            ->exists();

        if ($tieneBicicletas) {
            return back()->with('error', 'No se puede eliminar: hay bicicletas con esta combinación.');
        }

        $idModelo  = $relacion->id_modelo;
        $idVoltaje = $relacion->id_voltaje;
        $idNegocio = $relacion->id_negocio;

        $relacion->delete();

        CatalogService::invalidateModelo($idModelo, $idNegocio);
        CatalogService::invalidateVoltaje($idVoltaje, $idNegocio);

        return redirect()
            ->route($this->routeByRol('modelo-voltaje'))
            ->with('success', 'Relación eliminada correctamente.');
    }

    // ─── AJAX: VOLTAJES POR MODELO ───────────────────────────────────────────

    public function voltajesPorModelo(string $id_modelo)
{
    return response()->json(
        CatalogService::getVoltajesByModelo($id_modelo, null)->values()
    );
}
}