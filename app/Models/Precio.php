<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\GeneratesCustomId;


class Precio extends Model
{
    protected $table = 'precios';
    protected $primaryKey = 'id_precio';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id_precio',
        'id_producto',
        'precio',
    ];

    protected function idPrefix(): string
    {
        return 'PRE';
    }

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'id_producto', 'id_producto');
    }
}
