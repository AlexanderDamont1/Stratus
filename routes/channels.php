<?php

use Illuminate\Support\Facades\Broadcast;

// Temporalmente permitir cualquier acceso para pruebas
Broadcast::channel('notifications.{userId}', function ($user, $userId) {
    return true; // Permitir todos los accesos temporalmente
});