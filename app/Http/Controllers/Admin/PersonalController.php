<?php
// ══════════════════════════════════════════════════════════════
// app/Http/Controllers/Admin/PersonalController.php
// ══════════════════════════════════════════════════════════════
namespace App\Http\Controllers\Admin;
 
use App\Http\Controllers\Controller;
use App\Models\Personal;
use App\Models\Usuario;
use Illuminate\Http\Request;
 
class PersonalController extends Controller
{
    private function sucursales($idNegocio)
    {
        return Usuario::where('id_negocio', $idNegocio)
            ->where('id_rol', 2)
            ->orderBy('nombre_usuario')
            ->get();
    }
 
    public function index()
    {
        $user      = auth()->user();
        $personal  = Personal::with('sucursal')
            ->deNegocio($user->id_negocio)
            ->orderBy('nombre')
            ->paginate(20);
 
        return view('admin.personal.index', compact('personal'));
    }
 
    public function create()
    {
        $user      = auth()->user();
        $sucursales = $this->sucursales($user->id_negocio);
        return view('admin.personal.create', compact('sucursales'));
    }
 
    public function store(Request $request)
    {
        $user = auth()->user();
 
        $request->validate([
            'nombre'     => 'required|string|max:120',
            'sucursales' => 'required|array|min:1',
            'sucursales.*' => 'exists:usuarios,id_usuario',
        ]);
 
        // Un vendedor puede estar en varias sucursales → un registro por sucursal
        foreach ($request->sucursales as $idUsuario) {
            Personal::create([
                'id_usuario' => $idUsuario,
                'id_negocio' => $user->id_negocio,
                'nombre'     => $request->nombre,
                'activo'     => true,
            ]);
        }
 
        return redirect()->route('admin.personal.index')
            ->with('success', 'Vendedor registrado correctamente.');
    }
 
    public function edit(string $id)
    {
        $user     = auth()->user();
        $personal = Personal::deNegocio($user->id_negocio)->findOrFail($id);
        $sucursales = $this->sucursales($user->id_negocio);
 
        // IDs de sucursales donde ya está asignado (mismo nombre, mismo negocio)
        $asignadas = Personal::deNegocio($user->id_negocio)
            ->where('nombre', $personal->nombre)
            ->pluck('id_usuario')
            ->toArray();
 
        return view('admin.personal.edit', compact('personal', 'sucursales', 'asignadas'));
    }
 
    public function update(Request $request, string $id)
    {
        $user     = auth()->user();
        $personal = Personal::deNegocio($user->id_negocio)->findOrFail($id);
 
        $request->validate([
            'nombre' => 'required|string|max:120',
            'activo' => 'boolean',
        ]);
 
        // Actualizar solo nombre y activo del registro específico
        $personal->update([
            'nombre' => $request->nombre,
            'activo' => $request->boolean('activo', true),
        ]);
 
        return redirect()->route('admin.personal.index')
            ->with('success', 'Vendedor actualizado.');
    }
 
    public function destroy(string $id)
    {
        $user     = auth()->user();
        $personal = Personal::deNegocio($user->id_negocio)->findOrFail($id);
        $personal->update(['activo' => false]); // soft disable, no borrar
 
        return back()->with('success', 'Vendedor desactivado.');
    }
 
    // AJAX — vendedores activos de una sucursal (para el select en venta)
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