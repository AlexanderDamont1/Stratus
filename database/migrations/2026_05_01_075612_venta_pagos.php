<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('venta_pagos', function (Blueprint $table) {
            $table->char('id_pago', 20)->primary();
            $table->char('id_venta', 20);
            $table->char('id_negocio', 36);
            $table->string('metodo', 40);       // 'efectivo', 'tarjeta', 'transferencia', 'credito_interno'
            $table->decimal('monto', 10, 2);
            $table->string('referencia', 20)->nullable(); // folio transferencia, últimos 4 dígitos tarjeta, etc.
            $table->timestamps();

            $table->foreign('id_venta')->references('id_venta')->on('ventas')->cascadeOnDelete();
            $table->foreign('id_negocio')->references('id_negocio')->on('negocios')->cascadeOnDelete();

            $table->index(['id_venta']);
            $table->index(['id_negocio', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('venta_pagos');
    }
};