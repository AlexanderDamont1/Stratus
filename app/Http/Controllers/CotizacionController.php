<?php

namespace App\Http\Controllers;

use App\Services\ReparacionService;
use Illuminate\Http\Request;
use App\Models\Cotizacion;

class CotizacionController extends Controller
{
    /**
     * GET /cotizacion/{token}
     * Si viene con ?r=0 o ?r=1 desde el email, procesa directo.
     * Si no, muestra la página con botones.
     */
    public function show(string $token, Request $request)
    {
        // CORREGIDO: era with('mantenimiento')
        $cotizacion = Cotizacion::where('token', $token)
            ->with('reparacion')
            ->firstOrFail();

        if (!is_null($cotizacion->respuesta)) {
            return view('cotizacion.ya_respondida', compact('cotizacion'));
        }

        if ($cotizacion->expirada()) {
            return view('cotizacion.expirada', compact('cotizacion'));
        }

        // El cliente pulsó directamente el botón del email (?r=1 o ?r=0)
        if ($request->has('r') && in_array($request->query('r'), ['0', '1'])) {
            try {
                $cotizacion = ReparacionService::procesarRespuestaToken(
                    $token,
                    (int) $request->query('r')
                );
            } catch (\Symfony\Component\HttpKernel\Exception\HttpException $e) {
                // 409 ya respondida, 410 expirada
                if ($e->getStatusCode() === 409) {
                    return view('cotizacion.ya_respondida', compact('cotizacion'));
                }
                return view('cotizacion.expirada', compact('cotizacion'));
            }

            $vista = $cotizacion->aceptada()
                ? 'cotizacion.aceptada'
                : 'cotizacion.rechazada';

            return view($vista, compact('cotizacion'));
        }

        // Visita directa sin parámetro → página con botones
        return view('cotizacion.responder', compact('cotizacion'));
    }

    /**
     * POST /cotizacion/{token}/responder
     * Fallback por si el cliente usa el formulario de la página responder.
     */
    public function responder(Request $request, string $token)
    {
        $request->validate([
            'respuesta' => 'required|in:0,1',
        ]);

        try {
            $cotizacion = ReparacionService::procesarRespuestaToken(
                $token,
                (int) $request->respuesta
            );
        } catch (\Symfony\Component\HttpKernel\Exception\HttpException $e) {
            $cotizacion = \App\Models\Cotizacion::where('token', $token)->firstOrFail();

            if ($e->getStatusCode() === 409) {
                return view('cotizacion.ya_respondida', compact('cotizacion'));
            }
            return view('cotizacion.expirada', compact('cotizacion'));
        }

        $vista = $cotizacion->aceptada() ? 'cotizacion.aceptada' : 'cotizacion.rechazada';

        return view($vista, compact('cotizacion'));
    }
}