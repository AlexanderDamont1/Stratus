<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('modelos', function (Blueprint $table) {
            $table->char('id_modelo', 15)->primary();
            $table->char('id_marca', 15)->nullable();
            $table->char('id_negocio', 36)->nullable();
            $table->string('nombre_modelo');
            $table->timestamps();

            $table->foreign('id_marca')
                  ->references('id_marca')
                  ->on('marcas');
                

            $table->foreign('id_negocio')
                  ->references('id_negocio')
                  ->on('negocios');
                 
            $table->index('id_negocio');
            $table->index('id_marca');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('modelos');
    }
};