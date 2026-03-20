<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Marca extends Model
{
    protected $table      = 'marcas';
    protected $primaryKey = 'id_marca';

    public $incrementing = false;
    protected $keyType   = 'string';
    public $timestamps   = true;

    protected $fillable = [
        'id_marca',
        'id_negocio',
        'nombre_marca',
    ];

    protected $casts = [
        'id_marca'     => 'string',
        'id_negocio'   => 'string',
        'nombre_marca' => 'string',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $fecha  = now()->format('ymd');
            $letras = strtoupper(substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 3));
            $nums   = random_int(100, 999);

            $model->id_marca = "MRC{$fecha}{$letras}{$nums}";
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
        return $this->hasMany(Modelo::class, 'id_marca', 'id_marca');
    }
}