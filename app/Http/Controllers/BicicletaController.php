<?php

namespace App\Http\Controllers;

use App\Models\Bicicleta;
use App\Services\CatalogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BicicletaController extends Controller
{
    /* =====================================================
     | INDEX
     ===================================================== */
    public function index(Request $request)
    {
        $user = auth()->user();

        $query = Bicicleta::with(['negocio', 'modelo', 'voltaje', 'color']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('num_serie', 'like', "%{$search}%")
                    ->orWhere('status', 'like', "%{$search}%");
            });
        }

        if ($request->filled('id_negocio')) {
            $query->where('id_negocio', $request->id_negocio);
        }

        $bicicletas = $query->orderByDesc('created_at')->paginate(10);

        return view('gestor.Vehiculos.Bicicleta.index', [
            'bicicletas' => $bicicletas,
            'negocios'   => CatalogService::getNegocios(),   // ← cacheado
            'negocio'    => $user->negocio,
            'modelos'    => CatalogService::getModelos(),    // ← cacheado
        ]);
    }

    /* =====================================================
     | CREATE
     ===================================================== */
    public function create()
    {
        $user = auth()->user();

        if (!$user) {
            abort(401, 'Sesión no válida.');
        }

        if (!$user->id_negocio) {
            abort(403, 'No tienes un negocio asignado.');
        }

        return view('gestor.Vehiculos.Bicicleta.create', [
            'negocio' => $user->negocio,
            'modelos' => CatalogService::getModelos(),       // ← cacheado
        ]);
    }

    /* =====================================================
     | STORE
     ===================================================== */
   public function store(Request $request)
{
    $validator = Validator::make($request->all(), [
        'num_serie'  => 'required|string|size:17|unique:bicicletas,num_serie',
        'id_modelo'  => 'required|exists:modelos,id_modelo',
        'id_voltaje' => 'required|exists:voltajes,id_voltaje',
        'id_color'   => 'required|exists:colores,id_color',
        'id_pedido'  => 'nullable|exists:pedidos,id_pedido',
    ]);

    if ($validator->fails()) {
        if ($request->expectsJson()) {
            return response()->json([
                'ok'      => false,
                'mensaje' => $validator->errors()->first(),
            ], 422);
        }
        return back()->withErrors($validator)->withInput();
    }

    // ← AQUÍ, FUERA del if($validator->fails())
    if ($request->id_pedido) {
        $pedido = \App\Models\Pedido::with(['items', 'bicicletas'])->find($request->id_pedido);

        if ($pedido) {
            $item = $pedido->items->first(fn($i) =>
                $i->id_modelo  == $request->id_modelo &&
                $i->id_voltaje == $request->id_voltaje &&
                $i->id_color   == $request->id_color
            );

            if (!$item) {
                return response()->json([
                    'ok'      => false,
                    'mensaje' => 'Esta combinación no está en el pedido.',
                ], 422);
            }

            $yaEscaneadas = \App\Models\Bicicleta::where('id_pedido',  $request->id_pedido)
                ->where('id_modelo',  $request->id_modelo)
                ->where('id_voltaje', $request->id_voltaje)
                ->where('id_color',   $request->id_color)
                ->count();

            if ($yaEscaneadas >= $item->cantidad) {
                return response()->json([
                    'ok'      => false,
                    'mensaje' => "Ya se completaron las {$item->cantidad} unidades requeridas para esta combinación.",
                ], 422);
            }
        }
    }

    $bicicleta = Bicicleta::create([
        'num_serie'  => strtoupper($request->num_serie),
        'id_negocio' => auth()->user()->id_negocio,
        'id_modelo'  => $request->id_modelo,
        'id_voltaje' => $request->id_voltaje,
        'id_color'   => $request->id_color,
        'id_pedido'  => $request->id_pedido ?? null,
        'status'     => 'STOCK',
    ]);

    if ($request->expectsJson()) {
        return response()->json([
            'ok'        => true,
            'bicicleta' => $bicicleta,
        ]);
    }

    return redirect()
        ->route('gestor.vehiculos.bicicletas.index')
        ->with('success', 'Bicicleta registrada correctamente.');
}
    /* =====================================================
     | SHOW
     ===================================================== */
    public function show(string $num_serie)
    {
        $bicicleta = Bicicleta::with([
            'negocio',
            'modelo',
            'voltaje',
            'color',
            'mantenimientos',
        ])->where('num_serie', $num_serie)->firstOrFail();

        return view('gestor.Vehiculos.Bicicleta.show', compact('bicicleta'));
    }

    /* =====================================================
     | EDIT
     ===================================================== */
    public function edit(string $num_serie)
    {
        $bicicleta = Bicicleta::where('num_serie', $num_serie)->firstOrFail();

        // Cargamos voltajes y colores filtrados por el modelo actual
        $datosModelo = CatalogService::getModeloCompleto($bicicleta->id_modelo);

        return view('gestor.Vehiculos.Bicicleta.edit', [
            'bicicleta' => $bicicleta,
            'modelos'   => CatalogService::getModelos(),     // ← cacheado
            'voltajes'  => $datosModelo['voltajes'],         // ← cacheado
            'colores'   => $datosModelo['colores'],          // ← cacheado
        ]);
    }

    /* =====================================================
     | UPDATE
     ===================================================== */
    public function update(Request $request, string $num_serie)
    {
        $bicicleta = Bicicleta::where('num_serie', $num_serie)->firstOrFail();

        $validator = Validator::make($request->all(), [
            'id_modelo'  => 'required|exists:modelos,id_modelo',
            'id_voltaje' => 'required|exists:voltajes,id_voltaje',
            'id_color'   => 'required|exists:colores,id_color',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $bicicleta->update([
            'id_modelo'  => $request->id_modelo,
            'id_voltaje' => $request->id_voltaje,
            'id_color'   => $request->id_color,
        ]);

        return redirect()
            ->route('gestor.vehiculos.bicicletas.index')
            ->with('success', 'Bicicleta actualizada correctamente.');
    }

    /* =====================================================
     | DESTROY
     ===================================================== */
    public function destroy(string $num_serie)
    {
        $bicicleta = Bicicleta::where('num_serie', $num_serie)->firstOrFail();

        if ($bicicleta->mantenimientos()->count() > 0) {
            return back()->with('error', 'No se puede eliminar: tiene mantenimientos.');
        }

        $bicicleta->delete();

        return redirect()
            ->route('gestor.vehiculos.bicicletas.index')
            ->with('success', 'Bicicleta eliminada.');
    }

    /* =====================================================
     | UPDATE STATUS (AJAX)
     ===================================================== */
    public function updateStatus(Request $request, string $num_serie)
    {
        $bicicleta = Bicicleta::where('num_serie', $num_serie)->firstOrFail();

        $request->validate([
            'status' => 'required|in:disponible,en_mantenimiento,prestado,danada',
        ]);

        $bicicleta->update(['status' => $request->status]);

        return response()->json([
            'success' => true,
            'status'  => $bicicleta->status,
        ]);
    }

    /* =====================================================
     | GET BY CLIENTE
     ===================================================== */
    public function getByCliente(string $id_cliente)
    {
        return Bicicleta::with(['modelo', 'color'])
            ->where('id_cliente', $id_cliente)
            ->where('status', '!=', 'danada')
            ->get();
    }

    /* =====================================================
     | AJAX: COLORES POR MODELO
     ===================================================== */
    public function coloresPorModelo(string $id_modelo)
    {
        return response()->json(
            CatalogService::getColoresByModelo($id_modelo)
        );
    }



    // ══════════════════════════════════════════════════════════════
    //  AGREGAR en BicicletaController.php
    // ══════════════════════════════════════════════════════════════

    // ─── API: BUSCAR BICICLETA POR NUM_SERIE ─────────────────────
    public function showApi(string $num_serie)
    {
        $bicicleta = Bicicleta::with(['modelo', 'voltaje', 'color', 'negocio'])
            ->where('num_serie', $num_serie)
            ->first();

        if (!$bicicleta) {
            return response()->json(['encontrada' => false], 404);
        }

        return response()->json([
            'encontrada'  => true,
            'num_serie'   => $bicicleta->num_serie,
            'id_modelo'   => $bicicleta->id_modelo,
            'id_voltaje'  => $bicicleta->id_voltaje,
            'id_color'    => $bicicleta->id_color,
            'modelo'      => $bicicleta->modelo->nombre_modelo ?? 'N/D',
            'voltaje'     => $bicicleta->voltaje->tipo_voltaje  ?? 'N/D',
            'color'       => $bicicleta->color->nombre_color   ?? 'N/D',
            'id_pedido'   => $bicicleta->id_pedido,
            'status'      => $bicicleta->status,
        ]);
    }
}
