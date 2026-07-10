<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('gasto_limites', function (Blueprint $table) {
            $table->renameColumn('limite_mensual', 'limite_semanal');
        });
    }

    public function down(): void
    {
        Schema::table('gasto_limites', function (Blueprint $table) {
            $table->renameColumn('limite_semanal', 'limite_mensual');
        });
    }
};
