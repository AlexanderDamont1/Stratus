<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('usuarios', function (Blueprint $table) {

            $table->char('id_usuario', 36)->primary();

            // Root no pertenece a ningún negocio
            $table->char('id_negocio', 36)->nullable();

            $table->string('nombre_usuario');
            $table->string('correo')->unique();
            $table->string('username')->unique();
            $table->string('password');

            // Token para control de sesión única
            $table->string('session_token')->nullable();

            // 0 = Root | 1 = Admin | 2 = Vendedor
            $table->unsignedTinyInteger('id_rol')->default(1);

            $table->timestamps();

            $table->foreign('id_negocio')
                  ->references('id_negocio')
                  ->on('negocios')
                  ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('usuarios');
    }
};