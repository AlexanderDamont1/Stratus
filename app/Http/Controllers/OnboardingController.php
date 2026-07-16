<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class OnboardingController extends Controller
{
    public function completar(Request $request): Response
    {
        $data = $request->validate([
            'clave' => ['required', 'string', 'max:150'],
        ]);

        $request->user()->marcarOnboardingVisto($data['clave']);

        return response()->noContent();
    }

    public function reiniciar(Request $request): JsonResponse
    {
        $request->user()->reiniciarOnboarding();

        return response()->json([
            'ok'      => true,
            'mensaje' => 'Tutorial reiniciado correctamente. Las guías volverán a aparecer en cada pantalla.',
        ], 200);
    }
}
