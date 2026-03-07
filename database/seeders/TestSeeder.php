<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class TestSeeder extends Seeder
{
    public function run(): void
    {
        // Crear un modelo
        $id_modelo = 'MOD' . now()->format('ymd') . strtoupper(Str::random(3)) . rand(100, 999);
        DB::table('modelos')->insert([
            'id_modelo' => $id_modelo,
            'nombre_modelo' => 'Modelo ' . strtoupper(Str::random(4)),
        ]);

        // Crear un negocio
        $id_negocio = 'NEG' . now()->format('ymd') . strtoupper(Str::random(3)) . rand(100, 999);
        DB::table('negocios')->insert([
            'id_negocio' => $id_negocio,
            'nombre_negocio' => 'Negocio ' . strtoupper(Str::random(4)),
        ]);

        // Crear un color (requiere id_modelo)
        $id_color = 'COL' . now()->format('ymd') . strtoupper(Str::random(3)) . rand(100, 999);
        DB::table('colores')->insert([
            'id_color'  => $id_color,
            'id_modelo' => $id_modelo,
            'color'     => 'Color ' . strtoupper(Str::random(4)),
        ]);

        // Crear un voltaje (NO requiere id_modelo)
        $id_voltaje = 'VOL' . now()->format('ymd') . strtoupper(Str::random(3)) . rand(100, 999);
        DB::table('voltajes')->insert([
            'id_voltaje' => $id_voltaje,
            'voltaje'    => rand(24, 72) . 'V',
        ]);

        $this->command->info("Seeder completado: negocio $id_negocio, modelo $id_modelo, color $id_color, voltaje $id_voltaje");
    }
}