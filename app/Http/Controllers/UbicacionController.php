<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UbicacionController extends Controller
{
    public function index()
    {
        // Si ya tiene ubicación no necesita estar aquí
        if (Auth::user()->tieneUbicacion()) {
            return redirect()->route('dashboard');
        }

        return view('ubicacion.index');
    }

    public function guardar(Request $request)
    {
        $data = $request->validate([
            'direccion' => 'required|string|max:500',
            'lat'       => 'required|numeric|between:-90,90',
            'lng'       => 'required|numeric|between:-180,180',
            'place_id'  => 'nullable|string|max:255',
        ], [
            'direccion.required' => 'Selecciona una ubicación en el mapa.',
            'lat.required'       => 'Selecciona una ubicación válida.',
            'lng.required'       => 'Selecciona una ubicación válida.',
        ]);

        Auth::user()->update([
            'direccion' => $data['direccion'],
            'lat'       => $data['lat'],
            'lng'       => $data['lng'],
            'place_id'  => $data['place_id'] ?? null,
        ]);

        return redirect()->route('dashboard')
            ->with('success', '¡Ubicación guardada! Bienvenido a ArrowK.');
    }
}