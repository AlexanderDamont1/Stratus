<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ── Reparaciones (ex ordenes_trabajo) ───────────────────────────────
        Schema::create('reparaciones', function (Blueprint $table) {

            $table->string('id_reparacion', 20)->primary(); // OT-00001

            $table->char('id_negocio', 36);
            $table->foreign('id_negocio')
                  ->references('id_negocio')
                  ->on('negocios')
                  ->cascadeOnDelete();

            $table->char('id_usuario_sucursal', 36);
            $table->foreign('id_usuario_sucursal')
                  ->references('id_usuario')
                  ->on('usuarios');

            $table->char('id_tecnico', 36)->nullable();
            $table->foreign('id_tecnico')
                  ->references('id_usuario')
                  ->on('usuarios')
                  ->nullOnDelete();

            // Sin FK — num_serie se guarda aunque no esté en bicicletas
            $table->string('num_serie', 17)->nullable();

            $table->string('unidad_descripcion', 150)->nullable(); // ex bici_descripcion

            $table->char('id_cliente', 36)->nullable();
            $table->foreign('id_cliente')
                  ->references('id_cliente')
                  ->on('clientes')
                  ->nullOnDelete();

            $table->string('cliente_nombre', 120)->nullable();
            $table->string('cliente_email', 150)->nullable();
            $table->string('cliente_telefono', 20)->nullable();

            $table->boolean('id_verificada')->default(false);

            $table->enum('tipo', [
                'mantenimiento',
                'garantia',
                'reparacion',
            ])->default('reparacion');

            $table->text('problema_reportado');
            $table->text('diagnostico')->nullable();

            $table->enum('estado', [
                'recibida',
                'diagnostico',
                'cotizacion_enviada',   // reemplaza esperando_aprobacion
                'en_proceso',
                'lista',
                'entregada',
                'cancelada',
                // eliminados: mandado_fabrica, esperando_aprobacion
            ])->default('recibida');

            $table->decimal('costo_mano_obra',     10, 2)->default(0);
            $table->decimal('costo_piezas',        10, 2)->default(0);
            $table->decimal('costo_total',         10, 2)->default(0);

            // Solo aplica a tipo=reparacion. No viene de catálogo —
            // es un acuerdo interno que ingresa el trabajador al crear.
            $table->decimal('costo_reparacion', 10, 2)->default(0)
                  ->comment('Solo aplica a tipo=reparacion. No viene de catálogo.');

            // Array de id_pieza confirmados al cerrar la OT
            $table->json('piezas_usadas')->nullable()
                  ->comment('Array de id_pieza confirmados al cerrar la OT.');

            // eliminados: costo_real_garantia, id_garantia_aprobada

            $table->boolean('notificacion_enviada')->default(false);
            $table->timestamp('notificacion_enviada_at')->nullable();

            $table->timestamp('recibida_at')->nullable();
            $table->timestamp('en_proceso_at')->nullable();
            $table->timestamp('lista_at')->nullable();
            $table->timestamp('entregada_at')->nullable();

            $table->text('notas_internas')->nullable();

            $table->timestamps();

            $table->index(['id_negocio', 'estado']);
            $table->index('num_serie');
            $table->index('id_cliente');
        });

        // ── Piezas usadas en cada reparación ───────────────────────────────
        // Tabla desacoplada intencionalmente: no tiene FK a reparaciones.
        // La integridad la maneja el servicio al sincronizar piezas.
        Schema::create('reparacion_piezas', function (Blueprint $table) {
              $table->string('id_reparacion_pieza', 20)->primary();

            // Sin FK a reparaciones — desacoplado intencionalmente
            $table->string('id_reparacion', 20);
            $table->index('id_reparacion', 'idx_rep_piezas_rep');

            $table->string('id_pieza', 20)->nullable();
            $table->foreign('id_pieza')
                  ->references('id_pieza')
                  ->on('piezas_catalogo')
                  ->nullOnDelete();

            $table->string('descripcion', 150)->nullable();
            $table->unsignedSmallInteger('cantidad')->default(1);
            $table->decimal('precio_unitario', 10, 2)->default(0);
            $table->decimal('subtotal',        10, 2)->default(0);
            $table->boolean('es_garantia')->default(false);
            $table->boolean('stock_descontado')->default(false);

            $table->timestamps();
        });

        // ── Historial de cambios de estado ────────────────────────────────────
        Schema::create('reparacion_historial', function (Blueprint $table) {
              $table->string('id_reparacion_historial', 20)->primary();

            $table->string('id_reparacion', 20);
            $table->foreign('id_reparacion')
                  ->references('id_reparacion')
                  ->on('reparaciones')
                  ->cascadeOnDelete();

            $table->string('estado_anterior', 40)->nullable();
            $table->string('estado_nuevo',    40);

            $table->char('id_usuario', 36);
            $table->foreign('id_usuario')
                  ->references('id_usuario')
                  ->on('usuarios');

            $table->text('nota')->nullable();

            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reparacion_historial');
        Schema::dropIfExists('reparacion_piezas');
        Schema::dropIfExists('reparaciones');
    }
};