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
        Schema::create('precios', function (Blueprint $table) {
    $table->char('id_precio', 36)->primary();
    $table->char('id_producto', 36);
    $table->decimal('precio', 10, 2);
    $table->timestamps();

    $table->foreign('id_producto')->references('id_producto')->on('productos');
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('precios');
    }
};
