<?php
// ══════════════════════════════════════════════════════════════
// app/Models/MetodoPago.php
// ══════════════════════════════════════════════════════════════
namespace App\Models;
 
use Illuminate\Database\Eloquent\Model;
use App\Traits\GeneratesCustomId;
 
class MetodoPago extends Model
{
    use GeneratesCustomId;
 
    protected $table      = 'metodos_pago';
    protected $primaryKey = 'id_metodo';
    public    $incrementing = false;
    protected $keyType    = 'string';
 
    protected $fillable = [
        'id_metodo',
        'id_negocio',
        'nombre',
        'es_efectivo',
        'requiere_referencia',
        'activo',
        'orden',
    ];
 
    protected $casts = [
        'es_efectivo'          => 'boolean',
        'requiere_referencia'  => 'boolean',
        'activo'               => 'boolean',
    ];
 
    protected function idPrefix(): string { return 'MET'; }
 
    public function negocio()
    {
        return $this->belongsTo(Negocio::class, 'id_negocio', 'id_negocio');
    }
 
    public function scopeActivo($q)        { return $q->where('activo', true); }
    public function scopeDeNegocio($q, $id) { return $q->where('id_negocio', $id)->orderBy('orden'); }
}
 
 