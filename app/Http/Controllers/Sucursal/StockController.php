<?php

//Stck de piezas para diagnóstico y reparación


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

        $config         = CatalogService::getConfigNegocio($user->id_negocio);
        $metodosActivos = $config['metodos_pago'] ?? ['efectivo'];

        $todasOpciones = \App\Models\NegocioConfig::where('clave', 'metodos_pago')
            ->where('activo', true)
            ->value('opciones');

        $opcionesMap = collect($todasOpciones ?? [])->keyBy('value');

        $metodos = collect($metodosActivos)->map(function ($value) use ($opcionesMap) {
            $opcion = $opcionesMap[$value] ?? null;
            return [
                'value'               => $value,
                'label'               => $opcion['label']               ?? $value,
                'es_efectivo'         => $opcion['es_efectivo']         ?? false,
                'requiere_referencia' => $opcion['requiere_referencia'] ?? false,
            ];
        })->values();

        return view('vendedor.stock_piezas.index', compact('items', 'categorias', 'metodos'));
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

    // ── Venta directa de pieza suelta (sin OT) ─────────────────────────────────

    public function vender(Request $request, string $id)
    {
        $validated = $request->validate([
            'cantidad'           => 'required|integer|min:1',
            'cliente_nombre'     => 'nullable|string|max:120',
            'cliente_telefono'   => 'nullable|string|max:20',
            'pagos'              => 'required|array|min:1',
            'pagos.*.metodo'     => 'required|string|max:40',
            'pagos.*.monto'      => 'required|numeric|min:0.01',
            'pagos.*.referencia' => 'nullable|string|max:20',
        ]);

        $user  = Auth::user();
        $pieza = PiezaCatalogo::where('id_negocio', $user->id_negocio)->findOrFail($id);

        $venta = StockService::venderSuelta(
            pieza:            $pieza,
            cantidad:         (int) $validated['cantidad'],
            clienteNombre:    $validated['cliente_nombre']   ?? null,
            clienteTelefono:  $validated['cliente_telefono'] ?? null,
            pagos:            $validated['pagos'],
            idUsuario:        $user->id_usuario,
            idNegocio:        $user->id_negocio,
        );

        return response()->json([
            'ok'       => true,
            'id_venta' => $venta->id_venta,
            'mensaje'  => "Venta {$venta->id_venta} registrada correctamente.",
        ], 201);
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