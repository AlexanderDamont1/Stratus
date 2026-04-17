<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BicicletaMovimiento extends Model
{
    use HasFactory;

    protected $table= 'bicicleta_movimientos';
    
    // 1. Corregir el nombre de la PK
    protected $primaryKey = 'id_movimiento';

    // 2. Indicar que NO es autoincremental y es de tipo string
    public $incrementing = false;
    protected $keyType = 'string';

    public $timestamps = true;

    protected $fillable = [
        'id_movimiento', // Agregado aquí
        'num_serie',
        'id_negocio',
        'id_usuario',
        'tipo_movimiento',
        'origen',
        'destino',
        'id_pedido',
        'notas',
        'fecha_movimiento',
    ];

    protected $casts = [
        'fecha_movimiento' => 'datetime',
    ];

    /* ======= RELACIONES ======= */

    public function bicicleta()
    {
        // Se asume que en el modelo Bicicleta la PK también es 'num_serie'
        return $this->belongsTo(Bicicleta::class, 'num_serie', 'num_serie');
    }

    public function negocio()
    {
        return $this->belongsTo(Negocio::class, 'id_negocio', 'id_negocio');
    }

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario', 'id_usuario');
    }

    public function pedido()
    {
        return $this->belongsTo(Pedido::class, 'id_pedido', 'id_pedido');
    }

    /* ======= SCOPES ======= */

    public function scopePorNegocio($query, string $id_negocio)
    {
        return $query->where('id_negocio', $id_negocio);
    }

    public function scopePorSerie($query, string $num_serie)
    {
        return $query->where('num_serie', $num_serie);
    }
}