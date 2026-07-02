<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\View\View;
use App\Events\SessionTokenUpdated;
use App\Models\Usuario;

class AuthenticatedSessionController extends Controller
{
    public function create(): View
    {
        return view('auth.login');
    }

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

        // Sesión única
        if ($usuario->requiereSesionUnica()) {
            $newToken = Str::uuid()->toString();
            $usuario->session_token = $newToken;
            $usuario->save();
            session(['session_token' => $newToken]);
            broadcast(new SessionTokenUpdated($usuario->id_usuario, $newToken));
        }

        $rol = $usuario->id_rol;

        // Roles permitidos
        $allowedRoles = [0, 1, 2, 5, 44];

        if (! in_array($rol, $allowedRoles)) {
            // Cerrar sesión por rol no autorizado
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')->withErrors([
                'correo' => 'Rol no autorizado para acceder al sistema.'
            ]);
        }

        // Redirigir según el rol
        if ($rol === 44) {
            return redirect()->route('rol44.dashboard');
        }

        return redirect()->intended($this->dashboardPorRol($rol));
    }

    public function dashboardPorRol(int $rol): string
    {
        return match ($rol) {
            0 => route('root.dashboard'),
            1 => route('administrador.dashboard'),
            2 => route('stock.index'),
            5 => route('gestor.dashboard'),
            default => route('rol44.dashboard'), // ← ahora va a la vista en blanco
        };
    }

    /**
     * Vista para el rol 44
     */
    public function rol44Dashboard(): View|RedirectResponse
    {
        $usuario = Auth::user();

        if (! $usuario) {
            return redirect()->route('login');
        }

        // Si el usuario no tiene rol 44, redirigir a su dashboard correspondiente
        if ($usuario->id_rol !== 44) {
            return redirect()->intended($this->dashboardPorRol($usuario->id_rol));
        }

        // Si necesitas pasar datos a la vista, hazlo aquí
        // $negocio = $usuario->negocio; // opcional

        return view('auth.rol44'); // ← vista en blanco
    }

    public function destroy(Request $request): RedirectResponse
    {
        $usuario = Auth::user();

        if ($usuario) {
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


    public function verificacionPendiente()
    {
        $user = auth()->user();

        if ($user->emailVerificado()) {
            // Al estar en la misma clase, puedes usar $this sin problemas
            return redirect()->to($this->dashboardPorRol($user->id_rol)); 
        }

        return view('auth.verificacion-pendiente');
    }
}