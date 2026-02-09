<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\GeneratesCustomId;


class Producto extends Model
{
    protected $table = 'productos';
    protected $primaryKey = 'id_producto';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id_producto',
        'id_negocio',
        'nombre_producto',
    ];

    protected function idPrefix(): string
    {
        return 'PRO';
    }

    public function negocio()
    {
        return $this->belongsTo(Negocio::class, 'id_negocio', 'id_negocio');
    }

    public function precios()
    {
        return $this->hasMany(Precio::class, 'id_producto', 'id_producto');
    }

    public function bicicletas()
    {
        return $this->hasMany(Bicicleta::class, 'id_producto', 'id_producto');
    }
}
