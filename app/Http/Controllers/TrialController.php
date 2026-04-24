<?php

namespace App\Http\Controllers;

class TrialController extends Controller
{
    public function expirado()
    {
        $negocio = auth()->user()->negocio;
        return view('trial.expirado', compact('negocio'));
    }

    public function suscripcionExpirada()
    {
        $negocio = auth()->user()->negocio;
        return view('trial.suscripcion-expirada', compact('negocio'));
    }
}