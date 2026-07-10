<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('piezas_movimientos', function (Blueprint $table) {
            // Igual que id_reparacion: FK lógica sin constraint, solo se
            // llena en salidas por venta directa de pieza (sin OT de por medio).
            $table->string('id_venta', 20)->nullable()->after('id_reparacion')
                  ->comment('FK lógica — sin constraint intencional, igual que id_reparacion.');
            $table->index('id_venta', 'idx_mov_venta');
        });
    }

    public function down(): void
    {
        Schema::table('piezas_movimientos', function (Blueprint $table) {
            $table->dropIndex('idx_mov_venta');
            $table->dropColumn('id_venta');
        });
    }
};
