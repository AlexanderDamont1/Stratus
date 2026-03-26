<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Usuario;
use Illuminate\Support\Facades\Auth;

class Administrador
{
    public function handle(Request $request, Closure $next): Response
    {
        $usuario = Auth::user();

        // 🔒 Blindaje total
        if (! $usuario instanceof Usuario) {
            abort(404);
        }

        if (! in_array($usuario->id_rol, [1])) {
            abort(404);
        }

        return $next($request);
    }
}