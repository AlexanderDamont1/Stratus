<?php

namespace App\Http\Requests\Auth;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'correo' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ];
    }

    public function authenticate(): void
{
    $this->ensureIsNotRateLimited();

    // DEBUG: Verifica que los datos lleguen
    \Log::info('Intento de login', [
        'correo' => $this->correo,
        'remember' => $this->boolean('remember')
    ]);

    $credentials = [
        'correo' => $this->correo,
        'password' => $this->password
    ];

    if (! Auth::attempt($credentials, $this->boolean('remember'))) {
        // DEBUG: Verifica si el usuario existe
        $usuario = \App\Models\Usuario::where('correo', $this->correo)->first();
        \Log::error('Login fallido', [
            'usuario_existe' => $usuario ? 'Sí' : 'No',
            'password_guardado' => $usuario ? substr($usuario->password, 0, 10) : 'N/A'
        ]);

        RateLimiter::hit($this->throttleKey());

        throw ValidationException::withMessages([
            'correo' => 'Credenciales incorrectas.',
        ]);
    }

    \Log::info('Login exitoso', ['correo' => $this->correo]);
    RateLimiter::clear($this->throttleKey());
}
    public function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'correo' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    public function throttleKey(): string
    {
        return Str::transliterate(
            Str::lower($this->string('correo')) . '|' . $this->ip()
        );
    }
}
