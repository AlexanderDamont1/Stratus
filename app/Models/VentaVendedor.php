<?php
// ══════════════════════════════════════════════════════════════
// app/Models/VentaPago.php
// ══════════════════════════════════════════════════════════════
namespace App\Models;
 
use Illuminate\Database\Eloquent\Model;
 
class VentaVendedor extends Model
{
    protected $table      = 'venta_vendedor';
    protected $primaryKey = 'id_venta';
    public    $incrementing = false;
    protected $keyType    = 'string';
 
    protected $fillable = [
        'id_venta',
        'id_personal',
        'nombre_snapshot',
    ];
 
    public function venta()    { return $this->belongsTo(Venta::class,   'id_venta',   'id_venta'); }
    public function personal() { return $this->belongsTo(Personal::class, 'id_personal', 'id_personal'); }
}
 
 