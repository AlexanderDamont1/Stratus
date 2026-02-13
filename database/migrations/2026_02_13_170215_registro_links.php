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

    $table->char('id_negocio', 36);
    $table->char('creado_por', 36); // debe ser admin

    $table->string('token')->unique();

    $table->boolean('usado')->default(false);
    $table->timestamp('expires_at')->nullable();

    $table->timestamps();

    $table->foreign('id_negocio')
          ->references('id_negocio')
          ->on('negocios')
          ->cascadeOnDelete();

    $table->foreign('creado_por')
          ->references('id_usuario')
          ->on('usuarios')
          ->cascadeOnDelete();
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
