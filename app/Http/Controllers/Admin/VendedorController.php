<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Usuario;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;
use App\Services\CatalogService;

class VendedorController extends Controller
{
    /**
     * Formulario para agregar vendedores adicionales (post-setup).
     * Solo Admin con rol 1 puede acceder.
     */
    public function create(): View
    {
        if (Auth::user()->enModoSetup()) {
            abort(403, 'Completa la configuración inicial primero.');
        }

        return view('admin.vendedores.create');
    }

    /**
     * Guardar vendedor adicional (post-setup normal).
     */
    public function store(Request $request): RedirectResponse
    {
        $admin = Auth::user();

        if ($admin->enModoSetup()) {
            abort(403, 'Completa la configuración inicial primero.');
        }

        $request->validate([
            'nombre_usuario' => ['required', 'string', 'max:255'],
            'correo'         => ['required', 'string', 'email', 'max:255', 'unique:usuarios,correo'],
           
            'password'       => ['required', 'confirmed', Rules\Password::defaults()],
        ], [
            'username.regex' => 'El username solo puede contener letras, números y guiones bajos.',
        ]);

        Usuario::create([
            'id_usuario'     => Usuario::generarId(),
            'id_negocio'     => $admin->id_negocio,
            'nombre_usuario' => $request->nombre_usuario,
            'correo'         => $request->correo,
            'password'       => Hash::make($request->password),
            'id_rol'         => 2,
        ]);
           
            CatalogService::invalidateStockVendedores($admin->id_negocio);
            CatalogService::invalidateSucursales($admin->id_negocio);


        return redirect()->route('admin.vendedores.create')
            ->with('success', 'Vendedor creado correctamente.');
    }
}