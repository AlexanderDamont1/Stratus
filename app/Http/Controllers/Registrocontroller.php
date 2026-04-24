<?php

namespace App\Http\Controllers;

use App\Models\Negocio;
use App\Models\RegistroLink;
use App\Models\Usuario;
use App\Notifications\VerificarEmailNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class RegistroController extends Controller
{
    public function show(string $token)
    {
        $link = $this->linkValido($token);
        return view('registro.admin', compact('link'));
    }

    public function store(Request $request, string $token)
    {
        $link = $this->linkValido($token);

        $request->validate([
            'nombre_negocio' => ['required', 'string', 'max:100'],
            'nombre_usuario'  => ['required', 'string', 'max:100'],
            'correo'          => ['required', 'email', 'unique:usuarios,correo'],
            'password'        => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $verificationToken = Str::random(64);

        DB::transaction(function () use ($request, $link, $verificationToken) {

            $negocio = Negocio::create([
                'nombre_negocio' => $request->nombre_negocio,
                'max_users'      => $link->max_users,
            ]);

            $usuario = Usuario::create([
                'id_negocio'               => $negocio->id_negocio,
                'nombre_usuario'           => $request->nombre_usuario,
                'correo'                   => $request->correo,
                'password'                 => Hash::make($request->password),
                'id_rol'                   => 44,
                'email_verified_at'        => null,   // ← pendiente
                'email_verification_token' => $verificationToken,
            ]);

            $link->update(['usado' => true]);

            // Enviar correo de verificación
            $usuario->notify(new VerificarEmailNotification(
                $verificationToken,
                $request->nombre_usuario
            ));
        });

        return redirect()->route('login')
            ->with('success', 'Cuenta creada. Revisa tu correo para verificarla antes de iniciar sesión.');
    }

    private function linkValido(string $token): RegistroLink
    {
        return RegistroLink::where('token', $token)
            ->where('usado', false)
            ->where(fn ($q) => $q->whereNull('expires_at')->orWhere('expires_at', '>', now()))
            ->firstOrFail();
    }
}