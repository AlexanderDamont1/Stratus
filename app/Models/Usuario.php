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
        'username',
        'password',
        'id_rol',
        'session_token',   // ← nuevo
    ];

    protected $hidden = [
        'password',
        'session_token',   // ← no exponemos el token en JSON/arrays
    ];

    protected $casts = [
        'id_rol' => 'integer',
    ];

    protected function idPrefix(): string
    {
        return 'USR';
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

    // Campo que usa Laravel para login
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

    /*
    |----------------------------------------
    | HELPERS DE ROL
    |----------------------------------------
    */

    public function esRoot(): bool
    {
        return $this->id_rol === 0;
    }

    public function esAdmin(): bool
    {
        return $this->id_rol === 1;
    }

    public function esVendedor(): bool
    {
        return $this->id_rol === 2;
    }

    /*
    |----------------------------------------
    | HELPERS DE SESIÓN
    |----------------------------------------
    */

    /**
     * Indica si este rol debe tener sesión única.
     */
    public function requiereSesionUnica(): bool
    {
        return in_array($this->id_rol, [1, 2]);
    }
}