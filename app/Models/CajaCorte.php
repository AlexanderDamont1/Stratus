<?php
// ============================================================
// app/Models/CajaCorte.php
// ============================================================
namespace App\Models;
 
use Illuminate\Database\Eloquent\Model;
 
class CajaCorte extends Model
{
    protected $primaryKey  = 'id_corte';
    public    $incrementing = false;
    protected $keyType     = 'string';
 
    protected $fillable = [
        'id_corte', 'id_sesion', 'id_negocio', 'id_usuario', 'snapshot',
    ];
 
    protected $casts = [
        'snapshot' => 'array',
    ];
 
    // ── Relaciones ─────────────────────────────────────────────────
 
    public function sesion()
    {
        return $this->belongsTo(CajaSesion::class, 'id_sesion', 'id_sesion');
    }
 
    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario', 'id_usuario');
    }
 
    // ── Helpers ────────────────────────────────────────────────────
 
    public function getTipoAttribute(): string
    {
        return $this->snapshot['tipo'] ?? 'parcial';
    }
 
    public function getTotalSistemaAttribute(): float
    {
        return $this->snapshot['totales']['total_sistema'] ?? 0.0;
    }
}