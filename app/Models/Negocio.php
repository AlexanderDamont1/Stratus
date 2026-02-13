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
        'max_users',
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

    public function admin()
    {
        return $this->belongsTo(Usuario::class, 'id_admin_principal', 'id_usuario');
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
}
