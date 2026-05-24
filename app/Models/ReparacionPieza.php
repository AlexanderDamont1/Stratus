<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReparacionPieza extends Model
{
    protected $table      = 'reparacion_piezas';
    protected $primaryKey = 'id_reparacion_pieza';
    protected $keyType    = 'string';
    public    $incrementing = false;

    protected $fillable = [
        'id_reparacion_pieza',
        'id_reparacion',
        'id_pieza',
        'descripcion',
        'cantidad',
        'precio_unitario',
        'subtotal',
        'es_garantia',
        'stock_descontado',
    ];

    protected $casts = [
        'es_garantia'      => 'boolean',
        'stock_descontado' => 'boolean',
        'cantidad'         => 'integer',
        'precio_unitario'  => 'decimal:2',
        'subtotal'         => 'decimal:2',
    ];

    public function reparacion(): BelongsTo
    {
        return $this->belongsTo(Reparaciones::class, 'id_reparacion', 'id_reparacion');
    }

    public function pieza(): BelongsTo
    {
        return $this->belongsTo(PiezaCatalogo::class, 'id_pieza', 'id_pieza');
    }

    // ── AUTO-PK (faltaba por completo) ────────────────────────────────────────
    protected static function booted(): void
    {
        static::creating(function (self $model) {
            if (empty($model->id_reparacion_pieza)) {
                $ultimo = static::orderByDesc('id_reparacion_pieza')
                    ->lockForUpdate()
                    ->value('id_reparacion_pieza');

                $num = $ultimo
                    ? (int) substr($ultimo, strrpos($ultimo, '-') + 1) + 1
                    : 1;

                $model->id_reparacion_pieza = 'RP-' . str_pad($num, 5, '0', STR_PAD_LEFT);
            }
        });
    }
}