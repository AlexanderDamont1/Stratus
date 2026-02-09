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

    protected function idPrefix(): string
    {
        return 'USR';
    }

    protected $hidden = [
        'password',
    ];

    public function negocio()
    {
        return $this->belongsTo(Negocio::class, 'id_negocio', 'id_negocio');
    }
}
