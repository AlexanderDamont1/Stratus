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
        Schema::create('negocios', function (Blueprint $table) {
            $table->char('id_negocio', 36)->primary();

            $table->string('nombre_negocio');

            // Límite total de usuarios tipo vendedor
            $table->unsignedInteger('max_users')->default(1);

            // Admin principal (único)
            $table->char('id_admin_principal', 36)->nullable()->unique();

            $table->timestamps();

            $table->foreign('id_admin_principal')
                ->references('id_usuario')
                ->on('usuarios')
                ->nullOnDelete();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('negocios');
    }
};
