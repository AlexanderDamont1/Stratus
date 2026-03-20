<?php

namespace App\Http\Controllers;

use App\Models\Marca;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Services\CatalogService;
use App\Traits\ResolvesAdminRoute;

class MarcaController extends Controller
{
    use ResolvesAdminRoute;
    // ─── INDEX ──────────────────────────────────────────────────────────────

    public function index()
    {
        $user = auth()->user();

        if ($user->id_rol !== 1) abort(403);

        $marcas = Marca::where('id_negocio', $user->id_negocio)
            ->orderBy('nombre_marca')
            ->paginate(15);

        return view('administrador.catalogo.marca.index', compact('marcas'));
    }

    // ─── CREATE ─────────────────────────────────────────────────────────────

    public function create()
    {
        $user = auth()->user();

        if ($user->id_rol !== 1) abort(403);

        return view('administrador.catalogo.marca.create');
    }

    // ─── STORE ──────────────────────────────────────────────────────────────

    public function store(Request $request)
    {
        $user = auth()->user();

        if ($user->id_rol !== 1) abort(403);

        $request->validate([
            'nombre_marca' => [
                'required', 'string', 'max:50',
                // Unicidad dentro del mismo negocio
                Rule::unique('marcas', 'nombre_marca')
                    ->where('id_negocio', $user->id_negocio),
            ],
        ]);

        $marca = Marca::create([
            'nombre_marca' => $request->nombre_marca,
            'id_negocio'   => $user->id_negocio,
        ]);

        CatalogService::invalidateMarca($marca->id_marca, $user->id_negocio);

        return redirect()
            ->route($this->routeByRol('marcas'))
            ->with('success', 'Marca creada correctamente.');
    }

    // ─── EDIT ────────────────────────────────────────────────────────────────

    public function edit(Marca $marca)
    {
        $user = auth()->user();

        if ($user->id_rol !== 1) abort(403);

        // Solo puede editar marcas de su propio negocio
        if ($marca->id_negocio !== $user->id_negocio) abort(403);

        return view('administrador.catalogo.marca.edit', compact('marca'));
    }

    // ─── UPDATE ──────────────────────────────────────────────────────────────

    public function update(Request $request, Marca $marca)
    {
        $user = auth()->user();

        if ($user->id_rol !== 1) abort(403);

        if ($marca->id_negocio !== $user->id_negocio) abort(403);

        $request->validate([
            'nombre_marca' => [
                'required', 'string', 'max:50',
                Rule::unique('marcas', 'nombre_marca')
                    ->where('id_negocio', $user->id_negocio)
                    ->ignore($marca->id_marca, 'id_marca'),
            ],
        ]);

        $marca->update([
            'nombre_marca' => $request->nombre_marca,
        ]);

        CatalogService::invalidateMarca($marca->id_marca, $user->id_negocio);

        return redirect()
            ->route($this->routeByRol('marcas'))
            ->with('success', 'Marca actualizada correctamente.');
    }

    // ─── DESTROY ─────────────────────────────────────────────────────────────

    public function destroy(Marca $marca)
    {
        $user = auth()->user();

        if ($user->id_rol !== 1) abort(403);

        if ($marca->id_negocio !== $user->id_negocio) abort(403);

        // No eliminar si tiene modelos asociados
        if ($marca->modelos()->exists()) {
            return back()->with('error', 'No se puede eliminar: tiene modelos asociados.');
        }

        $idMarca   = $marca->id_marca;
        $idNegocio = $marca->id_negocio;

        $marca->delete();

        CatalogService::invalidateMarca($idMarca, $idNegocio);

        return redirect()
            ->route($this->routeByRol('marcas'))
            ->with('success', 'Marca eliminada correctamente.');
    }
}