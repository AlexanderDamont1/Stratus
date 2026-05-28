<?php

namespace App\Models;

use App\Traits\GeneratesCustomId;
use Illuminate\Database\Eloquent\Model;

class ReporteRobo extends Model
{
    use GeneratesCustomId;

    protected $table      = 'reportes_robo';
    protected $primaryKey = 'id_reporte';
    public $incrementing  = false;
    protected $keyType    = 'string';

    // ── Constantes de estado ──────────────────────────────
    const PENDIENTE   = 0;
    const CONFIRMADO  = 1;
    const EN_CUSTODIA = 2;
    const CERRADO     = 3;

    protected $fillable = [
        'id_reporte',
        'num_serie',
        'id_negocio_origen',
        'id_negocio_reporta',
        'id_cliente',
        'estado',
        'token_confirmacion',
        'token_expires_at',
        'confirmado_at',
        'encontrado_at',
        'entregado_at',
        'id_negocio_encontrado',
        'notas',
    ];

    protected $casts = [
        'estado'           => 'integer',
        'token_expires_at' => 'datetime',
        'confirmado_at'    => 'datetime',
        'encontrado_at'    => 'datetime',
        'entregado_at'     => 'datetime',
    ];

    protected function idPrefix(): string { return 'ROB'; }

    // ── Helpers de estado ─────────────────────────────────
    public function esPendiente(): bool   { return $this->estado === self::PENDIENTE; }
    public function esConfirmado(): bool  { return $this->estado === self::CONFIRMADO; }
    public function enCustodia(): bool    { return $this->estado === self::EN_CUSTODIA; }
    public function esCerrado(): bool     { return $this->estado === self::CERRADO; }

    public function estaActivo(): bool
    {
        return in_array($this->estado, [self::PENDIENTE, self::CONFIRMADO, self::EN_CUSTODIA]);
    }

    public function etiquetaEstado(): string
    {
        return match($this->estado) {
            self::PENDIENTE   => 'Pendiente confirmación',
            self::CONFIRMADO  => 'Confirmado — en búsqueda',
            self::EN_CUSTODIA => 'En custodia',
            self::CERRADO     => 'Cerrado',
            default           => 'Desconocido',
        };
    }

    public function tokenValido(): bool
    {
        return $this->token_confirmacion !== null
            && $this->token_expires_at?->isFuture();
    }

    // ── Relaciones ────────────────────────────────────────
    public function bicicleta()
    {
        return $this->belongsTo(Bicicleta::class, 'num_serie', 'num_serie');
    }

    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'id_cliente', 'id_cliente');
    }

    public function negocioOrigen()
    {
        return $this->belongsTo(Negocio::class, 'id_negocio_origen', 'id_negocio');
    }

    public function negocioReporta()
    {
        return $this->belongsTo(Negocio::class, 'id_negocio_reporta', 'id_negocio');
    }

    public function negocioEncontrado()
    {
        return $this->belongsTo(Negocio::class, 'id_negocio_encontrado', 'id_negocio');
    }
}   