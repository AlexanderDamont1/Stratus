<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('producto_modelo', function (Blueprint $table) {
            $table->char('id_producto_modelo', 15)->primary();
            $table->char('id_producto', 15);
            $table->char('id_negocio', 36);
            $table->char('id_usuario', 36);        // Rol 2 = sucursal
            $table->char('id_modelo', 15);
            $table->char('id_voltaje', 15);        // La variante elegida
            $table->boolean('activo')->default(true);
            $table->timestamps();

            $table->foreign('id_producto')->references('id_producto')->on('productos')->cascadeOnDelete();
            $table->foreign('id_negocio')->references('id_negocio')->on('negocios')->cascadeOnDelete();
            $table->foreign('id_usuario')->references('id_usuario')->on('usuarios')->cascadeOnDelete();
            $table->foreign('id_modelo')->references('id_modelo')->on('modelos')->cascadeOnDelete();
            $table->foreign('id_voltaje')->references('id_voltaje')->on('voltajes')->cascadeOnDelete();

            // Una sucursal no puede duplicar el mismo modelo+voltaje
            $table->unique(['id_usuario', 'id_modelo', 'id_voltaje']);
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('producto_modelo');
    }
};
