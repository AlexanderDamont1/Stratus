<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ── 1. Pagos de venta ─────────────────────────────────────────────────
        Schema::create('venta_pagos', function (Blueprint $table) {
            $table->char('id_pago', 20)->primary();

            $table->char('id_venta', 20);
            $table->foreign('id_venta')
                  ->references('id_venta')
                  ->on('ventas')
                  ->onDelete('cascade');

            // Reemplaza id_metodo (FK) — ahora es string de la config
            $table->string('metodo', 40);               // value: 'efectivo', 'tarjeta', etc.
            $table->boolean('es_efectivo')->default(false);
            $table->boolean('requiere_referencia')->default(false);
            $table->string('label', 80)->nullable();    // snapshot: 'Efectivo', 'Tarjeta (Terminal)', etc.

            // Multi-tenant
            $table->char('id_negocio', 36);
            $table->foreign('id_negocio')
                  ->references('id_negocio')
                  ->on('negocios')
                  ->onDelete('cascade');

            $table->decimal('monto', 10, 2);
            $table->string('referencia', 120)->nullable();

            $table->timestamps();

            $table->index(['id_venta']);
            $table->index(['id_negocio', 'created_at']);
        });

        // ── 2. Vendedor de la venta ───────────────────────────────────────────
        Schema::create('venta_vendedor', function (Blueprint $table) {
            $table->char('id_venta', 20)->primary();
            $table->foreign('id_venta')
                  ->references('id_venta')
                  ->on('ventas')
                  ->onDelete('cascade');

            $table->char('id_personal', 20);
            $table->foreign('id_personal')
                  ->references('id_personal')
                  ->on('personal')
                  ->onDelete('restrict');

            $table->string('nombre_snapshot', 120);

            $table->timestamps();

            $table->index(['id_personal']);
        });

        // ── 3. Columnas de cambio en ventas ───────────────────────────────────
        Schema::table('ventas', function (Blueprint $table) {
            $table->decimal('monto_recibido', 10, 2)->nullable()->after('descuento_total');
            $table->decimal('cambio', 10, 2)->nullable()->after('monto_recibido');
        });
    }

    public function down(): void
    {
        Schema::table('ventas', function (Blueprint $table) {
            $table->dropColumn(['monto_recibido', 'cambio']);
        });

        Schema::dropIfExists('venta_vendedor');
        Schema::dropIfExists('venta_pagos');
    }
};