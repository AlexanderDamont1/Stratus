<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('negocio_config_valores', function (Blueprint $table) {
            $table->string('id_nvc', 25)->primary();
            $table->char('id_negocio', 36);
            $table->string('id_ncf', 25);
            $table->text('valor');
            $table->timestamps();

            $table->unique(['id_negocio', 'id_ncf']);

            $table->foreign('id_negocio')
                  ->references('id_negocio')
                  ->on('negocios')
                  ->cascadeOnDelete();

            $table->foreign('id_ncf')
                ->references('id_ncf')
                ->on('negocio_config')  // antes decía 'config_definiciones'
                ->cascadeOnDelete();;
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('negocio_config_valores');
    }
};