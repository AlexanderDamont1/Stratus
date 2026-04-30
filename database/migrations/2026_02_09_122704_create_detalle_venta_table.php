<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('detalle_venta');

        Schema::create('detalle_venta', function (Blueprint $table) {
            $table->char('id_detalleVenta', 20)->primary();
            $table->char('id_venta', 20);
            $table->char('id_producto', 20);
            $table->char('num_serie', 17)->nullable();
            $table->decimal('precio_unitario', 10, 2);
            $table->unsignedInteger('cantidad')->default(1);
            $table->timestamps();

            $table->foreign('id_venta')->references('id_venta')->on('ventas');
            $table->foreign('id_producto')->references('id_producto')->on('productos');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('detalle_venta');
    }
};