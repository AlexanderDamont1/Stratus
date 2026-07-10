<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reparaciones', function (Blueprint $table) {
            // Presencia de id_venta = la OT ya fue cobrada. Se llena al
            // completar el cobro (ReparacionService::cobrar), justo antes de
            // avanzar el estado a 'entregada'.
            $table->string('id_venta', 20)->nullable()->after('costo_total');
            $table->foreign('id_venta')
                  ->references('id_venta')
                  ->on('ventas')
                  ->nullOnDelete();
            $table->index('id_venta');
        });
    }

    public function down(): void
    {
        Schema::table('reparaciones', function (Blueprint $table) {
            $table->dropForeign(['id_venta']);
            $table->dropColumn('id_venta');
        });
    }
};
