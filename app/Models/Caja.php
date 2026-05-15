<?php
// ============================================================
// app/Models/Caja.php
// ============================================================
namespace App\Models;
 
use Illuminate\Database\Eloquent\Model;
 
class Caja extends Model
{
    protected $primaryKey  = 'id_caja';
    public    $incrementing = false;
    protected $keyType     = 'string';
 
    protected $fillable = [
        'id_caja', 'id_negocio', 'id_usuario', 'nombre', 'activa',
    ];
 
    protected $casts = ['activa' => 'boolean'];
 
    public function sesion()
    {
        return $this->hasMany(CajaSesion::class, 'id_caja');
    }
 
    public function sesionActiva()
    {
        return $this->hasOne(CajaSesion::class, 'id_caja')
            ->where('estado', 'abierta');
    }
 
    public function negocio()
    {
        return $this->belongsTo(Negocio::class, 'id_negocio', 'id_negocio');
    }
 
    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario', 'id_usuario');
    }
}