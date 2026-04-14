<?php
use Illuminate\Support\Facades\Broadcast;
use App\Models\Enlace;

/*
|--------------------------------------------------------------------------
| Canales de Broadcast
|--------------------------------------------------------------------------
*/

// ─────────────────────────────────────────────
// Canal: vendedor.{id}
// ─────────────────────────────────────────────
Broadcast::channel('vendedor.{idVendedor}', function ($user, $idVendedor) {

    // Vendedor (rol 1) escucha su propio canal
    if ($user->id_usuario == $idVendedor && $user->id_rol == 1) {
        return true;
    }

    // Gestor (rol 5) escucha si tiene enlace activo
    if ($user->id_rol == 5) {
        return Enlace::where('id_usuario2', $user->id_usuario)
            ->where('id_usuario1', $idVendedor)
            ->where('estado', 'activo')
            ->exists();
    }

    return false;
});


// ─────────────────────────────────────────────
// Canal: enlace-vendedor.{id}
// ─────────────────────────────────────────────
Broadcast::channel('enlace-vendedor.{idVendedor}', function ($user, $idVendedor) {
    return $user->id_rol === 1 && $user->id_usuario == $idVendedor;
});


// ─────────────────────────────────────────────
// Canal: user.{id}
// ─────────────────────────────────────────────
Broadcast::channel('user.{id}', function ($user, $id) {
<<<<<<< HEAD
    // ✔ Permite a CUALQUIER usuario autenticado escuchar su propio canal
    return (string) $user->id_usuario === (string) $id;
=======
    $autorizado = (string) $user->id_usuario === (string) $id;
    
    \Log::info('[channel user.{id}]', [
        'user_id'    => $user->id_usuario,
        'param_id'   => $id,
        'autorizado' => $autorizado,
    ]);

    return $autorizado;
>>>>>>> e4c1aaa6b73c3e7cee981f8eee8c0a78cc7b1558
});


// ─────────────────────────────────────────────
// Canal: catalogo.{idNegocio}
// ─────────────────────────────────────────────
Broadcast::channel('catalogo.{idNegocio}', function ($user, $idNegocio) {
    return $user->id_rol === 1 
        && (string) $user->id_negocio === (string) $idNegocio;
});