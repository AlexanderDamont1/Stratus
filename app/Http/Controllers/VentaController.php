<?php

namespace App\Http\Controllers;

use App\Events\VentaRealizada;
use App\Events\BicicletaActualizada;
use App\Models\Bicicleta;
use App\Models\Cliente;
use App\Models\DetalleVenta;
use App\Models\Producto;
use App\Models\ProductoModelo;
use App\Models\Venta;
use App\Models\Inventario;
use App\Services\CatalogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Personal;
use App\Models\MetodoPago;
use App\Models\VentaPago;
use App\Models\VentaVendedor;
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
            // getVentasByVendedor cachea con versión del tenant (TTL 5 min).
            // Se invalida con invalidateVentasByVendedor() tras cada venta.
            $ventas = CatalogService::getVentasByVendedor(
                $user->id_negocio,
                $user->id_usuario
            );
        } else {
            // FIX: La query original filtraba por id_usuario en la tabla ventas,
            // pero queryVentas() filtra por whereHas('detalles.producto', id_usuario).
            // Ambas deben ser consistentes. Se usa la misma lógica que queryVentas
            // para que los resultados de página 2+ coincidan con página 1 del caché.
            $ventas = Venta::with([
                    'cliente',
                    'detalles.producto',
                    'detalles.bicicleta.modelo',
                    'detalles.bicicleta.color',
                ])
                ->where('id_negocio', $user->id_negocio)
                ->whereHas('detalles.producto', fn($q) => $q->where('id_usuario', $user->id_usuario))
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

        // FIX: Reemplaza query directa a DB por CatalogService.
        // getProductosConRelaciones cachea con versión del tenant (TTL 1h)
        // y se invalida automáticamente con invalidateProductosConRelaciones().
        // Se filtra tipo='1' y precio > 0 en memoria (colección pequeña).
        $accesorios = CatalogService::getProductosConRelaciones($user->id_negocio, $user->id_usuario)
            ->where('tipo', '1')
            ->filter(fn($p) => $p->precio > 0)
            ->values();

        // MetodoPago y Personal son datos operativos de baja cardinalidad —
        // se podrían cachear, pero su TTL sería muy corto. Se deja sin caché
        // ya que create() no es un endpoint de alta frecuencia.
        $metodos  = MetodoPago::deNegocio($user->id_negocio)->activo()->get();
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

        // getBicicletaBySerie cachea con versión del tenant.
        // Se invalida con invalidateBicicleta() tras ventas y movimientos.
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

        // ProductoModelo se consulta directo porque depende de la bicicleta
        // encontrada en tiempo real y tiene condiciones compuestas difíciles
        // de cachear de forma granular. La bicicleta ya venía de caché.
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
            $parts = explode('|', $colorString, 2);
            $colorNombre = trim($parts[0]);
            if (isset($parts[1])) {
                $colorHexes = explode('/', $parts[1]);
            }
        }

        return response()->json([
            'ok'   => true,
            'bici' => [
                'num_serie'    => $bici->num_serie,
                'marca'        => $bici->modelo->marca->nombre_marca ?? '—',
                'modelo'       => $bici->modelo->nombre_modelo        ?? '—',
                'voltaje'      => $bici->voltaje->voltaje              ?? '—',
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
            'nombre_cliente'       => 'required|string|max:100',
            'apellido1'            => 'required|string|max:60',
            'apellido2'            => 'nullable|string|max:60',
            'telefono'             => 'required|string|max:15',
            'correo'               => 'nullable|email|max:120',
            'direccion'            => 'nullable|string|max:255',
            'items'                => 'required|array|min:1',
            'items.*.id_producto'  => 'required|exists:productos,id_producto',
            'items.*.num_serie'    => 'nullable|string',
            'items.*.cantidad'     => 'required|integer|min:1',
            'codigo_cupon'         => 'nullable|string|max:30',
            'pagos'                => 'required|array|min:1',
            'pagos.*.id_metodo'    => 'required|exists:metodos_pago,id_metodo',
            'pagos.*.monto'        => 'required|numeric|min:0.01',
            'pagos.*.referencia'   => 'nullable|string|max:120',
            'monto_recibido'       => 'nullable|numeric|min:0',
            'id_personal'          => 'nullable|exists:personal,id_personal',
        ]);

        DB::beginTransaction();

        try {
            // ── Pre-carga desde caché ────────────────────────────────────────
            // getProductosConRelaciones cachea con versión del tenant (TTL 1h).
            $todosLosProductos = CatalogService::getProductosConRelaciones(
                $user->id_negocio,
                $user->id_usuario
            )->keyBy('id_producto');

            $ids = collect($request->items)->pluck('id_producto')->unique();
            $productos = $ids->mapWithKeys(fn($id) => [
                $id => $todosLosProductos[$id] ?? null,
            ])->filter();

            $productoModelos = $todosLosProductos
                ->flatMap(fn($p) => $p->productoModelo ?? collect())
                ->filter(fn($pm) => $pm->activo)
                ->keyBy(fn($pm) => $pm->id_modelo . ':' . $pm->id_voltaje);

            // ── Bicicletas con lock pesimista (siempre DB, no caché) ─────────
            // lockForUpdate() requiere una query real — no se puede cachear.
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

            // ── Métodos de pago ──────────────────────────────────────────────
            $idMetodos   = collect($request->pagos)->pluck('id_metodo')->unique();
            $metodosPago = MetodoPago::whereIn('id_metodo', $idMetodos)->get()->keyBy('id_metodo');

            // ── Cliente ───────────────────────────────────────────────────────
            $cliente = Cliente::firstOrCreate(
                [
                    'id_negocio' => $user->id_negocio,
                    'telefono'   => $request->telefono,
                ],
                [
                    'nombre_cliente' => $request->nombre_cliente,
                    'apellido1'      => $request->apellido1,
                    'apellido2'      => $request->apellido2 ?? null,
                    'correo'         => $request->correo ?? null,
                ]
            );

            // ── Cupón ─────────────────────────────────────────────────────────
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
                            'cantidad'    => (int) ($item['cantidad'] ?? 1),
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

            // ── Venta ─────────────────────────────────────────────────────────
            $venta = Venta::create([
                'id_negocio'      => $user->id_negocio,
                'id_cliente'      => $cliente->id_cliente,
                'id_cupon'        => $cuponAplicado?->id_cupon,
                'descuento_total' => $descuentoTotal,
            ]);

            $totalVenta          = 0;
            $bicicletasBroadcast = [];
            $seriesParaInvalidar = [];
            $eventosBicicleta    = [];
            $jobsPostVenta       = [];

            // ── Items del carrito ─────────────────────────────────────────────
            foreach ($request->items as $item) {
                $producto = $productos[$item['id_producto']] ?? null;
                if (!$producto) {
                    throw new \Exception("Producto {$item['id_producto']} no válido para esta sucursal.");
                }

                $cantidad       = (int) ($item['cantidad'] ?? 1);
                $esGratis       = ($item['es_gratis'] ?? '0') === '1';
                $precioUnitario = $esGratis ? 0.00 : (float) $producto->precio;

                DetalleVenta::create([
                    'id_venta'        => $venta->id_venta,
                    'id_producto'     => $item['id_producto'],
                    'num_serie'       => $item['num_serie'] ?? null,
                    'precio_unitario' => $precioUnitario,
                    'cantidad'        => $cantidad,
                ]);

                $totalVenta += $precioUnitario * $cantidad;

                // ── Bicicleta ─────────────────────────────────────────────────
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
                        'modelo'    => $bici->modelo->nombre_modelo          ?? '—',
                        'marca'     => $bici->modelo->marca->nombre_marca    ?? '—',
                        'voltaje'   => $bici->voltaje->voltaje               ?? '—',
                        'color'     => $bici->color->color                   ?? '—',
                        'status'    => 2,
                    ];

                    // Decremento seguro de inventario
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
                        'modelo'         => $bici->modelo->nombre_modelo       ?? '—',
                        'marca'          => $bici->modelo->marca->nombre_marca ?? '—',
                        'voltaje'        => $bici->voltaje->voltaje             ?? '—',
                        'color'          => $bici->color->color                 ?? '—',
                        'tiene_garantia' => true,
                    ];
                }
                // ── Accesorio ─────────────────────────────────────────────────
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
                }
            }

            // ── Producto gratis por cupón ──────────────────────────────────────
            if ($cuponAplicado?->id_producto_gratis) {
                $idGratis    = $cuponAplicado->id_producto_gratis;
                $yaInsertado = collect($request->items)
                    ->contains(fn($i) => $i['id_producto'] === $idGratis);

                if (!$yaInsertado) {
                    $productoGratis = $productos[$idGratis] ?? null;
                    if ($productoGratis) {
                        DetalleVenta::create([
                            'id_venta'        => $venta->id_venta,
                            'id_producto'     => $productoGratis->id_producto,
                            'num_serie'       => null,
                            'precio_unitario' => 0.00,
                            'cantidad'        => 1,
                        ]);
                    }
                }
            }

            // ── Validación del total ───────────────────────────────────────────
            $totalFinal = $totalVenta - $descuentoTotal;
            $sumaPagos  = collect($request->pagos)->sum('monto');

            if (round($sumaPagos, 2) < round($totalFinal, 2)) {
                throw new \Exception('La suma de pagos no cubre el total de la venta.');
            }

            // ── Efectivo y cambio ──────────────────────────────────────────────
            $tieneEfectivo = $metodosPago->contains(fn($m) => $m->es_efectivo);
            $montoRecibido = null;
            $cambio        = null;

            if ($tieneEfectivo) {
                $montoRecibido = (float) ($request->monto_recibido ?? 0);
                $cambio        = max(0, $montoRecibido - $totalFinal);
            }

            $venta->monto_recibido = $montoRecibido;
            $venta->cambio         = $cambio;
            $venta->save();

            // ── Pagos ──────────────────────────────────────────────────────────
            foreach ($request->pagos as $pago) {
                VentaPago::create([
                    'id_venta'   => $venta->id_venta,
                    'id_metodo'  => $pago['id_metodo'],
                    'id_negocio' => $user->id_negocio,
                    'monto'      => $pago['monto'],
                    'referencia' => $pago['referencia'] ?? null,
                ]);
            }

            // ── Vendedor asignado ──────────────────────────────────────────────
            if ($request->filled('id_personal')) {
                $personal = Personal::find($request->id_personal);
                if ($personal) {
                    VentaVendedor::create([
                        'id_venta'        => $venta->id_venta,
                        'id_personal'     => $personal->id_personal,
                        'nombre_snapshot' => $personal->nombre,
                    ]);
                }
            }

            DB::commit();

            // ── Post-commit: cupón, jobs, caché, broadcasts ────────────────────

            if ($cuponAplicado) {
                CuponService::registrarUso(
                    idCupon:            $cuponAplicado->id_cupon,
                    idVenta:            $venta->id_venta,
                    idNegocio:          $user->id_negocio,
                    idUsuario:          $user->id_usuario,
                    descuentoAplicado:  $descuentoTotal,
                );
            }

            // getConfigNegocio cachea con versión del tenant (TTL 1h).
            // Solo se invalida cuando el admin cambia la config del negocio.
            $config      = CatalogService::getConfigNegocio($user->id_negocio);
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

            // Invalidaciones de caché — todas post-commit para no afectar
            // lecturas que ocurran durante la transacción.
            foreach ($seriesParaInvalidar as $serie) {
                CatalogService::invalidateBicicleta($serie, $user->id_negocio);
            }
            CatalogService::invalidateBicicletasPorUsuario($user->id_usuario, $user->id_negocio);
            CatalogService::invalidateSeccion($user->id_usuario, $user->id_negocio);
            CatalogService::invalidateSeccion(null, $user->id_negocio);
            CatalogService::invalidateVentasByVendedor($user->id_negocio, $user->id_usuario);
            // FIX: invalidateInventario llama incrementVersion internamente —
            // debe ir al final para no invalidar claves que otros métodos
            // anteriores aún necesitan leer con la versión actual.
            CatalogService::invalidateInventario($user->id_negocio, $user->id_usuario);

            // Broadcasts
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

            // ── Respuesta final ────────────────────────────────────────────────
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

        // Venta individual: se consulta directo porque incluye relaciones
        // profundas (pagos, vendedor) que no están en el caché de ventas,
        // y es un acceso de baja frecuencia (post-venta o consulta puntual).
        $venta = Venta::with([
            'cliente',
            'detalles.producto',
            'detalles.bicicleta.modelo.marca',
            'detalles.bicicleta.voltaje',
            'detalles.bicicleta.color',
            'vendedor.personal',
            'pagos.metodo',
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
            'vendedor.personal',
            'pagos.metodo',
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
            'vendedor'            => $venta->vendedor,
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
            'vendedor.personal',
            'pagos.metodo',
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
            'vendedor'   => $venta->vendedor,
            'fecha'      => now()->format('d/m/Y'),
            'pagos'      => $venta->pagos,
        ])->setPaper('letter', 'landscape');

        return response()->make($pdf->output(), 200, [
            'Content-Type'        => 'application/pdf',
            'Content-Disposition' => 'inline; filename="ticket-' . $venta->id_venta . '.pdf"',
        ]);
    }
}