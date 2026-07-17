<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Notifications\Notifiable;


class Cotizacion extends Model
{
     use  Notifiable; 
    protected $table      = 'cotizaciones';
    protected $primaryKey = 'id_cotizacion';
    protected $keyType    = 'string';
    public    $incrementing = false;

    protected $fillable = [
        'id_cotizacion',
        'id_reparacion',           // ← CORREGIDO: era id_mantenimiento
        'token',
        'costo_mano_obra',
        'costo_piezas',
        'costo_total',
        'costo_reparaciones_base', // ← CORREGIDO: era costo_mantenimiento_base
        'descripcion_trabajo',
        'piezas_detalle',
        'respuesta',
        'respondido_at',
        'piezas_aceptadas',
        'resolucion_manual',
        'nota_resolucion',
        'enviado_at',
        'expires_at',
    ];

    protected $casts = [
        'piezas_detalle'          => 'array',
        'piezas_aceptadas'        => 'array',
        'resolucion_manual'       => 'boolean',
        'respuesta'               => 'integer',
        'respondido_at'           => 'datetime',
        'enviado_at'              => 'datetime',
        'expires_at'              => 'datetime',
        'costo_mano_obra'         => 'decimal:2',
        'costo_piezas'            => 'decimal:2',
        'costo_total'             => 'decimal:2',
        'costo_reparaciones_base' => 'decimal:2',
    ];

    // ── Relaciones ────────────────────────────────────────────────────────────

    // CORREGIDO: era mantenimiento() → Mantenimiento::class
    public function reparacion(): BelongsTo
    {
        return $this->belongsTo(Reparaciones::class, 'id_reparacion', 'id_reparacion');
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    public function pendiente(): bool
    {
        return is_null($this->respuesta) && !$this->expirada();
    }

    public function expirada(): bool
    {
        return $this->expires_at
            && now()->isAfter($this->expires_at)
            && is_null($this->respuesta);
    }

    public function aceptada(): bool
    {
        return $this->respuesta === 1;
    }

    public function rechazada(): bool
    {
        return $this->respuesta === 0 && is_null($this->piezas_aceptadas);
    }

    public function aceptadaParcialmente(): bool
    {
        return $this->respuesta === 0 && !is_null($this->piezas_aceptadas);
    }

    // ── Auto-generar ID ───────────────────────────────────────────────────────

    protected static function booted(): void
    {
        static::creating(function (self $model) {
            if (empty($model->id_cotizacion)) {
                $model->id_cotizacion = 'COT-'
                    . now()->format('Ymd')
                    . strtoupper(substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 4))
                    . random_int(100, 999);
            }

            if (empty($model->token)) {
                $model->token = bin2hex(random_bytes(32));
            }
        });
    }

    public function routeNotificationForMail($notification): ?string
    {
        // La cotización le pide el correo a su reparación
        return $this->reparacion->cliente_email ?? $this->reparacion->cliente?->correo;
    }
}