<?php

namespace App\Providers;
use Illuminate\Support\Facades\Blade;
use App\Services\ModuloService;



use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        Blade::if('modulo', function (string $idModulo) {
            $user = auth()->user();
            if (!$user) return false;
            if ($user->id_rol === 0) return true; // Root ve todo
            return ModuloService::tiene($user->id_negocio, $user->id_rol, $idModulo);
        });
    }
}