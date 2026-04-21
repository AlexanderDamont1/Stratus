<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Inventario extends Model
{
    protected $table      = 'inventario';
    protected $primaryKey = 'id_inventario';
    public $incrementing  = false;
    protected $keyType    = 'string';

    public $timestamps = true;

    protected $fillable = [
        'id_inventario',
        'id_producto_modelo', // nullable — bicicletas
        'id_producto',        // nullable — accesorios
        'id_negocio',
        'id_usuario',
        'cantidad',
        'stock_minimo',
    ];

    protected $casts = [
        'cantidad'     => 'integer',
        'stock_minimo' => 'integer',
    ];

    /* ================= RELACIONES ================= */

    public function productoModelo()
    {
        return $this->belongsTo(ProductoModelo::class, 'id_producto_modelo', 'id_producto_modelo');
    }

    // ← nueva: para accesorios tipo 1
    public function producto()
    {
        return $this->belongsTo(Producto::class, 'id_producto', 'id_producto');
    }

    public function negocio()
    {
        return $this->belongsTo(Negocio::class, 'id_negocio', 'id_negocio');
    }

    public function sucursal()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario', 'id_usuario');
    }

    /* ================= SCOPES ================= */

    public function scopeSucursal($query, $idUsuario)
    {
        return $query->where('id_usuario', $idUsuario);
    }

    public function scopeAdmin($query)
    {
        return $query->whereNull('id_usuario');
    }

    public function scopeStockBajo($query)
    {
        return $query->whereColumn('cantidad', '<', 'stock_minimo');
    }

    public function scopeDelNegocio($query, $idNegocio)
    {
        return $query->where('id_negocio', $idNegocio);
    }

    /* ================= HELPERS ================= */

    public function tieneStockBajo(): bool
    {
        return $this->cantidad < $this->stock_minimo;
    }

    // Para bicicletas: recalcula cantidad desde la tabla bicicletas
    public function sincronizarDesdeBicicletas(): void
    {
        $pm = ProductoModelo::find($this->id_producto_modelo);
        if (!$pm) return;

        $this->cantidad = Bicicleta::where('id_negocio', $this->id_negocio)
            ->where('id_modelo',  $pm->id_modelo)
            ->where('id_voltaje', $pm->id_voltaje)
            ->when(
                $this->id_usuario,
                fn($q) => $q->where('id_usuario', $this->id_usuario),
                fn($q) => $q->whereNull('id_usuario')
            )
            ->where('status', 1)
            ->count();

        $this->save();
    }

    /* ================= HELPERS ================= */

    // Helper para saber si es accesorio
    public function esAccesorio(): bool
    {
        return $this->id_producto_modelo === null && $this->id_producto !== null;
    }

    // Helper para saber si es bicicleta
    public function esBicicleta(): bool
    {
        return $this->id_producto_modelo !== null;
    }

    // Obtener el nombre del producto sea accesorio o bicicleta
    public function getNombreProductoAttribute(): string
    {
        if ($this->esBicicleta()) {
            return $this->productoModelo?->producto?->nombre_producto ?? '—';
        }
        return $this->producto?->nombre_producto ?? '—';
    }
}