<?php

namespace App\Http\Controllers;

use App\Models\ModeloVoltaje;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Services\CatalogService;
use App\Traits\ResolvesAdminRoute;
use App\Events\CatalogoActualizado;

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
                Rule::unique('modelo_voltaje', 'id_modelo')
                    ->where('id_voltaje', $request->id_voltaje)
                    ->where('id_negocio', $idNegocio),
            ],
            'id_voltaje' => [
                'required',
                Rule::exists('voltajes', 'id_voltaje')->where('id_negocio', $idNegocio),
            ],
        ]);

        $fecha  = now()->format('ymd');
        $letras = strtoupper(substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 3));
        $nums   = random_int(100, 999);

        ModeloVoltaje::create([
            'id_mvoltaje' => "MV{$fecha}{$letras}{$nums}",
            'id_modelo'   => $request->id_modelo,
            'id_voltaje'  => $request->id_voltaje,
            'id_negocio'  => $idNegocio,
        ]);

        $idMarca = \App\Models\Modelo::find($request->id_modelo)?->id_marca ?? '';
        CatalogoActualizado::dispatch($user->id_negocio, 'voltaje', 'creado', $idMarca);

        CatalogService::invalidateModelo($request->id_modelo, $idNegocio);
        CatalogService::invalidateVoltaje($request->id_voltaje, $idNegocio);
        CatalogService::invalidateCatalogoCompleto($user->id_negocio);

        $mv = ModeloVoltaje::with('voltaje')->find("MV{$fecha}{$letras}{$nums}");

        if ($request->expectsJson()) {
            return response()->json([
                'ok'     => true,
                'pivote' => [
                    'id_mvoltaje' => $mv->id_mvoltaje,
                    'id_voltaje'  => $mv->id_voltaje,
                    'voltaje'     => $mv->voltaje->voltaje,
                ],
            ]);
        }

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

        $idMarca = \App\Models\Modelo::find($relacion->id_modelo)?->id_marca ?? '';
        CatalogoActualizado::dispatch(
            $user->id_negocio,
            'voltaje',
            'eliminado',
            $idMarca,
        );

        CatalogService::invalidateModelo($idModelo, $idNegocio);
        CatalogService::invalidateVoltaje($idVoltaje, $idNegocio);
        CatalogService::invalidateCatalogoCompleto($user->id_negocio);

        

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
