<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE bicicleta_movimientos MODIFY tipo_movimiento ENUM(
            'entrada_stock',
            'transferencia_sucursal',
            'venta',
            'mantenimiento',
            'ajuste',
            'ingreso_ot',
            'entrega_ot'
        ) NOT NULL");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE bicicleta_movimientos MODIFY tipo_movimiento ENUM(
            'entrada_stock',
            'transferencia_sucursal',
            'venta',
            'mantenimiento',
            'ajuste'
        ) NOT NULL");
    }
};
