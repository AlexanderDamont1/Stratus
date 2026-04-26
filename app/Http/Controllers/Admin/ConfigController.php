<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NegocioConfig;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ConfigController extends Controller
{
    public function index()
    {
        $user   = Auth::user();
        if ($user->id_rol !== 1) abort(403);

        $config = NegocioConfig::firstOrCreate(
            ['id_negocio' => $user->id_negocio],
            ['entrega_comprobante' => 'ticket']
        );

        return view('administrador.config.index', compact('config'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();
        if ($user->id_rol !== 1) abort(403);

        $request->validate([
            'entrega_comprobante' => 'required|in:ticket,correo',
        ]);

        NegocioConfig::updateOrCreate(
            ['id_negocio' => $user->id_negocio],
            ['entrega_comprobante' => $request->entrega_comprobante]
        );

        return back()->with('success', 'Configuración guardada correctamente.');
    }
}