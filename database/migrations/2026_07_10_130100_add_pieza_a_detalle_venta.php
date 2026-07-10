<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // id_producto deja de ser obligatorio: una línea de venta ahora puede
        // referenciar un producto del catálogo normal, una pieza de taller
        // (id_pieza) o un concepto libre (mano de obra, costo base de OT).
        // Se usa SQL crudo porque el proyecto no tiene doctrine/dbal instalado
        // (requerido por Schema::table(...)->change()).
        DB::statement('ALTER TABLE detalle_venta MODIFY id_producto CHAR(20) NULL');

        Schema::table('detalle_venta', function (Blueprint $table) {
            $table->string('id_pieza', 20)->nullable()->after('id_producto');
            $table->foreign('id_pieza')
                  ->references('id_pieza')
                  ->on('piezas_catalogo')
                  ->nullOnDelete();

            $table->string('concepto', 150)->nullable()->after('id_pieza')
                  ->comment('Línea sin producto/pieza de catálogo — ej. "Mano de obra".');
        });
    }

    public function down(): void
    {
        Schema::table('detalle_venta', function (Blueprint $table) {
            $table->dropForeign(['id_pieza']);
            $table->dropColumn(['id_pieza', 'concepto']);
        });

        DB::statement('ALTER TABLE detalle_venta MODIFY id_producto CHAR(20) NOT NULL');
    }
};
