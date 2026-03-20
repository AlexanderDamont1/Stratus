<?php

namespace App\Http\Controllers;

use App\Models\Color;
use App\Models\Modelo;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Services\CatalogService;
use App\Traits\ResolvesAdminRoute;

class ColorController extends Controller
{
     use ResolvesAdminRoute;
    // ─── INDEX ──────────────────────────────────────────────────────────────

    public function index()
    {
        $user = auth()->user();

        if (!in_array($user->id_rol, [1, 5])) abort(403);

        if ($user->id_rol === 5) {
            $colores = Color::with('modelo')
                ->whereNull('id_negocio')
                ->orderBy('color')
                ->paginate(15);

            $modelos = CatalogService::getModelos();

            return view('gestor.Vehiculos.color.index', compact('colores', 'modelos'));
        }

        // Rol 1 — solo colores de su negocio
        $colores = Color::with('modelo')
            ->where('id_negocio', $user->id_negocio)
            ->orderBy('color')
            ->paginate(15);

        $modelos = CatalogService::getModelosByNegocio($user->id_negocio);

        return view('gestor.Vehiculos.color.index', compact('colores', 'modelos'));
    }

    // ─── CREATE ─────────────────────────────────────────────────────────────

    public function create()
    {
        $user = auth()->user();

        if (!in_array($user->id_rol, [1, 5])) abort(403);

        $modelos = $user->id_rol === 1
            ? CatalogService::getModelosByNegocio($user->id_negocio)
            : CatalogService::getModelos();

        return view('gestor.Vehiculos.color.create', compact('modelos'));
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
                // Validar que el modelo pertenezca al negocio correcto
                Rule::exists('modelos', 'id_modelo')->where('id_negocio', $idNegocio),
            ],
            'color' => [
                'required', 'string', 'max:100',
                // Mismo color no puede repetirse en el mismo modelo + negocio
                Rule::unique('colores', 'color')
                    ->where('id_modelo', $request->id_modelo)
                    ->where('id_negocio', $idNegocio),
            ],
        ]);

        $color = Color::create([
            'id_modelo'  => $request->id_modelo,
            'id_negocio' => $idNegocio,
            'color'      => $request->color,
        ]);

        CatalogService::invalidateColor($color->id_color, $request->id_modelo, $idNegocio);

        return redirect()
            ->route($this->routeByRol('colores'))
            ->with('success', 'Color creado correctamente.');
    }

    // ─── EDIT ────────────────────────────────────────────────────────────────

    public function edit(Color $color)
    {
        $user = auth()->user();

        if (!in_array($user->id_rol, [1, 5])) abort(403);

        if ($user->id_rol === 1 && $color->id_negocio !== $user->id_negocio) abort(403);

        if ($user->id_rol === 5 && !is_null($color->id_negocio)) abort(403);

        $modelos = $user->id_rol === 1
            ? CatalogService::getModelosByNegocio($user->id_negocio)
            : CatalogService::getModelos();

        return view('gestor.Vehiculos.color.edit', compact('color', 'modelos'));
    }

    // ─── UPDATE ──────────────────────────────────────────────────────────────

    public function update(Request $request, Color $color)
    {
        $user      = auth()->user();
        $idNegocio = $color->id_negocio; // mantener negocio original

        if (!in_array($user->id_rol, [1, 5])) abort(403);

        if ($user->id_rol === 1 && $color->id_negocio !== $user->id_negocio) abort(403);

        if ($user->id_rol === 5 && !is_null($color->id_negocio)) abort(403);

        $request->validate([
            'id_modelo' => [
                'required',
                Rule::exists('modelos', 'id_modelo')->where('id_negocio', $idNegocio),
            ],
            'color' => [
                'required', 'string', 'max:100',
                Rule::unique('colores', 'color')
                    ->where('id_modelo', $request->id_modelo)
                    ->where('id_negocio', $idNegocio)
                    ->ignore($color->id_color, 'id_color'),
            ],
        ]);

        $oldModeloId = $color->id_modelo;

        $color->update([
            'id_modelo' => $request->id_modelo,
            'color'     => $request->color,
        ]);

        // Si cambió de modelo, invalidar ambos
        CatalogService::invalidateColor($color->id_color, $oldModeloId, $idNegocio);
        if ($oldModeloId !== $request->id_modelo) {
            CatalogService::invalidateColor($color->id_color, $request->id_modelo, $idNegocio);
        }

        return redirect()
            ->route($this->routeByRol('colores'))
            ->with('success', 'Color actualizado correctamente.');
 
    }

    // ─── DESTROY ─────────────────────────────────────────────────────────────

    public function destroy(Color $color)
    {
        $user = auth()->user();

        if (!in_array($user->id_rol, [1, 5])) abort(403);

        if ($user->id_rol === 1 && $color->id_negocio !== $user->id_negocio) abort(403);

        if ($user->id_rol === 5 && !is_null($color->id_negocio)) abort(403);

        // Verificar que no tenga bicicletas asociadas
        if ($color->bicicletas()->exists()) {
            return back()->with('error', 'No se puede eliminar: tiene bicicletas asociadas.');
        }

        $idColor   = $color->id_color;
        $idModelo  = $color->id_modelo;
        $idNegocio = $color->id_negocio;

        $color->delete();

        CatalogService::invalidateColor($idColor, $idModelo, $idNegocio);

       return redirect()
            ->route($this->routeByRol('colores'))
            ->with('success', 'Color eliminado correctamente.');
    }
}