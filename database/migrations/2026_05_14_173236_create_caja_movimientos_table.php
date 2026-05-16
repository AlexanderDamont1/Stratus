<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('caja_movimientos', function (Blueprint $table) {
            $table->char('id_movimiento', 15)->primary();
            $table->char('id_sesion', 15);
            $table->char('id_negocio', 36);
            $table->char('id_usuario', 36);
            $table->char('id_venta', 36)->nullable();

            // Método de pago como string directo — sin FK a tabla externa
            $table->string('metodo', 40)->nullable();
            $table->string('metodo_label', 80)->nullable();
            $table->boolean('es_efectivo')->default(false);

            $table->enum('tipo', [
                'apertura',
                'venta',
                'ingreso_manual',
                'retiro',
                'ajuste',
            ]);

            $table->decimal('monto', 12, 2);
            $table->boolean('es_entrada');
            $table->string('concepto', 200)->nullable();
            $table->string('referencia', 120)->nullable();
            $table->tinyInteger('origen_rol');
            $table->timestamps();

            $table->index(['id_negocio', 'tipo'], 'caja_mov_negocio_tipo_idx');
            $table->index('created_at', 'caja_mov_created_at_idx');
            $table->index(['id_sesion', 'tipo'], 'caja_mov_sesion_tipo_idx');

            $table->foreign('id_sesion')
                ->references('id_sesion')->on('caja_sesiones')->cascadeOnDelete();
            $table->foreign('id_venta')
                ->references('id_venta')->on('ventas')->nullOnDelete();
            $table->foreign('id_usuario')
                ->references('id_usuario')->on('usuarios');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('caja_movimientos');
    }
};