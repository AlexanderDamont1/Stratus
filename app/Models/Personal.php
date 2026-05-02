<?php
// ══════════════════════════════════════════════════════════════
// app/Models/Personal.php
// ══════════════════════════════════════════════════════════════
namespace App\Models;
 
use Illuminate\Database\Eloquent\Model;
use App\Traits\GeneratesCustomId;
 
class Personal extends Model
{
    use GeneratesCustomId;
 
    protected $table      = 'personal';
    protected $primaryKey = 'id_personal';
    public    $incrementing = false;
    protected $keyType    = 'string';
 
    protected $fillable = [
        'id_personal',
        'id_usuario',   // sucursal
        'id_negocio',
        'nombre',
        'activo',
    ];
 
    protected function idPrefix(): string { return 'PER'; }
 
    // Sucursal a la que pertenece
    public function sucursal()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario', 'id_usuario');
    }
 
    public function negocio()
    {
        return $this->belongsTo(Negocio::class, 'id_negocio', 'id_negocio');
    }
 
    // Ventas en las que participó
    public function ventaVendedor()
    {
        return $this->hasMany(VentaVendedor::class, 'id_personal', 'id_personal');
    }
 
    // Scopes
    public function scopeActivo($q)        { return $q->where('activo', true); }
    public function scopeDeNegocio($q, $idNegocio) { return $q->where('id_negocio', $idNegocio); }
    public function scopeDeSucursal($q, $idUsuario) { return $q->where('id_usuario', $idUsuario); }
}
 
 