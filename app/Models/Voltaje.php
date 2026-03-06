<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Voltaje extends Model
{
    use HasUuids;

    protected $table = 'voltajes';
    protected $primaryKey = 'id_voltaje';

    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'id_voltaje',
        'voltaje',
    ];

    protected $casts = [
        'id_voltaje' => 'string',
        'voltaje'    => 'string',
    ];

    public function modelos()
{
    return $this->belongsToMany(
        Modelo::class,
        'modelo_voltaje',  // tabla pivote
        'id_voltaje',      // FK de este modelo en la pivote
        'id_modelo'        // FK del otro modelo en la pivote
    )->withPivot('id_mvoltaje');
}
}