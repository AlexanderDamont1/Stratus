<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Detecta admin en modo instalación (id_rol = 44).
 *
 * En lugar de redirigir a otra página, pasa una variable a la sesión
 * flash para que el layout inyecte el modal obligatorio. El admin
 * puede navegar a cualquier ruta protegida, pero el modal estará
 * siempre presente e inamovible hasta completar el setup.
 */
class ForceAdminSetupMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check() && Auth::user()->enModoSetup()) {

            // Rutas que el admin en setup puede usar sin el modal activo
            // (POST del propio setup — evita loop)
            if (! $request->routeIs('admin.setup.completar')) {
                session()->flash('force_setup_modal', true);
            }
        }

        return $next($request);
    }
}