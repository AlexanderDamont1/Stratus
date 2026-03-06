<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ModeloVoltaje extends Model
{
    protected $table = 'modelo_voltaje';
    protected $primaryKey = 'id_mvoltaje';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id_mvoltaje',
        'id_modelo',
        'id_voltaje'
    ];

    public function modelo()
    {
        return $this->belongsTo(Modelo::class, 'id_modelo', 'id_modelo');
    }

    public function voltaje()
    {
        return $this->belongsTo(Voltaje::class, 'id_voltaje', 'id_voltaje');
    }
}