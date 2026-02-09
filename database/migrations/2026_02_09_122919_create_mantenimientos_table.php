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
        Schema::create('mantenimientos', function (Blueprint $table) {
    $table->char('id_mantenimiento', 36)->primary();
    $table->char('id_negocio', 36);
    $table->char('num_serie', 36);
    $table->char('id_cliente', 36);
    $table->enum('estado', ['EN REPARACION', 'SALIDO']);
    $table->enum('ubicacion', ['EN TIENDA', 'FABRICA']);
    $table->date('fecha_ingreso');
    $table->date('fecha_estimada')->nullable();
    $table->text('motivo');
    $table->timestamps();

    $table->foreign('id_negocio')->references('id_negocio')->on('negocios');
    $table->foreign('num_serie')->references('num_serie')->on('bicicletas');
    $table->foreign('id_cliente')->references('id_cliente')->on('clientes');
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mantenimientos');
    }
};
