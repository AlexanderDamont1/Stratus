<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gasto_limites', function (Blueprint $table) {
            $table->char('id_limite', 15)->primary();

            $table->char('id_negocio', 36);
            $table->char('id_usuario', 36)->unique(); // sucursal — un solo límite por sucursal

            $table->decimal('limite_mensual', 12, 2);

            $table->char('id_usuario_admin', 36); // quién lo configuró/editó por última vez
            $table->timestamps();

            $table->foreign('id_negocio')
                ->references('id_negocio')->on('negocios')->cascadeOnDelete();
            $table->foreign('id_usuario')
                ->references('id_usuario')->on('usuarios')->cascadeOnDelete();
            $table->foreign('id_usuario_admin')
                ->references('id_usuario')->on('usuarios');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gasto_limites');
    }
};