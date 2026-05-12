<?php

namespace App\Models;

use App\Traits\GeneratesCustomId;
use Illuminate\Database\Eloquent\Model;

class MarcaGarantiaConfig extends Model
{
    use GeneratesCustomId;

    protected $table      = 'marca_garantia_config';
    protected $primaryKey = 'id_marca_garantia';

    public $incrementing = false;
    protected $keyType   = 'string';

    protected $fillable = [
        'id_marca_garantia',
        'id_negocio',
        'id_marca',
        'activa',
        'politica_reemplazo',   // ← nuevo
        'mini_garantia_dias',   // ← nuevo
        'pdf_texto_extraido',
        'pdf_nombre_original',
        'estado_procesamiento',
        'ia_raw_json',
        'ia_procesado_at',
        'notas_admin',
    ];

    protected $casts = [
        'ia_raw_json'        => 'array',
        'activa'             => 'boolean',
        'ia_procesado_at'    => 'datetime',
        'mini_garantia_dias' => 'integer',
    ];

    protected $hidden = ['pdf_base64'];

    protected function idPrefix(): string { return 'MGC'; }

    // ─── Helpers ─────────────────────────────────────────────────────────

    public function tienePdf(): bool
    {
        return !empty($this->pdf_nombre_original);
    }

    /** Política efectiva con fallback seguro */
    public function politicaEfectiva(): string
    {
        return $this->politica_reemplazo ?? 'mini';
    }

    /** Días mini efectivos con fallback */
    public function miniDiasEfectivos(): int
    {
        return $this->mini_garantia_dias ?? 7;
    }

    // ─── Relaciones ───────────────────────────────────────────────────────

    public function marca()
    {
        return $this->belongsTo(Marca::class, 'id_marca', 'id_marca');
    }

    public function negocio()
    {
        return $this->belongsTo(Negocio::class, 'id_negocio', 'id_negocio');
    }

    public function componenteDefs()
    {
        return $this->hasMany(
            GarantiaComponenteDef::class,
            'id_marca_garantia',
            'id_marca_garantia'
        );
    }
}