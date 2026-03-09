<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\CatalogService;
use App\Models\Pedido;
use App\Models\PedidoItem;
use App\Models\Enlace;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Events\PedidoUpdated; // ← IMPORTANTE: Agregar esta línea

class PedidoController extends Controller
{
    public function __construct()
    {
        $this->middleware('enlace');
    }

    // ─── LISTAR PEDIDOS ───────────────────────────────────────────────
    public function index(Request $request)
    {
        $usuario = auth()->user();
        $page    = $request->input('page', 1);
        $status  = $request->input('status', 'all');
        $search  = $request->input('search', 'all');

        $modelos = CatalogService::getModelos();

        if ($usuario->id_rol === 5) {
            $idsUsuario1 = Enlace::where('id_usuario2', $usuario->id_usuario)
                ->where('estado', 'activo')
                ->pluck('id_usuario1');

            $pedidos = Pedido::with(['negocio', 'usuario', 'items.modelo', 'items.voltaje', 'items.color'])
                ->whereIn('id_usuario', $idsUsuario1)
                ->when(
                    $request->input('search'),
                    fn($q, $s) =>
                    $q->where('id_pedido', 'like', "%{$s}%")
                )
                ->when(
                    $request->input('status') && $status !== 'all',
                    fn($q) =>
                    $q->where('status', $status)
                )
                ->orderByDesc('created_at')
                ->paginate(10)
                ->withQueryString();

            return view('pedidos.index', compact('pedidos', 'modelos'));
        }

        $cacheKey = "pedidos:index:{$usuario->id_usuario}:{$status}:{$search}:page:{$page}";

        $pedidos = Cache::remember(
            $cacheKey,
            300,
            fn() =>
            Pedido::with(['negocio', 'usuario', 'items.modelo', 'items.voltaje', 'items.color'])
                ->where('id_usuario', $usuario->id_usuario)
                ->when(
                    $request->input('search'),
                    fn($q, $s) =>
                    $q->where('id_pedido', 'like', "%{$s}%")
                )
                ->when(
                    $request->input('status') && $status !== 'all',
                    fn($q) =>
                    $q->where('status', $status)
                )
                ->orderByDesc('created_at')
                ->paginate(10)
                ->withQueryString()
        );

        return view('pedidos.index', compact('pedidos', 'modelos'));
    }

    // ─── CREAR PEDIDO ────────────────────────────────────────────────
    public function create()
    {
        $modelos = CatalogService::getModelos();

        $voltajes = [];
        $colores  = [];

        foreach ($modelos as $modelo) {
            $voltajes[$modelo->id_modelo] = CatalogService::getVoltajesByModelo($modelo->id_modelo, true);
            $colores[$modelo->id_modelo]  = CatalogService::getColoresByModelo($modelo->id_modelo, true);
        }

        return view('pedidos.create', compact('modelos', 'voltajes', 'colores'));
    }

    // ─── GUARDAR PEDIDO ──────────────────────────────────────────────
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
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

        $usuario = auth()->user();
        $nuevoPedidoId = null;

        DB::transaction(function () use ($request, $usuario, &$nuevoPedidoId) {
            $pedido = Pedido::create([
                'id_negocio' => $usuario->id_negocio,
                'id_usuario' => $usuario->id_usuario,
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

            Cache::forget("pedidos:index:{$usuario->id_usuario}:all:all:page:1");

            // Guardar el ID del pedido creado
            $nuevoPedidoId = $pedido->id_pedido;

            // Disparar evento WebSocket para pedido creado
            // Disparar evento WebSocket para pedido creado
            event(
                new PedidoUpdated(
                    $pedido->fresh(['negocio', 'usuario', 'items.modelo', 'items.voltaje', 'items.color']),
                    'created'
                )
            );
        });

        return redirect()
            ->route('pedidos.index')
            ->with('success', 'Pedido creado correctamente.')
            ->with('nuevo_pedido_id', $nuevoPedidoId); // Pasar el ID del nuevo pedido
    }

    // ─── MOSTRAR PEDIDO ──────────────────────────────────────────────
    public function show(string $id_pedido)
    {
        $cacheKey = "pedido:{$id_pedido}";

        $pedido = Cache::remember(
            $cacheKey,
            300,
            fn() =>
            Pedido::with(['negocio', 'usuario', 'items.modelo', 'items.voltaje', 'items.color'])
                ->findOrFail($id_pedido)
        );

        return view('pedidos.show', compact('pedido'));
    }

    // ─── EDITAR PEDIDO ───────────────────────────────────────────────
    public function edit(string $id_pedido)
    {
        $usuario = auth()->user();

        $pedido = Pedido::with(['items.modelo', 'items.voltaje', 'items.color'])
            ->findOrFail($id_pedido);

        abort_if($usuario->id_rol !== 1, 403);
        abort_if($pedido->id_usuario !== $usuario->id_usuario, 403);
        abort_if($pedido->status !== 1, 403, 'Solo se puede editar un pedido en estado Solicitado.');

        $modelos = CatalogService::getModelos();

        return view('pedidos.edit', compact('pedido', 'modelos'));
    }

    // ─── ACTUALIZAR PEDIDO ───────────────────────────────────────────
    public function update(Request $request, string $id_pedido)
    {
        $usuario = auth()->user();

        $pedido = Pedido::findOrFail($id_pedido);

        abort_if($usuario->id_rol !== 1, 403);
        abort_if($pedido->id_usuario !== $usuario->id_usuario, 403);
        abort_if($pedido->status !== 1, 403, 'Solo se puede editar un pedido en estado Solicitado.');

        $validator = Validator::make($request->all(), [
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

        DB::transaction(function () use ($request, $pedido, $usuario) {
            $oldStatus = $pedido->status; // Guardar status anterior
            $pedido->update(['notas' => $request->notas]);

            // Elimina todos los items y los reemplaza
            $pedido->items()->delete();

            foreach ($request->items as $item) {
                PedidoItem::create([
                    'id_pedido'  => $pedido->id_pedido,
                    'id_modelo'  => $item['id_modelo'],
                    'id_voltaje' => $item['id_voltaje'],
                    'id_color'   => $item['id_color'],
                    'cantidad'   => $item['cantidad'],
                ]);
            }

            Cache::forget("pedido:{$pedido->id_pedido}");
            Cache::forget("pedidos:index:{$usuario->id_usuario}:all:all:page:1");

            // 🔥 NUEVO: Disparar evento WebSocket para pedido actualizado
            event(new PedidoUpdated($pedido->fresh(['negocio', 'usuario', 'items.modelo', 'items.voltaje', 'items.color']), 'updated'));
        });

        return redirect()
            ->route('pedidos.index')
            ->with('success', 'Pedido actualizado correctamente.');
    }

    // ─── ACTUALIZAR STATUS ───────────────────────────────────────────
    public function updateStatus(Request $request, string $id_pedido)
    {
        $pedido = Pedido::findOrFail($id_pedido);

        $request->validate([
            'status' => 'required|in:1,2,3',
        ]);

        $oldStatus = $pedido->status;
        $pedido->update(['status' => $request->status]);

        Cache::forget("pedido:{$id_pedido}");
        Cache::forget("pedidos:index:" . auth()->user()->id_usuario . ":all:all:page:1");

        // 🔥 NUEVO: Disparar evento WebSocket para cambio de status
        event(new PedidoUpdated($pedido->fresh(['negocio', 'usuario', 'items.modelo', 'items.voltaje', 'items.color']), 'updated'));

        return back()->with('success', 'Status actualizado correctamente.');
    }

    // ─── ELIMINAR PEDIDO ─────────────────────────────────────────────
    public function destroy(string $id_pedido)
    {
        $pedido = Pedido::findOrFail($id_pedido);

        abort_if($pedido->status > 1, 403, 'No se puede eliminar un pedido que ya fue preparado o entregado.');

        $usuario = auth()->user();
        $pedidoId = $pedido->id_pedido; // Guardar ID antes de eliminar

        $pedido->delete();

        Cache::forget("pedido:{$pedidoId}");
        Cache::forget("pedidos:index:" . auth()->user()->id_usuario . ":all:all:page:1");

        // 🔥 NUEVO: Disparar evento WebSocket para pedido eliminado
        // Nota: Usamos un objeto simple ya que el pedido ya no existe en BD
        event(new PedidoUpdated(new Pedido(['id_pedido' => $pedidoId]), 'deleted'));

        return redirect()
            ->route('pedidos.index')
            ->with('success', 'Pedido eliminado.');
    }
}
