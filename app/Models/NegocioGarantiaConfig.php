<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NegocioGarantiaConfig extends Model
{
    protected $table      = 'negocio_garantia_config';
    protected $primaryKey = 'id_negocio';

    public $incrementing = false;
    protected $keyType   = 'string';
    public $timestamps   = false; // solo updated_at manual

    protected $fillable = [
        'id_negocio',
        'politica_reemplazo',
        'mini_garantia_dias',
    ];

    protected $casts = [
        'mini_garantia_dias' => 'integer',
    ];

    public function negocio()
    {
        return $this->belongsTo(Negocio::class, 'id_negocio', 'id_negocio');
    }
}