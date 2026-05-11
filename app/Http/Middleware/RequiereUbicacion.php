<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RequiereUbicacion
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        if (
            $user &&
            $user->esVendedor() &&
            ! $user->tieneUbicacion() &&
            ! $request->routeIs('ubicacion.*') &&
            ! $request->routeIs('logout')
        ) {
            return redirect()->route('ubicacion.index');
        }

        return $next($request);
    }
}