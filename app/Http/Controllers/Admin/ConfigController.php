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

            Log::info('CONFIG UPDATE START', [
                'user' => $user->id_usuario ?? null,
                'negocio' => $user->id_negocio ?? null,
                'request' => $request->all()
            ]);

            if ($user->id_rol !== 1) {
                abort(403);
            }

            $definiciones = NegocioConfig::where('activo', true)
                ->get()
                ->keyBy('clave');

            Log::info('CONFIG DEFINICIONES', [
                'count' => $definiciones->count()
            ]);

            foreach ($definiciones as $clave => $def) {

                Log::info('CONFIG LOOP', [
                    'clave' => $clave,
                    'id_ncf' => $def->id_ncf,
                    'has_request' => $request->has($clave),
                    'request_value' => $request->input($clave)
                ]);

                if (!$request->has($clave)) {
                    continue;
                }

                $valor = $def->tipo === 'checkbox_multi'
                    ? json_encode($request->input($clave, []))
                    : $request->input($clave);

                Log::info('CONFIG VALOR PROCESADO', [
                    'clave' => $clave,
                    'valor' => $valor
                ]);

                $registro = NegocioConfigValor::updateOrCreate(
                    [
                        'id_negocio' => $user->id_negocio,
                        'id_ncf' => $def->id_ncf
                    ],
                    [
                        'valor' => $valor
                    ]
                );

                Log::info('CONFIG GUARDADO', [
                    'saved' => $registro->toArray()
                ]);
            }

            CatalogService::invalidateConfigNegocio($user->id_negocio);

            Log::info('CONFIG CACHE INVALIDATED');

            return back()->with(
                'success',
                'Configuración guardada correctamente.'
            );

        } catch (\Throwable $e) {

            Log::error('CONFIG UPDATE ERROR', [
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
                'trace' => $e->getTraceAsString()
            ]);

            dd($e);

        }
    }
}