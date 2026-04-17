<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bicicleta extends Model
{
    protected $table = 'bicicletas';
    protected $primaryKey = 'num_serie';

    public $incrementing = false;
    protected $keyType = 'string';

    // Actívalo solo si tu tabla tiene created_at y updated_at
    public $timestamps = true;

    protected $fillable = [
        'num_serie',
        'id_negocio',
        'id_producto',
        'id_modelo',
        'id_voltaje',
        'id_marca',
        'id_color',
        'status',
        'id_pedido',
    ];

    /* ================= RELACIONES ================= */

    public function negocio()
    {
        return $this->belongsTo(Negocio::class, 'id_negocio', 'id_negocio');
    }

    public function marca()
    {
        return $this->belongsTo(Marca::class, 'id_marca', 'id_marca');
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

    public function mantenimientos()
    {
        return $this->hasMany(Mantenimiento::class, 'num_serie', 'num_serie');
    }
    
        public function movimientos()
    {
        return $this->hasMany(BicicletaMovimiento::class, 'num_serie', 'num_serie')
                    ->orderBy('fecha_movimiento', 'asc');
    }

    public function ultimoMovimiento()
    {
        return $this->hasOne(BicicletaMovimiento::class, 'num_serie', 'num_serie')
                    ->latestOfMany('fecha_movimiento');
    }
    
}