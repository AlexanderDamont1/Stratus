<?php

namespace App\Http\Middleware;

use App\Services\ModuloService;
use Closure;
use Illuminate\Http\Request;

class VerificarModulo
{
    public function handle(Request $request, Closure $next, string $idModulo): mixed
    {
        $user = auth()->user();

        if (!$user) abort(403);

        // Root siempre pasa — nunca se bloquea
        if ($user->id_rol === 0) {
            return $next($request);
        }

        if (!ModuloService::tiene($user->id_negocio, $user->id_rol, $idModulo)) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Módulo no disponible.'], 403);
            }
            abort(403, 'Tu plan no incluye este módulo.');
        }

        return $next($request);
    }
}