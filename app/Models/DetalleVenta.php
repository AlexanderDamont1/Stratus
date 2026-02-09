<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\GeneratesCustomId;


class DetalleVenta extends Model
{
    protected $table = 'detalle_venta';
    protected $primaryKey = 'id_detalleVenta';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id_detalleVenta',
        'id_venta',
        'id_producto',
        'id_precio',
        'num_serie',
    ];

    protected function idPrefix(): string
    {
        return 'DVE';
    }

    public function venta()
    {
        return $this->belongsTo(Venta::class, 'id_venta', 'id_venta');
    }

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'id_producto', 'id_producto');
    }

    public function precio()
    {
        return $this->belongsTo(Precio::class, 'id_precio', 'id_precio');
    }

    public function bicicleta()
    {
        return $this->belongsTo(Bicicleta::class, 'num_serie', 'num_serie');
    }
}
