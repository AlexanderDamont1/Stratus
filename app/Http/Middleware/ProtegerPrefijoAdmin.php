<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use App\Models\Usuario;

class ProtegerPrefijoAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        $usuario = Auth::user();

        if (!$usuario instanceof Usuario || $usuario->id_rol !== 1) {
            abort(404);
        }

        return $next($request);
    }
}