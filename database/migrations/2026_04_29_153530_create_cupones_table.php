<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('cupones')) {
            Schema::create('cupones', function (Blueprint $table) {
                $table->char('id_cupon', 20)->primary();
                $table->char('id_negocio', 36);
                $table->char('tipo_cupon', 1)->default('1');         // 1=Normal, 2=Accesorio gratis, 3=Mantenimiento
                $table->string('codigo', 30)->unique();
                $table->string('nombre', 120);
                $table->string('mensaje_vendedor', 255)->nullable(); // aparece al aplicar el cupón
                $table->enum('tipo_descuento', ['porcentaje', 'monto_fijo'])->nullable();
                $table->decimal('valor_descuento', 10, 2)->nullable();
                $table->enum('aplica_a', ['total', 'producto'])->default('total');
                $table->decimal('monto_minimo', 10, 2)->nullable();  // compra mínima para que aplique
                $table->char('id_producto_gratis', 20)->nullable();
                $table->boolean('activo')->default(true);
                $table->unsignedInteger('usos_maximos')->nullable();
                $table->unsignedInteger('usos_actuales')->default(0);
                $table->dateTime('fecha_inicio')->nullable();
                $table->dateTime('fecha_fin')->nullable();
                $table->timestamps();

                $table->foreign('id_negocio')->references('id_negocio')->on('negocios');
                $table->foreign('id_producto_gratis')->references('id_producto')->on('productos')->nullOnDelete();
                $table->index('id_negocio');
                $table->index('codigo');
                $table->index('activo');
                $table->index('tipo_cupon');
            });

            Schema::create('cupon_reglas', function (Blueprint $table) {
                $table->char('id_regla', 20)->primary();
                $table->char('id_cupon', 20);
                $table->char('id_negocio', 36);
                $table->enum('tipo', [
                    'sucursal',      // a qué sucursal aplica
                    'marca',         // solo bicicletas de esta marca
                    'modelo',        // solo este modelo específico
                    'voltaje',       // solo este voltaje
                    'monto_minimo',  // compra mínima (alternativa como regla)
                ]);
                $table->string('valor', 100)->nullable();

                $table->foreign('id_cupon')->references('id_cupon')->on('cupones')->onDelete('cascade');
                $table->foreign('id_negocio')->references('id_negocio')->on('negocios');
                $table->index('id_cupon');
                $table->index('id_negocio');
            });

            Schema::create('cupon_usos', function (Blueprint $table) {
                $table->char('id_uso', 20)->primary();
                $table->char('id_cupon', 20);
                $table->char('id_venta', 20)->nullable(); // nullable para cuando se use en mantenimientos
                $table->char('id_negocio', 36);
                $table->char('id_usuario', 36);
                $table->decimal('descuento_aplicado', 10, 2);
                $table->timestamp('created_at')->useCurrent();

                $table->foreign('id_cupon')->references('id_cupon')->on('cupones');
                $table->foreign('id_venta')->references('id_venta')->on('ventas')->nullOnDelete();
                $table->index('id_cupon');
                $table->index('id_venta');
                $table->index('id_negocio');
                $table->index('id_usuario'); // útil para buscar usos por vendedor
            });

            Schema::table('ventas', function (Blueprint $table) {
                $table->char('id_cupon', 20)->nullable()->after('id_cliente');
                $table->decimal('descuento_total', 10, 2)->default(0)->after('id_cupon');
                $table->foreign('id_cupon')->references('id_cupon')->on('cupones')->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        Schema::table('ventas', function (Blueprint $table) {
            $table->dropForeign(['id_cupon']);
            $table->dropColumn(['id_cupon', 'descuento_total']);
        });

        Schema::dropIfExists('cupon_usos');
        Schema::dropIfExists('cupon_reglas');
        Schema::dropIfExists('cupones');
    }
};