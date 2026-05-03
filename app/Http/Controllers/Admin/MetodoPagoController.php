<?php
// ══════════════════════════════════════════════════════════════
// app/Http/Controllers/Admin/MetodoPagoController.php
// ══════════════════════════════════════════════════════════════
namespace App\Http\Controllers\Admin;
 
use App\Http\Controllers\Controller;
use App\Models\MetodoPago;
use Illuminate\Http\Request;
 
class MetodoPagoController extends Controller
{
    public function index()
    {
        $user    = auth()->user();
        $metodos = MetodoPago::deNegocio($user->id_negocio)->get();
        return view('administrador.metodos_pago.index', compact('metodos'));
    }
 
    public function store(Request $request)
    {
        $user = auth()->user();
 
        $request->validate([
            'nombre'              => 'required|string|max:60',
            'es_efectivo'         => 'boolean',
            'requiere_referencia' => 'boolean',
        ]);
 
        // Solo puede haber un método marcado como efectivo
        if ($request->boolean('es_efectivo')) {
            $yaExiste = MetodoPago::deNegocio($user->id_negocio)
                ->where('es_efectivo', true)
                ->exists();
            if ($yaExiste) {
                return back()->withErrors(['es_efectivo' => 'Ya existe un método de efectivo.']);
            }
        }
 
        $orden = MetodoPago::deNegocio($user->id_negocio)->count();
 
        MetodoPago::create([
            'id_negocio'          => $user->id_negocio,
            'nombre'              => $request->nombre,
            'es_efectivo'         => $request->boolean('es_efectivo'),
            'requiere_referencia' => $request->boolean('requiere_referencia'),
            'activo'              => true,
            'orden'               => $orden,
        ]);
 
        return back()->with('success', 'Método de pago creado.');
    }
 
    public function update(Request $request, string $id)
    {
        $user   = auth()->user();
        $metodo = MetodoPago::deNegocio($user->id_negocio)->findOrFail($id);
 
        $request->validate([
            'nombre'              => 'required|string|max:60',
            'es_efectivo'         => 'boolean',
            'requiere_referencia' => 'boolean',
            'activo'              => 'boolean',
        ]);
 
        if ($request->boolean('es_efectivo') && !$metodo->es_efectivo) {
            $yaExiste = MetodoPago::deNegocio($user->id_negocio)
                ->where('es_efectivo', true)
                ->where('id_metodo', '!=', $id)
                ->exists();
            if ($yaExiste) {
                return back()->withErrors(['es_efectivo' => 'Ya existe un método de efectivo.']);
            }
        }
 
        $metodo->update($request->only([
            'nombre', 'es_efectivo', 'requiere_referencia', 'activo',
        ]));
 
        return back()->with('success', 'Método actualizado.');
    }
 
    public function reordenar(Request $request)
    {
        $user = auth()->user();
        $request->validate(['orden' => 'required|array']);
 
        foreach ($request->orden as $pos => $id) {
            MetodoPago::deNegocio($user->id_negocio)
                ->where('id_metodo', $id)
                ->update(['orden' => $pos]);
        }
 
        return response()->json(['ok' => true]);
    }
 
    public function destroy(string $id)
    {
        $user   = auth()->user();
        $metodo = MetodoPago::deNegocio($user->id_negocio)->findOrFail($id);
        $metodo->update(['activo' => false]);
        return back()->with('success', 'Método desactivado.');
    }
}
 







