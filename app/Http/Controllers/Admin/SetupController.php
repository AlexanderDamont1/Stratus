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
use Illuminate\Support\Str;
use App\Services\CatalogService;

class SetupController extends Controller
{
        private function generarIdUsuario(): string
    {
        $fecha  = Carbon::now()->format('ymd'); // Ejemplo: 260314
        $letras = Str::upper(Str::random(3));   // Genera exactamente 3 letras
        $random = random_int(100, 999);         // Bajé a 3 dígitos para que el ID sea simétrico, pero puedes dejar 9999 si prefieres.

        return 'USR' . $fecha . $letras . $random;
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
                $verificationToken = Str::random(64);

                $nuevoVendedor = Usuario::create([
                    'id_usuario'               => $this->generarIdUsuario(),
                    'id_negocio'               => $admin->id_negocio,
                    'nombre_usuario'           => $vendedor['nombre'],
                    'correo'                   => $vendedor['correo'],
                    'password'                 => Hash::make($vendedor['password']),
                    'id_rol'                   => 2,
                    'email_verified_at'        => null,
                    'email_verification_token' => $verificationToken,
                ]);

                $nuevoVendedor->notify(new VerificarEmailNotification(
                    $verificationToken,
                    $vendedor['nombre']
                ));
            }

            $admin->negocio->update([
                'trial_ends_at'  => now()->addDays(14),
                'negocio_status' => 'trial',
            ]);

            $admin->update(['id_rol' => 1]);

            CatalogService::invalidateStockVendedores($admin->id_negocio);
            CatalogService::invalidateSucursales($admin->id_negocio);
        });

        return redirect()
            ->route('dashboard')
            ->with('success', 'Cuenta activada correctamente');
    }
}