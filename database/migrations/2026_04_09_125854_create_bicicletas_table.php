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
            $table->char('id_negocio', 36);
            $table->char('id_usuario', 36)->nullable();

            // Atributos físicos
            $table->char('id_marca', 15)->nullable();
            $table->char('id_modelo', 15);
            $table->char('id_voltaje', 15);
            $table->char('id_color', 15);

            // Estado de la bicicleta
            $table->char('status', 1)->default(1);

            $table->char('id_pedido', 15)->nullable();


            $table->timestamps();

            /*
            ==========================
            FOREIGN KEYS
            ==========================
            */
            $table->foreign('id_pedido')
                  ->references('id_pedido')
                  ->on('pedidos');

            $table->foreign('id_usuario')
                  ->references('id_usuario')
                  ->on('usuarios');

            $table->foreign('id_negocio')
                  ->references('id_negocio')
                  ->on('negocios');

            $table->foreign('id_marca')
                  ->references('id_marca')
                  ->on('marcas');      

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
