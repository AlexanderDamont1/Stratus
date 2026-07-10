<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Ya no se crea un Mantenimiento por cada reclamo — ahora se crea una
        // Reparación (OT). id_mantenimiento se conserva por compatibilidad
        // con datos previos, pero deja de ser obligatorio.
        DB::statement('ALTER TABLE garantia_reclamo MODIFY id_mantenimiento VARCHAR(30) NULL');

        Schema::table('garantia_reclamo', function (Blueprint $table) {
            $table->string('id_reparacion', 20)->nullable()->after('id_mantenimiento');
            $table->unsignedInteger('kilometraje')->nullable()->after('motivo_reclamo');
            $table->string('ia_sugerencia', 20)->nullable()->after('resultado');
            $table->text('ia_razonamiento')->nullable()->after('ia_sugerencia');

            $table->foreign('id_reparacion')
                  ->references('id_reparacion')
                  ->on('reparaciones')
                  ->nullOnDelete();

            $table->index('id_reparacion', 'idx_reclamo_reparacion');
        });
    }

    public function down(): void
    {
        Schema::table('garantia_reclamo', function (Blueprint $table) {
            $table->dropForeign(['id_reparacion']);
            $table->dropIndex('idx_reclamo_reparacion');
            $table->dropColumn(['id_reparacion', 'kilometraje', 'ia_sugerencia', 'ia_razonamiento']);
        });

        DB::statement('ALTER TABLE garantia_reclamo MODIFY id_mantenimiento VARCHAR(30) NOT NULL');
    }
};
