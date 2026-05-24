<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Notifications\Notifiable;

class Reparaciones extends Model
{
    use Notifiable;
    protected $table      = 'reparaciones';
    protected $primaryKey = 'id_reparacion';
    protected $keyType    = 'string';
    public    $incrementing = false;

    protected $fillable = [
        'id_reparacion',
        'id_negocio',
        'id_usuario_sucursal',
        'id_tecnico',
        'num_serie',
        'unidad_descripcion',
        'id_cliente',
        'cliente_nombre',
        'cliente_email',          // cliente_telefono eliminado: no existe en la migración
        'id_verificada',
        'tipo',
        'problema_reportado',
        'diagnostico',
        'estado',
        'costo_reparacion',       // costo base, solo tipo=reparacion
        'costo_mano_obra',
        'costo_piezas',
        'costo_total',
        'piezas_usadas',
        'notificacion_enviada',
        'notificacion_enviada_at',
        'recibida_at',
        'en_proceso_at',
        'lista_at',
        'entregada_at',
        'notas_internas',
    ];

    protected $casts = [
        'id_verificada'           => 'boolean',
        'notificacion_enviada'    => 'boolean',
        'piezas_usadas'           => 'array',
        'notificacion_enviada_at' => 'datetime',
        'recibida_at'             => 'datetime',
        'en_proceso_at'           => 'datetime',
        'lista_at'                => 'datetime',
        'entregada_at'            => 'datetime',
        'costo_reparacion'        => 'decimal:2',  // faltaba
        'costo_mano_obra'         => 'decimal:2',
        'costo_piezas'            => 'decimal:2',
        'costo_total'             => 'decimal:2',
    ];

    // ── Relaciones ────────────────────────────────────────────────────────────

    public function negocio(): BelongsTo
    {
        return $this->belongsTo(Negocio::class, 'id_negocio', 'id_negocio');
    }

    public function usuarioSucursal(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'id_usuario_sucursal', 'id_usuario');
    }

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class, 'id_cliente', 'id_cliente');
    }

    public function bicicleta(): BelongsTo
    {
        return $this->belongsTo(Bicicleta::class, 'num_serie', 'num_serie');
    }

    public function piezas(): HasMany
    {
        return $this->hasMany(ReparacionPieza::class, 'id_reparacion', 'id_reparacion');
    }

    public function historial(): HasMany
    {
        return $this->hasMany(ReparacionHistorial::class, 'id_reparacion', 'id_reparacion')
                    ->orderBy('created_at');
    }

    public function cotizacion(): HasOne
    {
        return $this->hasOne(Cotizacion::class, 'id_reparacion', 'id_reparacion')
                    ->latest();
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    /**
     * ¿El cliente está registrado en el sistema?
     * Si tiene id_cliente tiene cuenta — puede optar a Garantía.
     */
    public function clienteRegistrado(): bool
    {
        return !is_null($this->id_cliente);
    }

    /**
     * ¿La cotización expiró sin respuesta?
     */
    public function cotizacionExpirada(): bool
    {
        $cot = $this->cotizacion;
        if (!$cot || !is_null($cot->respuesta)) return false;
        return $cot->expires_at && now()->isAfter($cot->expires_at);
    }

    /**
     * Avanza el estado y registra en historial.
     * El servicio llama este método — nunca el controlador directamente.
     */
    public function avanzarEstado(string $nuevoEstado, string $idUsuario, ?string $nota = null): void
    {
        $estadoAnterior = $this->estado;

        $timestamps = [
            'recibida'   => 'recibida_at',
            'en_proceso' => 'en_proceso_at',
            'lista'      => 'lista_at',
            'entregada'  => 'entregada_at',
        ];

        $updates = ['estado' => $nuevoEstado];
        if (isset($timestamps[$nuevoEstado]) && is_null($this->{$timestamps[$nuevoEstado]})) {
            $updates[$timestamps[$nuevoEstado]] = now();
        }

        $this->update($updates);

        ReparacionHistorial::create([
            'id_reparacion'  => $this->id_reparacion,
            'estado_anterior' => $estadoAnterior,
            'estado_nuevo'    => $nuevoEstado,
            'id_usuario'      => $idUsuario,
            'nota'            => $nota,
        ]);
    }

    // ── Auto-generar ID ───────────────────────────────────────────────────────

    protected static function booted(): void
    {
        static::creating(function (self $model) {
            if (empty($model->id_reparacion)) {
                $ultimo = static::where('id_negocio', $model->id_negocio)
                    ->orderByDesc('id_reparacion')
                    ->lockForUpdate()
                    ->value('id_reparacion');

                $num = $ultimo
                    ? (int) substr($ultimo, strrpos($ultimo, '-') + 1) + 1
                    : 1;

                $model->id_reparacion = 'REP-' . str_pad($num, 5, '0', STR_PAD_LEFT);
            }

            if (is_null($model->recibida_at)) {
                $model->recibida_at = now();
            }
        });
    }
    public function routeNotificationForMail($notification): ?string
    {
    
    return $this->cliente_email ?? $this->cliente?->correo;
    }
}