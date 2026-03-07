<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pedidos', function (Blueprint $table) {
            $table->string('id_pedido', 15)->primary();
            $table->string('id_negocio', 36);
            $table->string('id_usuario', 36);
            $table->tinyInteger('status')->default(1)->comment('1:Solicitado, 2:Preparado, 3:Entregado');
            $table->text('notas')->nullable();
            $table->timestamps();

            $table->foreign('id_negocio')->references('id_negocio')->on('negocios')->onDelete('cascade');
            $table->foreign('id_usuario')->references('id_usuario')->on('usuarios')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pedidos');
    }
};