<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('marca_garantia_config', function (Blueprint $table) {
            $table->enum('politica_reemplazo', ['heredar', 'nueva', 'mini'])
                  ->default('mini')
                  ->after('activa');

            $table->unsignedSmallInteger('mini_garantia_dias')
                  ->default(7)
                  ->after('politica_reemplazo');
        });
    }

    public function down(): void
    {
        Schema::table('marca_garantia_config', function (Blueprint $table) {
            $table->dropColumn(['politica_reemplazo', 'mini_garantia_dias']);
        });
    }
};