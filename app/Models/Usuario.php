<?php

namespace App\Models;

use App\Traits\GeneratesCustomId;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Usuario extends Authenticatable
{
    use GeneratesCustomId, Notifiable;

    protected $table      = 'usuarios';
    protected $primaryKey = 'id_usuario';
    public $incrementing  = false;
    protected $keyType    = 'string';
    public $timestamps    = true;

    protected $fillable = [
        'id_usuario',
        'id_negocio',
        'nombre_usuario',
        'correo',
        'password',
        'id_rol',
        'session_token',
        'google_id',
        'email_verified_at',
        'email_verification_token',
        // Ubicación (sucursales / rol 2)
        'direccion',
        'lat',
        'lng',
        'place_id',
    ];

    protected $hidden = [
        'password',
        'session_token',
        'email_verification_token',
    ];

    protected $casts = [
        'id_rol'            => 'integer',
        'email_verified_at' => 'datetime',
        'lat'               => 'float',
        'lng'               => 'float',
    ];

    protected function idPrefix(): string
    {
        return 'USR';
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($usuario) {
            if (empty($usuario->id_usuario)) {
                $usuario->id_usuario = static::generarId();
            }
        });
    }

    // ── Autenticación ─────────────────────────────────────

    public function getAuthIdentifierName(): string
    {
        return 'id_usuario';
    }

    public function username(): string
    {
        return 'correo';
    }

    public function getAuthPassword(): string
    {
        return $this->password;
    }

    // ── Verificación de email ─────────────────────────────

    public function emailVerificado(): bool
    {
        return $this->email_verified_at !== null;
    }

    public function tieneGoogleId(): bool
    {
        return $this->google_id !== null;
    }

    public function generarTokenVerificacion(): string
    {
        $token = \Illuminate\Support\Str::random(64);
        $this->update(['email_verification_token' => $token]);
        return $token;
    }

    public function marcarEmailVerificado(): void
    {
        $this->update([
            'email_verified_at'        => now(),
            'email_verification_token' => null,
        ]);
    }

    // ── Helpers de ubicación ──────────────────────────────

    public function tieneUbicacion(): bool
    {
        return $this->lat !== null && $this->lng !== null;
    }

    // ── Relaciones ────────────────────────────────────────

    public function negocio()
    {
        return $this->belongsTo(Negocio::class, 'id_negocio', 'id_negocio');
    }

    public function enlace()
    {
        return $this->hasOne(Enlace::class, 'id_usuario1', 'id_usuario');
    }

    public function enlaces()
    {
        return $this->hasMany(Enlace::class, 'id_usuario2', 'id_usuario');
    }

    // ── Helpers de rol ────────────────────────────────────

    public function esRoot(): bool        { return $this->id_rol === 0; }
    public function esAdmin(): bool       { return in_array($this->id_rol, [1, 44]); }
    public function esAdminNormal(): bool { return $this->id_rol === 1; }
    public function esVendedor(): bool    { return $this->id_rol === 2; }
    public function enModoSetup(): bool   { return $this->id_rol === 44; }

    public function requiereSesionUnica(): bool
    {
        return in_array($this->id_rol, [6, 44]);
    }

    public function routeNotificationForMail($notification = null): string
    {
        return $this->correo;
    }
}