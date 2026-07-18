<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

// Las opciones de negocio_config.clave='metodos_pago' se sembraron sin las
// llaves 'es_efectivo' y 'requiere_referencia' que todo el código ya espera
// (VentaController, ReparacionController::cobrarForm, StockController,
// VentaService::validarPagos) — por eso esos flags siempre evaluaban false.
// Esta migración corrige los datos, no el esquema (la columna 'opciones' ya
// es JSON en el schema original).
return new class extends Migration
{
    private const FLAGS = [
        'efectivo'        => ['es_efectivo' => true,  'requiere_referencia' => false],
        'tarjeta'         => ['es_efectivo' => false, 'requiere_referencia' => true],
        'transferencia'   => ['es_efectivo' => false, 'requiere_referencia' => true],
        'credito_interno' => ['es_efectivo' => false, 'requiere_referencia' => false],
    ];

    public function up(): void
    {
        $filas = DB::table('negocio_config')->where('clave', 'metodos_pago')->get();

        foreach ($filas as $fila) {
            $opciones = json_decode($fila->opciones, true) ?? [];

            $opciones = array_map(function ($opcion) {
                $flags = self::FLAGS[$opcion['value']] ?? ['es_efectivo' => false, 'requiere_referencia' => false];
                return array_merge($opcion, $flags);
            }, $opciones);

            DB::table('negocio_config')
                ->where('id_ncf', $fila->id_ncf)
                ->update(['opciones' => json_encode($opciones)]);
        }
    }

    public function down(): void
    {
        $filas = DB::table('negocio_config')->where('clave', 'metodos_pago')->get();

        foreach ($filas as $fila) {
            $opciones = json_decode($fila->opciones, true) ?? [];

            $opciones = array_map(function ($opcion) {
                unset($opcion['es_efectivo'], $opcion['requiere_referencia']);
                return $opcion;
            }, $opciones);

            DB::table('negocio_config')
                ->where('id_ncf', $fila->id_ncf)
                ->update(['opciones' => json_encode($opciones)]);
        }
    }
};
