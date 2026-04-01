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
        Schema::create('productos', function (Blueprint $table) {
            $table->char('id_producto', 15)->primary();
            $table->char('id_negocio', 36);
            $table->char('id_usuario', 36);
            $table->string('nombre_producto');
            $table->decimal('precio', 10, 2);      // El precio que pone la sucursal
            $table->char('tipo', 3)->default('1');
            $table->timestamps();

            $table->foreign('id_negocio')->references('id_negocio')->on('negocios')->cascadeOnDelete();

            $table->foreign('id_usuario')->references('id_usuario')->on('usuarios')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('productos');
    }
};
