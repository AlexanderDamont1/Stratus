<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('negocio_garantia_config', function (Blueprint $table) {
            $table->string('id_negocio', 30)->primary();
            $table->enum('politica_reemplazo', ['heredar', 'nueva', 'mini'])
                  ->default('mini');
            $table->smallInteger('mini_garantia_dias')->unsigned()->default(7);
            $table->timestamp('updated_at')
                  ->useCurrent()
                  ->useCurrentOnUpdate();

            $table->foreign('id_negocio')
                  ->references('id_negocio')
                  ->on('negocios');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('negocio_garantia_config');
    }
};