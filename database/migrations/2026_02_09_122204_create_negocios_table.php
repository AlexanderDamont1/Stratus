<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('negocios', function (Blueprint $table) {
            $table->char('id_negocio', 36)->primary();
            $table->string('nombre_negocio');
            $table->unsignedInteger('max_users')->default(1);
            $table->timestamp('trial_ends_at')->nullable();
            $table->timestamp('subscribed_until')->nullable();
            $table->string('negocio_status', 30)->default('trial');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('negocios');
    }
};