<?php

namespace App\Http\Controllers;

use App\Models\Color;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Services\CatalogService;
use App\Traits\ResolvesAdminRoute;
use App\Http\Requests\StoreColorRequest;
use App\Events\CatalogoActualizado;

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

    // ColorController@store
    public function store(StoreColorRequest $request)
    {
        $user      = auth()->user();
        $idNegocio = $user->id_rol === 1 ? $user->id_negocio : null;

        $color = Color::create([
            'id_modelo'  => $request->id_modelo,
            'id_negocio' => $idNegocio,
            'color'      => $request->color,
        ]);

        $idMarca = \App\Models\Modelo::find($color->id_modelo)?->id_marca ?? '';

        CatalogoActualizado::dispatch(
            $user->id_negocio,
            'color',
            'creado',
            $idMarca,
        );

        CatalogService::invalidateColor($color->id_color, $request->id_modelo, $idNegocio);
        CatalogService::invalidateCatalogoCompleto($user->id_negocio);

        if ($request->expectsJson()) {
            return response()->json([
                'ok'    => true,
                'color' => [
                    'id_color'  => $color->id_color,
                    'id_modelo' => $color->id_modelo,
                    'color'     => $color->color,  // "Rojo|#EF4444" o "Rojo/Azul|#EF4444/#3B82F6"
                ],
            ]);
        }

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
        $idNegocio = $color->id_negocio;

        if (!in_array($user->id_rol, [1, 5])) abort(403);
        if ($user->id_rol === 1 && $color->id_negocio !== $user->id_negocio) abort(403);
        if ($user->id_rol === 5 && !is_null($color->id_negocio)) abort(403);

        $request->validate([
            'id_modelo' => [
                'required',
                Rule::exists('modelos', 'id_modelo')->where('id_negocio', $idNegocio),
            ],
            'color' => [
                'required',
                'string',
                'max:255',
                function ($attribute, $value, $fail) use ($idNegocio) {
                    $partes = explode('|', $value);
                    if (count($partes) !== 2) {
                        $fail('Formato inválido.');
                        return;
                    }
                    [$nombre, $hexRaw] = $partes;
                    $nombres = explode('/', $nombre);
                    $hexes   = explode('/', $hexRaw);

                    if (count($nombres) > 2 || count($hexes) > 2) {
                        $fail('Máximo 2 colores combinados.');
                        return;
                    }

                    $bloqueadas = ['con', 'y', 'e', 'o', 'u', 'del', 'de', 'la', 'el', 'los', 'las'];
                    $sufijos    = ['ito', 'ita', 'itos', 'itas', 'illo', 'illa', 'ote', 'ota'];

                    foreach ($nombres as $n) {
                        $n = strtolower(trim($n));
                        if (in_array($n, $bloqueadas)) {
                            $fail("\"$n\" no es un nombre de color válido.");
                            return;
                        }
                        foreach ($sufijos as $s) {
                            if (str_ends_with($n, $s) && strlen($n) > strlen($s) + 2) {
                                $fail("\"$n\" parece un diminutivo. Usa el nombre base.");
                                return;
                            }
                        }
                    }

                    foreach ($hexes as $hex) {
                        if (!preg_match('/^#[0-9A-Fa-f]{6}$/', trim($hex))) {
                            $fail("\"$hex\" no es un hex válido.");
                            return;
                        }
                    }
                },
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

        $idMarca = \App\Models\Modelo::find($color->id_modelo)?->id_marca ?? '';

        CatalogoActualizado::dispatch(
            $user->id_negocio,
            'color',
            'actualizado',
            $idMarca,
        );

        CatalogService::invalidateColor($color->id_color, $oldModeloId, $idNegocio);
        CatalogService::invalidateCatalogoCompleto($user->id_negocio);
        CatalogService::invalidateModelo( $request->id_modelo, $idNegocio);
        
        
        if ($oldModeloId !== $request->id_modelo) {
            CatalogService::invalidateColor($color->id_color, $request->id_modelo, $idNegocio);
        }

        if ($request->expectsJson()) {
            return response()->json([
                'ok'    => true,
                'color' => [
                    'id_color' => $color->id_color,
                    'color'    => $color->color,
                ],
            ]);
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
        $idMarca = \App\Models\Modelo::find($color->id_modelo)?->id_marca ?? '';

        CatalogoActualizado::dispatch(
            $user->id_negocio,
            'color',
            'eliminado',
            $idMarca,
        );

        CatalogService::invalidateColor($idColor, $idModelo, $idNegocio);
        CatalogService::invalidateCatalogoCompleto($user->id_negocio);

        

        return redirect()
            ->route($this->routeByRol('colores'))
            ->with('success', 'Color eliminado correctamente.');
    }
}
