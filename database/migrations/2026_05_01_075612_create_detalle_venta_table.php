<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('detalle_venta', function (Blueprint $table) {
            $table->char('id_detalle', 20)->primary();
            $table->char('id_venta', 20);
            $table->char('id_negocio', 36);
            $table->char('id_producto', 20);
            $table->char('num_serie', 17)->nullable();
            $table->decimal('precio_unitario', 10, 2);
            $table->unsignedInteger('cantidad')->default(1);
            $table->timestamps();

            $table->foreign('id_venta')->references('id_venta')->on('ventas')->cascadeOnDelete();
            $table->foreign('id_negocio')->references('id_negocio')->on('negocios')->cascadeOnDelete();
            $table->foreign('id_producto')->references('id_producto')->on('productos')->restrictOnDelete();

            $table->index(['id_venta']);
            $table->index(['id_negocio']);
            $table->index(['num_serie']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('detalle_venta');
    }
};