<?php

namespace App\Http\Controllers;

use App\Models\Modelo;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Services\CatalogService;
use App\Traits\ResolvesAdminRoute;
use App\Events\CatalogoActualizado;

class ModeloController extends Controller
{

    use ResolvesAdminRoute;
    // ─── INDEX ──────────────────────────────────────────────────────────────

    public function index()
    {
        $user = auth()->user();

        if (!in_array($user->id_rol, [1, 5])) {
            abort(403);
        }

        if ($user->id_rol === 5) {
            // Rol 5 ve solo modelos públicos (sin negocio)
            $modelosPag = Modelo::whereNull('id_negocio')
                ->orderBy('nombre_modelo')
                ->paginate(15);

            $modelosAll = CatalogService::getModelos(); // públicos cacheados

            return view('gestor.Vehiculos.modelo.index', [
                'modelos'    => $modelosPag,
                'modelosAll' => $modelosAll,
            ]);
        }

        // Rol 1 ve solo modelos de su negocio
        $modelosPag = Modelo::where('id_negocio', $user->id_negocio)
            ->with('marca')
            ->orderBy('nombre_modelo')
            ->paginate(15);

        $modelosAll  = CatalogService::getModelosByNegocio($user->id_negocio);
        $marcas      = CatalogService::getMarcasByNegocio($user->id_negocio);

        return view('gestor.Vehiculos.modelo.index', [
            'modelos'    => $modelosPag,
            'modelosAll' => $modelosAll,
            'marcas'     => $marcas,
        ]);
    }

    // ─── CREATE ─────────────────────────────────────────────────────────────

    public function create()
    {
        $user = auth()->user();

        if (!in_array($user->id_rol, [1, 5])) {
            abort(403);
        }

        $marcas = $user->id_rol === 1
            ? CatalogService::getMarcasByNegocio($user->id_negocio)
            : collect(); // rol 5 no necesita marcas

        return view('gestor.Vehiculos.modelo.create', compact('marcas'));
    }

    // ─── STORE ──────────────────────────────────────────────────────────────

    public function store(Request $request)
    {


        $user = auth()->user();

        if (!in_array($user->id_rol, [1, 5])) {
            abort(403);
        }

        $idNegocio = $user->id_rol === 1 ? $user->id_negocio : null;

        $rules = [
            'nombre_modelo' => [
                'required',
                'string',
                'max:255',
                // Unicidad dentro del mismo negocio (null para públicos)
                Rule::unique('modelos')
                    ->where('id_negocio', $idNegocio),
            ],
        ];

        // Rol 1 debe seleccionar una marca de su negocio
        if ($user->id_rol === 1) {
            $rules['id_marca'] = [
                'required',
                Rule::exists('marcas', 'id_marca')->where('id_negocio', $idNegocio),
            ];
        }

        $request->validate($rules);

        $modelo = Modelo::create([
            'nombre_modelo' => $request->nombre_modelo,
            'id_marca'      => $user->id_rol === 1 ? $request->id_marca : null,
            'id_negocio'    => $idNegocio,
        ]);

        CatalogoActualizado::dispatch(
            $user->id_negocio,
            'modelo',
            'creado',
            $modelo->id_marca,
        );

        CatalogService::invalidateModelo($modelo->id_modelo, $idNegocio);
        CatalogService::invalidateCatalogoCompleto($user->id_negocio);

        if ($request->expectsJson()) {
            return response()->json([
                'ok'     => true,
                'modelo' => [
                    'id_modelo'     => $modelo->id_modelo,
                    'nombre_modelo' => $modelo->nombre_modelo,
                    'id_marca'      => $modelo->id_marca,
                ],
            ]);
        }

        return redirect()->route($this->routeByRol('modelos'))
            ->with('success', 'Modelo creado correctamente.');
    }

    // ─── EDIT ────────────────────────────────────────────────────────────────

    public function edit(Modelo $modelo)
    {
        $user = auth()->user();

        if (!in_array($user->id_rol, [1, 5])) abort(403);

        // Rol 1 solo puede editar modelos de su negocio
        if ($user->id_rol === 1 && $modelo->id_negocio !== $user->id_negocio) abort(403);

        // Rol 5 solo puede editar modelos públicos
        if ($user->id_rol === 5 && !is_null($modelo->id_negocio)) abort(403);

        $marcas = $user->id_rol === 1
            ? CatalogService::getMarcasByNegocio($user->id_negocio)
            : collect();

        return view('gestor.Vehiculos.modelo.edit', compact('modelo', 'marcas'));
    }

    // ─── UPDATE ──────────────────────────────────────────────────────────────

    public function update(Request $request, Modelo $modelo)
    {

        $user = auth()->user();

        if (!in_array($user->id_rol, [1, 5])) abort(403);

        // Rol 1 solo puede editar modelos de su negocio
        if ($user->id_rol === 1 && $modelo->id_negocio !== $user->id_negocio) abort(403);

        // Rol 5 solo puede editar modelos públicos
        if ($user->id_rol === 5 && !is_null($modelo->id_negocio)) abort(403);

        $idNegocio = $modelo->id_negocio; // mantener el negocio original

        $rules = [
            'nombre_modelo' => [
                'required',
                'string',
                'max:255',
                Rule::unique('modelos', 'nombre_modelo')
                    ->where('id_negocio', $idNegocio)
                    ->ignore($modelo->id_modelo, 'id_modelo'),
            ],
        ];

        if ($user->id_rol === 1) {
            $rules['id_marca'] = [
                'required',
                Rule::exists('marcas', 'id_marca')->where('id_negocio', $idNegocio),
            ];
        }

        $request->validate($rules);

        $modelo->update([
            'nombre_modelo' => $request->nombre_modelo,
            'id_marca'      => $user->id_rol === 1 ? $request->id_marca : $modelo->id_marca,
        ]);

        CatalogoActualizado::dispatch(
            $user->id_negocio,
            'modelo',
            'actualizado',
            $modelo->id_marca,
        );

        CatalogService::invalidateModelo($modelo->id_modelo, $idNegocio);
        CatalogService::invalidateCatalogoCompleto($user->id_negocio);

        if ($request->expectsJson()) {
            return response()->json([
                'ok'     => true,
                'modelo' => [
                    'id_modelo'     => $modelo->id_modelo,
                    'nombre_modelo' => $modelo->nombre_modelo,
                ],
            ]);
        }

        return redirect()
            ->route($this->routeByRol('modelos'))
            ->with('success', 'Modelo actualizado correctamente.');
    }

    // ─── DESTROY ─────────────────────────────────────────────────────────────

    public function destroy(Modelo $modelo)
    {
        $user = auth()->user();

        if (!in_array($user->id_rol, [1, 5])) abort(403);
        if ($user->id_rol === 1 && $modelo->id_negocio !== $user->id_negocio) abort(403);
        if ($user->id_rol === 5 && !is_null($modelo->id_negocio)) abort(403);

        if ($modelo->bicicletas()->exists()) {
            return back()->with('error', 'No se puede eliminar: tiene bicicletas asociadas.');
        }

        $idModelo  = $modelo->id_modelo;
        $idNegocio = $modelo->id_negocio;

        // ── Eliminar en cascada desde PHP ──────────────────────────────
        // 1. Colores del modelo
        \App\Models\Color::where('id_modelo', $idModelo)->delete();

        // 2. Relaciones modelo-voltaje
        \App\Models\ModeloVoltaje::where('id_modelo', $idModelo)->delete();

        // 3. Invalidar cache de cada color eliminado (opcional pero recomendado)
       

        // ── Eliminar el modelo ─────────────────────────────────────────
        $modelo->delete();

        $idMarca = $modelo->id_marca; 


        CatalogoActualizado::dispatch(
            $user->id_negocio,
            'modelo',
            'eliminado',
             $idMarca,
        );
        
        
        CatalogService::invalidateModelo($idModelo, $idNegocio);
        CatalogService::invalidateCatalogoCompleto($user->id_negocio);

        return redirect()
            ->route('admin.catalogo.index')
            ->with('success', 'Modelo y sus datos asociados eliminados correctamente.');
    }
}
