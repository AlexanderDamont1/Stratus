<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('marca_garantia_config', function (Blueprint $table) {
            $table->string('id_marca_garantia', 30)->primary();
            $table->string('id_negocio', 30);
            $table->string('id_marca', 30);
            $table->boolean('activa')->default(false);

            // PDF como base64 — pólizas son documentos ligeros (<2MB)
            // MEDIUMTEXT soporta hasta 16MB, suficiente con margen
            $table->mediumText('pdf_base64')->nullable();
            $table->string('pdf_nombre_original', 255)->nullable();

            $table->enum('estado_procesamiento', [
                'sin_pdf', 'pendiente', 'procesando', 'completado', 'error'
            ])->default('sin_pdf');

            // Respuesta cruda de la IA antes de validación humana
            $table->json('ia_raw_json')->nullable();
            $table->timestamp('ia_procesado_at')->nullable();
            $table->text('notas_admin')->nullable();

            $table->timestamps();

            $table->unique(['id_marca', 'id_negocio'], 'uq_marca_negocio');
            $table->foreign('id_negocio')->references('id_negocio')->on('negocios');
            $table->foreign('id_marca')->references('id_marca')->on('marcas');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('marca_garantia_config');
    }
};