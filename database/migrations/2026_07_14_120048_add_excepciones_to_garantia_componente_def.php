<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('garantia_componente_def', function (Blueprint $table) {
            // Excepciones de garantía propias del componente (ej. "Cortos circuitos por
            // modificaciones no originales"). Reutilizables entre marcas vía copia manual
            // desde la UI de configuración, no vía referencia compartida.
            $table->json('excepciones')->nullable()->after('cobertura');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('garantia_componente_def', function (Blueprint $table) {
            $table->dropColumn('excepciones');
        });
    }
};
