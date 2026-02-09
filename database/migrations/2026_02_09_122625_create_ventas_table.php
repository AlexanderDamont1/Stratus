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
        Schema::create('ventas', function (Blueprint $table) {
    $table->char('id_venta', 36)->primary();
    $table->char('id_negocio', 36);
    $table->char('id_cliente', 36);
    $table->timestamps();

    $table->foreign('id_negocio')->references('id_negocio')->on('negocios');
    $table->foreign('id_cliente')->references('id_cliente')->on('clientes');
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ventas');
    }
};
