<?php
// ══════════════════════════════════════════════════════════════
// app/Http/Controllers/Admin/PersonalController.php  (completo)
// ══════════════════════════════════════════════════════════════
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Personal;
use App\Models\Usuario;
use Illuminate\Http\Request;

class PersonalController extends Controller
{
    // ── Sucursales del negocio (helper) ──────────────────────────────────────
    private function sucursales(string $idNegocio)
    {
        return Usuario::where('id_negocio', $idNegocio)
            ->where('id_rol', 2)
            ->orderBy('nombre_usuario')
            ->get(['id_usuario', 'nombre_usuario', 'correo']);
    }

    // ── Construye el objeto "personal agrupado" que devuelve la vista/JS ─────
    private function buildPersonalItem(string $nombre, string $idNegocio): array
    {
        $registros = Personal::with('sucursal:id_usuario,nombre_usuario')
            ->deNegocio($idNegocio)
            ->where('nombre', $nombre)
            ->get();

        if ($registros->isEmpty()) return [];

        $primero = $registros->first();

        return [
            'id_personal' => $primero->id_personal,
            'nombre'      => $primero->nombre,
            'activo'      => (bool) $primero->activo,
            'sucursales'  => $registros->map(fn ($p) => [
                'id_usuario'     => $p->id_usuario,
                'nombre_usuario' => $p->sucursal->nombre_usuario ?? '—',
            ])->values()->toArray(),
        ];
    }

    // ── INDEX ────────────────────────────────────────────────────────────────
    public function index()
{
    $user = auth()->user();
 
    $registros = Personal::with('sucursal:id_usuario,nombre_usuario')
        ->deNegocio($user->id_negocio)
        ->orderBy('nombre')
        ->get();
 
    // Agrupar por nombre y construir el array plano que necesita el JS
    $personal = $registros
        ->groupBy('nombre')
        ->map(function ($grupo) {
            $primero = $grupo->first();
            return [
                'id_personal' => $primero->id_personal,
                'nombre'      => $primero->nombre,
                'activo'      => (bool) $primero->activo,
                'sucursales'  => $grupo->map(fn ($p) => [
                    'id_usuario'     => $p->id_usuario,
                    'nombre_usuario' => $p->sucursal->nombre_usuario ?? '—',
                ])->values()->toArray(),
            ];
        })
        ->values()
        ->toArray(); // ← array puro, no Collection
 
    $sucursales = $this->sucursales($user->id_negocio);
 
    // Preparar también sucursalesDisponibles como array plano
    $sucursalesJs = $sucursales->map(fn ($s) => [
        'id_usuario'     => $s->id_usuario,
        'nombre_usuario' => $s->nombre_usuario,
    ])->values()->toArray();
 
    return view('administrador.personal.index', compact(
        'personal',
        'sucursales',
        'sucursalesJs',
    ));
}

    // ── STORE ────────────────────────────────────────────────────────────────
    public function store(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'nombre'       => 'required|string|max:120',
            'sucursales'   => 'required|array|min:1',
            'sucursales.*' => 'exists:usuarios,id_usuario',
        ]);

        // Evitar duplicados: si ya existe ese nombre en esa sucursal, no re-crear
        foreach ($request->sucursales as $idUsuario) {
            $existe = Personal::where('id_negocio', $user->id_negocio)
                ->where('id_usuario', $idUsuario)
                ->where('nombre', $request->nombre)
                ->exists();

            if (!$existe) {
                Personal::create([
                    'id_usuario' => $idUsuario,
                    'id_negocio' => $user->id_negocio,
                    'nombre'     => $request->nombre,
                    'activo'     => true,
                ]);
            }
        }

        $item = $this->buildPersonalItem($request->nombre, $user->id_negocio);

        return response()->json([
            'message'  => 'Vendedor creado correctamente.',
            'personal' => $item,
        ], 201);
    }

    // ── UPDATE ───────────────────────────────────────────────────────────────
    // Recibe el id_personal del primer registro del grupo (nombre).
    // Actualiza nombre y activo en TODOS los registros del mismo nombre.
    // Sincroniza las sucursales: agrega las nuevas, elimina las que ya no están.
    public function update(Request $request, string $id)
    {
        $user     = auth()->user();
        $personal = Personal::deNegocio($user->id_negocio)->findOrFail($id);

        $request->validate([
            'nombre'       => 'required|string|max:120',
            'sucursales'   => 'required|array|min:1',
            'sucursales.*' => 'exists:usuarios,id_usuario',
            'activo'       => 'nullable|boolean',
        ]);

        $nombreAnterior = $personal->nombre;
        $nombreNuevo    = $request->nombre;
        $activo         = $request->boolean('activo', true);

        // Actualizar todos los registros del mismo nombre en este negocio
        Personal::deNegocio($user->id_negocio)
            ->where('nombre', $nombreAnterior)
            ->update([
                'nombre' => $nombreNuevo,
                'activo' => $activo,
            ]);

        // Sincronizar sucursales
        $sucursalesActuales = Personal::deNegocio($user->id_negocio)
            ->where('nombre', $nombreNuevo)
            ->pluck('id_usuario')
            ->toArray();

        $sucursalesNuevas   = $request->sucursales;

        // Agregar las que no existen
        foreach (array_diff($sucursalesNuevas, $sucursalesActuales) as $idUsuario) {
            Personal::create([
                'id_usuario' => $idUsuario,
                'id_negocio' => $user->id_negocio,
                'nombre'     => $nombreNuevo,
                'activo'     => $activo,
            ]);
        }

        // Eliminar las que ya no están (solo si no tienen ventas asociadas)
        foreach (array_diff($sucursalesActuales, $sucursalesNuevas) as $idUsuario) {
            $reg = Personal::deNegocio($user->id_negocio)
                ->where('nombre', $nombreNuevo)
                ->where('id_usuario', $idUsuario)
                ->first();

            if ($reg) {
                // Si tiene ventas, solo desactivar — no borrar
                $tieneVentas = \App\Models\VentaVendedor::where('id_personal', $reg->id_personal)->exists();
                if ($tieneVentas) {
                    $reg->update(['activo' => false]);
                } else {
                    $reg->delete();
                }
            }
        }

        $item = $this->buildPersonalItem($nombreNuevo, $user->id_negocio);

        return response()->json([
            'message'  => 'Vendedor actualizado.',
            'personal' => $item,
        ]);
    }

    // ── DESTROY (toggle activo) ───────────────────────────────────────────────
    public function destroy(string $id)
    {
        $user     = auth()->user();
        $personal = Personal::deNegocio($user->id_negocio)->findOrFail($id);

        $nuevoEstado = !$personal->activo;

        // Aplica a todos los registros del mismo nombre
        Personal::deNegocio($user->id_negocio)
            ->where('nombre', $personal->nombre)
            ->update(['activo' => $nuevoEstado]);

        return response()->json([
            'message' => $nuevoEstado ? 'Vendedor activado.' : 'Vendedor desactivado.',
            'activo'  => $nuevoEstado,
        ]);
    }

    // ── AJAX: vendedores activos de la sucursal actual (para punto de venta) ─
    public function porSucursal(Request $request)
    {
        $user = auth()->user();

        $personal = Personal::deNegocio($user->id_negocio)
            ->deSucursal($user->id_usuario)
            ->activo()
            ->orderBy('nombre')
            ->get(['id_personal', 'nombre']);

        return response()->json($personal);
    }
}