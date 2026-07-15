<?php

namespace App\Models;

use App\Traits\GeneratesCustomId;
use Illuminate\Database\Eloquent\Model;

class GarantiaComponenteDef extends Model
{
    use GeneratesCustomId;

    protected $table      = 'garantia_componente_def';
    protected $primaryKey = 'id_garantia_def';

    public $incrementing = false;
    protected $keyType   = 'string';

    protected $fillable = [
        'id_garantia_def',
        'id_marca_garantia',
        'id_negocio',
        'clave_componente',
        'nombre_componente',
        'incluye',
        'duracion_meses',
        'cobertura',
        'excepciones',
        'serializable',
        'excluido',
        'activo',
    ];

    protected $casts = [
        'incluye'       => 'array',
        'excepciones'   => 'array',
        'serializable'  => 'boolean',
        'excluido'      => 'boolean',
        'activo'        => 'boolean',
        'duracion_meses'=> 'integer',
    ];

    protected function idPrefix(): string { return 'GCD'; }

    public function marcaGarantia()
    {
        return $this->belongsTo(
            MarcaGarantiaConfig::class,
            'id_marca_garantia',
            'id_marca_garantia'
        );
    }
}