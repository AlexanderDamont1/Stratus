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
            $table->char('id_modelo', 15);
            $table->timestamps();

            // Llaves foráneas
            $table->foreign('id_producto')->references('id_producto')->on('productos')->cascadeOnDelete();
            $table->foreign('id_modelo')->references('id_modelo')->on('modelos')->cascadeOnDelete();

            // Evita duplicados
            $table->unique(['id_producto', 'id_modelo']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('producto_modelo');
    }
};