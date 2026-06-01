<?php

namespace App\Http\Controllers;

use App\Events\VentaRealizada;
use App\Events\VentaRegistrada;
use App\Events\BicicletaActualizada;
use App\Events\StockActualizado;
use App\Models\Bicicleta;
use App\Models\Cliente;
use App\Models\DetalleVenta;
use App\Models\Producto;
use App\Models\ProductoModelo;
use App\Models\Venta;
use App\Models\Inventario;
use App\Services\CatalogService;
use App\Services\CajaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Personal;
use App\Models\VentaPago;
use App\Jobs\ProcesarPostVenta;
use App\Services\CuponService;

class VentaController extends Controller
{
    /* =====================================================
     | CONSTRUCTOR — autorización centralizada
     ===================================================== */

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (auth()->user()?->id_rol !== 2) {
                abort(403);
            }
            return $next($request);
        });
    }

    /* =====================================================
     | INDEX
     ===================================================== */

    public function index(Request $request)
    {
        $user = auth()->user();
        $page = $request->get('page', 1);

        if ($page == 1) {
            $ventas = CatalogService::getVentasByVendedor(
                $user->id_negocio,
                $user->id_usuario
            );
        } else {
            $ventas = Venta::with([
                    'cliente',
                    'detalles.producto',
                    'detalles.bicicleta.modelo',
                    'detalles.bicicleta.color',
                ])
                ->where('id_negocio', $user->id_negocio)
                ->where('id_usuario', $user->id_usuario)
                ->latest()
                ->paginate(15, ['*'], 'page', $page);
        }

        return view('vendedor.ventas.index', compact('ventas'));
    }

    /* =====================================================
     | CREATE
     ===================================================== */

    public function create()
    {
        $user = auth()->user();

        // ── Accesorios con stock disponible ──────────────────────────────
        $accesorios = CatalogService::getProductosConRelaciones($user->id_negocio, $user->id_usuario)
            ->where('tipo', '1')
            ->filter(fn($p) => $p->precio > 0)
            ->values()
            ->map(function ($producto) use ($user) {
                $inventario = Inventario::where('id_producto', $producto->id_producto)
                    ->where('id_negocio', $user->id_negocio)
                    ->where('id_usuario', $user->id_usuario)
                    ->whereNull('id_producto_modelo')
                    ->first();

                $producto->stock_disponible = $inventario ? (int) $inventario->cantidad : 0;
                return $producto;
            });

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

        $personal = Personal::deSucursal($user->id_usuario)->activo()->orderBy('nombre')->get();

        return view('vendedor.ventas.create', compact('accesorios', 'metodos', 'personal'));
    }

    /* =====================================================
     | AJAX — buscar bicicleta por num_serie
     ===================================================== */

    public function buscarSerie(Request $request)
    {
        $user     = auth()->user();
        $numSerie = strtoupper(trim($request->get('num_serie', '')));

        if (!$numSerie) {
            return response()->json([
                'ok'      => false,
                'mensaje' => 'Indica un número de serie.',
            ], 422);
        }

        $bici = CatalogService::getBicicletaBySerie($numSerie, $user->id_negocio);

        if (
            !$bici ||
            $bici->id_negocio !== $user->id_negocio ||
            $bici->status != 1 ||
            ($bici->id_usuario !== null && $bici->id_usuario !== $user->id_usuario)
        ) {
            return response()->json([
                'ok'      => false,
                'mensaje' => 'Bicicleta no encontrada en tu stock o ya fue vendida.',
            ], 404);
        }

        $pm = ProductoModelo::with('producto')
            ->where('id_modelo', $bici->id_modelo)
            ->where('id_voltaje', $bici->id_voltaje)
            ->where('id_negocio', $user->id_negocio)
            ->where(function ($q) use ($user) {
                $q->where('id_usuario', $user->id_usuario)
                    ->orWhereNull('id_usuario');
            })
            ->where('activo', true)
            ->first();

        if (!$pm || !$pm->producto) {
            return response()->json([
                'ok'      => false,
                'mensaje' => 'Esta bicicleta no tiene un producto configurado.',
            ], 404);
        }

        if (!$pm->producto->precio || $pm->producto->precio <= 0) {
            return response()->json([
                'ok'      => false,
                'mensaje' => 'Esta bicicleta no tiene un precio configurado.',
            ], 404);
        }

        $colorString = $bici->color->color ?? '';
        $colorNombre = '—';
        $colorHexes  = ['#cccccc'];

        if ($colorString) {
            $parts       = explode('|', $colorString, 2);
            $colorNombre = trim($parts[0]);
            if (isset($parts[1])) {
                $colorHexes = explode('/', $parts[1]);
            }
        }

        return response()->json([
            'ok'      => true,
            'bici'    => [
                'num_serie'    => $bici->num_serie,
                'marca'        => $bici->modelo->marca->nombre_marca ?? '—',
                'modelo'       => $bici->modelo->nombre_modelo       ?? '—',
                'voltaje'      => $bici->voltaje->voltaje             ?? '—',
                'color_nombre' => $colorNombre,
                'color_hexes'  => $colorHexes,
            ],
            'producto' => [
                'id_producto' => $pm->producto->id_producto,
                'nombre'      => $pm->producto->nombre_producto,
                'precio'      => (float) $pm->producto->precio,
            ],
        ]);
    }

    /* =====================================================
     | STORE
     ===================================================== */

    public function store(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'nombre_cliente'      => 'required|string|max:100',
            'apellido1'           => 'required|string|max:60',
            'apellido2'           => 'nullable|string|max:60',
            'telefono'            => 'required|string|max:15',
            'correo'              => 'nullable|email|max:120',
            'direccion'           => 'nullable|string|max:255',
            'items'               => 'required|array|min:1',
            'items.*.id_producto' => 'required|exists:productos,id_producto',
            'items.*.num_serie'   => 'nullable|string',
            'items.*.cantidad'    => 'required|integer|min:1',
            'codigo_cupon'        => 'nullable|string|max:30',
            'pagos'               => 'required|array|min:1',
            'pagos.*.metodo'      => 'required|string|max:40',
            'pagos.*.monto'       => 'required|numeric|min:0.01',
            'pagos.*.referencia'  => 'nullable|string|max:20',
            'id_personal'         => 'nullable|exists:personal,id_personal',
        ]);

        DB::beginTransaction();

        try {
            // ── Pre-carga desde caché ────────────────────────────────────────
            $todosLosProductos = CatalogService::getProductosConRelaciones(
                $user->id_negocio,
                $user->id_usuario
            )->keyBy('id_producto');

            $ids      = collect($request->items)->pluck('id_producto')->unique();
            $productos = $ids->mapWithKeys(fn($id) => [
                $id => $todosLosProductos[$id] ?? null,
            ])->filter();

            $productoModelos = $todosLosProductos
                ->flatMap(fn($p) => $p->productoModelo ?? collect())
                ->filter(fn($pm) => $pm->activo)
                ->keyBy(fn($pm) => $pm->id_modelo . ':' . $pm->id_voltaje);

            // ── Bicicletas con lock pesimista ────────────────────────────────
            $series = collect($request->items)
                ->pluck('num_serie')
                ->filter()
                ->unique()
                ->values();

            $bicicletas = $series->isNotEmpty()
                ? Bicicleta::with(['modelo.marca', 'voltaje', 'color'])
                    ->whereIn('num_serie', $series)
                    ->where('id_negocio', $user->id_negocio)
                    ->lockForUpdate()
                    ->get()
                    ->keyBy('num_serie')
                : collect();

            // ── Métodos de pago desde config ─────────────────────────────────
            $config         = CatalogService::getConfigNegocio($user->id_negocio);
            $metodosActivos = $config['metodos_pago'] ?? ['efectivo'];

            $todasOpciones = \App\Models\NegocioConfig::where('clave', 'metodos_pago')
                ->where('activo', true)
                ->value('opciones');

            $opcionesMap     = collect($todasOpciones ?? [])->keyBy('value');
            $metodosEnviados = collect($request->pagos)->pluck('metodo')->unique();

            foreach ($metodosEnviados as $m) {
                if (!in_array($m, $metodosActivos)) {
                    throw new \Exception("Método de pago '{$m}' no está habilitado para este negocio.");
                }
            }

            $metodosPago = $metodosEnviados->mapWithKeys(function ($value) use ($opcionesMap) {
                $opcion = $opcionesMap[$value] ?? [];
                return [$value => [
                    'value'               => $value,
                    'es_efectivo'         => (bool) ($opcion['es_efectivo']         ?? false),
                    'requiere_referencia' => (bool) ($opcion['requiere_referencia'] ?? false),
                ]];
            });

            $metodosEfectivo = $metodosPago->filter(fn($m) => $m['es_efectivo']);
            if ($metodosEfectivo->count() > 1) {
                throw new \Exception('Solo puede haber un método de efectivo por venta.');
            }

            // ── Cliente ──────────────────────────────────────────────────────
            $cliente = Cliente::firstOrCreate(
                [
                    'id_negocio' => $user->id_negocio,
                    'telefono'   => $request->telefono,
                ],
                [
                    'nombre_cliente' => $request->nombre_cliente,
                    'apellido1'      => $request->apellido1,
                    'apellido2'      => $request->apellido2 ?? null,
                    'correo'         => $request->correo    ?? null,
                ]
            );

            // ── Cupón ────────────────────────────────────────────────────────
            $cuponAplicado  = null;
            $descuentoTotal = 0;

            if ($request->filled('codigo_cupon')) {
                $cupon = CuponService::buscarPorCodigo(
                    $request->codigo_cupon,
                    $user->id_negocio
                );

                if ($cupon) {
                    $itemsParaValidar = collect($request->items)->map(function ($item) use ($productos) {
                        $producto = $productos[$item['id_producto']] ?? null;
                        if (!$producto) return null;

                        $pm = $producto->productoModelo?->first();

                        return [
                            'id_producto' => $item['id_producto'],
                            'id_modelo'   => $pm?->id_modelo,
                            'id_marca'    => $pm?->modelo?->id_marca,
                            'precio'      => (float) ($producto->precio ?? 0),
                            'cantidad'    => (int)   ($item['cantidad'] ?? 1),
                        ];
                    })->filter()->values()->toArray();

                    $resultado = CuponService::validar(
                        $cupon,
                        $itemsParaValidar,
                        $user->id_usuario
                    );

                    if ($resultado['valido']) {
                        $cuponAplicado  = $cupon;
                        $descuentoTotal = $resultado['descuento'];
                    }
                }
            }

            // ── Calcular total ────────────────────────────────────────────────
            $subtotal = collect($request->items)->sum(function ($item) use ($productos) {
                $producto       = $productos[$item['id_producto']] ?? null;
                $esGratis       = ($item['es_gratis'] ?? '0') === '1';
                $precioUnitario = $esGratis ? 0.00 : (float) ($producto->precio ?? 0);
                return $precioUnitario * (int) ($item['cantidad'] ?? 1);
            });

            $totalFinal = $subtotal - $descuentoTotal;

            // ── Personal (vendedor) ───────────────────────────────────────────
            $idPersonal = null;
            if ($request->filled('id_personal')) {
                $personalObj = Personal::where('id_personal', $request->id_personal)
                    ->where('id_usuario', $user->id_usuario)
                    ->first();
                if ($personalObj) {
                    $idPersonal = $personalObj->id_personal;
                }
            }

            // ── Venta ────────────────────────────────────────────────────────
            $venta = Venta::create([
                'id_negocio'      => $user->id_negocio,
                'id_cliente'      => $cliente->id_cliente,
                'id_usuario'      => $user->id_usuario,
                'id_personal'     => $idPersonal ?? null,
                'id_cupon'        => $cuponAplicado?->id_cupon,
                'descuento_total' => $descuentoTotal,
                'total'           => $totalFinal,
            ]);

            $bicicletasBroadcast  = [];
            $seriesParaInvalidar  = [];
            $eventosBicicleta     = [];
            $jobsPostVenta        = [];
            $accesoriosVendidos   = []; // para el evento StockActualizado

            // ── Items del carrito ────────────────────────────────────────────
            foreach ($request->items as $item) {
                $producto = $productos[$item['id_producto']] ?? null;
                if (!$producto) {
                    throw new \Exception("Producto {$item['id_producto']} no válido para esta sucursal.");
                }

                $cantidad       = (int)   ($item['cantidad'] ?? 1);
                $esGratis       = ($item['es_gratis'] ?? '0') === '1';
                $precioUnitario = $esGratis ? 0.00 : (float) $producto->precio;

                DetalleVenta::create([
                    'id_venta'        => $venta->id_venta,
                    'id_negocio'      => $user->id_negocio,
                    'id_producto'     => $item['id_producto'],
                    'num_serie'       => $item['num_serie'] ?? null,
                    'precio_unitario' => $precioUnitario,
                    'cantidad'        => $cantidad,
                ]);

                // ── Bicicleta ─────────────────────────────────────────────
                if ($producto->tipo === '2' && !empty($item['num_serie'])) {
                    $bici = $bicicletas[$item['num_serie']] ?? null;

                    if (!$bici) {
                        throw new \Exception("Bicicleta {$item['num_serie']} no encontrada en este negocio.");
                    }
                    if ($bici->status != 1) {
                        throw new \Exception("La bicicleta {$item['num_serie']} ya fue vendida o no está disponible.");
                    }
                    if ($bici->id_usuario !== null && $bici->id_usuario !== $user->id_usuario) {
                        throw new \Exception("La bicicleta {$item['num_serie']} no pertenece a esta sucursal.");
                    }

                    $bici->status = 2;
                    $bici->save();

                    $jobsPostVenta[] = new ProcesarPostVenta(
                        numSerie:       $bici->num_serie,
                        idMarca:        $bici->modelo->id_marca,
                        idNegocio:      $user->id_negocio,
                        nombreCliente:  $request->nombre_cliente . ' ' . $request->apellido1,
                        idVenta:        $venta->id_venta,
                        nombreVendedor: $user->nombre_usuario,
                    );

                    $eventosBicicleta[] = [
                        'num_serie' => $bici->num_serie,
                        'modelo'    => $bici->modelo->nombre_modelo      ?? '—',
                        'marca'     => $bici->modelo->marca->nombre_marca ?? '—',
                        'voltaje'   => $bici->voltaje->voltaje            ?? '—',
                        'color'     => $bici->color->color                ?? '—',
                        'status'    => 2,
                    ];

                    $pmKey = $bici->id_modelo . ':' . $bici->id_voltaje;
                    $pm    = $productoModelos[$pmKey] ?? null;

                    if ($pm) {
                        $inventario = Inventario::where('id_producto_modelo', $pm->id_producto_modelo)
                            ->where('id_negocio', $user->id_negocio)
                            ->where('id_usuario', $user->id_usuario)
                            ->first();

                        if (!$inventario || $inventario->cantidad < 1) {
                            throw new \Exception("Stock insuficiente para la bicicleta {$bici->num_serie}.");
                        }
                        $inventario->decrement('cantidad', 1);
                    }

                    $seriesParaInvalidar[] = $bici->num_serie;

                    $bicicletasBroadcast[] = [
                        'num_serie'      => $bici->num_serie,
                        'modelo'         => $bici->modelo->nombre_modelo      ?? '—',
                        'marca'          => $bici->modelo->marca->nombre_marca ?? '—',
                        'voltaje'        => $bici->voltaje->voltaje            ?? '—',
                        'color'          => $bici->color->color                ?? '—',
                        'tiene_garantia' => true,
                    ];
                }
                // ── Accesorio ─────────────────────────────────────────────
                elseif ($producto->tipo === '1') {
                    $inventario = Inventario::where('id_producto', $item['id_producto'])
                        ->where('id_negocio', $user->id_negocio)
                        ->where('id_usuario', $user->id_usuario)
                        ->whereNull('id_producto_modelo')
                        ->first();

                    if (!$inventario || $inventario->cantidad < $cantidad) {
                        throw new \Exception("Stock insuficiente para el producto {$producto->nombre_producto}.");
                    }
                    $inventario->decrement('cantidad', $cantidad);

                    // Guardar el nuevo stock para el evento Reverb
                    $accesoriosVendidos[] = [
                        'id_producto' => $item['id_producto'],
                        'stock'       => max(0, (int) $inventario->cantidad - $cantidad),
                    ];
                }
            }

            // ── Producto gratis por cupón ─────────────────────────────────
            if ($cuponAplicado?->id_producto_gratis) {
                $idGratis    = $cuponAplicado->id_producto_gratis;
                $yaInsertado = collect($request->items)
                    ->contains(fn($i) => $i['id_producto'] === $idGratis);

                if (!$yaInsertado) {
                    $productoGratis = $productos[$idGratis] ?? null;
                    if ($productoGratis) {
                        DetalleVenta::create([
                            'id_venta'        => $venta->id_venta,
                            'id_negocio'      => $user->id_negocio,
                            'id_producto'     => $productoGratis->id_producto,
                            'num_serie'       => null,
                            'precio_unitario' => 0.00,
                            'cantidad'        => 1,
                        ]);
                    }
                }
            }

            // ── Validación del total ──────────────────────────────────────
            $sumaPagos = collect($request->pagos)->sum('monto');

            if (round($sumaPagos, 2) < round($totalFinal, 2)) {
                throw new \Exception('La suma de pagos no cubre el total de la venta.');
            }

            // ── Pagos ─────────────────────────────────────────────────────
            foreach ($request->pagos as $pago) {
                VentaPago::create([
                    'id_venta'   => $venta->id_venta,
                    'id_negocio' => $user->id_negocio,
                    'metodo'     => $pago['metodo'],
                    'monto'      => $pago['monto'],
                    'referencia' => $pago['referencia'] ?? null,
                ]);
            }

            DB::commit();

           
            $snapshot = null;
            try {
                $caja = CajaService::cajaDeUsuario($user->id_usuario, $user->id_negocio);

                if ($caja) {
                    $sesionActiva = CajaService::sesionActiva($caja->id_caja);

                    if (!$sesionActiva) {
                        // No hay sesión — abrimos automáticamente con fondo 0
                        $sesionActiva = CajaService::abrirSesion(
                            caja:         $caja,
                            idUsuario:    $user->id_usuario,
                            fondoInicial: 0.0,
                        );

                        Log::info('CajaService: auto-apertura de sesión al registrar venta', [
                            'id_sesion'  => $sesionActiva->id_sesion,
                            'id_venta'   => $venta->id_venta,
                            'id_usuario' => $user->id_usuario,
                        ]);
                    }

                    $snapshot = CajaService::registrarVenta(
                        venta:     $venta,
                        idUsuario: $user->id_usuario,
                        idNegocio: $user->id_negocio,
                    );
                }
            } catch (\Throwable $cajaEx) {
                Log::error('CajaService: error inesperado al registrar venta', [
                    'id_venta' => $venta->id_venta,
                    'mensaje'  => $cajaEx->getMessage(),
                ]);
            }

            // ── VentaRegistrada (dashboard en tiempo real) ─────────────────
            if ($snapshot) {
                event(new VentaRegistrada(
                    idNegocio:      $user->id_negocio,
                    idUsuario:      $user->id_usuario,
                    nombreVendedor: $user->nombre_usuario,
                    total:          $totalFinal,
                    ventasCount:    $snapshot['ventas_count'],
                    totalSistema:   $snapshot['totales']['total_sistema'],
                ));
            }

            // ── Post-commit ───────────────────────────────────────────────
            if ($cuponAplicado) {
                CuponService::registrarUso(
                    idCupon:           $cuponAplicado->id_cupon,
                    idVenta:           $venta->id_venta,
                    idNegocio:         $user->id_negocio,
                    idUsuario:         $user->id_usuario,
                    descuentoAplicado: $descuentoTotal,
                );
            }

            $modoEntrega = $config['entrega_comprobante'] ?? 'ticket';
            $debeCorreo  = in_array($modoEntrega, ['correo', 'ambos']) && !empty($cliente->correo);
            $debeTicket  = in_array($modoEntrega, ['ticket', 'ambos']);

            $totalJobs = count($jobsPostVenta);
            foreach ($jobsPostVenta as $i => $job) {
                if ($debeCorreo && $i === $totalJobs - 1) {
                    $job->enviarCorreo = true;
                }
                dispatch($job);
            }

            // ── Invalidaciones de caché ───────────────────────────────────
            foreach ($seriesParaInvalidar as $serie) {
                CatalogService::invalidateBicicleta($serie, $user->id_negocio);
            }
            CatalogService::invalidateBicicletasPorUsuario($user->id_usuario, $user->id_negocio);
            CatalogService::invalidateSeccion($user->id_usuario, $user->id_negocio);
            CatalogService::invalidateSeccion(null, $user->id_negocio);
            CatalogService::invalidateVentasByVendedor($user->id_negocio, $user->id_usuario);
            CatalogService::invalidateInventario($user->id_negocio, $user->id_usuario);

            // ── Broadcasts ────────────────────────────────────────────────
            foreach ($eventosBicicleta as $ev) {
                event(new BicicletaActualizada(
                    numSerie:       $ev['num_serie'],
                    idNegocio:      $user->id_negocio,
                    idUsuario:      $user->id_usuario,
                    nombreVendedor: $user->nombre_usuario,
                    modelo:         $ev['modelo'],
                    voltaje:        $ev['voltaje'],
                    color:          $ev['color'],
                    status:         $ev['status'],
                ));
            }

            event(new VentaRealizada(
                idVenta:        $venta->id_venta,
                idNegocio:      $user->id_negocio,
                idVendedor:     $user->id_usuario,
                nombreVendedor: $user->nombre_usuario,
                nombreCliente:  $cliente->nombre_cliente . ' ' . $cliente->apellido1,
                total:          $totalFinal,
                bicicletas:     $bicicletasBroadcast,
            ));

            if (!empty($accesoriosVendidos)) {
                // Re-leer el stock real post-decrement desde DB para precisión
                $stockFinal = collect($accesoriosVendidos)->map(function ($item) use ($user) {
                    $inv = Inventario::where('id_producto', $item['id_producto'])
                        ->where('id_negocio', $user->id_negocio)
                        ->where('id_usuario', $user->id_usuario)
                        ->whereNull('id_producto_modelo')
                        ->value('cantidad');

                    return [
                        'id_producto' => $item['id_producto'],
                        'stock'       => (int) ($inv ?? 0),
                    ];
                })->toArray();

                event(new StockActualizado(
                    idNegocio: $user->id_negocio,
                    idUsuario: $user->id_usuario,
                    accesorios: $stockFinal,
                ));
            }

            // ── Respuesta final ───────────────────────────────────────────
            if ($debeCorreo && $debeTicket) {
                session()->flash('auto_ticket', $venta->id_venta);
                $mensajeExito = 'Venta registrada. Ticket generado y comprobante enviado al correo del cliente.';
            } elseif ($debeCorreo) {
                $mensajeExito = 'Venta registrada. Comprobante enviado al correo del cliente.';
            } elseif ($debeTicket) {
                session()->flash('auto_ticket', $venta->id_venta);
                $mensajeExito = 'Venta registrada. Generando ticket...';
            } else {
                $mensajeExito = 'Venta registrada correctamente.';
            }

            return redirect()
                ->route('ventas.show', $venta->id_venta)
                ->with('success', $mensajeExito);

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Error al registrar venta', [
                'mensaje' => $e->getMessage(),
                'usuario' => $user->id_usuario,
                'negocio' => $user->id_negocio,
                'request' => $request->except('_token'),
                'linea'   => $e->getLine(),
                'archivo' => $e->getFile(),
            ]);

            return back()
                ->withInput()
                ->with('error', 'Ocurrió un error al procesar la venta: ' . $e->getMessage());
        }
    }

    /* =====================================================
     | SHOW
     ===================================================== */

    public function show(string $id_venta)
    {
        $user = auth()->user();

        $venta = Venta::with([
            'cliente',
            'detalles.producto',
            'detalles.bicicleta.modelo.marca',
            'detalles.bicicleta.voltaje',
            'detalles.bicicleta.color',
            'personal',
            'pagos',
        ])
            ->where('id_negocio', $user->id_negocio)
            ->findOrFail($id_venta);

        $tieneGarantia = $venta->detalles
            ->filter(fn($d) => $d->bicicleta !== null)
            ->contains(function ($detalle) use ($user) {
                $idMarca = $detalle->bicicleta->modelo->id_marca ?? null;
                if (!$idMarca) return false;
                return \App\Models\MarcaGarantiaConfig::where('id_marca', $idMarca)
                    ->where('id_negocio', $user->id_negocio)
                    ->where('activa', true)
                    ->exists();
            });

        $autoTicket = session()->pull('auto_ticket') === $id_venta;

        return view('vendedor.ventas.show', compact('venta', 'tieneGarantia', 'autoTicket'));
    }

    /* =====================================================
     | PDF — póliza de garantía
     ===================================================== */

    public function poliza(string $id_venta)
    {
        $user = auth()->user();

        $venta = Venta::with([
            'cliente',
            'negocio',
            'detalles.producto',
            'detalles.bicicleta.modelo.marca',
            'detalles.bicicleta.voltaje',
            'detalles.bicicleta.color',
            'personal',
            'pagos',
        ])
            ->where('id_negocio', $user->id_negocio)
            ->findOrFail($id_venta);

        $bicicletas = $venta->detalles
            ->filter(fn($d) => $d->bicicleta !== null)
            ->values();

        if ($bicicletas->isEmpty()) {
            return back()->with('error', 'Esta venta no tiene bicicletas para generar póliza.');
        }

        $idMarcas = $bicicletas
            ->map(fn($d) => $d->bicicleta->modelo->id_marca ?? null)
            ->filter()
            ->unique()
            ->values();

        $configs = \App\Models\MarcaGarantiaConfig::with([
            'componenteDefs' => fn($q) => $q->where('activo', true)->orderBy('duracion_meses'),
            'marca',
        ])
            ->where('id_negocio', $user->id_negocio)
            ->whereIn('id_marca', $idMarcas)
            ->where('activa', true)
            ->get()
            ->keyBy('id_marca');

        $componentesPorMarca = $idMarcas->mapWithKeys(function ($idMarca) use ($configs) {
            $config = $configs->get($idMarca);
            return [
                $idMarca => [
                    'marca'       => $config?->marca,
                    'componentes' => $config?->componenteDefs ?? collect(),
                ],
            ];
        });

        $pdf = Pdf::loadView('vendedor.ventas.poliza', [
            'venta'               => $venta,
            'cliente'             => $venta->cliente,
            'negocio'             => $venta->negocio,
            'bicicletas'          => $bicicletas,
            'personal'            => $venta->personal,
            'fecha'               => now()->format('d/m/Y'),
            'pagos'               => $venta->pagos,
            'componentesPorMarca' => $componentesPorMarca,
        ])->setPaper('letter', 'landscape');

        return response()->make($pdf->output(), 200, [
            'Content-Type'        => 'application/pdf',
            'Content-Disposition' => 'inline; filename="poliza-' . $venta->id_venta . '.pdf"',
        ]);
    }

    /* =====================================================
     | PDF — ticket
     ===================================================== */

    public function ticket(string $id_venta)
    {
        $user = auth()->user();

        $venta = Venta::with([
            'cliente',
            'negocio',
            'detalles.producto',
            'detalles.bicicleta.modelo.marca',
            'detalles.bicicleta.voltaje',
            'detalles.bicicleta.color',
            'personal',
            'pagos',
        ])
            ->where('id_negocio', $user->id_negocio)
            ->findOrFail($id_venta);

        $bicicletas = $venta->detalles
            ->filter(fn($d) => $d->bicicleta !== null)
            ->values();

        if ($bicicletas->isEmpty()) {
            return back()->with('error', 'Esta venta no tiene bicicletas para generar ticket.');
        }

        $pdf = Pdf::loadView('vendedor.ventas.ticket', [
            'venta'      => $venta,
            'cliente'    => $venta->cliente,
            'negocio'    => $venta->negocio,
            'bicicletas' => $bicicletas,
            'personal'   => $venta->personal,
            'fecha'      => now()->format('d/m/Y'),
            'pagos'      => $venta->pagos,
        ])->setPaper('letter', 'landscape');

        return response()->make($pdf->output(), 200, [
            'Content-Type'        => 'application/pdf',
            'Content-Disposition' => 'inline; filename="ticket-' . $venta->id_venta . '.pdf"',
        ]);
    }
}