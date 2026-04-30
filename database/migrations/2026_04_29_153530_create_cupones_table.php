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
       
        
        // ── CUPONES ──────────────────────────────────────────────────────────
        Schema::create('cupones', function (Blueprint $table) {
            $table->char('id_cupon', 20)->primary();
            $table->char('id_negocio', 36);
            $table->string('codigo', 30)->unique();
            $table->string('nombre', 120);
            $table->enum('tipo_descuento', ['porcentaje', 'monto_fijo']);
            $table->decimal('valor_descuento', 10, 2);
            $table->enum('aplica_a', ['total', 'producto']);
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
        });

        // ── REGLAS DEL CUPÓN ─────────────────────────────────────────────────
        Schema::create('cupon_reglas', function (Blueprint $table) {
            $table->char('id_regla', 20)->primary();
            $table->char('id_cupon', 20);
            $table->enum('tipo', [
                'modelo',
                'voltaje',
                'marca',
                'cantidad_minima',
                'sucursal',
            ]);
            $table->string('valor', 100)->nullable(); // null = cualquiera

            $table->foreign('id_cupon')->references('id_cupon')->on('cupones')->onDelete('cascade');
            $table->index('id_cupon');
        });

        // ── USOS DEL CUPÓN ───────────────────────────────────────────────────
        Schema::create('cupon_usos', function (Blueprint $table) {
            $table->char('id_uso', 20)->primary();
            $table->char('id_cupon', 20);
            $table->char('id_venta', 20);
            $table->char('id_negocio', 36);
            $table->char('id_usuario', 36);
            $table->decimal('descuento_aplicado', 10, 2);
            $table->timestamp('created_at')->useCurrent();

            $table->foreign('id_cupon')->references('id_cupon')->on('cupones');
            $table->foreign('id_venta')->references('id_venta')->on('ventas');
            $table->index('id_cupon');
            $table->index('id_venta');
        });

        // ── AGREGAR CAMPOS A VENTAS ───────────────────────────────────────────
        Schema::table('ventas', function (Blueprint $table) {
            $table->char('id_cupon', 20)->nullable()->after('id_cliente');
            $table->decimal('descuento_total', 10, 2)->default(0)->after('id_cupon');

            $table->foreign('id_cupon')->references('id_cupon')->on('cupones')->nullOnDelete();
        });

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