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
                'icono'         => 'M12 7.5h1.5m-1.5 3h1.5m-7.5 3h7.5m-7.5 3h7.5m3-9h3.4c.6 0 1.1.5 1.1 1.1V18a2.3 2.3 0 0 1-2.3 2.3M16.5 7.5V18a2.3 2.3 0 0 0 2.3 2.3M16.5 7.5V4.9c0-.6-.5-1.1-1.1-1.1H4.1C3.5 3.8 3 4.3 3 4.9V18a2.3 2.3 0 0 0 2.3 2.3h13.5M6 7.5h3v3H6z',
                'tipo'          => 'radio',
                'opciones'      => json_encode([
                    ['value' => 'ticket', 'label' => 'Ticket PDF',         'descripcion' => 'El vendedor genera y entrega el PDF. Ideal para puntos físicos.'],
                    ['value' => 'correo', 'label' => 'Correo electrónico', 'descripcion' => 'Se envía automáticamente al correo del cliente. Sin papel.'],
                ]),
                'valor_default' => 'ticket',
                'grupo'         => 'ventas',
                'orden'         => 0,
                'activo'        => true,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
            [
                'id_ncf'        => 'NCF230425ABC124',
                'clave'         => 'metodos_pago',
                'nombre'        => 'Métodos de Pago',
                'descripcion'   => 'Selecciona que métodos de pago va a poder aceptar las sucursales',
                'icono'         => 'M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 0 0 2.25-2.25V6.75A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25v10.5A2.25 2.25 0 0 0 4.5 19.5Z',
                'tipo'          => 'checkbox_multi',
                'opciones'      => json_encode([
                    ['value' => 'efectivo', 'label' => 'Efectivo', 'descripcion' => 'Pago en billetes y monedas'],
                    ['value' => 'tarjeta', 'label' => 'Tarjeta (Terminal)', 'descripcion' => 'Débito o crédito con terminal bancaria'],
                    ['value' => 'transferencia', 'label' => 'Transferencia / SPEI', 'descripcion' => 'Depósito o transferencia bancaria'],
                    ['value' => 'credito_interno', 'label' => 'Crédito interno', 'descripcion' => 'El negocio otorga crédito al cliente'],
                ]),
                'valor_default' => 'efectivo',
                'grupo'         => 'ventas',
                'orden'         => 1,
                'activo'        => true,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
            [
                'id_ncf'        => 'NCF230425REP001',
                'clave'         => 'reparaciones.sucursal_puede_reparar',
                'nombre'        => 'Sucursal puede realizar reparaciones',
                'descripcion'   => 'Si está activo, la sucursal realiza las reparaciones en sitio. Si no, la unidad se envía a fábrica.',
                'icono'         => 'M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z',
                'tipo'          => 'toggle',
                'opciones'      => null,
                'valor_default' => 'false',
                'grupo'         => 'reparaciones',
                'orden'         => 0,
                'activo'        => false,   // ← inactivo hasta que Root active el módulo
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