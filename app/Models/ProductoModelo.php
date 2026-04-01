<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductoModelo extends Model
{
    protected $table      = 'producto_modelo';
    protected $primaryKey = 'id_producto_modelo';
    public $incrementing  = false;
    protected $keyType    = 'string';

    protected $fillable = [
        'id_producto_modelo',
        'id_producto',
        'id_negocio',
        'id_usuario',
        'id_modelo',
        'id_voltaje',
        'activo',
    ];

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'id_producto', 'id_producto');
    }

    public function modelo()
    {
        return $this->belongsTo(Modelo::class, 'id_modelo', 'id_modelo');
    }

    public function voltaje()
    {
        return $this->belongsTo(Voltaje::class, 'id_voltaje', 'id_voltaje');
    }

    public function sucursal()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario', 'id_usuario');
    }
}