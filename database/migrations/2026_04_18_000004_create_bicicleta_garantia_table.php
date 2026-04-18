<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bicicleta_garantia', function (Blueprint $table) {
            $table->string('id_bicicleta_garantia', 30)->primary();
            $table->string('id_negocio', 30);
            $table->string('num_serie', 60);
            $table->string('id_garantia_def', 30);

            // Desnormalizado — evita joins en el mapa visual
            $table->string('clave_componente', 60);

            $table->date('fecha_inicio');
            $table->date('fecha_expiracion');

            // Solo motor/batería
            $table->string('num_serie_componente', 60)->nullable();

            // SIN 'por_vencer' — se calcula como accessor en tiempo real
            $table->enum('estado', [
                'vigente',
                'expirada',
                'reemplazada',
                'invalidada',
            ])->default('vigente');

            // Apunta a la nueva garantía que tomó su lugar
            $table->string('id_reemplazada_por', 30)->nullable();

            $table->timestamps();

            $table->index(['num_serie', 'id_negocio'], 'idx_garantia_serie');
            $table->index('estado', 'idx_garantia_estado');

            $table->foreign('id_negocio')
                  ->references('id_negocio')
                  ->on('negocios');

            $table->foreign('id_garantia_def')
                  ->references('id_garantia_def')
                  ->on('garantia_componente_def');

            $table->foreign('id_reemplazada_por')
                  ->references('id_bicicleta_garantia')
                  ->on('bicicleta_garantia');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bicicleta_garantia');
    }
};