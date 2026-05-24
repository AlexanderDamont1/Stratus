<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReparacionHistorial extends Model
{
    protected $table      = 'reparacion_historial';
    protected $primaryKey = 'id_reparacion_historial';
    protected $keyType    = 'string';
    public    $incrementing = false;
    public    $timestamps   = false;

    protected $fillable = [
        'id_reparacion_historial',
        'id_reparacion',
        'estado_anterior',
        'estado_nuevo',
        'id_usuario',
        'nota',
        'created_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function reparacion(): BelongsTo
    {
        return $this->belongsTo(Reparaciones::class, 'id_reparacion', 'id_reparacion');
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'id_usuario', 'id_usuario');
    }

    // ── AUTO-PK (faltaba por completo) ────────────────────────────────────────
    protected static function booted(): void
    {
        static::creating(function (self $model) {
            if (empty($model->id_reparacion_historial)) {
                $ultimo = static::orderByDesc('id_reparacion_historial')
                    ->lockForUpdate()
                    ->value('id_reparacion_historial');

                $num = $ultimo
                    ? (int) substr($ultimo, strrpos($ultimo, '-') + 1) + 1
                    : 1;

                $model->id_reparacion_historial = 'RH-' . str_pad($num, 5, '0', STR_PAD_LEFT);
            }

            if (empty($model->created_at)) {
                $model->created_at = now();
            }
        });
    }
}