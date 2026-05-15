<?php

// database/migrations/2026_05_14_000003_create_caja_movimientos_table.php

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

            $table->char('id_venta', 36)
                ->nullable();

            $table->char('id_metodo', 15)
                ->nullable();

            $table->enum('tipo', [
                'apertura',
                'venta',
                'ingreso_manual',
                'retiro',
                'ajuste',
            ]);

            $table->decimal('monto', 12, 2);

            $table->boolean('es_entrada');

            $table->string('concepto', 200)
                ->nullable();

            $table->string('referencia', 120)
                ->nullable();

            $table->tinyInteger('origen_rol');

            $table->timestamps();

            $table->index(
                ['id_negocio', 'tipo'],
                'caja_movimientos_id_negocio_tipo_index'
            );

            $table->index(
                'created_at',
                'caja_movimientos_created_at_index'
            );

            $table->foreign('id_sesion')
                ->references('id_sesion')
                ->on('caja_sesiones')
                ->cascadeOnDelete();

            $table->foreign('id_venta')
                ->references('id_venta')
                ->on('ventas')
                ->nullOnDelete();

            $table->foreign('id_metodo')
                ->references('id_metodo')
                ->on('metodos_pago')
                ->nullOnDelete();

            $table->foreign('id_usuario')
                ->references('id_usuario')
                ->on('usuarios');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('caja_movimientos');
    }
};