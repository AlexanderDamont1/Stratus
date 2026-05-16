<?php
// ============================================================
// app/Models/CajaSesion.php
// ============================================================
namespace App\Models;
 
use Illuminate\Database\Eloquent\Model;
 
class CajaSesion extends Model
{
    protected $table = 'caja_sesiones';
    protected $primaryKey  = 'id_sesion';
    public    $incrementing = false;
    protected $keyType     = 'string';
 
    protected $fillable = [
        'id_sesion', 'id_caja', 'id_negocio',
        'id_usuario_apertura', 'id_usuario_cierre',
        'fondo_inicial',
        'monto_cierre_declarado', 'monto_cierre_sistema', 'diferencia',
        'estado', 'motivo_cierre', 'notas_cierre',
        'abierta_at', 'cerrada_at',
    ];
 
    protected $casts = [
        'fondo_inicial'           => 'decimal:2',
        'monto_cierre_declarado'  => 'decimal:2',
        'monto_cierre_sistema'    => 'decimal:2',
        'diferencia'              => 'decimal:2',
        'abierta_at'              => 'datetime',
        'cerrada_at'              => 'datetime',
    ];
 
    // ── Relaciones ─────────────────────────────────────────────────
 
    public function caja()
    {
        return $this->belongsTo(Caja::class, 'id_caja', 'id_caja');
    }
 
    public function movimientos()
    {
        return $this->hasMany(CajaMovimiento::class, 'id_sesion');
    }
 
    public function cortes()
    {
        return $this->hasMany(CajaCorte::class, 'id_sesion');
    }
 
    public function usuarioApertura()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario_apertura', 'id_usuario');
    }
 
    public function usuarioCierre()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario_cierre', 'id_usuario');
    }
 
    // ── Helpers ────────────────────────────────────────────────────
 
    public function getEstaAbiertaAttribute(): bool
    {
        return $this->estado === 'abierta';
    }
 
    public function getDuracionAttribute(): string
    {
        $desde = $this->abierta_at;
        $hasta = $this->cerrada_at ?? now();
        $diff  = $desde->diff($hasta);
 
        if ($diff->h > 0) {
            return $diff->h . 'h ' . $diff->i . 'min';
        }
        return $diff->i . ' min';
    }
}