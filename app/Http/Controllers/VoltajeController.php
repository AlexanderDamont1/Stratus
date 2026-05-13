<?php

namespace App\Http\Controllers;

use App\Models\Voltaje;
use App\Models\ModeloVoltaje;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Services\CatalogService;
use App\Traits\ResolvesAdminRoute;
use App\Events\CatalogoActualizado;


class VoltajeController extends Controller
{
    use ResolvesAdminRoute;

    // ─── INDEX ──────────────────────────────────────────────────────────────

    public function index()
    {
        $user = auth()->user();

        if (!in_array($user->id_rol, [1, 5])) abort(403);

        if ($user->id_rol === 5) {

            $voltajes = CatalogService::getAllVoltajes();

            return view('gestor.Vehiculos.voltaje.index', compact('voltajes'));
        }
        $voltajes = CatalogService::getVoltajesByNegocio($user->id_negocio);

        return view('gestor.Vehiculos.voltaje.index', compact('voltajes'));
    }

    // ─── CREATE ─────────────────────────────────────────────────────────────

    public function create()
    {
        $user = auth()->user();

        if (!in_array($user->id_rol, [1, 5])) abort(403);

        return view('gestor.Vehiculos.voltaje.create');
    }

    // ─── STORE ──────────────────────────────────────────────────────────────

    public function store(Request $request)
    {
        $user      = auth()->user();
        $idNegocio = $user->id_rol === 1 ? $user->id_negocio : null;

        if (!in_array($user->id_rol, [1, 5])) abort(403);

        $request->validate([
            'voltaje' => [
                'required', 'string', 'max:10',
                Rule::unique('voltajes', 'voltaje')
                    ->where('id_negocio', $idNegocio),
            ],
        ]);

        $voltaje = Voltaje::create([
            'voltaje'    => $request->voltaje,
            'id_negocio' => $idNegocio,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        CatalogService::invalidateVoltaje($voltaje->id_voltaje, $idNegocio);

        return redirect()
            ->route($this->routeByRol('voltajes'))
            ->with('success', 'Voltaje creado correctamente.');
    }

    // ─── EDIT ────────────────────────────────────────────────────────────────

    public function edit(Voltaje $voltaje)
    {
        $user = auth()->user();

        if (!in_array($user->id_rol, [1, 5])) abort(403);

        if ($user->id_rol === 1 && $voltaje->id_negocio !== $user->id_negocio) abort(403);

        if ($user->id_rol === 5 && !is_null($voltaje->id_negocio)) abort(403);

        return view('gestor.Vehiculos.voltaje.edit', compact('voltaje'));
    }

    // ─── UPDATE ──────────────────────────────────────────────────────────────

    public function update(Request $request, Voltaje $voltaje)
    {
        $user      = auth()->user();
        $idNegocio = $voltaje->id_negocio;

        if (!in_array($user->id_rol, [1, 5])) abort(403);

        if ($user->id_rol === 1 && $voltaje->id_negocio !== $user->id_negocio) abort(403);

        if ($user->id_rol === 5 && !is_null($voltaje->id_negocio)) abort(403);

        $request->validate([
            'voltaje' => [
                'required', 'string', 'max:10',
                Rule::unique('voltajes', 'voltaje')
                    ->where('id_negocio', $idNegocio)
                    ->ignore($voltaje->id_voltaje, 'id_voltaje'),
            ],
        ]);

        $voltaje->update(['voltaje' => $request->voltaje]);

        CatalogoActualizado::dispatch($user->id_negocio, 'voltaje', 'actualizado', '');

        CatalogService::invalidateVoltaje($voltaje->id_voltaje, $idNegocio);

        return redirect()
            ->route($this->routeByRol('voltajes'))
            ->with('success', 'Voltaje actualizado correctamente.');
    }

    // ─── DESTROY ─────────────────────────────────────────────────────────────

    public function destroy(Voltaje $voltaje)
    {
        $user = auth()->user();

        if (!in_array($user->id_rol, [1, 5])) abort(403);

        if ($user->id_rol === 1 && $voltaje->id_negocio !== $user->id_negocio) abort(403);

        if ($user->id_rol === 5 && !is_null($voltaje->id_negocio)) abort(403);

        $tieneBicicletas = ModeloVoltaje::where('id_voltaje', $voltaje->id_voltaje)
            ->whereHas('modelo', fn($q) => $q->whereHas('bicicletas'))
            ->exists();

        if ($tieneBicicletas) {
            return back()->with('error', 'No se puede eliminar: tiene bicicletas asociadas.');
        }

        $idVoltaje = $voltaje->id_voltaje;
        $idNegocio = $voltaje->id_negocio;

        ModeloVoltaje::where('id_voltaje', $idVoltaje)->delete();
        $voltaje->delete();

        CatalogService::invalidateVoltaje($idVoltaje, $idNegocio);

        return redirect()
            ->route($this->routeByRol('voltajes'))
            ->with('success', 'Voltaje eliminado correctamente.');
    }
}