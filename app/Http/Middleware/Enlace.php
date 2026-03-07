<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Usuario;
use Illuminate\Support\Facades\Auth;

class Enlace
{
    public function handle(Request $request, Closure $next): Response
    {
        $usuario = Auth::user();

        // 🔒 Blindaje total
        if (! $usuario instanceof Usuario) {
            abort(403, 'Acceso restringido.');
        }

        if (! in_array($usuario->id_rol, [1, 5])) {
            abort(403, 'Acceso restringido.');
        }

        return $next($request);
    }
}