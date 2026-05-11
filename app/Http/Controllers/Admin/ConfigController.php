<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NegocioConfig;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\CatalogService;

class ConfigController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        if ($user->id_rol !== 1) abort(403);

        $definiciones = NegocioConfig::where('activo', true)
            ->orderBy('grupo')
            ->orderBy('orden')
            ->get();

        // Valores actuales del negocio desde caché
        $valores = CatalogService::getConfigNegocio($user->id_negocio);

        return view('administrador.config.index', compact('definiciones', 'valores'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();
        if ($user->id_rol !== 1) abort(403);

        $definiciones = NegocioConfig::where('activo', true)->get()->keyBy('clave');

        foreach ($definiciones as $clave => $def) {
            if (!$request->has($clave)) continue;

            $valor = $def->tipo === 'checkbox_multi'
                ? json_encode($request->input($clave, []))
                : $request->input($clave);

            \App\Models\NegocioConfigValor::updateOrCreate(
                ['id_negocio' => $user->id_negocio, 'clave' => $clave],
                ['valor' => $valor]
            );
        }

        CatalogService::invalidateConfigNegocio($user->id_negocio);

        return back()->with('success', 'Configuración guardada correctamente.');
    }
}