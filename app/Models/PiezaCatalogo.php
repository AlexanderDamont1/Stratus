<?php
// app/Models/PiezaCatalogo.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class PiezaCatalogo extends Model
{
    protected $table      = 'piezas_catalogo';
    protected $primaryKey = 'id_pieza';

    protected $fillable = [
        'id_negocio',
        'clave',
        'nombre',
        'categoria',
        'marca_pieza',
        'modelos_compatibles',
        'voltaje_compatible',
        'descripcion',
        'precio_costo',
        'precio_venta',
        'stock_actual',
        'stock_minimo',
        'serializable',
        'activo',
    ];

    protected $casts = [
        'modelos_compatibles' => 'array',
        'precio_costo'        => 'decimal:2',
        'precio_venta'        => 'decimal:2',
        'serializable'        => 'boolean',
        'activo'              => 'boolean',
    ];

    // ── Scopes ───────────────────────────────────────────────────────────────

    public function scopeActivas(Builder $q): Builder
    {
        return $q->where('activo', true);
    }

    public function scopeStockBajo(Builder $q): Builder
    {
        return $q->whereRaw('stock_actual > 0 AND stock_actual <= stock_minimo');
    }

    public function scopeAgotadas(Builder $q): Builder
    {
        return $q->where('stock_actual', 0);
    }

    public function scopeDeNegocio(Builder $q, string $idNegocio): Builder
    {
        return $q->where('id_negocio', $idNegocio);
    }

    // ── Accessors ────────────────────────────────────────────────────────────

    // 'ok' | 'bajo' | 'agotado'
    public function getEstadoStockAttribute(): string
    {
        if ($this->stock_actual === 0)                             return 'agotado';
        if ($this->stock_actual <= $this->stock_minimo)           return 'bajo';
        return 'ok';
    }

    public function getMargenAttribute(): float
    {
        if ($this->precio_costo <= 0) return 0;
        return round((($this->precio_venta - $this->precio_costo) / $this->precio_costo) * 100, 2);
    }

    // ── Relaciones ───────────────────────────────────────────────────────────

    public function negocio()
    {
        return $this->belongsTo(Negocio::class, 'id_negocio', 'id_negocio');
    }

    // Piezas usadas en OTs
    public function otPiezas()
    {
        return $this->hasMany(OtPieza::class, 'id_pieza', 'id_pieza');
    }
}