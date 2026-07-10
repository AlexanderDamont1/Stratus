<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\GeneratesCustomId;

class Venta extends Model
{
    use GeneratesCustomId;

    protected $table      = 'ventas';
    protected $primaryKey = 'id_venta';
    public    $incrementing = false;
    protected $keyType    = 'string';

    protected $fillable = [
        'id_venta',
        'id_negocio',
        'id_cliente',
        'id_usuario',
        'id_personal',
        'id_cupon',
        'descuento_total',
        'total',
        'comision_porcentaje',
        'comision_monto',
        'origen',
        'id_reparacion',
    ];

    protected $casts = [
        'descuento_total'     => 'decimal:2',
        'total'               => 'decimal:2',
        'comision_porcentaje' => 'decimal:2',
        'comision_monto'      => 'decimal:2',
    ];

    protected function idPrefix(): string { return 'VEN'; }

    public function negocio()  { return $this->belongsTo(Negocio::class,  'id_negocio', 'id_negocio'); }
    public function cliente()  { return $this->belongsTo(Cliente::class,  'id_cliente', 'id_cliente'); }
    public function usuario()  { return $this->belongsTo(Usuario::class,  'id_usuario', 'id_usuario'); }
    public function cupon()    { return $this->belongsTo(Cupon::class,    'id_cupon',   'id_cupon'); }
    public function detalles() { return $this->hasMany(DetalleVenta::class, 'id_venta', 'id_venta'); }
    public function pagos()    { return $this->hasMany(VentaPago::class,    'id_venta', 'id_venta'); }
    public function personal() { return $this->belongsTo(Personal::class, 'id_personal', 'id_personal'); }
    public function reparacion() { return $this->belongsTo(Reparaciones::class, 'id_reparacion', 'id_reparacion'); }

    /**
     * Concepto real de la venta para mostrar en caja/comprobantes — 'venta'
     * es el tipo genérico del ledger de caja, esto da el texto real que ve
     * el usuario (Reparación/Mantenimiento/Garantía/Venta de pieza/Venta).
     */
    public function getLabelOrigenAttribute(): string
    {
        return match ($this->origen) {
            'pieza_suelta' => 'Venta de pieza',
            'reparacion'   => match ($this->reparacion?->tipo) {
                'mantenimiento' => 'Mantenimiento',
                'garantia'      => 'Garantía',
                default         => 'Reparación',
            },
            default => 'Venta',
        };
    }
}