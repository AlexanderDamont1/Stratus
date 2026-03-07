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

            $table->timestamps();

            // Foreign Keys
            $table->foreign('id_modelo')
                ->references('id_modelo')
                ->on('modelos')
                ->onDelete('cascade');

            $table->foreign('id_voltaje')
                ->references('id_voltaje')
                ->on('voltajes')
                ->onDelete('cascade');
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