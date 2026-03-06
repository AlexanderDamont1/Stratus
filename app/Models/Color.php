<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Color extends Model
{
    use HasUuids;

    protected $table = 'colores';
    protected $primaryKey = 'id_color';

    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'id_color',
        'id_modelo',
        'color',
    ];

    protected $casts = [
        'id_color'  => 'string',
        'id_modelo' => 'string',
        'color'     => 'string',
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
}