<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Usuario;

class SingleSessionMiddleware

{
    public function handle(Request $request, Closure $next): Response
    {
        // Broadcasting maneja su propia autenticación en channels.php
        // No necesita validación de sesión única
        if ($request->is('broadcasting/auth')) {
           // \Log::info('broadcasting/auth bypass activado');
            return $next($request);
        }

        $usuario = Auth::user();

        if (! $usuario instanceof Usuario) {
            return $next($request);
        }

        if (! method_exists($usuario, 'requiereSesionUnica')) {
            return $next($request);
        }

        if ($usuario->requiereSesionUnica()) {

            $tokenEnSesion = session('session_token');
            $tokenEnBD     = $usuario->session_token;

            if (! $tokenEnSesion || $tokenEnSesion !== $tokenEnBD) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect()
                    ->route('login')
                    ->withErrors([
                        'sesion' => 'Tu sesión fue iniciada en otro dispositivo. Por seguridad, fuiste desconectado.'
                    ]);
            }
        }

        return $next($request);
    }
}
