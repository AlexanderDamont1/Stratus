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
        Schema::create('colores', function (Blueprint $table) {
    $table->char('id_color', 15)->primary();
    $table->char('id_modelo', 15);
    $table->char('id_negocio', 36)->nullable();
    $table->string('color');

    $table->foreign('id_modelo')
        ->references('id_modelo')
        ->on('modelos');

    $table->foreign('id_negocio')
            ->references('id_negocio')
            ->on('negocios');
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('colores');
    }
};
