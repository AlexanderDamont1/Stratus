<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('garantia_reclamo', function (Blueprint $table) {
            $table->string('id_reclamo', 30)->primary();
            $table->string('id_negocio', 30);
            $table->string('id_mantenimiento', 30);
            $table->string('id_bicicleta_garantia', 30);

            // Desnormalizado para queries directas
            $table->string('num_serie', 60);
            $table->string('clave_componente', 60);

            // Label flexible — no ENUM para no romper con estados futuros
            $table->string('estado', 40)->default('pendiente');

            $table->text('motivo_reclamo')->nullable();
            $table->text('resultado')->nullable();
            $table->boolean('requiere_reemplazo')->default(false);

            $table->timestamps();

            $table->index('id_mantenimiento', 'idx_reclamo_mant');
            $table->index('num_serie', 'idx_reclamo_serie');

            $table->foreign('id_negocio')
                  ->references('id_negocio')
                  ->on('negocios');

            $table->foreign('id_mantenimiento')
                  ->references('id_mantenimiento')
                  ->on('mantenimientos');

            $table->foreign('id_bicicleta_garantia')
                  ->references('id_bicicleta_garantia')
                  ->on('bicicleta_garantia');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('garantia_reclamo');
    }
};