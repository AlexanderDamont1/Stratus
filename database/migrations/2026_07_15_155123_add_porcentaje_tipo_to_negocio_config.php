<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // No hay doctrine/dbal instalado, se modifica el ENUM con SQL crudo.
        DB::statement("ALTER TABLE negocio_config MODIFY tipo ENUM('radio', 'checkbox_multi', 'toggle', 'texto', 'numero', 'porcentaje') NOT NULL");

        DB::table('negocio_config')
            ->where('clave', 'comision_venta_porcentaje')
            ->update(['tipo' => 'porcentaje']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('negocio_config')
            ->where('clave', 'comision_venta_porcentaje')
            ->update(['tipo' => 'numero']);

        DB::statement("ALTER TABLE negocio_config MODIFY tipo ENUM('radio', 'checkbox_multi', 'toggle', 'texto', 'numero') NOT NULL");
    }
};
