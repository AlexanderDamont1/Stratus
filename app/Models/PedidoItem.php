<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PedidoItem extends Model
{
    protected $primaryKey = 'id_pedido_item';
    public $incrementing  = false;
    protected $keyType    = 'string';

    protected $fillable = [
        'id_pedido_item',
        'id_pedido',
        'id_modelo',
        'id_voltaje',
        'id_color',
        'cantidad',
    ];

    protected static function boot()
{
    parent::boot();

    static::creating(function ($model) {

        if (!$model->id_pedido_item) {
            $model->id_pedido_item =
                'PIT'
                . now()->format('ymd')
                . strtoupper(substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 3))
                . random_int(100, 999);
        }

    });
}

    public function pedido()
    {
        return $this->belongsTo(Pedido::class, 'id_pedido', 'id_pedido');
    }

    public function modelo()
    {
        return $this->belongsTo(Modelo::class, 'id_modelo', 'id_modelo');
    }

    public function voltaje()
    {
        return $this->belongsTo(Voltaje::class, 'id_voltaje', 'id_voltaje');
    }

    public function color()
    {
        return $this->belongsTo(Color::class, 'id_color', 'id_color');
    }
}