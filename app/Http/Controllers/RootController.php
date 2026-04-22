<?php

namespace App\Http\Controllers;

use App\Models\Negocio;
use App\Models\RegistroLink;
use App\Services\ModuloService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class RootController extends Controller
{
    /*
    |----------------------------------------
    | Dashboard principal del Root
    |----------------------------------------
    */
    public function index()
    {
        $links    = RegistroLink::latest()->paginate(10);
        $negocios = Negocio::with('admin')->latest()->paginate(10);

        return view('root.dashboard', compact('links', 'negocios'));
    }

    /*
    |----------------------------------------
    | Crear link de registro
    |----------------------------------------
    */
    public function storeLink(Request $request)
    {
        $request->validate([
            'max_users' => ['required', 'integer', 'min:1', 'max:100'],
        ]);

        RegistroLink::create([
            'token'      => Str::uuid(),
            'max_users'  => $request->max_users,
            'usado'      => false,
            'expires_at' => now()->addHours(24),
        ]);

        return back()->with('success', 'Link creado. Expira en 24 horas.');
    }

    /*
    |----------------------------------------
    | Eliminar link
    |----------------------------------------
    */
    public function destroyLink(RegistroLink $link)
    {
        $link->delete();

        return back()->with('success', 'Link eliminado correctamente.');
    }

    /*
    |----------------------------------------
    | Módulos por negocio
    |----------------------------------------
    */
    public function modulos(string $idNegocio)
    {
        $negocio = Negocio::findOrFail($idNegocio);
        $estado  = ModuloService::getEstadoCompleto($idNegocio);

        return view('root.modulos', compact('negocio', 'estado'));
    }

    /*
    |----------------------------------------
    | Toggle AJAX — activar/desactivar módulo
    |----------------------------------------
    */
    public function toggleModulo(Request $request)
    {
        $request->validate([
            'id_negocio' => 'required|string|exists:negocios,id_negocio',
            'id_modulo'  => 'required|string|exists:modulos,id_modulo',
            'id_rol'     => 'required|integer|in:1,2,5',
            'activo'     => 'required|boolean',
        ]);

        ModuloService::toggle(
            $request->id_negocio,
            $request->id_modulo,
            $request->id_rol,
            $request->activo
        );

        return response()->json(['ok' => true]);
    }
}