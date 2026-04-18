<?php

namespace App\Models;

use App\Traits\GeneratesCustomId;
use Illuminate\Database\Eloquent\Model;

class GarantiaReemplazo extends Model
{
    use GeneratesCustomId;

    protected $table      = 'garantia_reemplazo';
    protected $primaryKey = 'id_reemplazo';

    public $incrementing = false;
    protected $keyType   = 'string';

    public $timestamps = false;

    protected $fillable = [
        'id_reemplazo',
        'id_negocio',
        'id_reclamo',
        'id_garantia_anterior',
        'id_garantia_nueva',
        'num_serie_nuevo_componente',
        'politica_aplicada',
        'fecha_reemplazo',
        'notas',
    ];

    protected $casts = [
        'fecha_reemplazo' => 'date',
        'created_at'      => 'datetime',
    ];

    protected function idPrefix(): string { return 'GRP'; }

    public function reclamo()
    {
        return $this->belongsTo(GarantiaReclamo::class, 'id_reclamo', 'id_reclamo');
    }

    public function garantiaAnterior()
    {
        return $this->belongsTo(
            BicicletaGarantia::class,
            'id_garantia_anterior',
            'id_bicicleta_garantia'
        );
    }

    public function garantiaNueva()
    {
        return $this->belongsTo(
            BicicletaGarantia::class,
            'id_garantia_nueva',
            'id_bicicleta_garantia'
        );
    }
}