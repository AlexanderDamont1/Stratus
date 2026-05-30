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
      
        'id_cupon',
        'descuento_total',
        'total',
    ];

    protected $casts = [
        'descuento_total' => 'decimal:2',
        'total'           => 'decimal:2',
    ];

    protected function idPrefix(): string { return 'VEN'; }

    public function negocio()  { return $this->belongsTo(Negocio::class,  'id_negocio', 'id_negocio'); }
    public function cliente()  { return $this->belongsTo(Cliente::class,  'id_cliente', 'id_cliente'); }
    public function usuario()  { return $this->belongsTo(Usuario::class,  'id_usuario', 'id_usuario'); }
    public function cupon()    { return $this->belongsTo(Cupon::class,    'id_cupon',   'id_cupon'); }
    public function detalles() { return $this->hasMany(DetalleVenta::class, 'id_venta', 'id_venta'); }
    public function pagos()    { return $this->hasMany(VentaPago::class,    'id_venta', 'id_venta'); }
    public function personal() { return $this->belongsTo(Personal::class, 'id_personal', 'id_personal'); }
}