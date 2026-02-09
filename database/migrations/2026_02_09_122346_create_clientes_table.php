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
        Schema::create('clientes', function (Blueprint $table) {
    $table->char('id_cliente', 36)->primary();
    $table->char('id_negocio', 36);
    $table->string('nombre_cliente');
    $table->string('apellido1');
    $table->string('apellido2')->nullable();
    $table->string('telefono')->nullable();
    $table->string('correo')->nullable();
    $table->timestamps();

    $table->foreign('id_negocio')->references('id_negocio')->on('negocios');
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clientes');
    }
};
