<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('mantenimientos', function (Blueprint $table) {

            $table->char('id_mantenimiento', 36)->primary();

            $table->char('id_negocio', 36);

            // Bicicleta que entra a mantenimiento
            $table->char('num_serie', 36);

            // Estado del mantenimiento
            $table->enum('estado', [
                'EN_REPARACION',
                'SALIDO'
            ])->default('EN_REPARACION');

            // Ubicación actual
            $table->enum('ubicacion', [
                'EN_TIENDA',
                'FABRICA'
            ])->default('EN_TIENDA');

            // Fechas clave
            $table->dateTime('fecha_ingreso');
            $table->dateTime('fecha_estimada_salida')->nullable();
            $table->dateTime('fecha_salida')->nullable();

            // Motivo
            $table->text('motivo');

            $table->timestamps();

            /*
            ==========================
            FOREIGN KEYS
            ==========================
            */

            $table->foreign('id_negocio')
                  ->references('id_negocio')
                  ->on('negocios');

            $table->foreign('num_serie')
                  ->references('num_serie')
                  ->on('bicicletas');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mantenimientos');
    }
};
