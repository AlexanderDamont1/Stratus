<?php

namespace App\Http\Controllers;

use App\Models\Marca;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Services\CatalogService;
use App\Traits\ResolvesAdminRoute;
use Illuminate\View\View;
use App\Events\CatalogoActualizado;

class MarcaController extends Controller
{
    use ResolvesAdminRoute;

    // ─── INDEX ──────────────────────────────────────────────────────────────

    public function index()
    {
        $user = auth()->user();

        if ($user->id_rol !== 1) abort(403);

        $marcas = CatalogService::getMarcasByNegocio($user->id_negocio);

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
                'required',
                'string',
                'max:50',
                Rule::unique('marcas', 'nombre_marca')
                    ->where('id_negocio', $user->id_negocio),
            ],
        ]);

        $marca = Marca::create([
            'nombre_marca' => $request->nombre_marca,
            'id_negocio'   => $user->id_negocio,
        ]);

        CatalogoActualizado::dispatch(
            $user->id_negocio,
            'marca',
            'creado',
            $marca->id_marca,
        );

        
        CatalogService::invalidateMarca($marca->id_marca, $user->id_negocio);
        CatalogService::invalidateCatalogoCompleto($user->id_negocio);

        if ($request->expectsJson()) {
            return response()->json([
                'ok'      => true,
                'mensaje' => 'Marca creada correctamente.',
                'marca'   => [
                    'id_marca'     => $marca->id_marca,
                    'nombre_marca' => $marca->nombre_marca,
                ],
            ]);
        }

        return redirect()
            ->route($this->routeByRol('marcas'))
            ->with('success', 'Marca creada correctamente.');
    }

    // ─── EDIT ────────────────────────────────────────────────────────────────

    public function edit(Marca $marca)
    {
        $user = auth()->user();

        if ($user->id_rol !== 1) abort(403);
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
                'required',
                'string',
                'max:50',
                Rule::unique('marcas', 'nombre_marca')
                    ->where('id_negocio', $user->id_negocio)
                    ->ignore($marca->id_marca, 'id_marca'),
            ],
        ]);

        $marca->update([
            'nombre_marca' => $request->nombre_marca,
        ]);

        CatalogoActualizado::dispatch(
            $user->id_negocio,
            'marca',
            'actualizado',
            $marca->id_marca,
        );

        CatalogService::invalidateMarca($marca->id_marca, $user->id_negocio);
        CatalogService::invalidateCatalogoCompleto($user->id_negocio);

        if ($request->expectsJson()) {
            return response()->json([
                'ok'      => true,
                'mensaje' => 'Marca actualizada correctamente.',
                'marca'   => [
                    'id_marca'     => $marca->id_marca,
                    'nombre_marca' => $marca->nombre_marca,
                ],
            ]);
        }

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

        if ($marca->modelos()->exists()) {
            return back()->with('error', 'No se puede eliminar: tiene modelos asociados.');
        }

       
        $idMarca   = $marca->id_marca;
        $idNegocio = $marca->id_negocio;

        $marca->delete();

        CatalogoActualizado::dispatch(
            $user->id_negocio,
            'marca',
            'eliminado',
            $idMarca,
        );

        CatalogService::invalidateMarca($idMarca, $idNegocio);
        CatalogService::invalidateCatalogoCompleto($user->id_negocio);

        return redirect()
            ->route($this->routeByRol('marcas'))
            ->with('success', 'Marca eliminada correctamente.');
    }

    // ─── CATÁLOGO ────────────────────────────────────────────────────────────

    public function catalogo(): View
    {
        $user = auth()->user();
        if ($user->id_rol !== 1) abort(403);

        $idNegocio = $user->id_negocio;

       
        $marcas = CatalogService::getCatalogoCompleto($idNegocio);

        return view('administrador.catalogo.index', compact('marcas', 'idNegocio'))
            ->with([
                'totalMarcas'  => $marcas->count(),
                'limiteMarcas' => 10,
            ]);
    }

    // ─── CARD AJAX ───────────────────────────────────────────────────────────

    public function card(string $idMarca): \Illuminate\Http\Response
    {
        $user = auth()->user();
        if ($user->id_rol !== 1) abort(403);

        
        $marca = \App\Models\Marca::with([
            'modelos.colores',
            'modelos.voltajes',
        ])
            ->where('id_negocio', $user->id_negocio)
            ->findOrFail($idMarca);

        return response(view('administrador.catalogo._marca_card', compact('marca'))->render());
    }
}