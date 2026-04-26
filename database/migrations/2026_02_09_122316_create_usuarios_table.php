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
            $table->char('id_negocio', 36)->nullable();
            $table->string('nombre_usuario');
            $table->string('correo')->unique();
            $table->string('password');
            $table->string('google_id')->nullable()->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('email_verification_token', 64)->nullable();
            $table->string('session_token')->nullable();
            $table->unsignedTinyInteger('id_rol')->default(1);
            $table->rememberToken();
            $table->timestamps();

            $table->foreign('id_negocio')
                  ->references('id_negocio')
                  ->on('negocios')
                  ->nullOnDelete();
        });

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
        if (Schema::hasTable('sessions')) {
            Schema::table('sessions', function (Blueprint $table) {
                $table->dropForeign(['user_id']);
            });
        }

        Schema::dropIfExists('usuarios');
    }
};