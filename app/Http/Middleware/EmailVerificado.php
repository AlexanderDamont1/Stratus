<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EmailVerificado
{
    public function handle(Request $request, Closure $next)
    {
        $user = auth()->user();

        if (!$user) return $next($request);

        // Root siempre pasa
        if ($user->id_rol === 0) return $next($request);

        // Rutas excluidas
        if ($request->is('verificar-email*', 'reenviar-verificacion', 'logout', 'login')) {
            return $next($request);
        }

        if (!$user->emailVerificado()) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Email no verificado.'], 403);
            }
            return redirect()->route('verificacion.pendiente');
        }

        return $next($request);
    }
}