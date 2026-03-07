<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Enlace extends Model
{
    protected $table = 'enlaces';
    protected $primaryKey = 'id_enlace';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id_enlace',
        'id_usuario1',
        'id_usuario2',
        'token_enlace',
        'estado',
    ];

    // -------------------------------------------------------
    // Boot: auto-genera UUID y token
    // -------------------------------------------------------
    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->id_enlace)) {
                $model->id_enlace = Str::uuid()->toString();
            }
            if (empty($model->token_enlace)) {
                $model->token_enlace = Str::random(32);
            }
        });
    }

    // -------------------------------------------------------
    // Relaciones
    // -------------------------------------------------------

    // Rol 1: dueño del enlace (solo puede tener UN enlace activo)
    public function usuarioAdmin()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario1', 'id_usuario');
    }

    // Rol 5: aceptó el enlace (puede tener MUCHOS enlaces con distintos rol 1)
    public function usuarioDestino()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario2', 'id_usuario');
    }

    // Pedidos del rol 1 visibles a través de este enlace
    public function pedidos()
    {
        return $this->hasMany(Pedido::class, 'id_usuario', 'id_usuario1');
    }

    // -------------------------------------------------------
    // Scopes
    // -------------------------------------------------------

    public function scopeActivo($query)
    {
        return $query->where('estado', 'activo');
    }

    public function scopePendiente($query)
    {
        return $query->where('estado', 'pendiente');
    }

    public function scopeCancelado($query)
    {
        return $query->where('estado', 'cancelado');
    }
}