<?php
// app/Models/BicicletaGarantia.php

namespace App\Models;

use App\Traits\GeneratesCustomId;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class BicicletaGarantia extends Model
{
    use GeneratesCustomId;

    protected $table      = 'bicicleta_garantia';
    protected $primaryKey = 'id_bicicleta_garantia';

    public $incrementing = false;
    protected $keyType   = 'string';

    protected $fillable = [
        'id_bicicleta_garantia',
        'id_negocio',
        'num_serie',
        'id_garantia_def',
        'clave_componente',
        'fecha_inicio',
        'fecha_expiracion',
        'num_serie_componente',
        'estado',
        'id_reemplazada_por',
    ];

    protected $casts = [
        'fecha_inicio'      => 'date',
        'fecha_expiracion'  => 'date',
    ];

    protected function idPrefix(): string { return 'BGT'; }

    // ─── ACCESSOR: estado visual para el mapa ───────────────────────────────
    //
    // Lógica:
    //   'reemplazada' / 'invalidada' / 'expirada' → se respeta el estado DB
    //   'vigente' → se evalúa en tiempo real:
    //       < 30 días restantes → 'por_vencer'
    //       >= 30 días          → 'vigente'
    //
    // Se usa $this->estado_visual en lugar de sobrescribir getEstadoAttribute
    // para no romper el valor almacenado en DB.

    public function getEstadoVisualAttribute(): string
    {
        if ($this->estado !== 'vigente') {
            return $this->estado;
        }

        $diasRestantes = now()->startOfDay()
                              ->diffInDays($this->fecha_expiracion, false);

        if ($diasRestantes < 0)  return 'expirada';
        if ($diasRestantes < 30) return 'por_vencer';

        return 'vigente';
    }

    // Color para el mapa visual — listo para Blade/Vue/React
    public function getColorMapaAttribute(): string
    {
        return match($this->estado_visual) {
            'vigente'     => 'green',
            'por_vencer'  => 'yellow',
            'expirada',
            'reemplazada',
            'invalidada'  => 'red',
            default       => 'gray',
        };
    }

    // Días restantes (puede ser negativo si expiró)
    public function getDiasRestantesAttribute(): int
    {
        return (int) now()->startOfDay()
                          ->diffInDays($this->fecha_expiracion, false);
    }

    // Porcentaje de vida restante (0–100)
    public function getPorcentajeVidaAttribute(): int
    {
        $def = $this->garantiaDef;
        if (!$def || $def->duracion_meses === 0) return 0;

        $totalDias     = Carbon::parse($this->fecha_inicio)->diffInDays(Carbon::parse($this->fecha_expiracion));
        $diasRestantes = max(0, $this->dias_restantes);

        if ($totalDias === 0) return 0;

        return (int) round(($diasRestantes / $totalDias) * 100);
    }

    // ─── RELACIONES ─────────────────────────────────────────────────────────

    public function bicicleta()
    {
        return $this->belongsTo(Bicicleta::class, 'num_serie', 'num_serie');
    }

    public function garantiaDef()
    {
        return $this->belongsTo(
            GarantiaComponenteDef::class,
            'id_garantia_def',
            'id_garantia_def'
        );
    }

    public function reemplazadaPor()
    {
        return $this->belongsTo(
            BicicletaGarantia::class,
            'id_reemplazada_por',
            'id_bicicleta_garantia'
        );
    }

    public function reclamos()
    {
        return $this->hasMany(GarantiaReclamo::class, 'id_bicicleta_garantia', 'id_bicicleta_garantia');
    }
}