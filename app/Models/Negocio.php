<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\GeneratesCustomId;


class Negocio extends Model
{
    protected $table = 'negocios';
    protected $primaryKey = 'id_negocio';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id_negocio',
        'nombre_negocio',
    ];

    protected function idPrefix(): string
    {
        return 'NEG';
    }

    /* ================= RELACIONES ================= */

    public function usuarios()
    {
        return $this->hasMany(Usuario::class, 'id_negocio', 'id_negocio');
    }

    public function clientes()
    {
        return $this->hasMany(Cliente::class, 'id_negocio', 'id_negocio');
    }

    public function bicicletas()
    {
        return $this->hasMany(Bicicleta::class, 'id_negocio', 'id_negocio');
    }

    public function ventas()
    {
        return $this->hasMany(Venta::class, 'id_negocio', 'id_negocio');
    }
}
