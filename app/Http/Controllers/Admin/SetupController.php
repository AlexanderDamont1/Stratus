<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Usuario;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class SetupController extends Controller
{
    /**
     * Procesa el formulario de setup obligatorio.
     *
     * Valida todos los vendedores en batch, los crea y
     * promueve al admin de rol 44 → 1 en una sola operación.
     */
    public function completar(Request $request): RedirectResponse
    {
        $admin = Auth::user();

        // Doble verificación: solo rol 44 puede llegar aquí
        if (! $admin->enModoSetup()) {
            abort(403, 'No tienes permiso para esta acción.');
        }

        $maxUsuarios = $admin->negocio->max_users ?? 1;

        // ── Validación batch de todos los vendedores ──────────────────────
        $rules = [];
        $messages = [];

        for ($i = 0; $i < $maxUsuarios; $i++) {
            $rules["vendedores.{$i}.nombre"]   = ['required', 'string', 'max:255'];
            $rules["vendedores.{$i}.correo"]   = ['required', 'email', 'max:255', 'unique:usuarios,correo'];
            $rules["vendedores.{$i}.username"] = [
                'required', 'string', 'max:255',
                'unique:usuarios,username',
                'regex:/^[a-zA-Z0-9_]+$/',
            ];
            $rules["vendedores.{$i}.password"] = ['required', Rules\Password::defaults()];

            $messages["vendedores.{$i}.correo.unique"]    = "El correo del vendedor " . ($i + 1) . " ya está registrado.";
            $messages["vendedores.{$i}.username.unique"]  = "El username del vendedor " . ($i + 1) . " ya existe.";
            $messages["vendedores.{$i}.username.regex"]   = "El username del vendedor " . ($i + 1) . " solo permite letras, números y _.";
        }

        $validated = $request->validate($rules, $messages);

        // ── Crear vendedores ──────────────────────────────────────────────
        foreach ($validated['vendedores'] as $datos) {
            Usuario::create([
    'id_negocio'     => $admin->id_negocio,
    'nombre_usuario' => $datos['nombre'],
    'correo'         => $datos['correo'],
    'username'       => $datos['username'],
    'password'       => Hash::make($datos['password']),
    'id_rol'         => 2,
]);
        }

        // ── Promover admin: rol 44 → 1 ────────────────────────────────────
        $admin->update(['id_rol' => 1]);

        return redirect()->route('dashboard')
            ->with('success', '¡Configuración completada! Bienvenido a tu panel de administración.');
    }
}