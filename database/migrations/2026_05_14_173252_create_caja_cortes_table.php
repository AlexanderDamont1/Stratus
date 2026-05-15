<?php

// database/migrations/2026_05_14_000004_create_caja_cortes_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('caja_cortes', function (Blueprint $table) {

            $table->char('id_corte', 15)->primary();

            $table->char('id_sesion', 15);

            $table->char('id_negocio', 36);

            $table->char('id_usuario', 36);

            $table->json('snapshot');

            $table->timestamps();

            $table->index(
                'id_negocio',
                'caja_cortes_id_negocio_index'
            );

            $table->foreign('id_sesion')
                ->references('id_sesion')
                ->on('caja_sesiones')
                ->cascadeOnDelete();

            $table->foreign('id_usuario')
                ->references('id_usuario')
                ->on('usuarios');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('caja_cortes');
    }
};