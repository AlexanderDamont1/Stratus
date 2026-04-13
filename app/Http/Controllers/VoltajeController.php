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
            $voltajes = Voltaje::whereNull('id_negocio')
                ->orderBy('voltaje')
                ->paginate(15);

            return view('gestor.Vehiculos.voltaje.index', compact('voltajes'));
        }

        // Rol 1 — solo voltajes de su negocio
        $voltajes = Voltaje::where('id_negocio', $user->id_negocio)
            ->orderBy('voltaje')
            ->paginate(15);

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
                // Unicidad dentro del mismo negocio
                Rule::unique('voltajes', 'voltaje')
                    ->where('id_negocio', $idNegocio),
            ],
        ]);

        $voltaje = Voltaje::create([
            'voltaje'    => $request->voltaje,
            'id_negocio' => $idNegocio,
            'created_at'  => now(),
            'updated_at'  => now(),
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
        $idNegocio = $voltaje->id_negocio; // mantener negocio original

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

        // Verificar que no tenga bicicletas asociadas a través de modelo_voltaje
        $tieneBicicletas = ModeloVoltaje::where('id_voltaje', $voltaje->id_voltaje)
            ->whereHas('modelo', fn($q) => $q->whereHas('bicicletas'))
            ->exists();

        if ($tieneBicicletas) {
            return back()->with('error', 'No se puede eliminar: tiene bicicletas asociadas.');
        }

        $idVoltaje = $voltaje->id_voltaje;
        $idNegocio = $voltaje->id_negocio;

        // Eliminar correlaciones modelo_voltaje primero
        ModeloVoltaje::where('id_voltaje', $idVoltaje)->delete();

        $voltaje->delete();

        CatalogService::invalidateVoltaje($idVoltaje, $idNegocio);

       return redirect()
            ->route($this->routeByRol('voltajes'))
            ->with('success', 'Voltaje eliminado correctamente.');
    }
}