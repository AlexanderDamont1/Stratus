<?php

// database/migrations/2026_05_14_000002_create_caja_sesiones_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('caja_sesiones', function (Blueprint $table) {

            $table->char('id_sesion', 15)->primary();

            $table->char('id_caja', 15);
            $table->char('id_negocio', 36);

            $table->char('id_usuario_apertura', 36);
            $table->char('id_usuario_cierre', 36)->nullable();

            $table->decimal('fondo_inicial', 12, 2)
                ->default(0);

            $table->decimal('monto_cierre_declarado', 12, 2)
                ->nullable();

            $table->decimal('monto_cierre_sistema', 12, 2)
                ->nullable();

            $table->decimal('diferencia', 12, 2)
                ->nullable();

            $table->enum('estado', [
                'abierta',
                'cerrada',
                'auto_cerrada',
            ])->default('abierta');

            $table->string('motivo_cierre', 60)
                ->nullable();

            $table->text('notas_cierre')
                ->nullable();

            $table->timestamp('abierta_at')
                ->useCurrent();

            $table->timestamp('cerrada_at')
                ->nullable();

            $table->timestamps();

            $table->index(
                ['id_negocio', 'estado'],
                'caja_sesion_id_negocio_estado_index'
            );

            $table->index(
                'abierta_at',
                'caja_sesion_abierta_at_index'
            );

            $table->index(
                ['id_caja', 'estado'],
                'caja_sesion_activa_lookup'
            );

            $table->foreign('id_caja')
                ->references('id_caja')
                ->on('cajas')
                ->cascadeOnDelete();

            $table->foreign('id_usuario_apertura')
                ->references('id_usuario')
                ->on('usuarios');

            $table->foreign('id_usuario_cierre')
                ->references('id_usuario')
                ->on('usuarios');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('caja_sesiones');
    }
};