<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PiezaMovimiento extends Model
{
    protected $table      = 'piezas_movimientos';
    protected $primaryKey = 'id_movimiento';
    protected $keyType    = 'string';
    public    $incrementing = false;
    public    $timestamps   = false;

    protected $fillable = [
        'id_movimiento',
        'id_pieza',
        'id_negocio',
        'id_usuario',
        'id_reparacion',
        'tipo',
        'cantidad',
        'stock_antes',
        'stock_despues',
        'nota',
        'created_at',
    ];

    protected $casts = [
        'cantidad'     => 'integer',
        'stock_antes'  => 'integer',
        'stock_despues'=> 'integer',
        'created_at'   => 'datetime',
    ];

    public function pieza(): BelongsTo
    {
        return $this->belongsTo(PiezaCatalogo::class, 'id_pieza', 'id_pieza');
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'id_usuario', 'id_usuario');
    }

    // ── Auto-PK ───────────────────────────────────────────────────────────────

    protected static function booted(): void
    {
        static::creating(function (self $model) {
            if (empty($model->id_movimiento)) {
                $ultimo = static::orderByDesc('id_movimiento')
                    ->lockForUpdate()
                    ->value('id_movimiento');

                $num = $ultimo
                    ? (int) substr($ultimo, strrpos($ultimo, '-') + 1) + 1
                    : 1;

                $model->id_movimiento = 'PZM-' . str_pad($num, 5, '0', STR_PAD_LEFT);
            }

            if (empty($model->created_at)) {
                $model->created_at = now();
            }
        });
    }
}