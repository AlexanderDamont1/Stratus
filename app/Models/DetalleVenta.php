<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\GeneratesCustomId;

class DetalleVenta extends Model
{
    use GeneratesCustomId;

    protected $table      = 'detalle_venta';
    protected $primaryKey = 'id_detalle';
    public    $incrementing = false;
    protected $keyType    = 'string';

    protected $fillable = [
        'id_detalle',
        'id_venta',
        'id_negocio',
        'id_producto',
        'id_pieza',
        'concepto',
        'num_serie',
        'precio_unitario',
        'cantidad',
    ];

    protected $casts = [
        'precio_unitario' => 'decimal:2',
        'cantidad'        => 'integer',
    ];

    protected function idPrefix(): string { return 'DVE'; }

    public function venta()     { return $this->belongsTo(Venta::class,     'id_venta',    'id_venta'); }
    public function producto()  { return $this->belongsTo(Producto::class,  'id_producto', 'id_producto'); }
    public function bicicleta() { return $this->belongsTo(Bicicleta::class, 'num_serie',   'num_serie'); }
    public function pieza()     { return $this->belongsTo(PiezaCatalogo::class, 'id_pieza', 'id_pieza'); }

    // Nombre a mostrar sin importar si la línea viene de un producto,
    // una pieza de catálogo o un concepto libre (mano de obra, costo base).
    public function getNombreAttribute(): string
    {
        return $this->producto?->nombre_producto
            ?? $this->pieza?->nombre
            ?? $this->concepto
            ?? '—';
    }
}