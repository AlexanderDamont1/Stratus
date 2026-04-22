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
        'pdf_texto_extraido',
        'pdf_nombre_original',
        'estado_procesamiento',
        'ia_raw_json',
        'ia_procesado_at',
        'notas_admin',
    ];

    protected $casts = [
    'ia_raw_json'  => 'array',
    'activa'       => 'boolean',
    'ia_procesado_at' => 'datetime',
];

    // Nunca exponer el base64 en listados
    protected $hidden = ['pdf_base64'];

    protected function idPrefix(): string { return 'MGC'; }

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

    public function tienePdf(): bool
    {
        return !empty($this->pdf_base64);
    }
}