<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PedidoToken extends Model
{
    protected $table = 'pedido_tokens';
    protected $primaryKey = 'id_token';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id_token',
        'id_usuario1',
        'id_usuario2',
        'id_pedido',
        'token',
        'estado',
    ];

    public function pedido()
    {
        return $this->belongsTo(Pedido::class, 'id_pedido', 'id_pedido');
    }
}