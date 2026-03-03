<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class Gestor
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check() || auth()->user()->id_rol !== 5) {
            abort(403, 'Acceso restringido.');
        }

        return $next($request);
    }
}