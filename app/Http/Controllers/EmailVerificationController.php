<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EmailVerificationController extends Controller
{
    // Verificar desde el link del correo
    public function verify(string $token)
    {
        $usuario = Usuario::where('email_verification_token', $token)->first();

        if (!$usuario) {
            return redirect()->route('login')
                ->withErrors(['correo' => 'El enlace de verificación es inválido o ya fue usado.']);
        }

        // Mostrar página de confirmación antes de verificar
        return view('auth.confirmar-verificacion', compact('token'));
    }

    public function confirmar(string $token)
    {
        $usuario = Usuario::where('email_verification_token', $token)->first();

        if (!$usuario) {
            return redirect()->route('login')
                ->withErrors(['correo' => 'El enlace de verificación es inválido o ya fue usado.']);
        }

        $usuario->marcarEmailVerificado();

        return redirect()->route('login')
            ->with('success', '¡Correo verificado! Ya puedes iniciar sesión.');
    }

    // Reenviar correo de verificación
    public function reenviar(Request $request)
    {
        $usuario = Auth::user();

        if ($usuario->emailVerificado()) {
            return back()->with('success', 'Tu correo ya está verificado.');
        }

        $token = $usuario->generarTokenVerificacion();

        $usuario->notify(new \App\Notifications\VerificarEmailNotification(
            $token,
            $usuario->nombre_usuario
        ));

        return back()->with('success', 'Correo de verificación reenviado.');
    }
}