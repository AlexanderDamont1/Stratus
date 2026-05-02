<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('personal', function (Blueprint $table) {
            $table->char('id_personal', 20)->primary();

            // Sucursal a la que pertenece (usuario tipo 2)
            $table->char('id_usuario', 36);
            $table->foreign('id_usuario')
                  ->references('id_usuario')
                  ->on('usuarios')
                  ->onDelete('cascade');

            // Multi-tenant directo — evita JOINs innecesarios
            $table->char('id_negocio', 36);
            $table->foreign('id_negocio')
                  ->references('id_negocio')
                  ->on('negocios')
                  ->onDelete('cascade');

            $table->string('nombre', 120);
            $table->boolean('activo')->default(true);
            $table->timestamps();

            // Índices útiles para futura gestión de RH
            $table->index(['id_negocio', 'activo']);
            $table->index(['id_usuario', 'activo']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('personal');
    }
};