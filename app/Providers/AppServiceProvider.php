<?php

namespace App\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
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

        // ── Rate limiting para login (protección contra brute force) ──
        RateLimiter::for('login', function (Request $request) {
            $email = (string) $request->input('correo', $request->input('email'));
            $throttleKey = \Illuminate\Support\Str::transliterate(
                \Illuminate\Support\Str::lower($email) . '|' . $request->ip()
            );

            return Limit::perMinute(5)->by($throttleKey);
        });
    }
}