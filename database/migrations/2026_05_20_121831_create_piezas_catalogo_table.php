<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('piezas_catalogo', function (Blueprint $table) {
            $table->id('id_pieza');

            // Multi-tenancy
            $table->char('id_negocio', 36);
            $table->foreign('id_negocio')
                  ->references('id_negocio')
                  ->on('negocios')
                  ->cascadeOnDelete();

            // Identificación
            $table->string('clave', 80)
                  ->comment('Clave interna del negocio, ej: CTR-48V, BAT-36V-10');
            $table->string('nombre', 150);
            $table->string('categoria', 80)->nullable()
                  ->comment('Motor, Batería, Frenos, Electrónica, Transmisión, Chasis...');

           
            $table->string('marca_pieza', 80)->nullable()
                  ->comment('Marca del fabricante de la pieza, no del tenant');

            
            $table->json('modelos_compatibles')->nullable()
                  ->comment('JSON array de nombres de modelos compatibles, null = universal');

            // Voltaje compatible — nullable: piezas mecánicas no tienen voltaje
            $table->string('voltaje_compatible', 20)->nullable()
                  ->comment('Ej: 36V, 48V, 52V. null = no aplica');

            // Descripción libre para el técnico
            $table->text('descripcion')->nullable();

            // Precios
            $table->decimal('precio_costo', 10, 2)->default(0);
            $table->decimal('precio_venta', 10, 2)->default(0);

            // Stock
            $table->unsignedSmallInteger('stock_actual')->default(0);
            $table->unsignedSmallInteger('stock_minimo')->default(0)
                  ->comment('Alerta cuando stock_actual <= stock_minimo');

            // Serializable: motor/batería tienen num_serie propio
            $table->boolean('serializable')->default(false)
                  ->comment('Si la pieza tiene número de serie propio');

            $table->boolean('activo')->default(true);

            $table->timestamps();

            // Clave única por negocio — dos negocios pueden tener la misma clave
            $table->unique(['id_negocio', 'clave'], 'uq_pieza_negocio_clave');

            // Índices de búsqueda frecuente
            $table->index(['id_negocio', 'categoria'],       'idx_pieza_negocio_cat');
            $table->index(['id_negocio', 'stock_actual'],    'idx_pieza_stock');
            $table->index(['id_negocio', 'activo'],          'idx_pieza_activo');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('piezas_catalogo');
    }
};