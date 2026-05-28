<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reportes_robo', function (Blueprint $table) {
            $table->char('id_reporte', 36)->primary();
            $table->string('num_serie', 17);
            $table->char('id_negocio_origen', 36);
            $table->char('id_negocio_reporta', 36);
            $table->char('id_cliente', 36);

            // 0=pendiente 1=confirmado 2=en_custodia 3=cerrado
            $table->tinyInteger('estado')->default(0);

            $table->char('token_confirmacion', 64)->nullable();
            $table->dateTime('token_expires_at')->nullable();
            $table->dateTime('confirmado_at')->nullable();
            $table->dateTime('encontrado_at')->nullable();
            $table->dateTime('entregado_at')->nullable();
            $table->char('id_negocio_encontrado', 36)->nullable();
            $table->text('notas')->nullable();
            $table->timestamps();

            $table->foreign('num_serie')->references('num_serie')->on('bicicletas');
            $table->foreign('id_negocio_origen')->references('id_negocio')->on('negocios');
            $table->foreign('id_negocio_reporta')->references('id_negocio')->on('negocios');
            $table->foreign('id_cliente')->references('id_cliente')->on('clientes');
            $table->foreign('id_negocio_encontrado')->references('id_negocio')->on('negocios');

            $table->index('num_serie');
            $table->index('estado');
            $table->index('token_confirmacion');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reportes_robo');
    }
};