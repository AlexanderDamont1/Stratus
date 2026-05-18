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
    // ✔ Permite a CUALQUIER usuario autenticado escuchar su propio canal
    return (string) $user->id_usuario === (string) $id;
});


// ─────────────────────────────────────────────
// Canal: catalogo.{idNegocio}
// ─────────────────────────────────────────────
Broadcast::channel('catalogo.{idNegocio}', function ($user, $idNegocio) {
    return $user->id_rol === 1 
        && (string) $user->id_negocio === (string) $idNegocio;
});

Broadcast::channel('ventas.{idNegocio}', function ($user, $idNegocio) {
    // Solo el admin (rol 1) del negocio recibe este canal
    return $user->id_negocio === $idNegocio && $user->id_rol === 1;
});

// Todos los usuarios del negocio lo escuchan
Broadcast::channel('negocio.{idNegocio}', function ($user, $idNegocio) {
    return (string) $user->id_negocio === (string) $idNegocio;
});

// Solo el root (rol 0) puede escuchar
Broadcast::channel('root', function ($user) {
    return $user->id_rol === 0;
});



 
Broadcast::channel('audit.root', function ($user) {
    return (int) $user->id_rol === 0;
});