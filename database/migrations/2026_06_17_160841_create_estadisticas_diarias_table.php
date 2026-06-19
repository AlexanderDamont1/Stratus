<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('estadisticas_diarias', function (Blueprint $table) {
            $table->char('id_estadistica', 20)->primary();
            $table->char('id_negocio', 36);

            $table->date('fecha');

            // ── Ventas globales ───────────────────────────────────────────
            $table->unsignedSmallInteger('ventas_count')->default(0);
            $table->decimal('ingresos_total', 12, 2)->default(0);
            $table->decimal('ticket_promedio', 10, 2)->default(0);
            $table->decimal('descuentos_total', 10, 2)->default(0);

            // ── Clientes ──────────────────────────────────────────────────
            $table->unsignedSmallInteger('clientes_nuevos')->default(0);
            $table->unsignedSmallInteger('clientes_rec')->default(0);

            // ── Producto top (bicicleta) ──────────────────────────────────
            $table->char('modelo_top_id', 15)->nullable();
            $table->string('modelo_top_nombre', 100)->nullable();
            $table->unsignedSmallInteger('modelo_top_unidades')->default(0);

            // ── Configuración más vendida: "ModeloX · Rojo · 48V" ────────
            $table->string('config_top', 200)->nullable();
            $table->unsignedSmallInteger('config_top_unidades')->default(0);

            // ── Accesorio top ─────────────────────────────────────────────
            $table->string('accesorio_top_nombre', 100)->nullable();
            $table->unsignedSmallInteger('accesorio_top_uds')->default(0);

            // ── Combo más frecuente: "ModeloX + Casco" ───────────────────
            $table->string('combo_top', 200)->nullable();
            $table->unsignedSmallInteger('combo_top_uds')->default(0);

            // ── Cupones ───────────────────────────────────────────────────
            $table->unsignedSmallInteger('cupones_usados')->default(0);
            $table->decimal('cupones_descuento', 10, 2)->default(0);
            $table->string('cupon_top_codigo', 30)->nullable();
            $table->unsignedSmallInteger('cupon_top_usos')->default(0);

            // ── JSONs ─────────────────────────────────────────────────────
            $table->json('sucursal_data')->nullable();  // ver estructura abajo
            $table->json('horas_pico')->nullable();     // [{hora:11, cnt:3}, ...]
            $table->json('metodos_pago')->nullable();   // [{metodo:'efectivo', monto:X, usos:Y}, ...]

            $table->timestamps();

            // ── Índices ───────────────────────────────────────────────────
            $table->unique(['id_negocio', 'fecha'], 'uq_estdia_negocio_fecha');
            $table->index(['id_negocio', 'fecha'],  'idx_estdia_negocio_fecha');

            $table->foreign('id_negocio')
                  ->references('id_negocio')
                  ->on('negocios')
                  ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('estadisticas_diarias');
    }
};