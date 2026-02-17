<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('registro_links', function (Blueprint $table) {
            $table->char('id_link', 36)->primary();

            $table->string('token')->unique();

            // Cuántos vendedores puede crear este negocio
            $table->unsignedInteger('max_users')->default(1);

            $table->boolean('usado')->default(false);
            $table->timestamp('expires_at')->nullable();

            $table->timestamps();
        });
        // Sin FK a negocios ni usuarios:
        // El Root genera el link ANTES de que exista el negocio.
        // El negocio nace cuando el Admin usa el link.
    }

    public function down(): void
    {
        Schema::dropIfExists('registro_links');
    }
};