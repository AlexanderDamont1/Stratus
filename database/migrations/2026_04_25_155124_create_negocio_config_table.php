<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('negocio_config', function (Blueprint $table) {
             $table->char('id_ncf', 25)->primary();
            $table->string('clave')->unique();
            $table->string('nombre');
            $table->string('descripcion')->nullable();
            $table->string('icono')->nullable();
            $table->enum('tipo', ['radio', 'checkbox_multi', 'toggle', 'texto', 'numero']);
            $table->json('opciones')->nullable();
            $table->string('valor_default');
            $table->string('grupo')->default('general');
            $table->unsignedTinyInteger('orden')->default(0);
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });

        DB::table('negocio_config')->insert([
            [
                'id_ncf'        => 'NCF230425ABC123',
                'clave'         => 'entrega_comprobante',
                'nombre'        => 'Entrega de comprobante',
                'descripcion'   => 'Cómo se enviará el ticket al cliente después de cada venta.',
                'icono'         => 'ti-file-text',
                'tipo'          => 'radio',
                'opciones'      => json_encode([
                    ['value' => 'ticket', 'label' => 'Ticket PDF',         'descripcion' => 'El vendedor genera y entrega el PDF. Ideal para puntos físicos.'],
                    ['value' => 'correo', 'label' => 'Correo electrónico', 'descripcion' => 'Se envía automáticamente al correo del cliente. Sin papel.'],
                ]),
                'valor_default' => 'ticket',
                'grupo'         => 'ventas',
                'orden'         => 1,
                'activo'        => true,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('negocio_config');
    }
};