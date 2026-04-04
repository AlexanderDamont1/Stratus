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
        'id_producto_modelo',
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

    public function negocio()
    {
        return $this->belongsTo(Negocio::class, 'id_negocio', 'id_negocio');
    }

    public function sucursal()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario', 'id_usuario');
    }

    /* ================= SCOPES ================= */

    // Inventario de una sucursal específica
    public function scopeSucursal($query, $idUsuario)
    {
        return $query->where('id_usuario', $idUsuario);
    }

    // Inventario del admin (stock central)
    public function scopeAdmin($query)
    {
        return $query->whereNull('id_usuario');
    }

    // Solo registros con stock bajo
    public function scopeStockBajo($query)
    {
        return $query->whereColumn('cantidad', '<', 'stock_minimo');
    }

    // Filtrar por negocio (siempre debe usarse para multi-tenancy)
    public function scopeDelNegocio($query, $idNegocio)
    {
        return $query->where('id_negocio', $idNegocio);
    }

    /* ================= HELPERS ================= */

    // Saber si el stock está bajo
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
            ->where('status', 1) // ← en_stock = 1
            ->count();

        $this->save();
    }
}