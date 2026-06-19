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
use App\Jobs\EnviarVerificacionEmailJob;

class SetupController extends Controller
{
    private function generarIdUsuario(): string
    {
        $fecha  = Carbon::now()->format('ymd');
        $letras = Str::upper(Str::random(3));
        $random = random_int(100, 999);

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
            $messages["vendedores.$i.nombre.required"]   = "El nombre de la sucursal # $num es obligatorio.";
            $messages["vendedores.$i.correo.required"]   = "El correo de la sucursal # $num es obligatorio.";
            $messages["vendedores.$i.correo.email"]      = "El correo de la sucursal # $num no es válido.";
            $messages["vendedores.$i.correo.unique"]     = "Intenta con otro correo";
            $messages["vendedores.$i.password.required"] = "La contraseña de la sucursal # $num es obligatoria.";
            $messages["vendedores.$i.password.min"]      = "La contraseña de la sucursal # $num debe ser de al menos 8 caracteres";
        }

        $data = $request->validate($rules, $messages);

        $vendedoresCreados = [];

        DB::transaction(function () use ($data, $admin, &$vendedoresCreados) {
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

                $vendedoresCreados[] = [
                    'usuario' => $nuevoVendedor,
                    'token'   => $verificationToken,
                    'nombre'  => $vendedor['nombre'],
                ];
            }

            $admin->negocio->update([
                'trial_ends_at'  => now()->addDays(14),
                'negocio_status' => 'trial',
            ]);

            $admin->update(['id_rol' => 1]);

            CatalogService::invalidateStockVendedores($admin->id_negocio);
            CatalogService::invalidateSucursales($admin->id_negocio);
        });

        foreach ($vendedoresCreados as $item) {
            EnviarVerificacionEmailJob::dispatch(
                $item['usuario'],
                $item['token'],
                $item['nombre']
            );
        }

        return redirect()
            ->route('administrador.dashboard')
            ->with('success', 'Cuenta activada correctamente');
    }
}