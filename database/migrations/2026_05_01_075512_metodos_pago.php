v<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('metodos_pago', function (Blueprint $table) {
            $table->char('id_metodo', 20)->primary();

            $table->char('id_negocio', 36);
            $table->foreign('id_negocio')
                  ->references('id_negocio')
                  ->on('negocios')
                  ->onDelete('cascade');

            $table->string('nombre', 60);   // "Efectivo", "Tarjeta", "Transferencia", etc.

            // Si es efectivo → habilita monto_recibido y cambio en la venta
            $table->boolean('es_efectivo')->default(false);

            // Si requiere referencia → el cajero debe capturar un texto libre
            $table->boolean('requiere_referencia')->default(false);

            $table->boolean('activo')->default(true);
            $table->unsignedSmallInteger('orden')->default(0); // para ordenar en la UI

            $table->timestamps();

            $table->index(['id_negocio', 'activo', 'orden']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('metodos_pago');
    }
};