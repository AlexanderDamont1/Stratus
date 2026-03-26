<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Modelo extends Model
{
   

    protected $table      = 'modelos';
    protected $primaryKey = 'id_modelo';

    public $incrementing = false;
    protected $keyType   = 'string';
    public $timestamps   = true;

    protected $fillable = [
        'id_modelo',
        'id_marca',      // ← nuevo
        'id_negocio',    // ← nuevo
        'nombre_modelo',
    ];

    protected $casts = [
        'id_modelo'     => 'string',
        'id_marca'      => 'string',
        'id_negocio'    => 'string',
        'nombre_modelo' => 'string',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $fecha  = now()->format('ymd');
            $letras = strtoupper(substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 3));
            $nums   = random_int(100, 999);

            $model->id_modelo = "MOD{$fecha}{$letras}{$nums}";
        });
    }

    /*
    |----------------------------------------
    | RELACIONES
    |----------------------------------------
    */

    public function marca()
    {
        return $this->belongsTo(Marca::class, 'id_marca', 'id_marca');
    }

    public function negocio()
    {
        return $this->belongsTo(Negocio::class, 'id_negocio', 'id_negocio');
    }

    public function colores()
    {
        return $this->hasMany(Color::class, 'id_modelo', 'id_modelo');
    }
    public function bicicletas()
{
    return $this->hasMany(Bicicleta::class, 'id_modelo', 'id_modelo');
}

    public function voltajes()
    {
        return $this->belongsToMany(
            Voltaje::class,
            'modelo_voltaje',
            'id_modelo',
            'id_voltaje'
        )->withPivot('id_mvoltaje');
    }
}