<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('marcas', function (Blueprint $table) {
            $table->char('id_marca', 15)->primary();
            $table->char('id_negocio',36)->nullable(); // null = marca pública (Ecobici/rol5)
            $table->char('nombre_marca', 50);
            $table->timestamps();

            $table->foreign('id_negocio')
                  ->references('id_negocio')
                  ->on('negocios')
                  ->onDelete('cascade');

            $table->index('id_negocio');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('marcas');
    }
};