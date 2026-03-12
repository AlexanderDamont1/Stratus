<?php


use Illuminate\Support\Facades\Broadcast;


use App\Models\Enlace;
use App\Models\Usuario;

Broadcast::channel('vendedor.{idVendedor}', function ($user, $idVendedor) {

    // El propio vendedor (rol 1) escucha su canal
    if ($user->id_usuario == $idVendedor && $user->id_rol == 1) {
        return true;
    }

    // El gestor (rol 5) escucha si tiene un enlace activo con ese vendedor
    if ($user->id_rol == 5) {
        return Enlace::where('id_usuario2', $user->id_usuario)
            ->where('id_usuario1', $idVendedor)
            ->where('estado', 'activo')
            ->exists();
    }

    return false;
});


Broadcast::channel('enlace-vendedor.{idVendedor}', function ($user, $idVendedor) {
    return $user->id_rol === 1 && $user->id_usuario == $idVendedor;
});
