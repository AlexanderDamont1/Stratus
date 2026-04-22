<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NegocioModuloRol extends Model
{
    protected $table = 'negocio_modulo_rol';

    protected $fillable = [
        'id_negocio',
        'id_modulo',
        'id_rol',
        'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

    public function modulo()
    {
        return $this->belongsTo(Modulo::class, 'id_modulo', 'id_modulo');
    }

    public function negocio()
    {
        return $this->belongsTo(Negocio::class, 'id_negocio', 'id_negocio');
    }
}