<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ── 1. Pagos de venta (soporte mixto) ────────────────────────────────
        Schema::create('venta_pagos', function (Blueprint $table) {
            $table->char('id_pago', 20)->primary();

            $table->char('id_venta', 20);
            $table->foreign('id_venta')
                  ->references('id_venta')
                  ->on('ventas')
                  ->onDelete('cascade');

            $table->char('id_metodo', 20);
            $table->foreign('id_metodo')
                  ->references('id_metodo')
                  ->on('metodos_pago')
                  ->onDelete('restrict');

            // Multi-tenant — para consultas de caja por negocio sin JOIN a ventas
            $table->char('id_negocio', 36);
            $table->foreign('id_negocio')
                  ->references('id_negocio')
                  ->on('negocios')
                  ->onDelete('cascade');

            $table->decimal('monto', 10, 2);
            $table->string('referencia', 120)->nullable(); // libre, solo si requiere_referencia

            $table->timestamps();

            $table->index(['id_venta']);
            $table->index(['id_negocio', 'created_at']); // útil para cortes de caja futuros
        });

        // ── 2. Vendedor de la venta ───────────────────────────────────────────
        Schema::create('venta_vendedor', function (Blueprint $table) {
            // PK = id_venta (relación 1:1, una venta tiene un vendedor)
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

            // Snapshot del nombre por si el registro de personal cambia
            $table->string('nombre_snapshot', 120);

            $table->timestamps();

            $table->index(['id_personal']); // para "ventas por vendedor"
        });

        // ── 3. Columnas de cambio en ventas (solo efectivo) ──────────────────
        Schema::table('ventas', function (Blueprint $table) {
            // monto_recibido: solo cuando uno de los pagos es efectivo
            $table->decimal('monto_recibido', 10, 2)->nullable()->after('descuento_total');
            // cambio: total_recibido − total_venta, puede ser 0
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