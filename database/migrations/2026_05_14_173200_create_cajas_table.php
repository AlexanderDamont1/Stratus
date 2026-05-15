<?php

// database/migrations/2026_05_14_000001_create_cajas_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cajas', function (Blueprint $table) {

            $table->char('id_caja', 15)->primary();

            $table->char('id_negocio', 36);
            $table->char('id_usuario', 36);

            $table->string('nombre', 80)
                ->default('Caja principal');

            $table->boolean('activa')
                ->default(true);

            $table->timestamps();

            $table->unique(
                ['id_negocio', 'id_usuario'],
                'cajas_negocio_usuario_unique'
            );

            $table->foreign('id_negocio')
                ->references('id_negocio')
                ->on('negocios')
                ->cascadeOnDelete();

            $table->foreign('id_usuario')
                ->references('id_usuario')
                ->on('usuarios')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cajas');
    }
};