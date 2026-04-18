<?php

namespace App\Models;

use App\Traits\GeneratesCustomId;
use Illuminate\Database\Eloquent\Model;

class GarantiaReclamo extends Model
{
    use GeneratesCustomId;

    protected $table      = 'garantia_reclamo';
    protected $primaryKey = 'id_reclamo';

    public $incrementing = false;
    protected $keyType   = 'string';

    protected $fillable = [
        'id_reclamo',
        'id_negocio',
        'id_mantenimiento',
        'id_bicicleta_garantia',
        'num_serie',
        'clave_componente',
        'estado',
        'motivo_reclamo',
        'resultado',
        'requiere_reemplazo',
    ];

    protected $casts = [
        'requiere_reemplazo' => 'boolean',
    ];

    protected function idPrefix(): string { return 'GRC'; }

    public function mantenimiento()
    {
        return $this->belongsTo(Mantenimiento::class, 'id_mantenimiento', 'id_mantenimiento');
    }

    public function bicicletaGarantia()
    {
        return $this->belongsTo(
            BicicletaGarantia::class,
            'id_bicicleta_garantia',
            'id_bicicleta_garantia'
        );
    }

    public function reemplazo()
    {
        return $this->hasOne(GarantiaReemplazo::class, 'id_reclamo', 'id_reclamo');
    }
}