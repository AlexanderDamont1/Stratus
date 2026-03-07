<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('enlaces', function (Blueprint $table) {

            $table->char('id_enlace', 36)->primary();

            // Rol 1: solo puede tener UN enlace activo (unique)
            $table->char('id_usuario1', 36)->unique();

            // Rol 5: puede estar en muchos enlaces (sin unique)
            $table->char('id_usuario2', 36)->nullable();

            // Token único para compartir con rol 5
            $table->string('token_enlace', 64)->unique();

            // pendiente = esperando que rol 5 use el token
            // activo    = enlace confirmado
            // cancelado = revocado por rol 1
            $table->enum('estado', ['pendiente', 'activo', 'cancelado'])->default('pendiente');

            $table->timestamps();

            $table->foreign('id_usuario1')
                  ->references('id_usuario')
                  ->on('usuarios')
                  ->cascadeOnDelete();

            $table->foreign('id_usuario2')
                  ->references('id_usuario')
                  ->on('usuarios')
                  ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('enlaces');
    }
};