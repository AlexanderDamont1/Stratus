<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\View\View;
use App\Events\SessionTokenUpdated;
use App\Models\Usuario;


class AuthenticatedSessionController extends Controller
{
    /**
     * Mostrar formulario de login.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Manejar el intento de autenticación.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'correo'   => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::attempt($request->only('correo', 'password'), $request->boolean('remember'))) {
            return back()->withErrors([
                'correo' => __('Las credenciales proporcionadas no coinciden con nuestros registros.'),
            ])->onlyInput('correo');
        }

        $request->session()->regenerate();

        $usuario = Auth::user();

        if ($usuario->requiereSesionUnica()) {
            $newToken = Str::uuid()->toString();
            $usuario->session_token = $newToken;
            $usuario->save();
            session(['session_token' => $newToken]);
            broadcast(new SessionTokenUpdated($usuario->id_usuario, $newToken));
        }

        return redirect()->intended($this->dashboardPorRol($usuario->id_rol));
    }

    private function dashboardPorRol(int $rol): string
    {
        return match ($rol) {
            0 => route('root.dashboard'),
            1 => route('administrador.dashboard'),
            2 => route('stock.index'),
            5 => route('gestor.dashboard'),
            default => route('root.dashboard'),
        };
    }


    /**
     * Cerrar sesión.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $usuario = Auth::user();

        if ($usuario) {
            // ✅ Broadcast ANTES de logout para que el WS aún esté autenticado
            event(new \App\Events\UsuarioCerroSesion($usuario->id_usuario, 'logout'));

            if ($usuario->requiereSesionUnica()) {
                $usuario->session_token = null;
                $usuario->save();
            }
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
