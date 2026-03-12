<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pedido extends Model
{
    protected $primaryKey = 'id_pedido';
    public $incrementing  = false;
    protected $keyType    = 'string';

    protected $fillable = [
        'id_pedido',
        'id_negocio',
        'id_usuario',
        'status',
        'notas',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
           $model->id_pedido = 'PD'
            . now()->format('ymd')
            . strtoupper(substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 3))
            . random_int(1000, 9999);
                 });
    }

    // Relaciones
    public function negocio()
    {
        return $this->belongsTo(Negocio::class, 'id_negocio', 'id_negocio');
    }

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario', 'id_usuario');
    }

    public function items()
    {
        return $this->hasMany(PedidoItem::class, 'id_pedido', 'id_pedido');
    }

    public function bicicletas()
{
    return $this->hasMany(Bicicleta::class, 'id_pedido', 'id_pedido');
}

    // Helper status
    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            1 => 'Solicitado',
            2 => 'Verificando Pago',
            3 => 'Listo para Entregar',
            4 => 'Entregado',
            default => 'Desconocido',
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            1 => 'yellow',
            2 => 'blue',
            3 => 'green',
            default => 'red',
        };
    }

    protected $casts = [
    'status' => 'integer',
];
}