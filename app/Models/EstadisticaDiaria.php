<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class EstadisticaDiaria extends Model
{
    protected $table      = 'estadisticas_diarias';
    protected $primaryKey = 'id_estadistica';
    public    $incrementing = false;
    protected $keyType    = 'string';

    protected $fillable = [
        'id_estadistica',
        'id_negocio',
        'fecha',
        'ventas_count',
        'ingresos_total',
        'ticket_promedio',
        'descuentos_total',
        'clientes_nuevos',
        'clientes_rec',
        'modelo_top_id',
        'modelo_top_nombre',
        'modelo_top_unidades',
        'config_top',
        'config_top_unidades',
        'accesorio_top_nombre',
        'accesorio_top_uds',
        'combo_top',
        'combo_top_uds',
        'cupones_usados',
        'cupones_descuento',
        'cupon_top_codigo',
        'cupon_top_usos',
        'sucursal_data',
        'horas_pico',
        'metodos_pago',
        'ots_creadas',
        'ots_cerradas',
        'ots_ingresos_total',
        'ots_tiempo_promedio_horas',
    ];

    protected $casts = [
        'fecha'             => 'date',
        'sucursal_data'     => 'array',
        'horas_pico'        => 'array',
        'metodos_pago'      => 'array',
        'ingresos_total'    => 'float',
        'ticket_promedio'   => 'float',
        'descuentos_total'  => 'float',
        'cupones_descuento' => 'float',
    ];

    public static function generarId(\Carbon\Carbon $fecha): string
    {
        $dia    = $fecha->format('d');           // 15
        $mes    = $fecha->format('m');           // 06
        $anio   = $fecha->format('y');           // 25
        $letras = strtoupper(Str::random(3));    // ABC
        $nums   = str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT); // 1234

        return "ESTD{$dia}{$mes}{$anio}{$letras}{$nums}";
    }

    public function negocio()
    {
        return $this->belongsTo(Negocio::class, 'id_negocio', 'id_negocio');
    }
}