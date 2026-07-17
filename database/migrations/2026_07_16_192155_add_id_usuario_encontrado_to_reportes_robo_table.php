<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reportes_robo', function (Blueprint $table) {
            $table->char('id_usuario_encontrado', 36)->nullable()->after('id_negocio_encontrado');

            $table->foreign('id_usuario_encontrado')
                  ->references('id_usuario')
                  ->on('usuarios')
                  ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('reportes_robo', function (Blueprint $table) {
            $table->dropForeign(['id_usuario_encontrado']);
            $table->dropColumn('id_usuario_encontrado');
        });
    }
};
