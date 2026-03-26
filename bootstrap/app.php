<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {

    $middleware->alias([
        'es.root'         => \App\Http\Middleware\EsRoot::class,
        'single.session'  => \App\Http\Middleware\SingleSessionMiddleware::class,
        'force.setup'     => \App\Http\Middleware\ForceAdminSetupMiddleware::class,
        'gestor'       => \App\Http\Middleware\Gestor::class,
        'enlace'       => \App\Http\Middleware\Enlace::class,
        'administrador' => \App\Http\Middleware\Administrador::class,
        'ventas' => \App\Http\Middleware\Ventas::class,
        'prefijo.admin' => \App\Http\Middleware\ProtegerPrefijoAdmin::class, //Proteje todo dentro de /admin
    ]);

  
    $middleware->appendToGroup('web', [
        \App\Http\Middleware\SingleSessionMiddleware::class,
    ]);
})
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();