<?php

namespace App\Http\Controllers;

use App\Models\Usuario;

class SucursalesPublicasController extends Controller
{
    public function index()
    {
        $sucursales = Usuario::query()
            ->where('id_rol', 2)           // solo vendedores/sucursales
            ->whereNotNull('lat')
            ->whereNotNull('lng')
            ->select([
                'id_usuario',
                'nombre_usuario',
                'direccion',
                'lat',
                'lng',
                // Agrega estas columnas a tu tabla si las necesitas:
                // 'nombre_sucursal',
                // 'telefono',
                // 'horario_apertura',
                // 'horario_cierre',
            ])
            ->orderBy('nombre_usuario')
            ->get();

        return view('sucursales.index', compact('sucursales'));
    }
}



// ─────────────────────────────────────────────────────────────
// COLUMNAS OPCIONALES — migración si quieres agregar
// teléfono, nombre de sucursal y horarios al modelo Usuario
// ─────────────────────────────────────────────────────────────

// Schema::table('usuarios', function (Blueprint $table) {
//     $table->string('nombre_sucursal')->nullable()->after('nombre_usuario');
//     $table->string('telefono', 20)->nullable()->after('direccion');
//     $table->time('horario_apertura')->nullable();
//     $table->time('horario_cierre')->nullable();
// });