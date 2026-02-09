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
        Schema::create('bicicletas', function (Blueprint $table) {
    $table->char('num_serie', 36)->primary();
    $table->char('id_negocio', 36);
    $table->char('id_modelo', 36);
    $table->char('id_color', 36);
    $table->char('id_voltaje', 36);
    $table->char('id_producto', 36)->unique();
    $table->char('id_cliente', 36)->nullable();
    $table->boolean('vendida')->default(false);
    $table->timestamps();

    $table->foreign('id_negocio')->references('id_negocio')->on('negocios');
    $table->foreign('id_modelo')->references('id_modelo')->on('modelos');
    $table->foreign('id_color')->references('id_color')->on('colores');
    $table->foreign('id_voltaje')->references('id_voltaje')->on('voltajes');
    $table->foreign('id_producto')->references('id_producto')->on('productos');
    $table->foreign('id_cliente')->references('id_cliente')->on('clientes');
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bicicletas');
    }
};
