<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ventas', function (Blueprint $table) {
            // 'venta' = flujo normal de mostrador (bicicletas/accesorios).
            // 'reparacion' = cobro de una OT (reparación/mantenimiento/garantía).
            // 'pieza_suelta' = venta directa de una pieza del catálogo, sin OT.
            $table->string('origen', 20)->default('venta')->after('id_cupon');

            $table->string('id_reparacion', 20)->nullable()->after('origen');
            $table->foreign('id_reparacion')
                  ->references('id_reparacion')
                  ->on('reparaciones')
                  ->nullOnDelete();

            $table->index(['id_negocio', 'origen']);
        });
    }

    public function down(): void
    {
        Schema::table('ventas', function (Blueprint $table) {
            $table->dropForeign(['id_reparacion']);
            $table->dropIndex(['id_negocio', 'origen']);
            $table->dropColumn(['origen', 'id_reparacion']);
        });
    }
};
