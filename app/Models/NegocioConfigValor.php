<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class NegocioConfigValor extends Model
{
    protected $table = 'negocio_config_valores';

    protected $primaryKey = 'id_nvc';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'id_nvc',
        'id_negocio',
        'id_ncf',
        'clave',
        'valor',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {

            if (!$model->id_nvc) {

                $model->id_nvc =
                    'NVC' .
                    now()->format('ymd') .
                    strtoupper(Str::random(10));
            }
        });
    }

    public function definicion()
    {
        return $this->belongsTo(NegocioConfig::class, 'id_ncf', 'id_ncf');
    }

    public function negocio()
    {
        return $this->belongsTo(Negocio::class, 'id_negocio', 'id_negocio');
    }
}