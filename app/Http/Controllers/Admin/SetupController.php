<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Carbon\Carbon;

class SetupController extends Controller
{
    private function generarIdUsuario(): string
    {
        $fecha  = Carbon::now()->format('ymd');
        $random = random_int(100000000, 999999999);

        return 'USR' . $fecha . $random;
    }

    public function completar(Request $request)
    {
        $admin = Auth::user();

        if (! $admin instanceof Usuario || ! $admin->enModoSetup()) {
            abort(403, 'No autorizado');
        }

        $maxUsuarios = optional($admin->negocio)->max_users ?? 2;

        $rules = [
            'vendedores' => 'required|array|min:1',
        ];

        for ($i = 0; $i < $maxUsuarios; $i++) {
            $rules["vendedores.$i.nombre"]   = 'required|string|max:255';
            $rules["vendedores.$i.correo"]   = 'required|email|unique:usuarios,correo';
            $rules["vendedores.$i.password"] = ['required', Rules\Password::defaults()];
        }

        $messages = [];
        for ($i = 0; $i < $maxUsuarios; $i++) {
            $num = $i + 1;
            $messages["vendedores.$i.nombre.required"]   = "El nombre del vendedor $num es obligatorio.";
            $messages["vendedores.$i.correo.required"]   = "El correo del vendedor $num es obligatorio.";
            $messages["vendedores.$i.correo.email"]      = "El correo del vendedor $num no es válido.";
            $messages["vendedores.$i.correo.unique"]     = "Intenta con otro correo";
            $messages["vendedores.$i.password.required"] = "La contraseña del vendedor $num es obligatoria.";
        }

        $data = $request->validate($rules, $messages);

        DB::transaction(function () use ($data, $admin) {

            foreach ($data['vendedores'] as $vendedor) {
                Usuario::create([
                    'id_usuario'     => $this->generarIdUsuario(),
                    'id_negocio'     => $admin->id_negocio,
                    'nombre_usuario' => $vendedor['nombre'],
                    'correo'         => $vendedor['correo'],
                    'password'       => Hash::make($vendedor['password']),
                    'id_rol'         => 2,
                ]);
            }

            // salir del modo setup
            $admin->update(['id_rol' => 1]);
        });

        return redirect()
            ->route('dashboard')
            ->with('success', 'Cuenta activada correctamente');
    }
}