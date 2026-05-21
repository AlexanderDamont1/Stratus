<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OtPieza extends Model
{
    protected $table    = 'ot_piezas';
    protected $fillable = [
        'id_ot', 'id_pieza', 'descripcion',
        'cantidad', 'precio_unitario', 'subtotal',
        'es_garantia', 'stock_descontado',
    ];
    protected $casts = [
        'es_garantia'      => 'boolean',
        'stock_descontado' => 'boolean',
        'precio_unitario'  => 'decimal:2',
        'subtotal'         => 'decimal:2',
    ];

    public function pieza() { return $this->belongsTo(PiezaCatalogo::class, 'id_pieza'); }
    public function ot()    { return $this->belongsTo(OrdenTrabajo::class,  'id_ot', 'id_ot'); }
}