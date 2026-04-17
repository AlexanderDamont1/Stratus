<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bicicleta_movimientos', function (Blueprint $table) {

            // PK — mismo patrón que el resto del proyecto (ej: 'MOV' + 12 chars)
            $table->char('id_movimiento', 15)->primary();

            // FK a bicicletas
            $table->char('num_serie', 17);

            // Multinegocio — mismo tipo que bicicletas.id_negocio
            $table->char('id_negocio', 36);

            // Usuario que registró el movimiento — nullable igual que bicicletas.id_usuario
            $table->char('id_usuario', 36)->nullable();

            // Tipo de evento
            $table->enum('tipo_movimiento', [
                'entrada_stock',
                'transferencia_sucursal',
                'venta',
                'mantenimiento',
                'ajuste',
            ]);

            // De dónde venía y a dónde fue
            $table->string('origen', 120)->nullable();
            $table->string('destino', 120)->nullable();

            // Pedido relacionado — mismo tipo que bicicletas.id_pedido
            $table->char('id_pedido', 15)->nullable();

            // Notas libres
            $table->text('notas')->nullable();

            // Cuándo ocurrió el movimiento (separado de created_at por si se registra retroactivo)
            $table->timestamp('fecha_movimiento');

            $table->timestamps();

            /*
            ==========================
            FOREIGN KEYS
            ==========================
            */
            $table->foreign('num_serie')
                  ->references('num_serie')
                  ->on('bicicletas')
                  ->onDelete('cascade'); // Si se borra la bici, se borra su historial

            $table->foreign('id_negocio')
                  ->references('id_negocio')
                  ->on('negocios');

            $table->foreign('id_usuario')
                  ->references('id_usuario')
                  ->on('usuarios')
                  ->nullOnDelete(); // Si se borra el usuario, el movimiento queda sin autor

            $table->foreign('id_pedido')
                  ->references('id_pedido')
                  ->on('pedidos')
                  ->nullOnDelete(); // Si se borra el pedido, el movimiento queda sin referencia

            /*
            ==========================
            ÍNDICES
            ==========================
            */
            // Historial de una bici ordenado por fecha — la consulta más frecuente
            $table->index(['num_serie', 'fecha_movimiento']);

            // Feed en vivo por negocio — segunda consulta más frecuente
            $table->index(['id_negocio', 'fecha_movimiento']);

            // Para filtrar por usuario (¿quién hizo qué?)
            $table->index(['id_usuario', 'fecha_movimiento']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bicicleta_movimientos');
    }
};