<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrdenTrabajo extends Model
{
    protected $table        = 'ordenes_trabajo';
    protected $primaryKey   = 'id_ot';
    protected $keyType      = 'string';
    public    $incrementing = false;

    protected $fillable = [
        'id_ot', 'id_negocio', 'id_usuario_sucursal', 'id_tecnico',
        'num_serie', 'bici_descripcion',
        'id_cliente', 'cliente_nombre', 'cliente_email',
        'id_verificada',
        'tipo', 'id_garantia_aprobada',
        'problema_reportado', 'diagnostico',
        'estado',
        'costo_mano_obra', 'costo_piezas', 'costo_total', 'costo_real_garantia',
        'notificacion_enviada', 'notificacion_enviada_at',
        'recibida_at', 'en_proceso_at', 'lista_at', 'entregada_at',
        'notas_internas',
    ];

    protected $casts = [
        'id_verificada'           => 'boolean',
        'notificacion_enviada'    => 'boolean',
        'notificacion_enviada_at' => 'datetime',
        'recibida_at'             => 'datetime',
        'en_proceso_at'           => 'datetime',
        'lista_at'                => 'datetime',
        'entregada_at'            => 'datetime',
        'costo_mano_obra'         => 'decimal:2',
        'costo_piezas'            => 'decimal:2',
        'costo_total'             => 'decimal:2',
        'costo_real_garantia'     => 'decimal:2',
    ];

    // ── ID secuencial por negocio ─────────────────────────────────────────────
    protected static function booted(): void
    {
        static::creating(function (self $ot) {
            if (empty($ot->id_ot)) {
                $ot->id_ot = self::generarId($ot->id_negocio);
            }
            $ot->recibida_at ??= now();
        });
    }

    public static function generarId(string $idNegocio): string
    {
        $ultimo = self::where('id_negocio', $idNegocio)
            ->lockForUpdate()
            ->max('id_ot');

        $siguiente = $ultimo ? ((int) substr($ultimo, 3)) + 1 : 1;

        return 'OT-' . str_pad($siguiente, 5, '0', STR_PAD_LEFT);
    }

    // ── Cambio de estado con log ──────────────────────────────────────────────
    public function avanzarEstado(string $nuevoEstado, string $idUsuario, ?string $nota = null): void
    {
        $anterior = $this->estado;

        $tsMap = [
            'en_proceso' => 'en_proceso_at',
            'lista'      => 'lista_at',
            'entregada'  => 'entregada_at',
        ];

        $this->estado = $nuevoEstado;
        if (isset($tsMap[$nuevoEstado])) {
            $this->{$tsMap[$nuevoEstado]} = now();
        }
        $this->save();

        OtHistorial::create([
            'id_ot'           => $this->id_ot,
            'estado_anterior' => $anterior,
            'estado_nuevo'    => $nuevoEstado,
            'id_usuario'      => $idUsuario,
            'nota'            => $nota,
        ]);
    }

    // ── Relaciones ────────────────────────────────────────────────────────────
    public function cliente()   { return $this->belongsTo(Cliente::class,   'id_cliente',          'id_cliente'); }
    public function bicicleta() { return $this->belongsTo(Bicicleta::class, 'num_serie',           'num_serie'); }
    public function tecnico()   { return $this->belongsTo(Usuario::class,   'id_tecnico',          'id_usuario'); }
    public function sucursal()  { return $this->belongsTo(Usuario::class,   'id_usuario_sucursal', 'id_usuario'); }
    public function piezas()    { return $this->hasMany(OtPieza::class,     'id_ot',               'id_ot'); }
    public function historial() { return $this->hasMany(OtHistorial::class, 'id_ot',               'id_ot')->orderBy('created_at'); }
}