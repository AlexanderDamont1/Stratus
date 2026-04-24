<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class TrialExpirado
{
    protected array $excluidas = [
        'trial/expirado',
        'suscripcion/expirada',
        'logout',
        'login',
    ];

    public function handle(Request $request, Closure $next)
    {
        $user = auth()->user();

        if (!$user || !$user->id_negocio) {
            return $next($request);
        }

        // Root nunca se bloquea
        if ($user->id_rol === 0) {
            return $next($request);
        }

        foreach ($this->excluidas as $ruta) {
            if ($request->is($ruta)) return $next($request);
        }

        $negocio = $user->negocio;

        if (!$negocio || $negocio->estaActivo()) {
            return $next($request);
        }

        $ruta = match($negocio->negocio_status) {
            'trial_expirado'        => route('trial.expirado'),
            'suscripcion_expirada'  => route('suscripcion.expirada'),
            'suspendido'            => route('trial.expirado'),
            default                 => null,
        };

        if (!$ruta) return $next($request);

        if ($request->expectsJson()) {
            return response()->json([
                'message'  => 'Acceso suspendido.',
                'redirect' => $ruta,
            ], 403);
        }

        return redirect($ruta);
    }
}