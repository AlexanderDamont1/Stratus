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
        Schema::create('usuarios', function (Blueprint $table) {
    $table->char('id_usuario', 36)->primary();
    $table->char('id_negocio', 36);
    $table->string('nombre_usuario');
    $table->string('correo');
    $table->string('username');
    $table->string('password');
    $table->char('id_rol', 36);
    $table->timestamps();

    $table->foreign('id_negocio')->references('id_negocio')->on('negocios');
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('usuarios');
    }
};
