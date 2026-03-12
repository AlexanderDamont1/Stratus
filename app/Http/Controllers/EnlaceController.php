<?php

namespace App\Http\Controllers;

use App\Models\Enlace;
use App\Models\Pedido;
use App\Events\EnlaceUpdated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnlaceController extends Controller
{
    // -------------------------------------------------------
    // GET /gestor → rol 5 (su dashboard)
    // -------------------------------------------------------
    public function dashboard()
    {
        $usuario = Auth::user();

        $enlaces = Enlace::where('id_usuario2', $usuario->id_usuario)
                         ->with('usuarioAdmin:id_usuario,nombre_usuario,correo')
                         ->orderBy('updated_at', 'desc')
                         ->paginate(10);

        return view('gestor.dashboard', compact('enlaces'));
    }

    // -------------------------------------------------------
    // GET /enlaces → rol 1 (ve su token/enlace)
    // -------------------------------------------------------
    public function index()
    {
        $usuario = Auth::user();

        $enlace = Enlace::where('id_usuario1', $usuario->id_usuario)
                        ->whereIn('estado', ['pendiente', 'activo'])
                        ->with('usuarioDestino:id_usuario,nombre_usuario,correo')
                        ->first();

        return view('administrador.dashboard', ['enlace' => $enlace]);
    }

    // -------------------------------------------------------
    // ROL 1: Genera un nuevo token de enlace
    // POST /enlaces/generar
    // -------------------------------------------------------
    public function generar(Request $request)
    {
        $usuario = Auth::user();

        if ($usuario->id_rol !== 1) {
            abort(403, 'Acceso restringido.');
        }

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

    // -------------------------------------------------------
    // ROL 5: Acepta el token y se enlaza con rol 1
    // POST /enlaces/aceptar
    // -------------------------------------------------------
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

        if ($usuario->id_rol !== 5) {
            abort(403, 'Acceso restringido.');
        }

        $enlace = Enlace::where('token_enlace', $request->token_enlace)
                        ->where('estado', 'pendiente')
                        ->first();

        if (! $enlace) {
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

        // ✅ Carga confiable de la relación
        $enlaceFresh = Enlace::with('usuarioDestino')->find($enlace->id_enlace);
        event(new EnlaceUpdated($enlaceFresh, 'aceptado'));

        return redirect()->route('gestor.dashboard')
                         ->with('success', 'Enlace activado correctamente.');
    }

    // -------------------------------------------------------
    // ROL 1 o ROL 5: Cancela / desenlaza
    // PATCH /enlaces/{id}/cancelar
    // -------------------------------------------------------
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

            return redirect()->route('administrador.dashboard')
                             ->with('success', 'Enlace eliminado correctamente.');
        }

        $enlace->update(['estado' => 'cancelado']);

        // ✅ Carga confiable de la relación
        $enlaceFresh = Enlace::with('usuarioDestino')->find($enlace->id_enlace);
        event(new EnlaceUpdated($enlaceFresh, 'cancelado'));

        return redirect()->route('gestor.dashboard')
                         ->with('success', 'Enlace cancelado correctamente.');
    }

    // -------------------------------------------------------
    // ROL 5: Reactiva un enlace cancelado
    // PATCH /enlaces/{id}/activar
    // -------------------------------------------------------
    public function activar(string $id_enlace)
    {
        $usuario = Auth::user();

        if ($usuario->id_rol !== 5) {
            abort(403, 'Acceso restringido.');
        }

        $enlace = Enlace::where('id_enlace', $id_enlace)
                        ->where('id_usuario2', $usuario->id_usuario)
                        ->where('estado', 'cancelado')
                        ->firstOrFail();

        $enlace->update(['estado' => 'activo']);

        // ✅ Carga confiable de la relación
        $enlaceFresh = Enlace::with('usuarioDestino')->find($enlace->id_enlace);
        event(new EnlaceUpdated($enlaceFresh, 'aceptado'));

        return redirect()->route('gestor.dashboard')
                         ->with('success', 'Enlace reactivado correctamente.');
    }

    // -------------------------------------------------------
    // ROL 5: Ve los pedidos de TODOS sus enlaces activos
    // GET /enlaces/pedidos
    // -------------------------------------------------------
    public function pedidosDeEnlaces()
    {
        $usuario = Auth::user();

        if ($usuario->id_rol !== 5) {
            abort(403, 'Acceso restringido.');
        }

        $idsUsuario1 = Enlace::where('id_usuario2', $usuario->id_usuario)
                             ->where('estado', 'activo')
                             ->pluck('id_usuario1');

        $pedidos = Pedido::whereIn('id_usuario', $idsUsuario1)
                         ->with([
                             'usuario:id_usuario,nombre_usuario',
                             'items.modelo',
                             'items.voltaje',
                             'items.color',
                         ])
                         ->orderBy('created_at', 'desc')
                         ->get();

        return view('enlaces.pedidos', compact('pedidos'));
    }
}