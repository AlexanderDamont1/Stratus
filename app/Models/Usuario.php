<?php

namespace App\Models;

use App\Traits\GeneratesCustomId;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Usuario extends Authenticatable
{
    use GeneratesCustomId;

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
    ];

    protected $hidden = [
        'password',
        'session_token',
    ];

    protected $casts = [
        'id_rol' => 'integer',
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

    /*
    |----------------------------------------
    | Autenticación
    |----------------------------------------
    */

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

    /*
    |----------------------------------------
    | RELACIONES
    |----------------------------------------
    */

    public function negocio()
    {
        return $this->belongsTo(Negocio::class, 'id_negocio', 'id_negocio');
    }

    // Rol 1: su único enlace generado
    public function enlace()
    {
        return $this->hasOne(Enlace::class, 'id_usuario1', 'id_usuario');
    }

    // Rol 5: todos sus enlaces con distintos rol 1
    public function enlaces()
    {
        return $this->hasMany(Enlace::class, 'id_usuario2', 'id_usuario');
    }

    /*
    |----------------------------------------
    | HELPERS DE ROL
    |  0  = Root
    |  1  = Admin normal
    |  2  = Vendedor
    | 44  = Admin en modo instalación (transitorio)
    |----------------------------------------
    */

    public function esRoot(): bool
    {
        return $this->id_rol === 0;
    }

    /** Admin normal O admin en modo setup */
    public function esAdmin(): bool
    {
        return in_array($this->id_rol, [1, 44]);
    }

    public function esAdminNormal(): bool
    {
        return $this->id_rol === 1;
    }

    public function esVendedor(): bool
    {
        return $this->id_rol === 2;
    }

    /**
     * Admin en modo instalación obligatoria.
     * Rol transitorio: 44 → 1 al completar setup.
     */
    public function enModoSetup(): bool
    {
        return $this->id_rol === 44;
    }

    /*
    |----------------------------------------
    | HELPERS DE SESIÓN
    |----------------------------------------
    */

    /** Roles 1, 2 y 44 tienen sesión única activa */
    public function requiereSesionUnica(): bool
    {
        return in_array($this->id_rol, [1, 2, 44]);
    }
}