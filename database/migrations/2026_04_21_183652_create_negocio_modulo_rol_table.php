<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('negocio_modulo_rol', function (Blueprint $table) {
            $table->id();
            $table->char('id_negocio', 36);
            $table->string('id_modulo', 40);
            $table->tinyInteger('id_rol'); // 1=admin, 2=vendedor, 5=gestor
            $table->boolean('activo')->default(true);
            $table->timestamps();

            $table->unique(['id_negocio', 'id_modulo', 'id_rol'], 'negocio_modulo_rol_unique');

            $table->foreign('id_negocio')
                  ->references('id_negocio')
                  ->on('negocios')
                  ->cascadeOnDelete();

            $table->foreign('id_modulo')
                  ->references('id_modulo')
                  ->on('modulos')
                  ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('negocio_modulo_rol');
    }
};