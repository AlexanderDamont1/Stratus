<?php

namespace App\Models;

use App\Traits\GeneratesCustomId;
use Illuminate\Database\Eloquent\Model;

class Negocio extends Model
{
    use GeneratesCustomId;

    protected $table      = 'negocios';
    protected $primaryKey = 'id_negocio';
    public $incrementing  = false;
    protected $keyType    = 'string';

    protected $fillable = [
        'id_negocio',
        'nombre_negocio',
        'max_users',
    ];

    protected function idPrefix(): string
    {
        return 'NEG';
    }

    /*
    |----------------------------------------
    | RELACIONES
    |----------------------------------------
    */

    public function usuarios()
    {
        return $this->hasMany(Usuario::class, 'id_negocio', 'id_negocio');
    }

    /**
     * El admin es el primer usuario con id_rol = 1 de este negocio.
     * No necesitamos columna extra: el rol lo gobierna todo.
     */
    public function admin()
    {
        return $this->hasOne(Usuario::class, 'id_negocio', 'id_negocio')
                    ->where('id_rol', 1);
    }

    public function vendedores()
    {
        return $this->hasMany(Usuario::class, 'id_negocio', 'id_negocio')
                    ->where('id_rol', 2);
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

    /*
    |----------------------------------------
    | HELPERS
    |----------------------------------------
    */

    public function puedeAgregarVendedor(): bool
    {
        return $this->vendedores()->count() < $this->max_users;
    }
}