<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ModeloVoltaje extends Model
{
    protected $table      = 'modelo_voltaje';
    protected $primaryKey = 'id_mvoltaje';

    public $incrementing = false;
    protected $keyType   = 'string';
    public $timestamps   = false;

    protected $fillable = [
        'id_mvoltaje',
        'id_modelo',
        'id_voltaje',
        'id_negocio',  // ← nuevo
    ];

    protected $casts = [
        'id_mvoltaje' => 'string',
        'id_modelo'   => 'string',
        'id_voltaje'  => 'string',
        'id_negocio'  => 'string',
    ];

    /*
    |----------------------------------------
    | RELACIONES
    |----------------------------------------
    */

    public function modelo()
    {
        return $this->belongsTo(Modelo::class, 'id_modelo', 'id_modelo');
    }

    public function voltaje()
    {
        return $this->belongsTo(Voltaje::class, 'id_voltaje', 'id_voltaje');
    }

    public function negocio()
    {
        return $this->belongsTo(Negocio::class, 'id_negocio', 'id_negocio');
    }
}