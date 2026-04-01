<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    protected $primaryKey = 'id_producto';
    public $incrementing  = false;
    protected $keyType    = 'string';

    protected $fillable = [
        'id_producto',
        'id_negocio',
        'id_usuario',
        'nombre_producto',
        'precio',
        'tipo',
    ];

    public function productoModelo()
    {
        return $this->hasMany(ProductoModelo::class, 'id_producto', 'id_producto');
    }

    public function negocio()
    {
        return $this->belongsTo(Negocio::class, 'id_negocio', 'id_negocio');
    }

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario', 'id_usuario');
    }
}