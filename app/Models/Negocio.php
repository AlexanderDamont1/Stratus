<?php

namespace App\Models;

use App\Traits\GeneratesCustomId;
use Illuminate\Database\Eloquent\Model;

class Negocio extends Model
{
    use GeneratesCustomId;

    protected $table      = 'negocios';
    protected $primaryKey = 'id_negocio';
    public $incrementing  = false;
    protected $keyType    = 'string';

    // ── Status constants ──────────────────────────────────
    const STATUS_TRIAL                = 'trial';
    const STATUS_ACTIVO               = 'activo';
    const STATUS_TRIAL_EXPIRADO       = 'trial_expirado';
    const STATUS_SUSCRIPCION_EXPIRADA = 'suscripcion_expirada';
    const STATUS_SUSPENDIDO           = 'suspendido';

    protected $fillable = [
        'id_negocio',
        'nombre_negocio',
        'max_users',
        'trial_ends_at',
        'subscribed_until',
        'negocio_status',
    ];

    protected $casts = [
        'trial_ends_at'    => 'datetime',
        'subscribed_until' => 'datetime',
    ];

    protected function idPrefix(): string
    {
        return 'NEG';
    }

    // ── Helpers de estado ─────────────────────────────────

    public function estaActivo(): bool
    {
        return in_array($this->negocio_status, [
            self::STATUS_TRIAL,
            self::STATUS_ACTIVO,
        ]);
    }

    public function diasRestantes(): int
    {
        $fecha = match($this->negocio_status) {
            self::STATUS_TRIAL  => $this->trial_ends_at,
            self::STATUS_ACTIVO => $this->subscribed_until,
            default             => null,
        };

        return $fecha ? max(0, (int) now()->diffInDays($fecha, false)) : 0;
    }

    public function activarSuscripcion(int $dias = 30): void
    {
        $this->update([
            'negocio_status'   => self::STATUS_ACTIVO,
            'subscribed_until' => now()->addDays($dias),
        ]);
    }

    public function suspender(): void
    {
        $this->update(['negocio_status' => self::STATUS_SUSPENDIDO]);
    }

    // Llamado por el comando scheduleable cada hora
    public function sincronizarStatus(): void
    {
        match($this->negocio_status) {
            self::STATUS_TRIAL => $this->trial_ends_at?->isPast()
                ? $this->update(['negocio_status' => self::STATUS_TRIAL_EXPIRADO])
                : null,

            self::STATUS_ACTIVO => $this->subscribed_until?->isPast()
                ? $this->update(['negocio_status' => self::STATUS_SUSCRIPCION_EXPIRADA])
                : null,

            default => null,
        };
    }

    // ── Relaciones ────────────────────────────────────────

    public function usuarios()
    {
        return $this->hasMany(Usuario::class, 'id_negocio', 'id_negocio');
    }

    public function admin()
    {
        return $this->hasOne(Usuario::class, 'id_negocio', 'id_negocio')
                    ->where('id_rol', 1);
    }

    public function vendedores()
    {
        return $this->hasMany(Usuario::class, 'id_negocio', 'id_negocio')
                    ->where('id_rol', 2);
    }

    public function clientes()
    {
        return $this->hasMany(Cliente::class, 'id_negocio', 'id_negocio');
    }

    public function bicicletas()
    {
        return $this->hasMany(Bicicleta::class, 'id_negocio', 'id_negocio');
    }

    public function config()
    {
        return $this->hasOne(NegocioConfig::class, 'id_negocio', 'id_negocio');
    }

    public function ventas()
    {
        return $this->hasMany(Venta::class, 'id_negocio', 'id_negocio');
    }

    // ── Otros helpers ─────────────────────────────────────

    public function puedeAgregarVendedor(): bool
    {
        return $this->vendedores()->count() < $this->max_users;
    }

    public function getConfig(): NegocioConfig
    {
        return $this->config ?? new NegocioConfig([
            'id_negocio'          => $this->id_negocio,
            'entrega_comprobante' => 'ticket',
        ]);
    }
}