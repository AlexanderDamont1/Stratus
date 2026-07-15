<?php

namespace Database\Seeders;

use App\Models\Marca;
use App\Models\Modelo;
use App\Models\Usuario;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class RootSeeder extends Seeder
{
    /**
     * Crea el único usuario Root del sistema y la marca pública del Gestor.
     * Ejecutar una sola vez: php artisan db:seed --class=RootSeeder
     */
    public function run(): void
    {
        // Evita duplicados si se corre más de una vez
        if (Usuario::where('id_rol', 0)->exists()) {
            $this->command->warn('El usuario Root ya existe. Omitiendo creación.');
        } else {
            Usuario::create([
                'id_negocio'     => null,           // Root no pertenece a ningún negocio
                'nombre_usuario' => 'Root',
                'correo'         => 'root@sistema.com',
                'password'       => Hash::make('root1234'),   // ← Cambiar en producción
                'id_rol'         => 0,
            ]);

            $this->command->info('✓ Root creado: root@sistema.com / root1234');
        }

        $this->seedMarcaGestor();
    }

    /**
     * Marca pública (id_negocio null) usada exclusivamente por el Gestor (rol 5)
     * para que todo su catálogo global tenga marca en vez de quedar sin ella.
     */
    private function seedMarcaGestor(): void
    {
        $marca = Marca::whereNull('id_negocio')->where('nombre_marca', 'Evobike')->first();

        if (!$marca) {
            $marca = Marca::create([
                'id_negocio'   => null,
                'nombre_marca' => 'Evobike',
            ]);

            $this->command->info('✓ Marca pública "Evobike" creada para el Gestor.');
        }

        // Backfill: modelos globales del Gestor que quedaron sin marca antes de este cambio.
        $actualizados = Modelo::whereNull('id_negocio')
            ->whereNull('id_marca')
            ->update(['id_marca' => $marca->id_marca]);

        if ($actualizados > 0) {
            $this->command->info("✓ {$actualizados} modelo(s) del Gestor actualizados con la marca Evobike.");
        }
    }
}
