<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
public function up(): void
{
    Schema::table('marca_garantia_config', function (Blueprint $table) {
        // Texto extraído del PDF (legible, ~10-50KB típicamente)
        $table->longText('pdf_texto_extraido')->nullable()->after('pdf_nombre_original');
        // Eliminar el base64 gigante
        $table->dropColumn('pdf_base64');
    });
}

public function down(): void
{
    Schema::table('marca_garantia_config', function (Blueprint $table) {
        $table->mediumText('pdf_base64')->nullable();
        $table->dropColumn('pdf_texto_extraido');
    });
}
};
