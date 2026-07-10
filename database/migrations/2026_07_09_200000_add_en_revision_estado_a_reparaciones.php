<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE reparaciones MODIFY estado ENUM(
            'en_revision',
            'recibida',
            'diagnostico',
            'cotizacion_enviada',
            'en_proceso',
            'lista',
            'entregada',
            'cancelada'
        ) NOT NULL DEFAULT 'recibida'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE reparaciones MODIFY estado ENUM(
            'recibida',
            'diagnostico',
            'cotizacion_enviada',
            'en_proceso',
            'lista',
            'entregada',
            'cancelada'
        ) NOT NULL DEFAULT 'recibida'");
    }
};
