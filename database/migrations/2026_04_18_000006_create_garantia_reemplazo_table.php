<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('garantia_reemplazo', function (Blueprint $table) {
            $table->string('id_reemplazo', 30)->primary();
            $table->string('id_negocio', 30);
            $table->string('id_reclamo', 30);
            $table->string('id_garantia_anterior', 30);
            $table->string('id_garantia_nueva', 30);

            $table->string('num_serie_nuevo_componente', 60)->nullable();
            $table->enum('politica_aplicada', ['heredar', 'nueva', 'mini']);
            $table->date('fecha_reemplazo');
            $table->text('notas')->nullable();

            $table->timestamp('created_at')->useCurrent();

            $table->foreign('id_negocio')
                  ->references('id_negocio')
                  ->on('negocios');

            $table->foreign('id_reclamo')
                  ->references('id_reclamo')
                  ->on('garantia_reclamo');

            $table->foreign('id_garantia_anterior')
                  ->references('id_bicicleta_garantia')
                  ->on('bicicleta_garantia');

            $table->foreign('id_garantia_nueva')
                  ->references('id_bicicleta_garantia')
                  ->on('bicicleta_garantia');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('garantia_reemplazo');
    }
};