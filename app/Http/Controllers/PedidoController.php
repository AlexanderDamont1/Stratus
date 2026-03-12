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
use Barryvdh\DomPDF\Facade\Pdf;
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

        // ─── Calcular canales WebSocket ───────────────────────────────
        $canalesVendedor = [];

        if ($usuario->id_rol === 1) {
            $canalesVendedor = [$usuario->id_usuario];
        } elseif ($usuario->id_rol === 5) {
            $canalesVendedor = Enlace::where('id_usuario2', $usuario->id_usuario)
                ->where('estado', 'activo')
                ->pluck('id_usuario1')
                ->toArray();
        }
        // ─────────────────────────────────────────────────────────────

        if ($usuario->id_rol === 5) {
            $idsUsuario1 = Enlace::where('id_usuario2', $usuario->id_usuario)
                ->where('estado', 'activo')
                ->pluck('id_usuario1');

            $pedidos = Pedido::with(['negocio', 'usuario', 'items.modelo', 'items.voltaje', 'items.color'])
                ->whereIn('id_usuario', $idsUsuario1)
                ->when(
                    $request->input('search'),
                    fn($q, $s) => $q->where('id_pedido', 'like', "%{$s}%")
                )
                ->when(
                    $request->input('status') && $status !== 'all',
                    fn($q) => $q->where('status', $status)
                )
                ->orderByDesc('updated_at')
                ->paginate(8)
                ->withQueryString();

            return view('pedidos.index', compact('pedidos', 'modelos', 'canalesVendedor')); // ← aquí
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
                    fn($q, $s) => $q->where('id_pedido', 'like', "%{$s}%")
                )
                ->when(
                    $request->input('status') && $status !== 'all',
                    fn($q) => $q->where('status', $status)
                )
                ->orderByDesc('created_at')
                ->paginate(10)
                ->withQueryString()
        );

        return view('pedidos.index', compact('pedidos', 'modelos', 'canalesVendedor')); // ← y aquí
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
        $pedido = Pedido::with(['negocio', 'usuario', 'items'])->findOrFail($id_pedido);

        abort_if($pedido->status > 1, 403, 'No se puede eliminar un pedido que ya fue preparado o entregado.');

        $usuarioId = $pedido->id_usuario;
        $pedidoId = $pedido->id_pedido; // Guardar ID antes de eliminar

        $pedido->delete();

        Cache::forget("pedido:{$pedidoId}");
        Cache::forget("pedidos:index:" . auth()->user()->id_usuario . ":all:all:page:1");

      
        event(new PedidoUpdated($pedido, 'deleted'));

        return redirect()
            ->route('pedidos.index')
            ->with('success', 'Pedido eliminado.');
    }




    // ══════════════════════════════════════════════════════════════
    //  AGREGAR en PedidoController.php
    // ══════════════════════════════════════════════════════════════

    // ─── VISTA REALIZAR PEDIDO ────────────────────────────────────
    public function realizar(string $id_pedido)
    {
        $usuario = auth()->user();

        abort_if($usuario->id_rol !== 5, 403);

        $pedido = Pedido::with([
            'usuario',
            'negocio',
            'items.modelo',
            'items.voltaje',
            'items.color',
            'bicicletas.modelo',
            'bicicletas.voltaje',
            'bicicletas.color',
        ])->findOrFail($id_pedido);

        abort_if(!in_array($pedido->status, [1, 2]), 403, 'Este pedido no puede ser realizado.');

        $resumen = [];
        foreach ($pedido->items as $item) {
            $key = $item->id_modelo . '-' . $item->id_voltaje . '-' . $item->id_color;
            $resumen[$key] = [
                'modelo'    => $item->modelo->nombre_modelo ?? 'N/D',
                'voltaje'   => $item->voltaje->voltaje ?? 'N/D',
                'color'     => $item->color->color ?? 'N/D',
                'id_modelo'  => $item->id_modelo,
                'id_voltaje' => $item->id_voltaje,
                'id_color'   => $item->id_color,
                'requerido'  => $item->cantidad,
                'escaneado'  => 0,
            ];
        }

        foreach ($pedido->bicicletas as $bic) {
            $key = $bic->id_modelo . '-' . $bic->id_voltaje . '-' . $bic->id_color;
            if (isset($resumen[$key])) {
                $resumen[$key]['escaneado']++;
            }
        }

        $modelos = \App\Services\CatalogService::getModelos();

        return view('pedidos.realizar', compact('pedido', 'resumen', 'modelos'));
    }

    public function pdf(Request $request, string $id_pedido)
    {
        $pedido = Pedido::with([
            'usuario',
            'negocio',
            'items.modelo',
            'items.voltaje',
            'items.color',
            'bicicletas.modelo',
            'bicicletas.voltaje',
            'bicicletas.color',
        ])->findOrFail($id_pedido);

        // ==> Calcular complementarios 
        $cargadores = [];
        $baterias   = [];

        foreach ($pedido->items as $item) {
            $modelo   = optional($item->modelo)->nombre_modelo ?? '';
            $voltaje  = optional($item->voltaje)->voltaje ?? '';
            $cantidad = $item->cantidad;

            if ($modelo === 'VmpS5') {
                // Siempre 48V/12Ah — 4 baterías de 12V/12Ah
                $cargadores['48V/12Ah']  = ($cargadores['48V/12Ah']  ?? 0) + $cantidad;
                $baterias['12V/12Ah']    = ($baterias['12V/12Ah']    ?? 0) + ($cantidad * 4);
            } else {

                $volts = intval($voltaje);
                $numBaterias = intval($volts / 12);

                // Cargador según voltaje
                if ($volts === 48) {
                    $cargadores['48V/20Ah'] = ($cargadores['48V/20Ah'] ?? 0) + $cantidad;
                } elseif ($volts === 60) {
                    $cargadores['60V']      = ($cargadores['60V']      ?? 0) + $cantidad;
                } elseif ($volts === 72) {
                    $cargadores['72V']      = ($cargadores['72V']      ?? 0) + $cantidad;
                }

                // Baterías siempre 12V/20Ah para los demás modelos
                $baterias['12V/20Ah'] = ($baterias['12V/20Ah'] ?? 0) + ($cantidad * $numBaterias);
            }
        }

        // ─── Lotes de batería desde query params ──────────────────────
        $lotesRaw = $request->input('lotes', []);
        $lotes = [];
        foreach ($lotesRaw as $idx => $valor) {
            $lotes[(int)$idx] = $valor;
        }
        // ─────────────────────────────────────────────────────────────

        $html = view('pedidos.pdf_create', compact('pedido', 'cargadores', 'baterias', 'lotes'))->render();


        $mpdf = new \Mpdf\Mpdf(['mode' => 'utf-8', 'format' => 'A4']);
        $mpdf->WriteHTML($html);

        return response($mpdf->Output("emision_{$pedido->id_pedido}.pdf", 'S'))
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'inline; filename="emision_' . $pedido->id_pedido . '.pdf"');
    }


    public function completarEntrega(Request $request, string $id_pedido)
    {
        $usuario = auth()->user();

        abort_if($usuario->id_rol !== 5, 403);

        $request->validate([
            'token' => 'required|string|size:10',
        ]);

        $pedido = Pedido::with(['bicicletas'])->findOrFail($id_pedido);

        abort_if($pedido->status !== 3, 422, 'El pedido no está listo para entregar.');

        $tokenRecord = \App\Models\PedidoToken::where('id_pedido', $id_pedido)
            ->where('id_usuario2', $usuario->id_usuario)
            ->where('token', strtoupper($request->token))
            ->where('estado', 0)
            ->first();

        if (!$tokenRecord) {
            return response()->json([
                'ok'      => false,
                'mensaje' => 'Token inválido o ya utilizado.',
            ], 422);
        }

        DB::transaction(function () use ($pedido, $tokenRecord) {
            // 1. Cambiar status del pedido a 4
            $pedido->update(['status' => 4]);

            // 2. Cambiar id_negocio de todas las bicicletas al negocio del vendedor
            \App\Models\Bicicleta::where('id_pedido', $pedido->id_pedido)
                ->update(['id_negocio' => $pedido->id_negocio]);

            // 3. Eliminar el token
            $tokenRecord->delete();

            // 4. Limpiar cache
            Cache::forget("pedido:{$pedido->id_pedido}");
            Cache::forget("pedidos:index:{$pedido->id_usuario}:all:all:page:1");

            // ── NUEVO: limpiar cache de bicicletas de ambos negocios ──
            $idNegocioGestor   = auth()->user()->id_negocio;
            $idNegocioVendedor = $pedido->id_negocio;

            \App\Services\CatalogService::clearBicicletaCache($idNegocioGestor);
            \App\Services\CatalogService::clearBicicletaCache($idNegocioVendedor);

            // 5. Disparar evento WebSocket
            $pedidoFresh = Pedido::with([
                'negocio',
                'usuario',
                'items.modelo',
                'items.voltaje',
                'items.color',
            ])->find($pedido->id_pedido);

            if ($pedidoFresh && $pedidoFresh->id_usuario) {
                event(new PedidoUpdated($pedidoFresh, 'updated'));
            }
        });

        return response()->json([
            'ok'      => true,
            'mensaje' => 'Pedido entregado correctamente.',
        ]);
    }

    public function token(string $id_pedido)
    {
        $usuario = auth()->user();

        abort_if($usuario->id_rol !== 1, 403);

        $pedido = Pedido::findOrFail($id_pedido);

        abort_if($pedido->id_usuario !== $usuario->id_usuario, 403);
        abort_if($pedido->status !== 3, 422, 'El pedido no está listo para entregar.');

        $tokenRecord = \App\Models\PedidoToken::where('id_pedido', $id_pedido)
            ->where('id_usuario1', $usuario->id_usuario)
            ->where('estado', 0)
            ->first();

        if (!$tokenRecord) {
            return response()->json([
                'ok'      => false,
                'mensaje' => 'Token no encontrado para este pedido.',
            ], 404);
        }

        return response()->json([
            'ok'    => true,
            'token' => $tokenRecord->token,
        ]);
    }
}
