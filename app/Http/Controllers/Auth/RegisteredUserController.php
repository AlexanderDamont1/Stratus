<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Usuario;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'nombre_usuario' => ['required', 'string', 'max:255'],
            'correo' => ['required', 'string', 'email', 'max:255', 'unique:usuarios,correo'],
            'username' => ['required', 'string', 'max:255', 'unique:usuarios,username'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $usuario = Usuario::create([
            'id_usuario' => Usuario::generarId(),
            'id_negocio' => $request->id_negocio ?? null, // si aplica
            'nombre_usuario' => $request->nombre_usuario,
            'correo' => $request->correo,
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'id_rol' => 0, // 🔥 ROOT por defecto
        ]);

        event(new Registered($usuario));

        Auth::login($usuario);

        return redirect()->route('dashboard');
    }
}
