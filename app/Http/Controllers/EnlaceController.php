<?php

namespace App\Http\Controllers;

use App\Models\Enlace;
use App\Models\Pedido;
use App\Events\EnlaceUpdated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class EnlaceController extends Controller
{
    const CACHE_PREFIX = 'enlace:';
    const CACHE_TTL = 300; // 5 minutos

    // Dashboard del gestor (sin caché para evitar inconsistencias)
    public function dashboard(Request $request)
    {
        $usuario = Auth::user();
        if ($usuario->id_rol !== 5) abort(404);
        $enlaces = Enlace::where('id_usuario2', $usuario->id_usuario)
            ->with('usuarioAdmin:id_usuario,nombre_usuario,correo')
            ->orderBy('updated_at', 'desc')
            ->paginate(10);

        return view('gestor.dashboard', compact('enlaces'));
    }

    // Vista del administrador (con caché)
    public function index()
{
    $usuario = Auth::user();

    $enlace = Enlace::where('id_usuario1', $usuario->id_usuario)
        ->with('usuarioDestino:id_usuario,nombre_usuario,correo')
        ->orderBy('created_at', 'desc')
        ->first();

    return view('administrador.dashboard', ['enlace' => $enlace]);
}

public function generar(Request $request)
{
    $usuario = Auth::user();
    if ($usuario->id_rol !== 1) abort(403);

    $existente = Enlace::where('id_usuario1', $usuario->id_usuario)
        ->whereIn('estado', ['pendiente', 'activo'])
        ->first();

    if ($existente) {
        return redirect()->route('enlaces.index')
            ->with('error', 'Ya tienes un enlace activo o pendiente.');
    }

    Enlace::create(['id_usuario1' => $usuario->id_usuario]);

    return redirect()->route('enlaces.index')
        ->with('success', 'Token generado correctamente. Compártelo con el gestor.');
}

    // Aceptar token (rol 5)
    public function aceptar(Request $request)
    {
        $validator = validator($request->all(), [
            'token_enlace' => 'required|string',
        ]);

        if ($validator->fails()) {
            return redirect()->route('gestor.dashboard')
                ->with('error', 'El token no puede estar vacío.');
        }

        $usuario = Auth::user();
        if ($usuario->id_rol !== 5) abort(403);

        $enlace = Enlace::where('token_enlace', $request->token_enlace)
            ->where('estado', 'pendiente')
            ->first();

        if (!$enlace) {
            return redirect()->route('gestor.dashboard')
                ->with('error', 'Token inválido o ya utilizado.');
        }

        if ($enlace->id_usuario1 === $usuario->id_usuario) {
            return redirect()->route('gestor.dashboard')
                ->with('error', 'No puedes enlazarte contigo mismo.');
        }

        $enlace->update([
            'id_usuario2' => $usuario->id_usuario,
            'estado'      => 'activo',
        ]);

        Cache::forget(self::CACHE_PREFIX . "usuario1:{$enlace->id_usuario1}");
        Cache::forget(self::CACHE_PREFIX . "pedidos:gestor:{$usuario->id_usuario}");

        $enlaceFresh = Enlace::with('usuarioDestino')->find($enlace->id_enlace);
        event(new EnlaceUpdated($enlaceFresh, 'aceptado'));

        return redirect()->route('gestor.dashboard')
            ->with('success', 'Enlace activado correctamente.');
    }

    // Cancelar/desenlazar (rol 1 o 5)
    public function cancelar(string $id_enlace)
    {
        $usuario = Auth::user();

        $enlace = Enlace::where('id_enlace', $id_enlace)
            ->where(function ($q) use ($usuario) {
                $q->where('id_usuario1', $usuario->id_usuario)
                    ->orWhere('id_usuario2', $usuario->id_usuario);
            })
            ->firstOrFail();

        if ($usuario->id_rol === 1) {
            $enlace->delete();
            Cache::forget(self::CACHE_PREFIX . "usuario1:{$usuario->id_usuario}");
            return redirect()->route('administrador.dashboard')
                ->with('success', 'Enlace eliminado correctamente.');
        }

        $enlace->update(['estado' => 'cancelado']);

        Cache::forget(self::CACHE_PREFIX . "usuario1:{$enlace->id_usuario1}");
        Cache::forget(self::CACHE_PREFIX . "pedidos:gestor:{$usuario->id_usuario}");

        $enlaceFresh = Enlace::with('usuarioDestino')->find($enlace->id_enlace);
        event(new EnlaceUpdated($enlaceFresh, 'cancelado'));

        return redirect()->route('gestor.dashboard')
            ->with('success', 'Enlace cancelado correctamente.');
    }

    // Reactivar enlace cancelado (rol 5)
    public function activar(string $id_enlace)
    {
        $usuario = Auth::user();
        if ($usuario->id_rol !== 5) abort(403);

        $enlace = Enlace::where('id_enlace', $id_enlace)
            ->where('id_usuario2', $usuario->id_usuario)
            ->where('estado', 'cancelado')
            ->firstOrFail();

        $enlace->update(['estado' => 'activo']);

        Cache::forget(self::CACHE_PREFIX . "usuario1:{$enlace->id_usuario1}");
        Cache::forget(self::CACHE_PREFIX . "pedidos:gestor:{$usuario->id_usuario}");

        $enlaceFresh = Enlace::with('usuarioDestino')->find($enlace->id_enlace);
        event(new EnlaceUpdated($enlaceFresh, 'aceptado'));

        return redirect()->route('gestor.dashboard')
            ->with('success', 'Enlace reactivado correctamente.');
    }

    // Lista de pedidos de los enlaces activos (con caché)
    public function pedidosDeEnlaces()
    {
        $usuario = Auth::user();
        if ($usuario->id_rol !== 5) abort(403);

        $cacheKey = self::CACHE_PREFIX . "pedidos:gestor:{$usuario->id_usuario}";

        $pedidos = Cache::remember($cacheKey, self::CACHE_TTL, function () use ($usuario) {
            $idsUsuario1 = Enlace::where('id_usuario2', $usuario->id_usuario)
                ->where('estado', 'activo')
                ->pluck('id_usuario1');

            return Pedido::whereIn('id_usuario', $idsUsuario1)
                ->with([
                    'usuario:id_usuario,nombre_usuario',
                    'items.modelo',
                    'items.voltaje',
                    'items.color',
                ])
                ->orderBy('created_at', 'desc')
                ->get();
        });

        return view('enlaces.pedidos', compact('pedidos'));
    }
}