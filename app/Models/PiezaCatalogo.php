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
        'precio_costo'        => 'decimal:2',
        'precio_venta'        => 'decimal:2',
        'stock_actual'        => 'integer',
        'stock_minimo'        => 'integer',
        'serializable'        => 'boolean',
        'activo'              => 'boolean',
    ];

    // ── Relaciones ────────────────────────────────────────────────────────────

    public function negocio(): BelongsTo
    {
        return $this->belongsTo(Negocio::class, 'id_negocio', 'id_negocio');
    }

    public function movimientos(): HasMany
    {
        return $this->hasMany(PiezaMovimiento::class, 'id_pieza', 'id_pieza')
                    ->orderByDesc('created_at');
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    public function stockBajo(): bool
    {
        return $this->stock_actual <= $this->stock_minimo;
    }

    public function esCompatibleCon(string $idModelo): bool
    {
        if (empty($this->modelos_compatibles)) return true; // universal
        return in_array($idModelo, $this->modelos_compatibles);
    }

    // ── Auto-PK ───────────────────────────────────────────────────────────────

    protected static function booted(): void
    {
        static::creating(function (self $model) {
            if (empty($model->id_pieza)) {
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