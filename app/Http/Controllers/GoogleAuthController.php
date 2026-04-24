<?php

namespace App\Http\Controllers;

use App\Models\Negocio;
use App\Models\RegistroLink;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class GoogleAuthController extends Controller
{
    // ── Redirigir a Google ────────────────────────────────

    /**
     * Login normal con Google (usuario ya registrado)
     */
    public function redirectLogin()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Registro nuevo con Google (viene desde link de registro)
     * Guarda el token en sesión para recuperarlo después del callback
     */
    public function redirectRegistro(string $token)
    {
        // Validar link antes de ir a Google
        $link = RegistroLink::where('token', $token)
            ->where('usado', false)
            ->where(fn ($q) => $q->whereNull('expires_at')->orWhere('expires_at', '>', now()))
            ->firstOrFail();

        session(['registro_token' => $token]);

        return Socialite::driver('google')
            ->with(['state' => 'registro'])
            ->redirect();
    }

    // ── Callbacks ─────────────────────────────────────────

    public function callbackLogin()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (\Exception $e) {
            return redirect()->route('login')
                ->withErrors(['correo' => 'No se pudo conectar con Google. Intenta de nuevo.']);
        }

        // Buscar usuario por google_id o correo
        $usuario = Usuario::where('google_id', $googleUser->getId())
            ->orWhere('correo', $googleUser->getEmail())
            ->first();

        if (!$usuario) {
            return redirect()->route('login')
                ->withErrors(['correo' => 'No existe una cuenta con este correo de Google.']);
        }

        // Si tenía cuenta manual, vincular google_id
        if (!$usuario->google_id) {
            $usuario->update([
                'google_id'          => $googleUser->getId(),
                'email_verified_at'  => $usuario->email_verified_at ?? now(),
            ]);
        }

        Auth::login($usuario, true);
        request()->session()->regenerate();

        return redirect()->intended(route('dashboard'));
    }

    public function callbackRegistro(Request $request)
    {
        // Verificar que viene de un flujo de registro
        $token = session('registro_token');
        if (!$token) {
            return redirect()->route('login')
                ->withErrors(['correo' => 'Sesión de registro expirada. Usa el link original.']);
        }

        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (\Exception $e) {
            return redirect()->route('login')
                ->withErrors(['correo' => 'Error al conectar con Google.']);
        }

        // Verificar que el correo no esté ya registrado
        if (Usuario::where('correo', $googleUser->getEmail())->exists()) {
            return redirect()->route('login')
                ->withErrors(['correo' => 'Este correo ya tiene una cuenta. Inicia sesión directamente.']);
        }

        $link = RegistroLink::where('token', $token)
            ->where('usado', false)
            ->where(fn ($q) => $q->whereNull('expires_at')->orWhere('expires_at', '>', now()))
            ->firstOrFail();

        // Guardar datos de Google en sesión y redirigir a form de nombre de negocio
        session([
            'google_registro' => [
                'google_id'    => $googleUser->getId(),
                'nombre'       => $googleUser->getName(),
                'correo'       => $googleUser->getEmail(),
                'avatar'       => $googleUser->getAvatar(),
                'link_token'   => $token,
                'max_users'    => $link->max_users,
            ]
        ]);

        return redirect()->route('registro.google.negocio');
    }

    // ── Formulario: solo pide nombre del negocio ──────────

    public function formNegocio()
    {
        if (!session('google_registro')) {
            return redirect()->route('login');
        }

        return view('registro.google-negocio');
    }

    public function storeNegocio(Request $request)
    {
        $datos = session('google_registro');
        if (!$datos) {
            return redirect()->route('login');
        }

        $request->validate([
            'nombre_negocio' => ['required', 'string', 'max:100'],
        ]);

        DB::transaction(function () use ($request, $datos) {

            // 1. Crear negocio
            $negocio = Negocio::create([
                'nombre_negocio' => $request->nombre_negocio,
                'max_users'      => $datos['max_users'],
            ]);

            // 2. Crear admin — Google ya verificó el email
            $usuario = Usuario::create([
                'id_negocio'        => $negocio->id_negocio,
                'nombre_usuario'    => $datos['nombre'],
                'correo'            => $datos['correo'],
                'password'          => bcrypt(Str::random(32)), // password aleatorio, nunca lo usará
                'id_rol'            => 44,
                'google_id'         => $datos['google_id'],
                'email_verified_at' => now(), // Google ya verificó
            ]);

            // 3. Matar el link
            RegistroLink::where('token', $datos['link_token'])
                ->update(['usado' => true]);
        });

        session()->forget('google_registro');

        // Logear al usuario recién creado
        $usuario = Usuario::where('correo', $datos['correo'])->first();
        Auth::login($usuario);
        request()->session()->regenerate();

        return redirect()->route('dashboard')
            ->with('success', 'Cuenta creada con Google correctamente.');
    }
}