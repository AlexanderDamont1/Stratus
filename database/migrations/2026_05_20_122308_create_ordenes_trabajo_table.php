<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ── Órdenes de trabajo ────────────────────────────────────────────────
        Schema::create('ordenes_trabajo', function (Blueprint $table) {

            $table->string('id_operacion', 20)->primary(); // OT-00001

            // Multi-tenancy
            $table->char('id_negocio', 36);
            $table->foreign('id_negocio')
                  ->references('id_negocio')
                  ->on('negocios')
                  ->cascadeOnDelete();

            // Usuario sucursal (id_rol = 2) que ingresó la OT
            $table->char('id_usuario_sucursal', 36);
            $table->foreign('id_usuario_sucursal')
                  ->references('id_usuario')
                  ->on('usuarios');

            // Técnico asignado (nullable al inicio)
            $table->char('id_tecnico', 36)->nullable();
            $table->foreign('id_tecnico')
                  ->references('id_usuario')
                  ->on('usuarios')
                  ->nullOnDelete();

            // Vehículo — FK a bicicletas.num_serie
            // nullable: talleres externos pueden traer bicis sin registro en el sistema
            $table->string('num_serie', 17)->nullable();
            $table->foreign('num_serie')
                  ->references('num_serie')
                  ->on('bicicletas')
                  ->nullOnDelete();

            // Descripción manual cuando la bici no está en sistema
            $table->string('bici_descripcion', 150)->nullable();

            // Cliente registrado (nullable: taller externo sin cliente en CRM)
            $table->char('id_cliente', 36)->nullable();
            $table->foreign('id_cliente')
                  ->references('id_cliente')
                  ->on('clientes')
                  ->nullOnDelete();

            // Datos de cliente cuando no está registrado (para el correo de notificación)
            $table->string('cliente_nombre', 120)->nullable();
            $table->string('cliente_email', 150)->nullable();

            // El cliente presentó identificación al entregar la unidad
            $table->boolean('id_verificada')->default(false);

            // Tipo de OT
            $table->enum('tipo', [
                'reparacion',
                'garantia',
                'mantenimiento',
            ])->default('reparacion');

            // Referencia a garantía aprobada (solo cuando tipo = garantia)
            // Reemplaza la línea nullable string sin FK:
            $table->string('id_garantia_aprobada', 30)->nullable();
            $table->foreign('id_garantia_aprobada')
                ->references('id_reclamo')
                ->on('garantia_reclamo')
                ->nullOnDelete();

            // Descripción del problema
            $table->text('problema_reportado');
            $table->text('diagnostico')->nullable();

            // Estado del flujo
            $table->enum('estado', [
                'recibida',
                'diagnostico',
                'esperando_aprobacion',
                'en_proceso',
                'mandado_fabrica',
                'lista',
                'entregada',
                'cancelada',
            ])->default('recibida');

            // Costos
            $table->decimal('costo_mano_obra', 10, 2)->default(0);
            $table->decimal('costo_piezas', 10, 2)->default(0);
            $table->decimal('costo_total', 10, 2)->default(0);
            // Costo real aunque el cliente pague $0 por garantía
            $table->decimal('costo_real_garantia', 10, 2)->default(0);

            // Notificación de "lista para recoger"
            $table->boolean('notificacion_enviada')->default(false);
            $table->timestamp('notificacion_enviada_at')->nullable();

            // Timestamps de estados clave para métricas
            $table->timestamp('recibida_at')->nullable();
            $table->timestamp('en_proceso_at')->nullable();
            $table->timestamp('lista_at')->nullable();
            $table->timestamp('entregada_at')->nullable();

            $table->text('notas_internas')->nullable();

            $table->timestamps();

            // Índices
            $table->index(['id_negocio', 'estado']);
            $table->index('num_serie');
            $table->index('id_cliente');
        });

        // ── Piezas usadas en cada OT ──────────────────────────────────────────
        Schema::create('ot_piezas', function (Blueprint $table) {
            $table->id();

            $table->string('id_operacion', 20);
            $table->foreign('id_operacion')
                  ->references('id_operacion')
                  ->on('ordenes_trabajo')
                  ->cascadeOnDelete();

            // Nullable: pieza externa sin catálogo
            $table->unsignedBigInteger('id_pieza')->nullable();
            $table->foreign('id_pieza')
                  ->references('id_pieza')
                  ->on('piezas_catalogo')
                  ->nullOnDelete();

            $table->string('descripcion', 150)->nullable();
            $table->unsignedSmallInteger('cantidad')->default(1);

            // Precio congelado al momento de crear la OT
            $table->decimal('precio_unitario', 10, 2)->default(0);
            $table->decimal('subtotal', 10, 2)->default(0);

            $table->boolean('es_garantia')->default(false);
            $table->boolean('stock_descontado')->default(false);

            $table->timestamps();
        });

        // ── Historial de cambios de estado ────────────────────────────────────
        Schema::create('ot_historial', function (Blueprint $table) {
            $table->id();

            $table->string('id_operacion', 20);
            $table->foreign('id_operacion')
                  ->references('id_operacion')
                  ->on('ordenes_trabajo')
                  ->cascadeOnDelete();

            $table->string('estado_anterior', 40)->nullable();
            $table->string('estado_nuevo', 40);

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
        Schema::dropIfExists('ot_historial');
        Schema::dropIfExists('ot_piezas');
        Schema::dropIfExists('ordenes_trabajo');
    }
};