<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class SingleSessionMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {

            $usuario = Auth::user();

            if ($usuario->requiereSesionUnica()) {

                $tokenEnSesion = session('session_token');
                $tokenEnBD     = $usuario->session_token;

                // Si los tokens no coinciden, esta sesión fue desplazada
                if (! $tokenEnSesion || $tokenEnSesion !== $tokenEnBD) {

                    Auth::logout();

                    $request->session()->invalidate();
                    $request->session()->regenerateToken();

                    return redirect()->route('login')
                        ->withErrors(['sesion' => 'Tu sesión fue iniciada en otro dispositivo. Por seguridad, fuiste desconectado.']);
                }
            }
        }

        return $next($request);
    }
}