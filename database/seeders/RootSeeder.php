<?php

namespace Database\Seeders;

use App\Models\Usuario;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class RootSeeder extends Seeder
{
    /**
     * Crea el único usuario Root del sistema.
     * Ejecutar una sola vez: php artisan db:seed --class=RootSeeder
     */
    public function run(): void
    {
        // Evita duplicados si se corre más de una vez
        if (Usuario::where('id_rol', 0)->exists()) {
            $this->command->warn('El usuario Root ya existe. Seeder omitido.');
            return;
        }

        Usuario::create([
            'id_negocio'     => null,           // Root no pertenece a ningún negocio
            'nombre_usuario' => 'Root',
            'correo'         => 'root@sistema.com',
            'password'       => Hash::make('root1234'),   // ← Cambiar en producción
            'id_rol'         => 0,
        ]);

        $this->command->info('✓ Root creado: root@sistema.com / root1234');
    }
    }
