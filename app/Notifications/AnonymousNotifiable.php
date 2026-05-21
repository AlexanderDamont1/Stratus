<?php

namespace App\Notifications;

use Illuminate\Notifications\RoutesNotifications;

/**
 * Notifiable anónimo para enviar correos a emails que no tienen
 * un modelo Usuario en la base de datos (clientes externos).
 */
class AnonymousNotifiable
{
    use RoutesNotifications;

    public function __construct(
        public readonly string $email,
    ) {}

    public function routeNotificationForMail(): string
    {
        return $this->email;
    }
}