<?php

namespace App\Models;

use App\Traits\GeneratesCustomId;
use Illuminate\Database\Eloquent\Model;

class Cupon extends Model
{
    use GeneratesCustomId;

    protected $table      = 'cupones';
    protected $primaryKey = 'id_cupon';
    public $incrementing  = false;
    protected $keyType    = 'string';

    protected $fillable = [
        'id_cupon',
        'id_negocio',
        'codigo',
        'nombre',
        'tipo_descuento',
        'valor_descuento',
        'aplica_a',
        'id_producto_gratis',
        'activo',
        'usos_maximos',
        'usos_actuales',
        'fecha_inicio',
        'fecha_fin',
    ];

    protected $casts = [
        'activo'          => 'boolean',
        'valor_descuento' => 'float',
        'usos_maximos'    => 'integer',
        'usos_actuales'   => 'integer',
        'fecha_inicio'    => 'datetime',
        'fecha_fin'       => 'datetime',
    ];

    protected function idPrefix(): string { return 'CUP'; }

    // ── Helpers ───────────────────────────────────────────

    public function estaVigente(): bool
    {
        if (!$this->activo) return false;
        if ($this->usos_maximos && $this->usos_actuales >= $this->usos_maximos) return false;
        if ($this->fecha_inicio && now()->lt($this->fecha_inicio)) return false;
        if ($this->fecha_fin && now()->gt($this->fecha_fin)) return false;
        return true;
    }

    public function esPorcentaje(): bool { return $this->tipo_descuento === 'porcentaje'; }
    public function esMonto(): bool      { return $this->tipo_descuento === 'monto_fijo'; }
    public function aplicaAlTotal(): bool { return $this->aplica_a === 'total'; }

    // ── Relaciones ────────────────────────────────────────

    public function negocio()
    {
        return $this->belongsTo(Negocio::class, 'id_negocio', 'id_negocio');
    }

    public function reglas()
    {
        return $this->hasMany(CuponRegla::class, 'id_cupon', 'id_cupon');
    }

    public function usos()
    {
        return $this->hasMany(CuponUso::class, 'id_cupon', 'id_cupon');
    }

    public function ventas()
    {
        return $this->hasMany(Venta::class, 'id_cupon', 'id_cupon');
    }

    public function productoGratis()
    {
        return $this->belongsTo(\App\Models\Producto::class, 'id_producto_gratis', 'id_producto');
    }

    
}