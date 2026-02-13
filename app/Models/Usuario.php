<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Traits\GeneratesCustomId;


class Usuario extends Authenticatable
{
    protected $table = 'usuarios';
    protected $primaryKey = 'id_usuario';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id_usuario',
        'id_negocio',
        'nombre_usuario',
        'correo',
        'username',
        'password',
        'id_rol',
    ];

    protected $hidden = [
        'password',
    ];

    /*
    |----------------------------------------
    | Generador de ID personalizado
    |----------------------------------------
    */
    public static function generarId()
    {
        $fecha = now()->format('ymd');

        $ultimo = self::whereDate('created_at', now())->count() + 1;

        return 'USR' . $fecha . str_pad($ultimo, 4, '0', STR_PAD_LEFT);
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
    public function esRoot()
    {
        return $this->id_rol === 0;
    }

    public function esAdmin()
    {
        return $this->id_rol === 1;
    }

    public function esVendedor()
    {
        return $this->id_rol === 2;
    }
}
