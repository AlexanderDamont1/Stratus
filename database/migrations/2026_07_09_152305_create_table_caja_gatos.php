<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('caja_gastos', function (Blueprint $table) {
            $table->char('id_gasto', 15)->primary();

            // Sesión opcional: permite registrar el gasto aunque la caja
            // ya esté cerrada al momento de capturarlo (ej. factura que llega tarde).
            $table->char('id_sesion', 15)->nullable();

            $table->char('id_negocio', 36);
            $table->char('id_usuario', 36); // sucursal a la que pertenece el gasto

            $table->string('motivo', 100);       // categoría libre: "Gasolina", "Proveedor X", etc.
            $table->decimal('monto', 12, 2);
            $table->decimal('limite', 12, 2)->nullable(); // tope vigente para ese motivo al momento de registrar

            $table->string('referencia', 120)->nullable(); // folio/factura opcional
            $table->text('notas')->nullable();

            $table->date('fecha_gasto'); // día contable del gasto (puede diferir de created_at)

            $table->char('id_usuario_registro', 36); // quién lo capturó
            $table->timestamps();

            $table->index(['id_negocio', 'id_usuario', 'fecha_gasto'], 'caja_gastos_negocio_sucursal_fecha_idx');
            $table->index(['id_negocio', 'motivo'], 'caja_gastos_negocio_motivo_idx');

            $table->foreign('id_sesion')
                ->references('id_sesion')->on('caja_sesiones')->nullOnDelete();
            $table->foreign('id_negocio')
                ->references('id_negocio')->on('negocios')->cascadeOnDelete();
            $table->foreign('id_usuario')
                ->references('id_usuario')->on('usuarios')->cascadeOnDelete();
            $table->foreign('id_usuario_registro')
                ->references('id_usuario')->on('usuarios');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('caja_gastos');
    }
};