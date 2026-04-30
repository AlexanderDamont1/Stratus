<?php

namespace App\Models;

use App\Traits\GeneratesCustomId;
use Illuminate\Database\Eloquent\Model;

class CuponUso extends Model
{
    use GeneratesCustomId;

    protected $table      = 'cupon_usos';
    protected $primaryKey = 'id_uso';
    public $incrementing  = false;
    protected $keyType    = 'string';
    public $timestamps    = false;

    protected $fillable = [
        'id_uso',
        'id_cupon',
        'id_venta',
        'id_negocio',
        'id_usuario',
        'descuento_aplicado',
    ];

    protected $casts = [
        'descuento_aplicado' => 'float',
        'created_at'         => 'datetime',
    ];

    protected function idPrefix(): string { return 'USO'; }

    public function cupon()
    {
        return $this->belongsTo(Cupon::class, 'id_cupon', 'id_cupon');
    }

    public function venta()
    {
        return $this->belongsTo(Venta::class, 'id_venta', 'id_venta');
    }
}