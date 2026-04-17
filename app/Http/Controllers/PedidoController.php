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
use Illuminate\Validation\Rule;
use App\Events\PedidoUpdated;
use App\Events\BicicletaActualizada;
use App\Services\BicicletaMovimientoService;


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

        // ✅ Pedidos siempre usan atributos públicos (id_negocio = null)
        $modelos = CatalogService::getModelos();

        $canalesVendedor = [];

        if ($usuario->id_rol === 1) {
            $canalesVendedor = [$usuario->id_usuario];
        } elseif ($usuario->id_rol === 5) {
            $canalesVendedor = Enlace::where('id_usuario2', $usuario->id_usuario)
                ->where('estado', 'activo')
                ->pluck('id_usuario1')
                ->toArray();
        }

        if ($usuario->id_rol === 5) {
            $idsUsuario1 = Enlace::where('id_usuario2', $usuario->id_usuario)
                ->where('estado', 'activo')
                ->pluck('id_usuario1');

            $pedidos = Pedido::with(['negocio', 'usuario', 'items.modelo', 'items.voltaje', 'items.color'])
                ->whereIn('id_usuario', $idsUsuario1)
                ->when($search !== 'all', fn($q) => $q->where('id_pedido', 'like', "%{$search}%"))
                ->when($status !== 'all', fn($q) => $q->where('status', $status))
                ->orderByDesc('updated_at')
                ->paginate(8)
                ->withQueryString();

            return view('pedidos.index', compact('pedidos', 'modelos', 'canalesVendedor'));
        }

        $cacheKey = "pedidos:index:{$usuario->id_usuario}:{$status}:{$search}:page:{$page}";

        $pedidos = Cache::remember($cacheKey, 300, fn() =>
            Pedido::with(['negocio', 'usuario', 'items.modelo', 'items.voltaje', 'items.color'])
                ->where('id_usuario', $usuario->id_usuario)
                ->when($search !== 'all', fn($q) => $q->where('id_pedido', 'like', "%{$search}%"))
                ->when($status !== 'all', fn($q) => $q->where('status', $status))
                ->orderByDesc('created_at')
                ->paginate(10)
                ->withQueryString()
        );

        return view('pedidos.index', compact('pedidos', 'modelos', 'canalesVendedor'));
    }

    // ─── CREAR PEDIDO ────────────────────────────────────────────────
   public function create()
    {
        // Bloqueo directo: Si el rol no es 1, lanza un 403 (Prohibido)
        if (auth()->user()->id_rol !== 1) {
            abort(403, 'No tienes permisos para crear pedidos.');
        }

        $usuario = auth()->user();
        
        $modelos  = CatalogService::getModelos();
        $voltajes = [];
        $colores  = [];

        foreach ($modelos as $modelo) {
            $voltajes[$modelo->id_modelo] = CatalogService::getVoltajesByModelo($modelo->id_modelo, null, true);
            $colores[$modelo->id_modelo]  = CatalogService::getColoresByModelo($modelo->id_modelo, null, true);
        }

        return view('pedidos.create', compact('modelos', 'voltajes', 'colores'));
    }

    // ─── GUARDAR PEDIDO ──────────────────────────────────────────────
    public function store(Request $request)
    {
        $usuario   = auth()->user();

        $validator = Validator::make($request->all(), [
            'notas'               => 'nullable|string|max:500',
            'items'               => 'required|array|min:1',
            // ✅ Pedidos siempre usan atributos públicos (id_negocio = null)
            'items.*.id_modelo'   => [
                'required',
                Rule::exists('modelos', 'id_modelo')->whereNull('id_negocio'),
            ],
            'items.*.id_voltaje'  => [
                'required',
                Rule::exists('voltajes', 'id_voltaje')->whereNull('id_negocio'),
            ],
            'items.*.id_color'    => [
                'required',
                Rule::exists('colores', 'id_color')->whereNull('id_negocio'),
            ],
            'items.*.cantidad'    => 'required|integer|min:1|max:999',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

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

            CatalogService::invalidatePedido($pedido->id_pedido, $pedido->id_negocio);
            CatalogService::invalidateNegocio($pedido->id_negocio);
            Cache::forget("pedidos:index:{$usuario->id_usuario}:all:all:page:1");

            $nuevoPedidoId = $pedido->id_pedido;

            event(new PedidoUpdated(
                $pedido->fresh(['negocio', 'usuario', 'items.modelo', 'items.voltaje', 'items.color']),
                'created'
            ));
        });

        return redirect()
            ->route('pedidos.index')
            ->with('success', 'Pedido creado correctamente.')
            ->with('nuevo_pedido_id', $nuevoPedidoId);
    }

    // ─── MOSTRAR PEDIDO ──────────────────────────────────────────────
    public function show(string $id_pedido)
    {
        $pedido = CatalogService::getPedidoById($id_pedido);
        abort_if(!$pedido, 404);

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

        // ✅ Pedidos siempre usan atributos públicos (id_negocio = null)
        $modelos  = CatalogService::getModelos();
        $voltajes = [];
        $colores  = [];
        foreach ($modelos as $modelo) {
            $voltajes[$modelo->id_modelo] = CatalogService::getVoltajesByModelo($modelo->id_modelo, null, true);
            $colores[$modelo->id_modelo]  = CatalogService::getColoresByModelo($modelo->id_modelo, null, true);
        }

        return view('pedidos.edit', compact('pedido', 'modelos', 'voltajes', 'colores'));
    }

    // ─── ACTUALIZAR PEDIDO ───────────────────────────────────────────
    public function update(Request $request, string $id_pedido)
    {
        $usuario   = auth()->user();
        $pedido    = Pedido::findOrFail($id_pedido);

        abort_if($usuario->id_rol !== 1, 403);
        abort_if($pedido->id_usuario !== $usuario->id_usuario, 403);
        abort_if($pedido->status !== 1, 403, 'Solo se puede editar un pedido en estado Solicitado.');

        $validator = Validator::make($request->all(), [
            'notas'               => 'nullable|string|max:500',
            'items'               => 'required|array|min:1',
            // ✅ Pedidos siempre usan atributos públicos (id_negocio = null)
            'items.*.id_modelo'   => [
                'required',
                Rule::exists('modelos', 'id_modelo')->whereNull('id_negocio'),
            ],
            'items.*.id_voltaje'  => [
                'required',
                Rule::exists('voltajes', 'id_voltaje')->whereNull('id_negocio'),
            ],
            'items.*.id_color'    => [
                'required',
                Rule::exists('colores', 'id_color')->whereNull('id_negocio'),
            ],
            'items.*.cantidad'    => 'required|integer|min:1|max:999',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        DB::transaction(function () use ($request, $pedido, $usuario) {
        $pedido->update(['notas' => $request->notas]);
        $pedido->items()->delete();
        $pedido->touch();

            foreach ($request->items as $item) {
                PedidoItem::create([
                    'id_pedido'  => $pedido->id_pedido,
                    'id_modelo'  => $item['id_modelo'],
                    'id_voltaje' => $item['id_voltaje'],
                    'id_color'   => $item['id_color'],
                    'cantidad'   => $item['cantidad'],
                ]);
            }

            CatalogService::invalidatePedido($pedido->id_pedido, $pedido->id_negocio);
            CatalogService::invalidateNegocio($pedido->id_negocio);
            Cache::forget("pedidos:index:{$usuario->id_usuario}:all:all:page:1");

            event(new PedidoUpdated(
                $pedido->fresh(['negocio', 'usuario', 'items.modelo', 'items.voltaje', 'items.color']),
                'updated'
            ));
        });

        return redirect()
            ->route('pedidos.index')
            ->with('success', 'Pedido actualizado correctamente.');
    }

    // ─── ACTUALIZAR STATUS ───────────────────────────────────────────
    public function updateStatus(Request $request, string $id_pedido)
    {
        $pedido = Pedido::findOrFail($id_pedido);
        $request->validate(['status' => 'required|in:1,2,3']);

        $pedido->update(['status' => $request->status]);

        CatalogService::invalidatePedido($id_pedido, $pedido->id_negocio);
        Cache::forget("pedidos:index:" . auth()->user()->id_usuario . ":all:all:page:1");

        event(new PedidoUpdated(
            $pedido->fresh(['negocio', 'usuario', 'items.modelo', 'items.voltaje', 'items.color']),
            'updated'
        ));

        return back()->with('success', 'Status actualizado correctamente.');
    }

    // ─── ELIMINAR PEDIDO ─────────────────────────────────────────────
    public function destroy(string $id_pedido)
    {
        $pedido = Pedido::with(['negocio', 'usuario', 'items'])->findOrFail($id_pedido);
        abort_if($pedido->status > 1, 403, 'No se puede eliminar un pedido que ya fue preparado o entregado.');

        $usuarioId = $pedido->id_usuario;
        $pedidoId  = $pedido->id_pedido;
        $idNegocio = $pedido->id_negocio;

        $pedido->delete();

        CatalogService::invalidatePedido($pedidoId, $idNegocio);
        CatalogService::invalidateNegocio($idNegocio);
        Cache::forget("pedidos:index:" . auth()->user()->id_usuario . ":all:all:page:1");

        event(new PedidoUpdated($pedido, 'deleted'));

        return redirect()
            ->route('pedidos.index')
            ->with('success', 'Pedido eliminado.');
    }

    // ─── VISTA REALIZAR PEDIDO ────────────────────────────────────
    public function realizar(string $id_pedido)
    {
        $usuario = auth()->user();
        abort_if($usuario->id_rol !== 5, 403);

        $pedido = Pedido::with([
            'usuario', 'negocio',
            'items.modelo', 'items.voltaje', 'items.color',
            'bicicletas.modelo', 'bicicletas.voltaje', 'bicicletas.color',
        ])->findOrFail($id_pedido);

        abort_if(!in_array($pedido->status, [1, 2]), 403, 'Este pedido no puede ser realizado.');

        $resumen = [];
        foreach ($pedido->items as $item) {
            $key = $item->id_modelo . '-' . $item->id_voltaje . '-' . $item->id_color;
            $resumen[$key] = [
                'modelo'     => $item->modelo->nombre_modelo ?? 'N/D',
                'voltaje'    => $item->voltaje->voltaje       ?? 'N/D',
                'color'      => $item->color->color           ?? 'N/D',
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

        // ✅ Rol 5 siempre usa modelos públicos
        $modelos = CatalogService::getModelos();

        return view('pedidos.realizar', compact('pedido', 'resumen', 'modelos'));
    }

    // ─── GENERAR PDF DEL PEDIDO ──────────────────────────────────
    public function pdf(Request $request, string $id_pedido)
    {
        $pedido = CatalogService::getPedidoById($id_pedido);
        abort_if(!$pedido, 404);

        $cliente     = $request->input('cliente',     optional($pedido->usuario)->nombre_usuario ?? '');
        $distancia   = $request->input('distancia',   $pedido->distancia);
        $transporte  = $request->input('transporte',  $pedido->transporte);
        $costo_envio = $request->input('costo_envio', $pedido->costo_envio);
        $lotesRaw    = $request->input('lotes', []);

        $lotes = [];
        foreach ($lotesRaw as $idx => $valor) {
            $lotes[(int)$idx] = $valor;
        }

        $cargadores = [];
        $baterias   = [];

        foreach ($pedido->items as $item) {
            $modelo   = optional($item->modelo)->nombre_modelo ?? '';
            $voltaje  = optional($item->voltaje)->voltaje       ?? '';
            $cantidad = $item->cantidad;

            if ($modelo === 'VmpS5') {
                $cargadores['48V/12Ah'] = ($cargadores['48V/12Ah'] ?? 0) + $cantidad;
                $baterias['12V/12Ah']   = ($baterias['12V/12Ah']   ?? 0) + ($cantidad * 4);
            } else {
                $volts       = intval($voltaje);
                $numBaterias = intval($volts / 12);

                if ($volts === 48)      $cargadores['48V/20Ah'] = ($cargadores['48V/20Ah'] ?? 0) + $cantidad;
                elseif ($volts === 60)  $cargadores['60V/20Ah'] = ($cargadores['60V/20Ah'] ?? 0) + $cantidad;
                elseif ($volts === 72)  $cargadores['72V/20Ah'] = ($cargadores['72V/20Ah'] ?? 0) + $cantidad;

                $baterias['12V/20Ah'] = ($baterias['12V/20Ah'] ?? 0) + ($cantidad * $numBaterias);
            }
        }

        $html = view('pedidos.pdf_create', compact(
            'pedido', 'cargadores', 'baterias', 'lotes',
            'cliente', 'distancia', 'transporte', 'costo_envio'
        ))->render();

        $mpdf = new \Mpdf\Mpdf(['mode' => 'utf-8', 'format' => 'A4']);
        $mpdf->WriteHTML($html);

        return response($mpdf->Output("emision_{$pedido->id_pedido}.pdf", 'S'))
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'inline; filename="emision_' . $pedido->id_pedido . '.pdf"');
    }

    // ─── COMPLETAR ENTREGA ───────────────────────────────────────
    public function completarEntrega(Request $request, string $id_pedido)
    {
        $usuario = auth()->user();
        abort_if($usuario->id_rol !== 5, 403);

        $request->validate(['token' => 'required|string|size:10']);

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

        $idNegocioGestor     = $usuario->id_negocio;
        $movimientoService   = app(BicicletaMovimientoService::class); // ← instancia una vez

        DB::transaction(function () use ($pedido, $tokenRecord, $idNegocioGestor, $movimientoService) {
            $pedido->update(['status' => 4]);

            foreach ($pedido->bicicletas as $bici) {
                CatalogService::invalidateBicicleta($bici->num_serie, $bici->id_negocio);
                CatalogService::invalidateSeccion($bici->id_usuario, $idNegocioGestor);

                $bici->update(['id_negocio' => $pedido->id_negocio]);
                $movimientoService->entradaStockGeneral($bici->num_serie, $pedido->id_pedido);


                CatalogService::invalidateBicicleta($bici->num_serie, $pedido->id_negocio);
                CatalogService::invalidateSeccion($bici->id_usuario, $pedido->id_negocio);

                if ($bici->id_usuario) {
                    CatalogService::invalidateBicicletasPorUsuario($bici->id_usuario, $idNegocioGestor);
                    CatalogService::invalidateBicicletasPorUsuario($bici->id_usuario, $pedido->id_negocio);
                }
                
                event(new BicicletaActualizada(
                    numSerie:       $bici->num_serie,
                    idNegocio:      $pedido->id_negocio,
                    idUsuario:      $bici->id_usuario ?? '',
                    nombreVendedor: '',
                    modelo:         $bici->modelo->nombre_modelo ?? '—',
                    voltaje:        $bici->voltaje->voltaje       ?? '—',
                    color:          $bici->color->color           ?? '—',
                    status:         $bici->status,
                ));
            }

            $tokenRecord->delete();

            CatalogService::invalidateSeccion(null, $idNegocioGestor);
            CatalogService::invalidateSeccion(null, $pedido->id_negocio);
            CatalogService::invalidateStockVendedores($idNegocioGestor);
            CatalogService::invalidateStockVendedores($pedido->id_negocio);
            CatalogService::invalidateInventario($idNegocioGestor);
            CatalogService::invalidateInventario($pedido->id_negocio);
            CatalogService::invalidateNegocio($idNegocioGestor);
            CatalogService::invalidateNegocio($pedido->id_negocio);
            CatalogService::invalidatePedido($pedido->id_pedido, $pedido->id_negocio);

            $pedidoFresh = Pedido::with([
                'negocio', 'usuario',
                'items.modelo', 'items.voltaje', 'items.color',
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

    // ─── OBTENER TOKEN DE ENTREGA ─────────────────────────────────
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

    // ─── CREAR PEDIDO RÁPIDO ─────────────────────────────────────
    public function crearRapido()
    {
        $usuario = auth()->user();
        abort_if($usuario->id_rol !== 5, 403);

        // ✅ Rol 5 siempre usa modelos públicos
        $modelos  = CatalogService::getModelos();
        $voltajes = [];
        $colores  = [];

        foreach ($modelos as $modelo) {
            $voltajes[$modelo->id_modelo] = CatalogService::getVoltajesByModelo($modelo->id_modelo, null);
            $colores[$modelo->id_modelo]  = CatalogService::getColoresByModelo($modelo->id_modelo, null);
        }

        return view('pedidos.rapido', compact('modelos', 'voltajes', 'colores'));
    }

    // ─── GENERAR PDF RÁPIDO ──────────────────────────────────────
    public function generarPdfRapido(Request $request)
    {
        $usuario = auth()->user();
        abort_if($usuario->id_rol !== 5, 403);

        $request->validate([
            // tus validaciones aquí
        ]);

        $datos = [
            'fecha'       => $request->fecha,
            'cliente'     => $request->cliente,
            'distancia'   => $request->distancia  ?? '/',
            'transporte'  => $request->transporte ?? '/',
            'costo_envio' => $request->costo_envio ?? '/',
            'Nchofer'     => $request->Nchofer ?? '',
            'Tchofer'     => $request->Tchofer ?? '',
            'items'       => [],
        ];

        $cargadores = [];
        $baterias   = [];

        foreach ($request->items as $item) {
            // ✅ Rol 5 usa modelos/voltajes/colores públicos (id_negocio = null)
            $modelo  = CatalogService::getModeloById($item['id_modelo']);
            $voltaje = CatalogService::getVoltajeById($item['id_voltaje']);
            $color   = CatalogService::getColorById($item['id_color']);

            $nombreModelo  = $modelo->nombre_modelo ?? 'N/D';
            $nombreVoltaje = $voltaje->voltaje       ?? 'N/D';
            $cantidad      = count($item['series']);

            $datos['items'][] = [
                'modelo'   => $nombreModelo,
                'voltaje'  => $nombreVoltaje,
                'color'    => $color->color ?? 'N/D',
                'cantidad' => $cantidad,
                'series'   => $item['series'],
                'lote'     => $item['lote'] ?? '',
            ];

            if ($nombreModelo === 'VmpS5') {
                $cargadores['48V/12Ah'] = ($cargadores['48V/12Ah'] ?? 0) + $cantidad;
                $baterias['12V/12Ah']   = ($baterias['12V/12Ah']   ?? 0) + ($cantidad * 4);
            } else {
                $volts       = intval($nombreVoltaje);
                $numBaterias = intval($volts / 12);

                if ($volts === 48)      $cargadores['48V/20Ah'] = ($cargadores['48V/20Ah'] ?? 0) + $cantidad;
                elseif ($volts === 60)  $cargadores['60V/20Ah'] = ($cargadores['60V/20Ah'] ?? 0) + $cantidad;
                elseif ($volts === 72)  $cargadores['72V/20Ah'] = ($cargadores['72V/20Ah'] ?? 0) + $cantidad;

                $baterias['12V/20Ah'] = ($baterias['12V/20Ah'] ?? 0) + ($cantidad * $numBaterias);
            }
        }

        $html = view('pedidos.pdf_rapido', compact('datos', 'cargadores', 'baterias'))->render();

        $mpdf = new \Mpdf\Mpdf(['mode' => 'utf-8', 'format' => 'A4']);
        $mpdf->WriteHTML($html);

        return response($mpdf->Output("emision_rapida.pdf", 'S'))
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'inline; filename="emision_rapida.pdf"');
    }
}