<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RegistroLink extends Model
{
    protected $table = 'registro_links';
    protected $primaryKey = 'id_link';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id_link',
        'id_negocio',
        'creado_por',
        'token',
        'usado',
        'expires_at',
    ];

    /*
    |----------------------------------------
    | RELACIONES
    |----------------------------------------
    */

    public function negocio()
    {
        return $this->belongsTo(Negocio::class, 'id_negocio', 'id_negocio');
    }

    public function creador()
    {
        return $this->belongsTo(Usuario::class, 'creado_por', 'id_usuario');
    }

    /*
    |----------------------------------------
    | Validaciones de estado
    |----------------------------------------
    */

    public function estaDisponible()
    {
        if ($this->usado) return false;

        if ($this->expires_at && now()->greaterThan($this->expires_at)) {
            return false;
        }

        return true;
    }
}

