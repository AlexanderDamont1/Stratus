<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Modulo extends Model
{
    protected $table      = 'modulos';
    protected $primaryKey = 'id_modulo';
    public $incrementing  = false;
    protected $keyType    = 'string';

    protected $fillable = [
        'id_modulo',
        'nombre',
        'descripcion',
        'activo_por_defecto',
    ];

    protected $casts = [
        'activo_por_defecto' => 'boolean',
    ];
}