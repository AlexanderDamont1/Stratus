<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Personal;
use App\Services\CatalogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class PersonalController extends Controller
{
    // ── INDEX ────────────────────────────────────────────────────────────────
    public function index(Request $request)
    {
        $idNegocio    = auth()->user()->id_negocio;
        $personal     = CatalogService::getPersonalByNegocio($idNegocio);
        $sucursales   = CatalogService::getSucursalesByNegocio($idNegocio);
        $sucursalesJs = $sucursales->map(fn($s) => [
            'id_usuario'     => $s->id_usuario,
            'nombre_usuario' => $s->nombre_usuario,
        ])->values()->toArray();

        return view('administrador.personal.index', compact(
            'personal',
            'sucursales',
            'sucursalesJs',
        ));
    }

    // ── STORE ────────────────────────────────────────────────────────────────
    public function store(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'nombre'       => 'required|string|max:120',
            'sucursales'   => 'required|array|min:1',
            'sucursales.*' => 'exists:usuarios,id_usuario',
        ]);

        foreach ($request->sucursales as $idUsuario) {
            $existe = Personal::where('id_negocio', $user->id_negocio)
                ->where('id_usuario', $idUsuario)
                ->where('nombre', $request->nombre)
                ->exists();

            if (!$existe) {
                Personal::create([
                    'id_usuario' => $idUsuario,
                    'id_negocio' => $user->id_negocio,
                    'nombre'     => $request->nombre,
                    'activo'     => true,
                ]);
            }
        }

        CatalogService::invalidatePersonal($user->id_negocio);

        $item = CatalogService::getPersonalItem($request->nombre, $user->id_negocio);

        return response()->json([
            'message'  => 'Vendedor creado correctamente.',
            'personal' => $item,
        ], 201);
    }

    // ── UPDATE ───────────────────────────────────────────────────────────────
    public function update(Request $request, string $id)
    {
        $user     = auth()->user();
        $personal = Personal::deNegocio($user->id_negocio)->findOrFail($id);

        $request->validate([
            'nombre'       => 'required|string|max:120',
            'sucursales'   => 'required|array|min:1',
            'sucursales.*' => 'exists:usuarios,id_usuario',
            'activo'       => 'nullable|boolean',
        ]);

        $nombreAnterior = $personal->nombre;
        $nombreNuevo    = $request->nombre;
        $activo         = $request->boolean('activo', true);

        Personal::deNegocio($user->id_negocio)
            ->where('nombre', $nombreAnterior)
            ->update(['nombre' => $nombreNuevo, 'activo' => $activo]);

        $sucursalesActuales = Personal::deNegocio($user->id_negocio)
            ->where('nombre', $nombreNuevo)
            ->pluck('id_usuario')
            ->toArray();

        $sucursalesNuevas = $request->sucursales;

        foreach (array_diff($sucursalesNuevas, $sucursalesActuales) as $idUsuario) {
            Personal::create([
                'id_usuario' => $idUsuario,
                'id_negocio' => $user->id_negocio,
                'nombre'     => $nombreNuevo,
                'activo'     => $activo,
            ]);
        }

        foreach (array_diff($sucursalesActuales, $sucursalesNuevas) as $idUsuario) {
            $reg = Personal::deNegocio($user->id_negocio)
                ->where('nombre', $nombreNuevo)
                ->where('id_usuario', $idUsuario)
                ->first();

            if ($reg) {
                $tieneVentas = \App\Models\VentaVendedor::where('id_personal', $reg->id_personal)->exists();
                $tieneVentas ? $reg->update(['activo' => false]) : $reg->delete();
            }
        }

        CatalogService::invalidatePersonal($user->id_negocio);

        $item = CatalogService::getPersonalItem($nombreNuevo, $user->id_negocio);

        return response()->json([
            'message'  => 'Vendedor actualizado.',
            'personal' => $item,
        ]);
    }

    // ── DESTROY (toggle activo) ───────────────────────────────────────────────
    public function destroy(string $id)
    {
        $user     = auth()->user();
        $personal = Personal::deNegocio($user->id_negocio)->findOrFail($id);

        $nuevoEstado = !$personal->activo;

        Personal::deNegocio($user->id_negocio)
            ->where('nombre', $personal->nombre)
            ->update(['activo' => $nuevoEstado]);

        CatalogService::invalidatePersonal($user->id_negocio);

        return response()->json([
            'message' => $nuevoEstado ? 'Vendedor activado.' : 'Vendedor desactivado.',
            'activo'  => $nuevoEstado,
        ]);
    }

    // ── AJAX: vendedores activos de la sucursal (punto de venta) ─────────────
    public function porSucursal(Request $request)
    {
        $user = auth()->user();

        $personal = Personal::deNegocio($user->id_negocio)
            ->deSucursal($user->id_usuario)
            ->activo()
            ->orderBy('nombre')
            ->get(['id_personal', 'nombre']);

        return response()->json($personal);
    }
}