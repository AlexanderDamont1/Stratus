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
        Schema::create('modelo_voltaje', function (Blueprint $table) {

            $table->char('id_mvoltaje', 15)->primary();
            $table->char('id_modelo', 15);
            $table->char('id_voltaje', 15);
            $table->char('id_negocio', 36)->nullable();

            $table->timestamps();

            // Foreign Keys
            $table->foreign('id_modelo')
                ->references('id_modelo')
                ->on('modelos');
           

            $table->foreign('id_voltaje')
                ->references('id_voltaje')
                ->on('voltajes');

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
        Schema::dropIfExists('modelo_voltaje');
    }
};