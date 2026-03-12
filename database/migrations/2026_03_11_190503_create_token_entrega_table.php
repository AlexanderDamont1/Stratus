<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pedido_tokens', function (Blueprint $table) {
            $table->string('id_token', 15)->primary();
            $table->string('id_usuario1', 36); // vendedor
            $table->string('id_usuario2', 36); // gestor
            $table->string('id_pedido', 15);
            $table->string('token', 10);
            $table->tinyInteger('estado')->default(0); // 0 activo, 1 usado
            $table->timestamps();

            $table->foreign('id_pedido')->references('id_pedido')->on('pedidos')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pedido_tokens');
    }
};