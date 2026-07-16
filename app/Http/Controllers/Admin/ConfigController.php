<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NegocioConfig;
use App\Models\NegocioConfigValor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Services\CatalogService;

class ConfigController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if ($user->id_rol !== 1) {
            abort(403);
        }

        $definiciones = NegocioConfig::where('activo', true)
            ->orderBy('grupo')
            ->orderBy('orden')
            ->get();

        $valores = CatalogService::getConfigNegocio($user->id_negocio);

        return view(
            'administrador.config.index',
            compact('definiciones', 'valores')
        );
    }

    public function update(Request $request)
    {
        try {
            $user = Auth::user();

            if ($user->id_rol !== 1) {
                abort(403);
            }

            $definiciones = NegocioConfig::where('activo', true)
                ->get()
                ->keyBy('clave');

            $reglas = [];
            $atributos = [];
            foreach ($definiciones as $clave => $def) {
                if (!$request->has($clave)) {
                    continue;
                }

                $reglas[$clave] = match ($def->tipo) {
                    'numero'     => ['required', 'numeric'],
                    'porcentaje' => ['required', 'numeric', 'min:0', 'max:100'],
                    'texto'      => ['nullable', 'string', 'max:255'],
                    default      => ['nullable'],
                };
                $atributos[$clave] = $def->nombre;
            }

            $mensajes = [
                'required' => 'El campo :attribute es obligatorio.',
                'numeric'  => 'El campo :attribute debe ser un número.',
                'min'      => 'El campo :attribute no puede ser menor a :min.',
                'max'      => 'El campo :attribute no puede ser mayor a :max.',
                'string'   => 'El campo :attribute debe ser texto.',
            ];

            $request->validate($reglas, $mensajes, $atributos);

            foreach ($definiciones as $clave => $def) {
                if (!$request->has($clave)) {
                    continue;
                }

                $valor = $def->tipo === 'checkbox_multi'
                    ? json_encode($request->input($clave, []))
                    : $request->input($clave);

                NegocioConfigValor::updateOrCreate(
                    [
                        'id_negocio' => $user->id_negocio,
                        'id_ncf' => $def->id_ncf
                    ],
                    [
                        'valor' => $valor
                    ]
                );
            }

            CatalogService::invalidateConfigNegocio($user->id_negocio);

            return back()->with(
                'success',
                'Configuración guardada correctamente.'
            );

        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e;
        } catch (\Throwable $e) {

            Log::error('CONFIG UPDATE ERROR', [
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
            ]);

            return back()->with('error', 'Ocurrió un error al guardar la configuración.');
        }
    }
}