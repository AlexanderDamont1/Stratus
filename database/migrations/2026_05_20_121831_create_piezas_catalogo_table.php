<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
      public function up(): void
      {
            Schema::create('piezas_catalogo', function (Blueprint $table) {
                  $table->string('id_pieza', 20)->primary();

                  $table->char('id_negocio', 36);
                  $table->foreign('id_negocio')
                        ->references('id_negocio')
                        ->on('negocios')
                        ->cascadeOnDelete();

                  $table->string('nombre', 120);
                  $table->string('clave', 40);
                  $table->string('categoria', 60)->nullable();
                  $table->string('marca_pieza', 80)->nullable();

                  // IDs de modelos compatibles — array de id_modelo
                  $table->json('modelos_compatibles')->nullable()
                        ->comment('Array de id_modelo compatibles. null = universal');

                  $table->string('voltaje_compatible', 20)->nullable()
                        ->comment('Ej: 36V, 48V. null = no aplica');

                  $table->text('descripcion')->nullable();

                  $table->decimal('precio_costo', 10, 2)->default(0);
                  $table->decimal('precio_venta', 10, 2)->default(0);

                  $table->unsignedSmallInteger('stock_actual')->default(0);
                  $table->unsignedSmallInteger('stock_minimo')->default(0)
                        ->comment('Alerta cuando stock_actual <= stock_minimo');

                  $table->boolean('serializable')->default(false);
                  $table->boolean('activo')->default(true);

                  $table->timestamps();

                  $table->unique(['id_negocio', 'clave'], 'uq_pieza_negocio_clave');
                  $table->index(['id_negocio', 'categoria'],    'idx_pieza_negocio_cat');
                  $table->index(['id_negocio', 'stock_actual'], 'idx_pieza_stock');
                  $table->index(['id_negocio', 'activo'],       'idx_pieza_activo');
            });

            Schema::create('piezas_movimientos', function (Blueprint $table) {
                  $table->string('id_movimiento', 25)->primary(); // PZM-00001

                  $table->string('id_pieza', 20)->nullable();
                  $table->foreign('id_pieza')
                        ->references('id_pieza')
                        ->on('piezas_catalogo')
                        ->nullOnDelete();

                  $table->char('id_negocio', 36);
                  $table->foreign('id_negocio')
                        ->references('id_negocio')
                        ->on('negocios')
                        ->cascadeOnDelete();

                  $table->char('id_usuario', 36);
                  $table->foreign('id_usuario')
                        ->references('id_usuario')
                        ->on('usuarios');

                  // Solo se llena en salidas por OT
                  $table->string('id_reparacion', 20)->nullable()
                        ->comment('FK lógica — sin constraint intencional');

                  $table->enum('tipo', ['entrada', 'salida']);

                  $table->unsignedSmallInteger('cantidad');

                  // Snapshots para auditoría
                  $table->unsignedSmallInteger('stock_antes');
                  $table->unsignedSmallInteger('stock_despues');

                  $table->string('nota', 255)->nullable();

                  $table->timestamp('created_at')->useCurrent();

                  $table->index(['id_pieza', 'created_at'], 'idx_mov_pieza_fecha');
                  $table->index(['id_negocio', 'tipo'],     'idx_mov_negocio_tipo');
                  $table->index('id_reparacion',            'idx_mov_reparacion');
            });
      }

      public function down(): void
      {
            Schema::dropIfExists('piezas_movimientos');
            Schema::dropIfExists('piezas_catalogo');
      }
};
