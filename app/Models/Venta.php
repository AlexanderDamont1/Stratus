<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\GeneratesCustomId;

class Venta extends Model
{
    use GeneratesCustomId;  // ← esto faltaba

    protected $table      = 'ventas';
    protected $primaryKey = 'id_venta';
    public $incrementing  = false;
    protected $keyType    = 'string';

    protected $fillable = [
        'id_venta',
        'id_negocio',
        'id_cliente',
    ];

    protected function idPrefix(): string { return 'VEN'; }

    public function negocio()  { return $this->belongsTo(Negocio::class,    'id_negocio', 'id_negocio'); }
    public function cliente()  { return $this->belongsTo(Cliente::class,    'id_cliente', 'id_cliente'); }
    public function detalles() { return $this->hasMany(DetalleVenta::class, 'id_venta',   'id_venta'); }
    public function vendedor()
    {
        return $this->hasOne(VentaVendedor::class, 'id_venta', 'id_venta');
    }
 
    public function pagos()
    {
        return $this->hasMany(VentaPago::class, 'id_venta', 'id_venta');
    }
}