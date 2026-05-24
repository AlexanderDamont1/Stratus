<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cotizaciones', function (Blueprint $table) {

            $table->string('id_cotizacion', 20)->primary(); // COT-00001

            $table->string('id_reparacion', 20);
            $table->foreign('id_reparacion')
                  ->references('id_reparacion')
                  ->on('reparaciones')
                  ->cascadeOnDelete();

            // Token único para el link del email (64 chars hex)
            $table->string('token', 64)->unique();

            // Snapshot de la cotización al momento de enviar
            // (los costos pueden cambiar después, aquí quedan fijos)
            $table->decimal('costo_mano_obra', 10, 2)->default(0);
            $table->decimal('costo_piezas',    10, 2)->default(0);
            $table->decimal('costo_total',     10, 2)->default(0);

            // Para reparaciones: el costo base ya pagado aparte
            $table->decimal('costo_reparaciones_base', 10, 2)->default(0);

            // Descripción del trabajo a realizar (texto libre del técnico)
            $table->text('descripcion_trabajo');

            // Lista de piezas en la cotización — snapshot JSON
            // [{ "nombre": "Motor 350W", "cantidad": 1, "precio_unitario": 1200, "subtotal": 1200 }]
            $table->json('piezas_detalle')->nullable();

            // Respuesta del cliente
            // null = pendiente, 1 = acepta todo, 0 = rechaza
            $table->tinyInteger('respuesta')->nullable()
                  ->comment('null=pendiente, 1=acepta, 0=rechaza');

            $table->timestamp('respondido_at')->nullable();

            // Si rechaza pero luego acepta parcialmente, el trabajador
            // registra manualmente qué piezas sí van
            $table->json('piezas_aceptadas')->nullable()
                  ->comment('Solo se llena si respuesta=0 pero el cliente acepta parcialmente en persona');

            $table->boolean('resolucion_manual')->default(false)
                  ->comment('true cuando el trabajador resolvió la respuesta manualmente (sin link)');

            $table->text('nota_resolucion')->nullable()
                  ->comment('Nota del trabajador al resolver manualmente');

            // Tiempos
            $table->timestamp('enviado_at')->nullable();
            $table->timestamp('expires_at')->nullable()
                  ->comment('enviado_at + 12 horas');

            $table->timestamps();

            // Una OT solo puede tener una cotización activa a la vez
            $table->index('id_reparacion');
            $table->index('token');
            $table->index('expires_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cotizaciones');
    }
};