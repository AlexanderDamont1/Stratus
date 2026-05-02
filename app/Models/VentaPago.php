<?php
// ══════════════════════════════════════════════════════════════
// app/Models/VentaPago.php
// ══════════════════════════════════════════════════════════════
namespace App\Models;
 
use Illuminate\Database\Eloquent\Model;
use App\Traits\GeneratesCustomId;
 
class VentaPago extends Model
{
    use GeneratesCustomId;
 
    protected $table      = 'venta_pagos';
    protected $primaryKey = 'id_pago';
    public    $incrementing = false;
    protected $keyType    = 'string';
 
    protected $fillable = [
        'id_pago',
        'id_venta',
        'id_metodo',
        'id_negocio',
        'monto',
        'referencia',
    ];
 
    protected $casts = ['monto' => 'decimal:2'];
 
    protected function idPrefix(): string { return 'PAG'; }
 
    public function venta()    { return $this->belongsTo(Venta::class,      'id_venta',  'id_venta'); }
    public function metodo()   { return $this->belongsTo(MetodoPago::class, 'id_metodo', 'id_metodo'); }
}
 
 