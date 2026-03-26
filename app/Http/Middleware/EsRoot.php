<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EsRoot
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! auth()->check() || auth()->user()->id_rol !== 0) {
            abort(404);
        }

        return $next($request);
    }
}
