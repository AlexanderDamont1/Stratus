<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Jobs\EnviarBienvenidaJob;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;

class VerifyEmailController extends Controller
{
    public function __invoke(EmailVerificationRequest $request): RedirectResponse
    {
        $usuario = $request->user();

        if ($usuario->hasVerifiedEmail()) {
            return redirect()->intended(route('dashboard', absolute: false) . '?verified=1');
        }

        if ($usuario->markEmailAsVerified()) {
            event(new Verified($usuario));

            // Despachar bienvenida en background
            EnviarBienvenidaJob::dispatch(
                usuario:        $usuario,
                nombreNegocio:  $usuario->negocio?->nombre_negocio ?? 'tu negocio',
                trialEndsAt:    $usuario->negocio?->trial_ends_at?->toDateTimeString(),
            );
        }

        return redirect()->intended(route('dashboard', absolute: false) . '?verified=1');
    }
}