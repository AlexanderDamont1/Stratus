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
        Schema::create('detalle_venta', function (Blueprint $table) {
    $table->char('id_detalleVenta', 36)->primary();
    $table->char('id_venta', 36);
    $table->char('id_producto', 36)->unique();
    $table->char('id_precio', 36);

    $table->foreign('id_venta')->references('id_venta')->on('ventas');
    $table->foreign('id_producto')->references('id_producto')->on('productos');
    $table->foreign('id_precio')->references('id_precio')->on('precios');
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detalle_venta');
    }
};
