<?php

namespace App\Http\Controllers;

use App\Models\Negocio;
use App\Models\RegistroLink;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class RegistroController extends Controller
{
    /*
    |----------------------------------------
    | Mostrar formulario de registro
    |----------------------------------------
    */
    public function show(string $token)
    {
        $link = $this->linkValido($token);

        return view('registro.admin', compact('link'));
    }

    /*
    |----------------------------------------
    | Crear negocio + admin
    |----------------------------------------
    */
    public function store(Request $request, string $token)
    {
        $link = $this->linkValido($token);

        $request->validate([
            'nombre_negocio' => ['required', 'string', 'max:100'],
            'nombre_usuario'  => ['required', 'string', 'max:100'],
            'correo'          => ['required', 'email', 'unique:usuarios,correo'],
            'password'        => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        DB::transaction(function () use ($request, $link) {

            // 1. Crear el negocio
            $negocio = Negocio::create([
                'nombre_negocio' => $request->nombre_negocio,
                'max_users'      => $link->max_users,
            ]);

            // 2. Crear el admin con id_rol = 1
            Usuario::create([
                'id_negocio'     => $negocio->id_negocio,
                'nombre_usuario' => $request->nombre_usuario,
                'correo'         => $request->correo,
                'password'       => Hash::make($request->password),
                'id_rol'         => 44, // Admin siempre
            ]);

            // 3. Matar el link ☠️
            $link->update(['usado' => true]);
        });

        return redirect()->route('login')
                         ->with('success', 'Negocio creado. Ya puedes iniciar sesión.');
    }

    /*
    |----------------------------------------
    | Helper: obtener link válido o abortar
    |----------------------------------------
    */
    private function linkValido(string $token): RegistroLink
    {
        return RegistroLink::where('token', $token)
            ->where('usado', false)
            ->where(function ($query) {
                $query->whereNull('expires_at')
                      ->orWhere('expires_at', '>', now());
            })
            ->firstOrFail();
        // firstOrFail() lanza 404 automático si no existe, expiró o ya fue usado
    }
}