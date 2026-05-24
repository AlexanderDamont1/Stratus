<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PiezaCatalogo extends Model
{
    protected $table      = 'piezas_catalogo';
    protected $primaryKey = 'id_pieza';
    protected $keyType    = 'string';
    public    $incrementing = false;

    protected $fillable = [
        'id_pieza',
        'id_negocio',
        'nombre',
        'clave',
        'categoria',
        'marca_pieza',
        'modelos_compatibles',
        'voltaje_compatible',
        'descripcion',
        'precio_costo',
        'precio_venta',
        'stock_actual',
        'stock_minimo',
        'serializable',
        'activo',
    ];

    protected $casts = [
        'modelos_compatibles' => 'array',
        'serializable'        => 'boolean',
        'activo'              => 'boolean',
        'precio_costo'        => 'decimal:2',
        'precio_venta'        => 'decimal:2',
        'stock_actual'        => 'integer',
        'stock_minimo'        => 'integer',
    ];

    // ── Relaciones ────────────────────────────────────────────────────────────

    public function negocio(): BelongsTo
    {
        return $this->belongsTo(Negocio::class, 'id_negocio', 'id_negocio');
    }

    public function reparacionPiezas(): HasMany
    {
        return $this->hasMany(ReparacionPieza::class, 'id_pieza', 'id_pieza');
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    public function bajoStock(): bool
    {
        return $this->stock_actual <= $this->stock_minimo;
    }

    public function sinStock(): bool
    {
        return $this->stock_actual <= 0;
    }

    // ── AUTO-PK ───────────────────────────────────────────────────────────────

    protected static function booted(): void
    {
        static::creating(function (self $model) {
            if (empty($model->id_pieza)) {
                // Scoped por negocio para que cada tenant empiece en PC-00001
                $ultimo = static::where('id_negocio', $model->id_negocio)
                    ->orderByDesc('id_pieza')
                    ->lockForUpdate()
                    ->value('id_pieza');

                $num = $ultimo
                    ? (int) substr($ultimo, strrpos($ultimo, '-') + 1) + 1
                    : 1;

                $model->id_pieza = 'PC-' . str_pad($num, 5, '0', STR_PAD_LEFT);
            }
        });
    }
}