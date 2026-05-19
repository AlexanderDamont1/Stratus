<?php

namespace App\Listeners;

use App\Services\AuditLogger;
use Illuminate\Auth\Events\Failed;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Support\Facades\Request;

/**
 * AuditAuthListener
 * Engancha los eventos nativos de Auth de Laravel.
 * Registrar en AppServiceProvider::boot()
 */
class AuditAuthListener
{
    public function handleLogin(Login $event): void
    {
        $user = $event->user;
        AuditLogger::login($user->id_usuario, $user->id_negocio ?? 0, $user->correo);
    }

    public function handleLogout(Logout $event): void
    {
        if ($event->user) {
            AuditLogger::logout($event->user->id_usuario, $event->user->id_negocio ?? 0);
        }
    }

    public function handleFailed(Failed $event): void
    {
        AuditLogger::loginFailed($event->credentials['email'] ?? 'desconocido');
    }

    public function handleLockout(Lockout $event): void
    {
        $email = Request::input('email') ?? 'desconocido';

        AuditLogger::loginThrottled($email);
    }
}