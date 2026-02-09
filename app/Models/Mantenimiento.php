<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\GeneratesCustomId;


class Mantenimiento extends Model
{
    protected $table = 'mantenimientos';
    protected $primaryKey = 'id_mantenimiento';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id_mantenimiento',
        'id_negocio',
        'num_serie',
        'estado',
        'ubicacion',
        'fecha_ingreso',
        'fecha_estimada_salida',
        'fecha_salida',
        'motivo',
    ];

    protected function idPrefix(): string
    {
        return 'MAN';
    }

    public function bicicleta()
    {
        return $this->belongsTo(Bicicleta::class, 'num_serie', 'num_serie');
    }

    public function negocio()
    {
        return $this->belongsTo(Negocio::class, 'id_negocio', 'id_negocio');
    }
}
