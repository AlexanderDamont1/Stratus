<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventario', function (Blueprint $table) {
            $table->string('id_inventario', 20)->primary();

            // nullable para accesorios (tipo 1)
            $table->string('id_producto_modelo', 20)->nullable();
            $table->foreign('id_producto_modelo')
                ->references('id_producto_modelo')
                ->on('producto_modelo')
                ->nullOnDelete();

            // para accesorios (tipo 1) — nullable para bicicletas
            $table->string('id_producto', 20)->nullable();
            $table->foreign('id_producto')
                ->references('id_producto')
                ->on('productos')
                ->nullOnDelete();

            $table->string('id_negocio', 20);
            $table->foreign('id_negocio')
                ->references('id_negocio')
                ->on('negocios')
                ->cascadeOnDelete();

            // null = stock admin, con valor = stock de esa sucursal
            $table->string('id_usuario', 20)->nullable();
            $table->foreign('id_usuario')
                ->references('id_usuario')
                ->on('usuarios')
                ->nullOnDelete();

            $table->unsignedInteger('cantidad')->default(0);
            $table->unsignedInteger('stock_minimo')->default(3);

            $table->timestamps();

            $table->unique(
                ['id_producto_modelo', 'id_negocio', 'id_usuario'],
                'inv_pm_negocio_usuario_unique'
            );

            $table->unique(
                ['id_producto', 'id_negocio', 'id_usuario'],
                'inv_prod_negocio_usuario_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventario');
    }
};