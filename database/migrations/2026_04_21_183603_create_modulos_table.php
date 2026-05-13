<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('modulos', function (Blueprint $table) {
            $table->string('id_modulo', 40)->primary();
            $table->string('nombre', 80);
            $table->string('descripcion', 255)->nullable();
            $table->boolean('activo_por_defecto')->default(false);
            $table->timestamps();
        });

        DB::table('modulos')->insert([
            [
                'id_modulo'          => 'tracking',
                'nombre'             => 'Tracking de Bicicletas',
                'descripcion'        => 'Historial de movimientos por número de serie.',
                'activo_por_defecto' => false,
                'created_at'         => now(),
                'updated_at'         => now(),
            ],
            [
                'id_modulo'          => 'pedidos',
                'nombre'             => 'Control de Pedidos',
                'descripcion'        => 'Creacion de pedidos para la Fabrica Evobike.',
                'activo_por_defecto' => true,
                'created_at'         => now(),
                'updated_at'         => now(),
            ],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('modulos');
    }
};