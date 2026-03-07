<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Modelo extends Model
{
    use HasUuids;

    protected $table = 'modelos';
    protected $primaryKey = 'id_modelo';

    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = true;

    protected $fillable = [
        'id_modelo',
        'nombre_modelo',
    ];

    protected $casts = [
        'id_modelo'     => 'string',
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
}