<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Creamos la tabla de usuarios con todos sus campos
        Schema::create('usuarios', function (Blueprint $table) {
            $table->char('id_usuario', 36)->primary();
            $table->char('id_negocio', 36)->nullable();
            $table->string('nombre_usuario');
            $table->string('correo')->unique();
            $table->string('password');
            $table->string('session_token')->nullable();
            $table->unsignedTinyInteger('id_rol')->default(1);
            
            // Aquí añadimos el rememberToken que pediste
            $table->rememberToken(); 
            
            $table->timestamps();

            $table->foreign('id_negocio')
                  ->references('id_negocio')
                  ->on('negocios')
                  ->nullOnDelete();
        });

        // 2. Modificamos 'sessions' para que apunte a 'usuarios'
        if (Schema::hasTable('sessions')) {
            Schema::table('sessions', function (Blueprint $table) {
                $table->foreign('user_id')
                      ->references('id_usuario')
                      ->on('usuarios')
                      ->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        // 1. Quitamos la llave foránea de sessions primero
        if (Schema::hasTable('sessions')) {
            Schema::table('sessions', function (Blueprint $table) {
                $table->dropForeign(['user_id']);
            });
        }

        // 2. Borramos la tabla usuarios
        Schema::dropIfExists('usuarios');
    }
};