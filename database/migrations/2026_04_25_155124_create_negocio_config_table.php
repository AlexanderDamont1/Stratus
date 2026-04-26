<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('negocio_config', function (Blueprint $table) {
            $table->char('id_negocio', 36)->primary();
            $table->enum('entrega_comprobante', ['ticket', 'correo'])->default('ticket');
            $table->timestamps();

            $table->foreign('id_negocio')
                  ->references('id_negocio')
                  ->on('negocios')
                  ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('negocio_config');
    }
};