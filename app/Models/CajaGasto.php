<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class CajaGasto extends Model
{
    protected $table = 'caja_gastos';
    protected $primaryKey = 'id_gasto';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id_sesion',
        'id_negocio',
        'id_usuario',
        'motivo',
        'monto',
        'limite',
        'referencia',
        'notas',
        'fecha_gasto',
        'id_usuario_registro',
    ];

    protected $casts = [
        'monto'       => 'decimal:2',
        'limite'      => 'decimal:2',
        'fecha_gasto' => 'date',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (!$model->id_gasto) {
                $model->id_gasto = 'GTO' . strtoupper(Str::random(12));
            }
        });
    }

    public function sesion()
    {
        return $this->belongsTo(CajaSesion::class, 'id_sesion', 'id_sesion');
    }

    public function sucursal()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario', 'id_usuario');
    }

    public function usuarioRegistro()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario_registro', 'id_usuario');
    }
}