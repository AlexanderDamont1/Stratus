<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('garantia_componente_def', function (Blueprint $table) {
            $table->string('id_garantia_def', 30)->primary();
            $table->string('id_marca_garantia', 30);
            $table->string('id_negocio', 30);

            // Slug estable para joins/lógica: 'motor', 'bateria_litio', etc.
            $table->string('clave_componente', 60);
            // Label para UI: 'Motor', 'Batería de Litio'
            $table->string('nombre_componente', 120);
            // ["mando velocidad", "freno", "convertidor"]
            $table->json('incluye');

            $table->tinyInteger('duracion_meses')->unsigned();
            $table->string('cobertura', 255)->nullable();

            // TRUE = motor, batería (tienen num_serie propio)
            $table->boolean('serializable')->default(false);
            // TRUE = consumible, sin garantía
            $table->boolean('excluido')->default(false);
            $table->boolean('activo')->default(true);

            $table->timestamps();

            $table->unique(
                ['id_marca_garantia', 'clave_componente'],
                'uq_componente_marca'
            );

            $table->foreign('id_marca_garantia')
                  ->references('id_marca_garantia')
                  ->on('marca_garantia_config');

            $table->foreign('id_negocio')
                  ->references('id_negocio')
                  ->on('negocios');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('garantia_componente_def');
    }
};