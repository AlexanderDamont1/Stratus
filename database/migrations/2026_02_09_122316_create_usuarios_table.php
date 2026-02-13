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
    
    $table->char('id_negocio', 36)->nullable(); 
    // Root puede no pertenecer a negocio

    $table->string('nombre_usuario');
    $table->string('correo');
    $table->string('username');
    $table->string('password');

    // 0 = Root | 1 = Admin | 2 = Vendedor
    $table->unsignedTinyInteger('id_rol');

    $table->timestamps();

    $table->foreign('id_negocio')
          ->references('id_negocio')
          ->on('negocios')
          ->nullOnDelete();

    // Evita duplicados dentro del mismo negocio
    $table->unique(['id_negocio', 'correo']);
    $table->unique(['id_negocio', 'username']);
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
