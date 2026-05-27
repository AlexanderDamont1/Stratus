<?php

namespace App\Http\Controllers\Sucursal;

use App\Http\Controllers\Controller;
use App\Services\CatalogService;
use App\Services\StockService;
use App\Models\PiezaCatalogo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StockController extends Controller
{
    // ── Listado ───────────────────────────────────────────────────────────────

    public function index(Request $request)
    {
        $user      = Auth::user();
        $busqueda  = $request->query('q');
        $categoria = $request->query('categoria');
        $bajo      = (bool) $request->query('stock_bajo', false);
        $page      = (int)  $request->query('page', 1);

        $items      = StockService::listar($user->id_negocio, $page, $busqueda, $categoria, $bajo);
        $categorias = StockService::categorias($user->id_negocio);

        if ($request->wantsJson()) {
            return response()->json(['ok' => true, 'data' => $items, 'categorias' => $categorias]);
        }

        return view('vendedor.stock_piezas.index', compact('items', 'categorias'));
    }

    // ── Crear ─────────────────────────────────────────────────────────────────

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre'               => 'required|string|max:120',
            'clave'                => 'required|string|max:40',
            'categoria'            => 'nullable|string|max:60',
            'marca_pieza'          => 'nullable|string|max:80',
            'modelos_compatibles'  => 'nullable|array',
            'modelos_compatibles.*'=> 'string',
            'voltaje_compatible'   => 'nullable|string|max:20',
            'descripcion'          => 'nullable|string|max:1000',
            'precio_costo'         => 'nullable|numeric|min:0',
            'precio_venta'         => 'nullable|numeric|min:0',
            'stock_inicial'        => 'nullable|integer|min:0',
            'stock_minimo'         => 'nullable|integer|min:0',
            'serializable'         => 'boolean',
        ]);

        $user = Auth::user();
        $validated['id_usuario'] = $user->id_usuario;

        $pieza = StockService::crear($validated, $user->id_negocio);

        return response()->json([
            'ok'       => true,
            'id_pieza' => $pieza->id_pieza,
            'mensaje'  => "Pieza {$pieza->nombre} creada correctamente.",
        ], 201);
    }

    // ── Editar ────────────────────────────────────────────────────────────────

    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'nombre'               => 'required|string|max:120',
            'clave'                => 'required|string|max:40',
            'categoria'            => 'nullable|string|max:60',
            'marca_pieza'          => 'nullable|string|max:80',
            'modelos_compatibles'  => 'nullable|array',
            'modelos_compatibles.*'=> 'string',
            'voltaje_compatible'   => 'nullable|string|max:20',
            'descripcion'          => 'nullable|string|max:1000',
            'precio_costo'         => 'nullable|numeric|min:0',
            'precio_venta'         => 'nullable|numeric|min:0',
            'stock_minimo'         => 'nullable|integer|min:0',
            'serializable'         => 'boolean',
        ]);

        $user  = Auth::user();
        $pieza = PiezaCatalogo::where('id_negocio', $user->id_negocio)->findOrFail($id);

        $pieza = StockService::editar($pieza, $validated);

        return response()->json([
            'ok'      => true,
            'mensaje' => "Pieza actualizada.",
            'data'    => $pieza,
        ]);
    }

    // ── Entrada de stock ──────────────────────────────────────────────────────

    public function entrada(Request $request, string $id)
    {
        $validated = $request->validate([
            'cantidad' => 'required|integer|min:1',
            'nota'     => 'nullable|string|max:255',
        ]);

        $user  = Auth::user();
        $pieza = PiezaCatalogo::where('id_negocio', $user->id_negocio)->findOrFail($id);

        $mov = StockService::registrarEntrada(
            pieza:    $pieza,
            cantidad: (int) $validated['cantidad'],
            idUsuario: $user->id_usuario,
            nota:     $validated['nota'] ?? null,
        );

        $stockActual = $pieza->fresh()->stock_actual;

        return response()->json([
            'ok'            => true,
            'stock_actual'  => $stockActual,
            'id_movimiento' => $mov->id_movimiento,
            'mensaje'       => "Entrada registrada. Stock actual: {$stockActual}",
        ]);
    }

    // ── Historial ─────────────────────────────────────────────────────────────

    public function historial(Request $request, string $id)
    {
        $user = Auth::user();
        $page = (int) $request->query('page', 1);

        PiezaCatalogo::where('id_negocio', $user->id_negocio)->findOrFail($id);

        $historial = StockService::historial($id, $user->id_negocio, $page);

        return response()->json(['ok' => true, 'data' => $historial]);
    }

    // ── Buscar para diagnóstico ───────────────────────────────────────────────

    public function buscarParaDiagnostico(Request $request)
    {
        $request->validate([
            'q'         => 'required|string|min:2|max:80',
            'id_modelo' => 'nullable|string',
        ]);

        $user   = Auth::user();
        $piezas = StockService::buscarParaDiagnostico(
            idNegocio: $user->id_negocio,
            busqueda:  $request->q,
            idModelo:  $request->id_modelo,
        );

        return response()->json(['ok' => true, 'data' => $piezas]);
    }

    // ── Modelos para selector ─────────────────────────────────────────────────

    public function modelos()
    {
        $user = Auth::user();

        return response()->json([
            'ok'   => true,
            'data' => CatalogService::getModelosParaSelectorPiezas($user->id_negocio),
        ]);
    }
}