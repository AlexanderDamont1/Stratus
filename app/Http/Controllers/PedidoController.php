<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller; // ✅ Controller base
use App\Services\CatalogService;
use App\Models\Pedido;
use App\Models\PedidoItem;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PedidoController extends Controller
{
    public function __construct()
    {
        // Aplica middleware Enlace a todo el controladoraaa
        $this->middleware('enlace');
    }

    // ─── LISTAR PEDIDOS ───────────────────────────────────────────────
    public function index(Request $request)
    {
        $page   = $request->input('page', 1);
        $status = $request->input('status', 'all');
        $search = $request->input('search', 'all');

        $cacheKey = "pedidos:index:{$status}:{$search}:page:{$page}";

        $pedidos = Cache::remember($cacheKey, 300, fn() =>
            Pedido::with(['negocio', 'usuario', 'items'])
                ->when($request->input('search'), fn($q, $s) =>
                    $q->where('id_pedido', 'like', "%{$s}%")
                )
                ->when($request->input('status') && $status !== 'all', fn($q) =>
                    $q->where('status', $status)
                )
                ->orderByDesc('created_at')
                ->paginate(10)
                ->withQueryString()
        );

        return view('pedidos.index', compact('pedidos'));
    }

    // ─── CREAR PEDIDO ────────────────────────────────────────────────
    public function create()
    {
        $modelos  = CatalogService::getModelos();
        $negocios = CatalogService::getNegocios();

        $voltajes = [];
        $colores  = [];

        foreach ($modelos as $modelo) {
            $voltajes[$modelo->id_modelo] = CatalogService::getVoltajesByModelo($modelo->id_modelo, true);
            $colores[$modelo->id_modelo]  = CatalogService::getColoresByModelo($modelo->id_modelo, true);
        }

        return view('pedidos.create', compact('modelos', 'negocios', 'voltajes', 'colores'));
    }

    // ─── GUARDAR PEDIDO ──────────────────────────────────────────────
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_negocio'          => 'required|exists:negocios,id_negocio',
            'notas'               => 'nullable|string|max:500',
            'items'               => 'required|array|min:1',
            'items.*.id_modelo'   => 'required|exists:modelos,id_modelo',
            'items.*.id_voltaje'  => 'required|exists:voltajes,id_voltaje',
            'items.*.id_color'    => 'required|exists:colores,id_color',
            'items.*.cantidad'    => 'required|integer|min:1|max:999',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        DB::transaction(function () use ($request) {
            $pedido = Pedido::create([
                'id_negocio' => $request->id_negocio,
                'id_usuario' => auth()->user()->id_usuario,
                'status'     => 1,
                'notas'      => $request->notas,
            ]);

            foreach ($request->items as $item) {
                PedidoItem::create([
                    'id_pedido'  => $pedido->id_pedido,
                    'id_modelo'  => $item['id_modelo'],
                    'id_voltaje' => $item['id_voltaje'],
                    'id_color'   => $item['id_color'],
                    'cantidad'   => $item['cantidad'],
                ]);
            }

            // limpiar cache de listados y stats si aplica
            Cache::forget("pedidos:index:all:all:page:1");
        });

        return redirect()
            ->route('pedidos.index')
            ->with('success', 'Pedido creado correctamente.');
    }

    // ─── MOSTRAR PEDIDO ──────────────────────────────────────────────
    public function show(string $id_pedido)
    {
        $cacheKey = "pedido:{$id_pedido}";

        $pedido = Cache::remember($cacheKey, 300, fn() =>
            Pedido::with(['negocio', 'usuario', 'items.modelo', 'items.voltaje', 'items.color'])
                ->findOrFail($id_pedido)
        );

        return view('pedidos.show', compact('pedido'));
    }

    // ─── ACTUALIZAR STATUS ───────────────────────────────────────────
    public function updateStatus(Request $request, string $id_pedido)
    {
        $pedido = Pedido::findOrFail($id_pedido);

        $request->validate([
            'status' => 'required|in:1,2,3',
        ]);

        $pedido->update(['status' => $request->status]);

        // limpiar cache del pedido individual
        Cache::forget("pedido:{$id_pedido}");

        return back()->with('success', 'Status actualizado correctamente.');
    }

    // ─── ELIMINAR PEDIDO ─────────────────────────────────────────────
    public function destroy(string $id_pedido)
    {
        $pedido = Pedido::findOrFail($id_pedido);

        abort_if($pedido->status > 1, 403, 'No se puede eliminar un pedido que ya fue preparado o entregado.');

        $pedido->delete();

        // limpiar cache del pedido y listados
        Cache::forget("pedido:{$id_pedido}");
        Cache::forget("pedidos:index:all:all:page:1");

        return redirect()
            ->route('pedidos.index')
            ->with('success', 'Pedido eliminado.');
    }
}