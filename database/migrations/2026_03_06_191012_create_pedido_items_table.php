<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pedido_items', function (Blueprint $table) {
            $table->string('id_pedido_item', 15)->primary();
            $table->string('id_pedido', 15);
            $table->string('id_modelo', 15);
            $table->string('id_voltaje', 15);
            $table->string('id_color', 15);
            $table->unsignedInteger('cantidad')->default(1);
            $table->timestamps();

            $table->foreign('id_pedido')->references('id_pedido')->on('pedidos')->onDelete('cascade');
            $table->foreign('id_modelo')->references('id_modelo')->on('modelos')->onDelete('restrict');
            $table->foreign('id_voltaje')->references('id_voltaje')->on('voltajes')->onDelete('restrict');
            $table->foreign('id_color')->references('id_color')->on('colores')->onDelete('restrict');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pedido_items');
    }
};