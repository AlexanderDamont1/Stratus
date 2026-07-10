<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CajaMovimiento extends Model
{
    protected $primaryKey   = 'id_movimiento';
    public    $incrementing = false;
    protected $keyType      = 'string';

    protected $fillable = [
        'id_movimiento', 'id_sesion', 'id_negocio', 'id_usuario',
        'id_venta',
        'metodo', 'metodo_label', 'es_efectivo',
        'tipo', 'monto', 'es_entrada',
        'concepto', 'referencia', 'origen_rol',
    ];

    protected $casts = [
        'monto'       => 'decimal:2',
        'es_entrada'  => 'boolean',
        'es_efectivo' => 'boolean',
        'origen_rol'  => 'integer',
    ];

    public function sesion()
    {
        return $this->belongsTo(CajaSesion::class, 'id_sesion', 'id_sesion');
    }

    public function venta()
    {
        return $this->belongsTo(Venta::class, 'id_venta', 'id_venta');
    }

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario', 'id_usuario');
    }

    public function getSignoAttribute(): string
    {
        return $this->es_entrada ? '+' : '−';
    }

    public function getColorTipoAttribute(): string
    {
        return match($this->tipo) {
            'venta'          => 'green',
            'ingreso_manual' => 'blue',
            'retiro'         => 'red',
            'ajuste'         => 'yellow',
            default          => 'gray',
        };
    }

    public function getLabelTipoAttribute(): string
    {
        return match($this->tipo) {
            'venta'          => $this->labelVenta(),
            'ingreso_manual' => 'Ingreso',
            'retiro'         => 'Retiro',
            'ajuste'         => 'Ajuste',
            'apertura'       => 'Apertura',
            default          => ucfirst($this->tipo),
        };
    }

    /**
     * "Venta" es genérico para el ledger de caja (todo ingreso por venta usa
     * tipo='venta'), pero el comprobante debe reflejar el concepto real:
     * cobro de OT (Reparación/Mantenimiento/Garantía), venta de pieza suelta,
     * o venta normal de mostrador. Depende de $this->venta (y, si aplica,
     * $this->venta->reparacion) — cárgalos con with('venta.reparacion') antes
     * de listar varios movimientos para evitar N+1.
     */
    private function labelVenta(): string
    {
        return $this->venta?->label_origen ?? 'Venta';
    }
}