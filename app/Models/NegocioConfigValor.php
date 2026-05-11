<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NegocioConfigValor extends Model
{
    protected $table = 'negocio_config_valores';

    protected $fillable = [
        'id_negocio',
        'clave',
        'valor',
    ];

    public function definicion()
    {
        return $this->belongsTo(NegocioConfig::class, 'clave', 'clave');
    }

    public function negocio()
    {
        return $this->belongsTo(Negocio::class, 'id_negocio', 'id_negocio');
    }
}