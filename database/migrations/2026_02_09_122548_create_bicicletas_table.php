<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('bicicletas', function (Blueprint $table) {

            // PK
            $table->char('num_serie', 17)->primary();

            // Multinegocio
            $table->char('id_negocio', 26);

            // Atributos físicos
            $table->char('id_modelo', 15);
            $table->char('id_voltaje', 10);
            $table->char('id_color', 15);

            // Estado de la bicicleta
            $table->enum('status', [
                'STOCK',
                'VENDIDA',
                'REPARACION'
            ])->default('STOCK');

            $table->timestamps();

            /*
            ==========================
            FOREIGN KEYS
            ==========================
            */

            $table->foreign('id_negocio')
                  ->references('id_negocio')
                  ->on('negocios');


            $table->foreign('id_modelo')
                  ->references('id_modelo')
                  ->on('modelos');

            $table->foreign('id_voltaje')
                  ->references('id_voltaje')
                  ->on('voltajes');

            $table->foreign('id_color')
                  ->references('id_color')
                  ->on('colores');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bicicletas');
    }
};
