<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NegocioConfig extends Model
{
    protected $table      = 'negocio_config';
    protected $primaryKey = 'id_negocio';
    public $incrementing  = false;
    protected $keyType    = 'string';

    protected $fillable = [
        'id_negocio',
        'entrega_comprobante',
    ];

    protected $casts = [
        'entrega_comprobante' => 'string',
    ];

    // ── Helper ───────────────────────────────────────────
    public function entregaPorCorreo(): bool
    {
        return $this->entrega_comprobante === 'correo';
    }

    public function entregaPorTicket(): bool
    {
        return $this->entrega_comprobante === 'ticket';
    }

    // ── Relación ─────────────────────────────────────────
    public function negocio()
    {
        return $this->belongsTo(Negocio::class, 'id_negocio', 'id_negocio');
    }
}