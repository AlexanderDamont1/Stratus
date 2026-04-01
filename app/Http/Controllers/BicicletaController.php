<?php

namespace App\Http\Controllers;

use App\Models\Bicicleta;
use App\Models\Enlace;
use App\Models\Usuario;
use App\Services\CatalogService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use App\Events\BicicletaActualizada;

class BicicletaController extends Controller
{
    use \App\Traits\ResolvesAdminRoute;
    /* =====================================================
     | INDEX
     ===================================================== */
    public function index()
    {
        if (!in_array(auth()->user()->id_rol, [1, 5])) {
            abort(403);
        }

        $user       = auth()->user();
        $id_negocio = $user->id_negocio;

        if ($user->id_rol === 1) {
            // Construir el JSON del catálogo en cascada (cacheado por CatalogService)
            $catalogoJson = CatalogService::getCatalogoCompleto($id_negocio)
                ->map(fn($marca) => [
                    'id_marca'     => $marca->id_marca,
                    'nombre_marca' => $marca->nombre_marca,
                    'modelos'      => $marca->modelos->map(fn($modelo) => [
                        'id_modelo'     => $modelo->id_modelo,
                        'nombre_modelo' => $modelo->nombre_modelo,
                        'colores'       => $modelo->colores->map(fn($c) => [
                            'id_color' => $c->id_color,
                            'color'    => $c->color,
                        ])->values(),
                        'voltajes'      => $modelo->voltajes->map(fn($v) => [
                            'id_voltaje' => $v->id_voltaje,
                            'voltaje'    => $v->voltaje,
                        ])->values(),
                    ])->values(),
                ])->values()->toJson();

            return view('gestor.Vehiculos.Bicicleta.index', [
                'stockPorVendedor' => CatalogService::getStockPorVendedores($id_negocio),
                'stats'            => CatalogService::getBicicletaStats($id_negocio),
                'modelos'          => CatalogService::getModelosByNegocio($id_negocio),
                'negocios'         => CatalogService::getNegocios(),
                'marcas'           => CatalogService::getMarcasByNegocio($id_negocio),
                'colores'          => CatalogService::getColoresByNegocio($id_negocio),
                'voltajes'         => CatalogService::getVoltajesByNegocio($id_negocio),
                'catalogoJson'     => $catalogoJson, // 👈 único cambio
            ]);
        }

        // Rol 5 — no necesita catalogoJson (no tiene modal de creación)
        return view('gestor.Vehiculos.Bicicleta.index', [
            'bicicletas' => CatalogService::getBicicletasPaginadas($id_negocio),
            'stats'      => CatalogService::getBicicletaStats($id_negocio),
            'modelos'    => CatalogService::getModelos(),
            'negocios'   => CatalogService::getNegocios(),
        ]);
    }




   public function stockSeccion(Request $request)
    {
        $user = auth()->user();
        if ($user->id_rol !== 1) abort(404);

        $idNegocio = $user->id_negocio;
        $idUsuario = $request->get('id_usuario') ?: null;
        $page      = (int) $request->get('page', 1);

        $data = CatalogService::getBicicletasSeccion($idNegocio, $idUsuario, $page);

        // Modelos ya están en Redis, sin query adicional
        $modelos = CatalogService::getModelosByNegocio($idNegocio)
            ->keyBy('id_modelo');

        // Inyectar marca en cada bicicleta desde cache
        $data['data'] = collect($data['data'])->map(function ($bici) use ($modelos) {
            $modelo = $modelos->get($bici->id_modelo ?? $bici['id_modelo'] ?? null);
            $bici->marca_nombre = $modelo?->marca?->nombre_marca ?? '—';
            return $bici;
        })->all();

        return response()->json($data);
    }



    /* =====================================================
     | CREATE
     ===================================================== */
    public function create()
    {
        $user = auth()->user();

        if ($user->id_rol !== 1) abort(403);

        $id_negocio = $user->id_negocio;

        $catalogoJson = CatalogService::getCatalogoCompleto($id_negocio)
            ->map(fn($marca) => [
                'id_marca'     => $marca->id_marca,
                'nombre_marca' => $marca->nombre_marca,
                'modelos'      => $marca->modelos->map(fn($modelo) => [
                    'id_modelo'     => $modelo->id_modelo,
                    'nombre_modelo' => $modelo->nombre_modelo,
                    'colores'       => $modelo->colores->map(fn($c) => [
                        'id_color' => $c->id_color,
                        'color'    => $c->color,
                    ])->values(),
                    'voltajes'      => $modelo->voltajes->map(fn($v) => [
                        'id_voltaje' => $v->id_voltaje,
                        'voltaje'    => $v->voltaje,
                    ])->values(),
                ])->values(),
            ])->values()->toJson();

        return view('administrador.bicicletas.create', compact('catalogoJson'));
    }




    public function storeMasivo(Request $request)
    {
        if (auth()->user()->id_rol !== 1) abort(403);

        $request->validate([
            'bicicletas'                => 'required|array|min:1',
            'bicicletas.*.num_serie'    => 'required|string|size:17|distinct|unique:bicicletas,num_serie',
            'bicicletas.*.id_modelo'    => 'required|exists:modelos,id_modelo',
            'bicicletas.*.id_color'     => 'required|exists:colores,id_color',
            'bicicletas.*.id_voltaje'   => 'required|exists:voltajes,id_voltaje',
        ], [
            'bicicletas.*.num_serie.size'     => 'El N° de serie debe tener exactamente 17 caracteres.',
            'bicicletas.*.num_serie.distinct' => 'Hay números de serie repetidos en el listado.',
            'bicicletas.*.num_serie.unique'   => 'El N° de serie ya existe en el sistema.',
        ]);

        $user       = auth()->user();
        $id_negocio = $user->id_negocio;
        $ahora      = now();

        $inserts = collect($request->bicicletas)->map(fn($b) => [
            'num_serie'    => strtoupper(trim($b['num_serie'])),
            'id_modelo'    => $b['id_modelo'],
            'id_color'     => $b['id_color'],
            'id_voltaje'   => $b['id_voltaje'],
            'id_negocio'   => $id_negocio,
            'status'       => 1,
            'created_at'   => $ahora,
            'updated_at'   => $ahora,
        ])->toArray();

        \App\Models\Bicicleta::insert($inserts);

        // ✅ Invalidar caché
        CatalogService::invalidateBicicleta('masivo', $id_negocio);
        CatalogService::invalidateStockVendedores($id_negocio);
        CatalogService::invalidateSeccion(null, $id_negocio);

        // ✅ Broadcast por cada bicicleta insertada
        $series = collect($inserts)->pluck('num_serie');

        Bicicleta::with(['modelo', 'voltaje', 'color'])
            ->whereIn('num_serie', $series)
            ->get()
            ->each(function ($bici) use ($user, $id_negocio) {
                event(new BicicletaActualizada(
                    numSerie:       $bici->num_serie,
                    idNegocio:      $id_negocio,
                    idUsuario:      '',  // sin asignar
                    nombreVendedor: $user->nombre_usuario,
                    modelo:         $bici->modelo->nombre_modelo ?? '—',
                    voltaje:        $bici->voltaje->voltaje       ?? '—',
                    color:          $bici->color->color           ?? '—',
                    status:         $bici->status,
                ));
            });

        return redirect()->route('bicicletas.index')
            ->with('success', count($inserts) . ' bicicleta(s) registradas correctamente.');
    }

    /* =====================================================
     | STORE
     ===================================================== */
    public function store(Request $request)
    {
        $user      = auth()->user();
        $idNegocio = $user->id_negocio;

        $validator = Validator::make($request->all(), [
            'num_serie'  => 'required|string|size:17|unique:bicicletas,num_serie',
            'id_modelo'  => [
                'required',
                Rule::exists('modelos', 'id_modelo')->where(
                    'id_negocio',
                    $user->id_rol === 1 ? $idNegocio : null
                ),
            ],
            'id_voltaje' => [
                'required',
                Rule::exists('voltajes', 'id_voltaje')->where(
                    'id_negocio',
                    $user->id_rol === 1 ? $idNegocio : null
                ),
            ],
            'id_color'   => [
                'required',
                Rule::exists('colores', 'id_color')->where(
                    'id_negocio',
                    $user->id_rol === 1 ? $idNegocio : null
                ),
            ],
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
            $pedido = CatalogService::getPedidoById($request->id_pedido);

            if (!$pedido) {
                return response()->json(['ok' => false, 'mensaje' => 'Pedido no encontrado.'], 404);
            }

            $tieneAcceso = Enlace::where('id_usuario2', $user->id_usuario)
                ->where('id_usuario1', $pedido->id_usuario)
                ->where('estado', 'activo')
                ->exists();

            if (!$tieneAcceso) {
                return response()->json(['ok' => false, 'mensaje' => 'No tienes acceso a este pedido.'], 403);
            }

            $item = $pedido->items->first(
                fn($i) =>
                $i->id_modelo  == $request->id_modelo &&
                    $i->id_voltaje == $request->id_voltaje &&
                    $i->id_color   == $request->id_color
            );

            if (!$item) {
                return response()->json(['ok' => false, 'mensaje' => 'Esta combinación no está en el pedido.'], 422);
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
            'id_negocio' => $idNegocio,
            'id_modelo'  => $request->id_modelo,
            'id_voltaje' => $request->id_voltaje,
            'id_color'   => $request->id_color,
            'id_pedido'  => $request->id_pedido ?? null,
        ]);

        // ✅ Invalidar caché
        CatalogService::invalidateBicicleta($bicicleta->num_serie, $idNegocio);
        CatalogService::invalidateSeccion($bicicleta->id_usuario, $idNegocio);
        CatalogService::invalidateSeccion(null, $idNegocio);

        $completo = false;

        if ($request->id_pedido) {
            // ── Status 1 → 2 ──────────────────────────────────────────
            $pedidoActual = \App\Models\Pedido::find($request->id_pedido);
            if ($pedidoActual && $pedidoActual->status == 1) {
                $pedidoActual->update(['status' => 2]);
                CatalogService::invalidatePedido($pedidoActual->id_pedido, $pedidoActual->id_negocio);
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
                        'id_usuario2' => $user->id_usuario,
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
            ->route($this->routeByRol('bicicletas'))
            ->with('success', 'Bicicleta registrada correctamente.');
    }

    /* =====================================================
     | UPDATE
     ===================================================== */
    public function update(Request $request, string $num_serie)
    {
        $user      = auth()->user();
        $bicicleta = Bicicleta::where('num_serie', $num_serie)->firstOrFail();
        $idNegocio = $bicicleta->id_negocio;

        // ✅ Validar que los atributos pertenezcan al negocio correcto
        $validator = Validator::make($request->all(), [
            'id_modelo'  => [
                'required',
                Rule::exists('modelos', 'id_modelo')->where(
                    'id_negocio',
                    $user->id_rol === 1 ? $idNegocio : null
                ),
            ],
            'id_voltaje' => [
                'required',
                Rule::exists('voltajes', 'id_voltaje')->where(
                    'id_negocio',
                    $user->id_rol === 1 ? $idNegocio : null
                ),
            ],
            'id_color'   => [
                'required',
                Rule::exists('colores', 'id_color')->where(
                    'id_negocio',
                    $user->id_rol === 1 ? $idNegocio : null
                ),
            ],
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $bicicleta->update([
            'id_modelo'  => $request->id_modelo,
            'id_voltaje' => $request->id_voltaje,
            'id_color'   => $request->id_color,
        ]);

        CatalogService::invalidateBicicleta($bicicleta->num_serie, $idNegocio);

        // ✅ Invalidar también caché del vendedor si tiene uno asignado
        if ($bicicleta->id_usuario) {
            CatalogService::invalidateBicicletasPorUsuario($bicicleta->id_usuario, $idNegocio);
        }

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
        $idUsuario = $bicicleta->id_usuario; // ✅ guardar antes de eliminar

        $bicicleta->delete();

        // ✅ Invalidar caché
        CatalogService::invalidateBicicleta($numSerie, $idNegocio);
        CatalogService::invalidateSeccion($idUsuario, $idNegocio);
        CatalogService::invalidateSeccion(null, $idNegocio);

        if ($idUsuario) {
            CatalogService::invalidateBicicletasPorUsuario($idUsuario, $idNegocio);
        }

        if ($id_pedido) {
            $pedido = \App\Models\Pedido::find($id_pedido);
            if ($pedido) {
                CatalogService::invalidatePedido($id_pedido, $pedido->id_negocio);
            }
        }

        return response()->json([
            'ok'        => true,
            'mensaje'   => 'Bicicleta eliminada.',
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
            // ✅ Status ahora es numérico
            'status' => 'required|in:1,2,3',
        ]);

        $bicicleta->update(['status' => $request->status]);

        CatalogService::invalidateBicicleta($bicicleta->num_serie, $bicicleta->id_negocio);

        // ✅ Invalidar caché del vendedor si tiene uno asignado
        if ($bicicleta->id_usuario) {
            CatalogService::invalidateBicicletasPorUsuario($bicicleta->id_usuario, $bicicleta->id_negocio);
        }

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
        return CatalogService::getBicicletasByCliente($id_cliente);
    }

    /* =====================================================
     | AJAX: COLORES POR MODELO
     ===================================================== */
    public function coloresPorModelo(string $id_modelo)
    {
        return response()->json(
            CatalogService::getColoresByModelo($id_modelo, null)
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
            'voltaje'    => optional($bicicleta->voltaje)->voltaje       ?? 'N/D', // ✅ era tipo_voltaje, corregido
            'color'      => optional($bicicleta->color)->color           ?? 'N/D', // ✅ era nombre_color, corregido
            'id_pedido'  => $bicicleta->id_pedido,
            'status'     => $bicicleta->status,
        ]);
    }

    /* =====================================================
     | VENDEDOR: DASHBOARD
     ===================================================== */
    public function showB(Request $request)
    {
        $user = auth()->user();

        if ($user->id_rol != 2) abort(403);

        return view('vendedor.dashboard', [
            'bicicletas' => CatalogService::getBicicletasPorUsuarioPaginadas(
                $user->id_negocio,
                $user->id_usuario,
                $request->get('page', 1),
                $request->get('search')
            ),
        ]);
    }

    /* =====================================================
     | VENDEDOR: ASIGNAR USUARIO
     ===================================================== */
    public function asignarUsuario(Request $request)
    {
        $user = auth()->user();

        if ($user->id_rol != 2) abort(403);

        try {
            $bici = Bicicleta::where('num_serie', $request->num_serie)
                ->with(['modelo', 'voltaje', 'color'])
                ->first();

            if (!$bici) {
                return response()->json(['ok' => false, 'message' => 'Bicicleta no encontrada'], 404);
            }

            if ($bici->id_usuario) {
                return response()->json(['ok' => false, 'message' => 'Ya está asignada'], 400);
            }

            if ($bici->id_negocio != $user->id_negocio) {
                return response()->json(['ok' => false, 'message' => 'No tienes acceso a esta bicicleta'], 403);
            }

            $idNegocio        = $bici->id_negocio;
            $bici->id_usuario = $user->id_usuario;
            $guardado         = $bici->save();
            $bici->touch();

            if (!$guardado) {
                Log::error('No se pudo guardar la bicicleta', ['num_serie' => $bici->num_serie]);
                return response()->json(['ok' => false, 'message' => 'No se pudo asignar'], 500);
            }

            // ✅ Invalidar caché
            CatalogService::invalidateBicicleta($bici->num_serie, $idNegocio);
            CatalogService::invalidateSeccion($user->id_usuario, $idNegocio);
            CatalogService::invalidateSeccion(null, $idNegocio);
            CatalogService::invalidateBicicletasPorUsuario($user->id_usuario, $idNegocio);
            CatalogService::invalidateStockVendedores($idNegocio);

            // ✅ Broadcast al admin
            event(new BicicletaActualizada(
                numSerie:       $bici->num_serie,
                idNegocio:      $idNegocio,
                idUsuario:      $user->id_usuario,
                nombreVendedor: $user->nombre_usuario,
                modelo:         $bici->modelo->nombre_modelo ?? '—',
                voltaje:        $bici->voltaje->voltaje       ?? '—',
                color:          $bici->color->color           ?? '—',
                status:         $bici->status,
            ));

            return response()->json(['ok' => true]);

        } catch (\Exception $e) {
            Log::error('Error al asignar usuario a bicicleta', [
                'num_serie' => $request->num_serie,
                'error'     => $e->getMessage(),
            ]);

            return response()->json([
                'ok'      => false,
                'message' => 'Error interno: ' . $e->getMessage(),
            ], 500);
        }
    }

    /* =====================================================
     | VENDEDOR: BUSCAR POR QR
     ===================================================== */
    public function buscarPorSerieQr($num_serie)
    {
        $user = auth()->user();

        if ($user->id_rol != 2) abort(403);

        // ✅ Usar caché en lugar de query directa
        $bici = CatalogService::getBicicletaBySerie($num_serie);

        if (!$bici) {
            return response()->json(['ok' => false, 'message' => 'Bicicleta no encontrada'], 404);
        }

        if ($bici->id_negocio != $user->id_negocio) {
            return response()->json(['ok' => false, 'message' => 'No tienes acceso a esta bicicleta'], 403);
        }

        if ($bici->id_usuario) {
            return response()->json(['ok' => false, 'message' => 'La bicicleta ya está asignada'], 400);
        }

        return response()->json([
            'ok'        => true,
            'bicicleta' => [
                'num_serie' => $bici->num_serie,
                'modelo'    => $bici->modelo->nombre_modelo ?? '—',
                'voltaje'   => $bici->voltaje->voltaje       ?? '—',
                'color'     => $bici->color->color           ?? '—',
            ],
        ]);
    }

    /* =====================================================
     | MÉTODOS PRIVADOS
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
