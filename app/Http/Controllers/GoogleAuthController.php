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
    // ── Redirects ─────────────────────────────────────────

    public function redirectLogin()
    {
        return Socialite::driver('google')
            ->with(['state' => base64_encode('login')])
            ->redirect();
    }

    public function redirectRegistro(string $token)
    {
        RegistroLink::where('token', $token)
            ->where('usado', false)
            ->where(fn($q) => $q->whereNull('expires_at')->orWhere('expires_at', '>', now()))
            ->firstOrFail();

        session(['registro_token' => $token]);
        session()->save();

        return Socialite::driver('google')
            ->with(['state' => base64_encode('registro|' . $token)])
            ->redirect();
    }

    // ── Callback único ────────────────────────────────────

    public function callback(Request $request)
    {
        $stateRaw     = $request->get('state', '');
        $stateDecoded = base64_decode($stateRaw);
        $esRegistro   = str_starts_with($stateDecoded, 'registro|');

        try {
            $googleUser = Socialite::driver('google')->stateless()->user();
        } catch (\Exception $e) {
            return redirect()->route('login')
                ->withErrors(['correo' => 'Error: ' . $e->getMessage()]);
        }

        if ($esRegistro) {
            $tokenDelState = explode('|', $stateDecoded)[1] ?? null;
            if (!session('registro_token') && $tokenDelState) {
                session(['registro_token' => $tokenDelState]);
            }
            return $this->handleRegistro($googleUser, $request);
        }

        return $this->handleLogin($googleUser);
    }

    private function handleLogin($googleUser)
    {
        $usuario = Usuario::where('google_id', $googleUser->getId())
            ->orWhere('correo', $googleUser->getEmail())
            ->first();

        if (!$usuario) {
            return redirect()->route('login')
                ->withErrors(['correo' => 'No existe una cuenta con este correo de Google.']);
        }

        $usuario->update([
            'google_id'                => $usuario->google_id ?? $googleUser->getId(),
            'email_verified_at'        => $usuario->email_verified_at ?? now(), // ← si no tenía, lo verifica
            'email_verification_token' => null, // ← elimina el token pendiente
        ]);

        session(['google_login_usuario_id' => $usuario->id_usuario]);
        session()->save();

        return redirect()->route('google.login.finalizar');
    }

    // ── Registro con Google ───────────────────────────────

    private function handleRegistro($googleUser, Request $request)
    {
        $token = session('registro_token');

        if (!$token) {
            return redirect()->route('login')
                ->withErrors(['correo' => 'Sesión de registro expirada. Usa el link original.']);
        }

        if (Usuario::where('correo', $googleUser->getEmail())->exists()) {
            session()->forget('registro_token');
            return redirect()->route('login')
                ->withErrors(['correo' => 'Este correo ya tiene una cuenta. Inicia sesión directamente.']);
        }

        $link = RegistroLink::where('token', $token)
            ->where('usado', false)
            ->where(fn($q) => $q->whereNull('expires_at')->orWhere('expires_at', '>', now()))
            ->first();

        if (!$link) {
            session()->forget('registro_token');
            return redirect()->route('login')
                ->withErrors(['correo' => 'El link de registro ya no es válido.']);
        }

        session([
            'google_registro' => [
                'google_id'  => $googleUser->getId(),
                'nombre'     => $googleUser->getName(),
                'correo'     => $googleUser->getEmail(),
                'link_token' => $token,
                'max_users'  => $link->max_users,
            ]
        ]);

        session()->forget('registro_token');

        return redirect()->route('registro.google.negocio');
    }

    // ── Formulario: nombre del negocio ────────────────────

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

            Negocio::create([
                'nombre_negocio' => $request->nombre_negocio,
                'max_users'      => $datos['max_users'],
            ]);

            Usuario::create([
                'id_negocio'        => Negocio::where('nombre_negocio', $request->nombre_negocio)
                    ->latest()->first()->id_negocio,
                'nombre_usuario'    => $datos['nombre'],
                'correo'            => $datos['correo'],
                'password'          => bcrypt(Str::random(32)),
                'id_rol'            => 44,
                'google_id'         => $datos['google_id'],
                'email_verified_at' => now(),
            ]);

            RegistroLink::where('token', $datos['link_token'])
                ->update(['usado' => true]);
        });

        session()->forget('google_registro');

        $usuario = Usuario::where('correo', $datos['correo'])->first();
        Auth::login($usuario);
        request()->session()->regenerate();

        return redirect()->route('dashboard')
            ->with('success', 'Cuenta creada con Google correctamente.');
    }
}
