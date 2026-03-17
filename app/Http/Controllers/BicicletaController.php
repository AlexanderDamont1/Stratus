<?php

namespace App\Http\Controllers;

use App\Models\Bicicleta;
use App\Models\Enlace;
use App\Models\Usuario;
use App\Services\CatalogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Cache;


class BicicletaController extends Controller
{
    /* =====================================================
     | INDEX
     ===================================================== */
    public function index(Request $request)
    {
        $id_negocio = auth()->user()->id_negocio;
        $page = $request->get('page', 1);
        $search = $request->get('search');

        return view('gestor.Vehiculos.Bicicleta.index', [
            'bicicletas' => CatalogService::getBicicletasPaginadas($id_negocio, $page, $search),
            'stats'      => CatalogService::getBicicletaStats($id_negocio),
            'modelos'    => CatalogService::getModelos(),
            'negocios'   => CatalogService::getNegocios(),
        ]);
    }

    /* =====================================================
     | CREATE
     ===================================================== */
    public function create()
    {
        $user = auth()->user();

        if (!$user) abort(401, 'Sesión no válida.');
        if (!$user->id_negocio) abort(403, 'No tienes un negocio asignado.');

        return view('gestor.Vehiculos.Bicicleta.create', [
            'negocio' => $user->negocio,
            'modelos' => CatalogService::getModelos(),
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

        if ($request->id_pedido) {
            // Usar el servicio cacheado para obtener el pedido con relaciones
            $pedido = CatalogService::getPedidoById($request->id_pedido);

            if (!$pedido) {
                return response()->json([
                    'ok'      => false,
                    'mensaje' => 'Pedido no encontrado.',
                ], 404);
            }

            $tieneAcceso = Enlace::where('id_usuario2', auth()->user()->id_usuario)
                ->where('id_usuario1', $pedido->id_usuario)
                ->where('estado', 'activo')
                ->exists();

            if (!$tieneAcceso) {
                return response()->json([
                    'ok'      => false,
                    'mensaje' => 'No tienes acceso a este pedido.',
                ], 403);
            }

            $item = $pedido->items->first(
                fn($i) =>
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

            $yaEscaneadas = Bicicleta::where('id_pedido',  $request->id_pedido)
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

        $bicicleta = Bicicleta::create([
            'num_serie'  => strtoupper($request->num_serie),
            'id_negocio' => auth()->user()->id_negocio,
            'id_modelo'  => $request->id_modelo,
            'id_voltaje' => $request->id_voltaje,
            'id_color'   => $request->id_color,
            'id_pedido'  => $request->id_pedido ?? null,
        ]);

        // Invalidar caché de la bicicleta recién creada (por si acaso) y stats del negocio
        $idNegocio = auth()->user()->id_negocio;
        CatalogService::invalidateBicicleta($bicicleta->num_serie, $idNegocio);

        $completo = false;

        if ($request->id_pedido) {
            // ── Status 1 → 2 ──────────────────────────────────────────
            $pedidoActual = \App\Models\Pedido::find($request->id_pedido);
            if ($pedidoActual && $pedidoActual->status == 1) {
                $pedidoActual->update(['status' => 2]);
                // Invalidar caché del pedido y stats
                CatalogService::invalidatePedido($pedidoActual->id_pedido, $pedidoActual->id_negocio);
                // También invalidar el índice de pedidos del usuario (se podría hacer con invalidateNegocio, pero el índice es manual)
                Cache::forget("pedidos:index:{$pedidoActual->id_usuario}:all:all:page:1");

                $pedidoFresh = \App\Models\Pedido::with([
                    'negocio',
                    'usuario',
                    'items.modelo',
                    'items.voltaje',
                    'items.color'
                ])->find($pedidoActual->id_pedido);

                event(new \App\Events\PedidoUpdated($pedidoFresh, 'updated'));
            }

            // ── Verificar si completo → Status 2 → 3 ──────────────────
            $pedidoFull = \App\Models\Pedido::with(['items', 'bicicletas'])->find($request->id_pedido);
            if ($pedidoFull) {
                $completo = $pedidoFull->items->every(
                    fn($item) =>
                    Bicicleta::where('id_pedido',  $pedidoFull->id_pedido)
                        ->where('id_modelo',  $item->id_modelo)
                        ->where('id_voltaje', $item->id_voltaje)
                        ->where('id_color',   $item->id_color)
                        ->count() >= $item->cantidad
                );

                if ($completo && $pedidoFull->status == 2) {
                    $pedidoFull->update(['status' => 3]);
                    CatalogService::invalidatePedido($pedidoFull->id_pedido, $pedidoFull->id_negocio);
                    Cache::forget("pedidos:index:{$pedidoFull->id_usuario}:all:all:page:1");

                    \App\Models\PedidoToken::where('id_pedido', $pedidoFull->id_pedido)->delete();
                    \App\Models\PedidoToken::create([
                        'id_token'    => self::generarIdToken(),
                        'id_pedido'   => $pedidoFull->id_pedido,
                        'id_usuario1' => $pedidoFull->id_usuario,
                        'id_usuario2' => auth()->user()->id_usuario,
                        'token'       => self::generarToken(),
                        'estado'      => 0,
                    ]);

                    $pedidoFullFresh = \App\Models\Pedido::with([
                        'negocio',
                        'usuario',
                        'items.modelo',
                        'items.voltaje',
                        'items.color'
                    ])->find($pedidoFull->id_pedido);

                    event(new \App\Events\PedidoUpdated($pedidoFullFresh, 'updated'));
                }
            }
        }

        if ($request->expectsJson()) {
            return response()->json([
                'ok'              => true,
                'pedido_completo' => $completo,
            ]);
        }

        return redirect()
            ->route('gestor.vehiculos.bicicletas.index')
            ->with('success', 'Bicicleta registrada correctamente.');
    }

    /* =====================================================
     | EDIT
     ===================================================== */
    public function edit(string $num_serie)
    {
        $bicicleta = CatalogService::getBicicletaBySerie($num_serie);
        if (!$bicicleta) {
            abort(404);
        }

        $modelos = CatalogService::getModelos();
        $voltajes = CatalogService::getVoltajesByModelo($bicicleta->id_modelo);
        $colores  = CatalogService::getColoresByModelo($bicicleta->id_modelo);

        return view('gestor.Vehiculos.Bicicleta.edit', [
            'bicicleta' => $bicicleta,
            'modelos'   => $modelos,
            'voltajes'  => $voltajes,
            'colores'   => $colores,
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

        $oldNegocio = $bicicleta->id_negocio; // para invalidar stats del negocio original si cambia? (no cambia en este update)

        $bicicleta->update([
            'id_modelo'  => $request->id_modelo,
            'id_voltaje' => $request->id_voltaje,
            'id_color'   => $request->id_color,
        ]);

        // Invalidar caché de la bicicleta actualizada y stats del negocio
        CatalogService::invalidateBicicleta($bicicleta->num_serie, $bicicleta->id_negocio);
        // Si el negocio pudiera cambiar, también habría que invalidar el anterior, pero no es el caso.

        return redirect()
            ->route('gestor.vehiculos.bicicletas.index')
            ->with('success', 'Bicicleta actualizada correctamente.');
    }

    /* =====================================================
     | DESTROY (desde pedido)
     ===================================================== */
    public function destroyFromPedido(string $num_serie)
    {
        $bicicleta = Bicicleta::where('num_serie', $num_serie)->firstOrFail();

        if ($bicicleta->mantenimientos()->count() > 0) {
            return response()->json([
                'ok'      => false,
                'mensaje' => 'No se puede eliminar: tiene mantenimientos.',
            ], 422);
        }

        $id_pedido = $bicicleta->id_pedido;
        $idNegocio = $bicicleta->id_negocio;
        $numSerie  = $bicicleta->num_serie;

        $bicicleta->delete();

        // Invalidar caché de la bicicleta eliminada y stats del negocio
        CatalogService::invalidateBicicleta($numSerie, $idNegocio);
        if ($id_pedido) {
            // También invalidar el pedido si estaba asociado
            $pedido = \App\Models\Pedido::find($id_pedido);
            if ($pedido) {
                CatalogService::invalidatePedido($id_pedido, $pedido->id_negocio);
            }
        }

        return response()->json([
            'ok'       => true,
            'mensaje'  => 'Bicicleta eliminada.',
            'id_pedido' => $id_pedido,
        ]);
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

        // Invalidar caché de la bicicleta y stats
        CatalogService::invalidateBicicleta($bicicleta->num_serie, $bicicleta->id_negocio);

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
        // Se puede cachear con una clave específica por cliente (TTL corto)
        $cacheKey = "bicicletas:cliente:{$id_cliente}";
        $bicicletas = Cache::remember($cacheKey, 300, function () use ($id_cliente) {
            return Bicicleta::with(['modelo', 'color'])
                ->where('id_cliente', $id_cliente)
                ->where('status', '!=', 'danada')
                ->get();
        });
        return $bicicletas;
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

    /* =====================================================
     | API: BUSCAR BICICLETA POR NUM_SERIE
     ===================================================== */
    public function showApi(string $num_serie)
    {
        $bicicleta = CatalogService::getBicicletaBySerie($num_serie);

        if (!$bicicleta) {
            return response()->json(['encontrada' => false], 404);
        }

        return response()->json([
            'encontrada' => true,
            'num_serie'  => $bicicleta->num_serie,
            'id_modelo'  => $bicicleta->id_modelo,
            'id_voltaje' => $bicicleta->id_voltaje,
            'id_color'   => $bicicleta->id_color,
            'modelo'     => optional($bicicleta->modelo)->nombre_modelo ?? 'N/D',
            'voltaje'    => optional($bicicleta->voltaje)->tipo_voltaje  ?? 'N/D', // ¿tipo_voltaje o voltaje?
            'color'      => optional($bicicleta->color)->nombre_color   ?? 'N/D',
            'id_pedido'  => $bicicleta->id_pedido,
            'status'     => $bicicleta->status,
        ]);
    }

    /* =====================================================
     | MÉTODOS PRIVADOS PARA GENERAR TOKEN
     ===================================================== */
    private static function generarToken(): string
    {
        $letras  = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $numeros = '0123456789';

        $parte1 = '';
        for ($i = 0; $i < 4; $i++) $parte1 .= $letras[random_int(0, 25)];

        $parte2 = '';
        for ($i = 0; $i < 4; $i++) $parte2 .= $numeros[random_int(0, 9)];

        $parte3 = '';
        for ($i = 0; $i < 2; $i++) $parte3 .= $letras[random_int(0, 25)];

        return $parte1 . $parte2 . $parte3;
    }

    private static function generarIdToken(): string
    {
        return 'TKN' . strtoupper(substr(md5(uniqid()), 0, 12));
    }
}