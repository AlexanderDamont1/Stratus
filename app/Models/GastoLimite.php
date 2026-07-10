<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class GastoLimite extends Model
{
    protected $table = 'gasto_limites';
    protected $primaryKey = 'id_limite';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id_negocio',
        'id_usuario',
        'limite_semanal',
        'id_usuario_admin',
    ];

    protected $casts = [
        'limite_semanal' => 'decimal:2',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (!$model->id_limite) {
                $model->id_limite = 'LIM' . strtoupper(Str::random(12));
            }
        });
    }

    public function sucursal()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario', 'id_usuario');
    }
}