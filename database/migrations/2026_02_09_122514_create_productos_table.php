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
        Schema::create('productos', function (Blueprint $table) {
    $table->char('id_producto', 36)->primary();
    $table->char('id_negocio', 36);
    $table->string('nombre_producto');
    $table->timestamps();

    $table->foreign('id_negocio')->references('id_negocio')->on('negocios');
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('productos');
    }
};
