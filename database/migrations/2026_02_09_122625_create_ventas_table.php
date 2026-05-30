<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ventas', function (Blueprint $table) {
            $table->char('id_venta', 20)->primary();
            $table->char('id_negocio', 36);
            $table->char('id_cliente', 36);
            $table->char('id_usuario', 36);
            $table->char('id_personal', 20)->nullable();
            $table->char('id_cupon', 20)->nullable();
            $table->decimal('descuento_total', 10, 2)->default(0);
            $table->decimal('total', 10, 2);
            $table->timestamps();

            $table->foreign('id_negocio')->references('id_negocio')->on('negocios')->cascadeOnDelete();
            $table->foreign('id_cliente')->references('id_cliente')->on('clientes')->cascadeOnDelete();
            $table->foreign('id_usuario')->references('id_usuario')->on('usuarios')->restrictOnDelete();
            $table->foreign('id_cupon')->references('id_cupon')->on('cupones')->nullOnDelete();
            $table->foreign('id_personal')->references('id_personal')->on('personal')->nullOnDelete();

            $table->index(['id_negocio', 'created_at']);
            $table->index(['id_cliente']);
            $table->index(['id_usuario']);
            $table->index(['id_personal']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ventas');
    }
};