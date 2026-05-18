<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Voltaje extends Model
{

    protected $table      = 'voltajes';
    protected $primaryKey = 'id_voltaje';

    public $incrementing = false;
    protected $keyType   = 'string';
    public $timestamps   = false;

    protected $fillable = [
        'id_voltaje',
        'id_negocio',  // ← nuevo
        'voltaje',
    ];

    protected $casts = [
        'id_voltaje' => 'string',
        'id_negocio' => 'string',
        'voltaje'    => 'string',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $fecha  = now()->format('ymd');
            $letras = strtoupper(substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 3));
            $nums   = random_int(100, 999);

            $model->id_voltaje = "VOL{$fecha}{$letras}{$nums}";
        });
    }

    /*
    |----------------------------------------
    | RELACIONES
    |----------------------------------------
    */

    public function negocio()
    {
        return $this->belongsTo(Negocio::class, 'id_negocio', 'id_negocio');
    }

    public function modelos()
    {
        return $this->belongsToMany(
            Modelo::class,
            'modelo_voltaje',
            'id_voltaje',
            'id_modelo'
        )->withPivot('id_mvoltaje');
    }
}