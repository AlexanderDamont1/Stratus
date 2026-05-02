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
use App\Services\BicicletaMovimientoService;
use App\Services\GarantiaService;
use App\Models\Personal;
use App\Models\MetodoPago;
use App\Models\VentaPago;
use App\Models\VentaVendedor;


class VentaController extends Controller
{
    /* =====================================================
     | INDEX
     ===================================================== */
    public function index(Request $request)
    {
        $user = auth()->user();
        if ($user->id_rol != 2)
            abort(403);

        $page = $request->get('page', 1);

        // Solo cachea página 1 — las demás van directo a DB
        if ($page == 1) {
            $ventas = CatalogService::getVentasByVendedor(
                $user->id_negocio,
                $user->id_usuario
            );
        } else {
            $ventas = self::queryVentas($user->id_negocio, $user->id_usuario)
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
        if ($user->id_rol != 2) abort(403);

        $accesorios = Producto::where('id_negocio', $user->id_negocio)
            ->where('id_usuario', $user->id_usuario)
            ->where('tipo', '1')
            ->get()
            ->filter(fn($p) => $p->precio > 0)
            ->values();

        return view('vendedor.ventas.create', compact('accesorios'));
    }

    /* =====================================================
     | AJAX — buscar bicicleta por num_serie
     ===================================================== */
    public function buscarSerie(Request $request)
    {
        $user = auth()->user();
        if ($user->id_rol != 2)
            abort(403);

        $numSerie = strtoupper(trim($request->get('num_serie', '')));

        if (!$numSerie) {
            return response()->json([
                'ok' => false,
                'mensaje' => 'Indica un número de serie.',
            ], 422);
        }

        $bici = CatalogService::getBicicletaBySerie($numSerie);

        if (
            !$bici ||
            $bici->id_negocio !== $user->id_negocio ||
            $bici->status != 1 ||
            ($bici->id_usuario !== null && $bici->id_usuario !== $user->id_usuario)
        ) {
            return response()->json([
                'ok' => false,
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
                'ok' => false,
                'mensaje' => 'Esta bicicleta no tiene un producto configurado.',
            ], 404);
        }

        if (!$pm->producto->precio || $pm->producto->precio <= 0) {
            return response()->json([
                'ok' => false,
                'mensaje' => 'Esta bicicleta no tiene un precio configurado.',
            ], 404);
        }

        return response()->json([
            'ok' => true,
            'bici' => [
                'num_serie' => $bici->num_serie,
                'marca' => $bici->modelo->marca->nombre_marca ?? '—',
                'modelo' => $bici->modelo->nombre_modelo ?? '—',
                'voltaje' => $bici->voltaje->voltaje ?? '—',
                'color_nombre' => $bici->color->color ?? '—',
                'color_hexes' => [],
            ],
            'producto' => [
                'id_producto' => $pm->producto->id_producto,
                'nombre' => $pm->producto->nombre_producto,
                'precio' => (float) $pm->producto->precio,
            ],
        ]);
    }

    /* =====================================================
     | STORE
     ===================================================== */
    public function store(Request $request)
    {
        $user = auth()->user();
        if ($user->id_rol != 2) abort(403);

        $request->validate([
            'nombre_cliente' => 'required|string|max:100',
            'apellido1' => 'required|string|max:60',
            'apellido2' => 'nullable|string|max:60',
            'telefono' => 'required|string|max:15',
            'correo' => 'nullable|email|max:120',
            'direccion' => 'nullable|string|max:255',
            'items' => 'required|array|min:1',
            'items.*.id_producto' => 'required|exists:productos,id_producto',
            'items.*.num_serie' => 'nullable|string',
            'items.*.cantidad' => 'required|integer|min:1',
            'codigo_cupon' => 'nullable|string|max:30',
            'pagos' => 'required|array|min:1',
            'pagos.*.id_metodo' => 'required|exists:metodos_pago,id_metodo',
            'pagos.*.monto' => 'required|numeric|min:0.01',
            'pagos.*.referencia' => 'nullable|string|max:120',
            'monto_recibido' => 'nullable|numeric|min:0',
            'cambio' => 'nullable|numeric|min:0',
            'id_personal' => 'nullable|exists:personal,id_personal',
        ]);

        DB::beginTransaction();

        try {
            $cliente = Cliente::firstOrCreate(
                [
                    'id_negocio' => $user->id_negocio,
                    'telefono' => $request->telefono,
                ],
                [
                    'nombre_cliente' => $request->nombre_cliente,
                    'apellido1' => $request->apellido1,
                    'apellido2' => $request->apellido2 ?? null,
                    'correo' => $request->correo ?? null,
                ]
            );


            // ── Cupón ──────────────────────────────────────────────────────────
            $cuponAplicado = null;
            $descuentoTotal = 0;

            if ($request->filled('codigo_cupon')) {
                $cupon = \App\Services\CuponService::buscarPorCodigo(
                    $request->codigo_cupon,
                    $user->id_negocio
                );

                if ($cupon) {
                    $itemsParaValidar = collect($request->items)->map(function ($item) use ($user) {
                        $producto = Producto::with('productoModelo.modelo.marca')
                            ->where('id_producto', $item['id_producto'])
                            ->where('id_negocio', $user->id_negocio)
                            ->first();

                        $pm = $producto?->productoModelo?->first();

                        return [
                            'id_producto' => $item['id_producto'],
                            'id_modelo' => $pm?->id_modelo,
                            'id_marca' => $pm?->modelo?->id_marca,
                            'precio' => (float) ($producto?->precio ?? 0),
                            'cantidad' => (int) ($item['cantidad'] ?? 1),
                        ];
                    })->filter()->values()->toArray();

                    $resultado = \App\Services\CuponService::validar(
                        $cupon,
                        $itemsParaValidar,
                        $user->id_usuario
                    );

                    if ($resultado['valido']) {
                        $cuponAplicado = $cupon;
                        $descuentoTotal = $resultado['descuento'];
                    }
                }
            }

            $venta = Venta::create([
                'id_negocio' => $user->id_negocio,
                'id_cliente' => $cliente->id_cliente,
                'id_cupon' => $cuponAplicado?->id_cupon,
                'descuento_total' => $descuentoTotal,
            ]);

            $totalVenta = 0;
            $bicicletasBroadcast = [];
            $seriesParaInvalidar = [];
            $eventosBicicleta = [];
            $jobsPostVenta = [];

            // ── Items del carrito ───────────────────────────────────────────────
            foreach ($request->items as $item) {

                $producto = Producto::where('id_producto', $item['id_producto'])
                    ->where('id_usuario', $user->id_usuario)
                    ->where('id_negocio', $user->id_negocio)
                    ->firstOrFail();

                $cantidad = (int) ($item['cantidad'] ?? 1);
                $precioUnitario = (float) $producto->precio;

                DetalleVenta::create([
                    'id_venta' => $venta->id_venta,
                    'id_producto' => $item['id_producto'],
                    'num_serie' => $item['num_serie'] ?? null,
                    'precio_unitario' => $precioUnitario,
                    'cantidad' => $cantidad,
                ]);

                $totalVenta += $precioUnitario * $cantidad;

                if ($producto->tipo === '2' && !empty($item['num_serie'])) {

                    $bici = Bicicleta::with(['modelo.marca', 'voltaje', 'color'])
                        ->where('num_serie', $item['num_serie'])
                        ->where('id_negocio', $user->id_negocio)
                        ->where('id_usuario', $user->id_usuario)
                        ->where('status', 1)
                        ->lockForUpdate()
                        ->firstOrFail();

                    $bici->status = 2;
                    $bici->save();

                    $jobsPostVenta[] = new \App\Jobs\ProcesarPostVenta(
                        numSerie: $bici->num_serie,
                        idMarca: $bici->modelo->id_marca,
                        idNegocio: $user->id_negocio,
                        nombreCliente: $request->nombre_cliente . ' ' . $request->apellido1,
                        idVenta: $venta->id_venta,
                        nombreVendedor: $user->nombre_usuario,
                    );

                    $eventosBicicleta[] = [
                        'num_serie' => $bici->num_serie,
                        'modelo' => $bici->modelo->nombre_modelo ?? '—',
                        'marca' => $bici->modelo->marca->nombre_marca ?? '—',
                        'voltaje' => $bici->voltaje->voltaje ?? '—',
                        'color' => $bici->color->color ?? '—',
                        'status' => 2,
                    ];

                    $pm = ProductoModelo::where('id_modelo', $bici->id_modelo)
                        ->where('id_voltaje', $bici->id_voltaje)
                        ->where('id_usuario', $user->id_usuario)
                        ->first();

                    if ($pm) {
                        Inventario::where('id_producto_modelo', $pm->id_producto_modelo)
                            ->where('id_negocio', $user->id_negocio)
                            ->where('id_usuario', $user->id_usuario)
                            ->where('cantidad', '>', 0)
                            ->decrement('cantidad');
                    }

                    $seriesParaInvalidar[] = $bici->num_serie;

                    $bicicletasBroadcast[] = [
                        'num_serie' => $bici->num_serie,
                        'modelo' => $bici->modelo->nombre_modelo ?? '—',
                        'marca' => $bici->modelo->marca->nombre_marca ?? '—',
                        'voltaje' => $bici->voltaje->voltaje ?? '—',
                        'color' => $bici->color->color ?? '—',
                        'tiene_garantia' => true,
                    ];
                } elseif ($producto->tipo === '1') {

                    Inventario::where('id_producto', $item['id_producto'])
                        ->where('id_negocio', $user->id_negocio)
                        ->where('id_usuario', $user->id_usuario)
                        ->whereNull('id_producto_modelo')
                        ->where('cantidad', '>', 0)
                        ->decrement('cantidad', $cantidad);
                }
            }

            // ── Accesorio gratis por cupón ──────────────────────────────────────
            if ($cuponAplicado?->id_producto_gratis) {
                $productoGratis = Producto::where('id_producto', $cuponAplicado->id_producto_gratis)
                    ->where('id_negocio', $user->id_negocio)
                    ->first();

                if ($productoGratis) {
                    DetalleVenta::create([
                        'id_venta' => $venta->id_venta,
                        'id_producto' => $productoGratis->id_producto,
                        'num_serie' => null,
                        'precio_unitario' => 0.00,
                        'cantidad' => 1,
                    ]);
                }
            }

            $sumaPagos = collect($request->pagos)->sum('monto');
            // $totalVenta - $descuentoTotal se calcula antes de este punto
            // (ya existía en tu código original)
            if (round($sumaPagos, 2) < round($totalVenta - $descuentoTotal, 2)) {
                throw new \Exception('La suma de pagos no cubre el total de la venta.');
            }
            // Actualizar monto_recibido y cambio en ventas (solo si hay efectivo)
            $tieneEfectivo = false;
            foreach ($request->pagos as $pago) {
                $metodo = \App\Models\MetodoPago::find($pago['id_metodo']);
                if ($metodo?->es_efectivo) {
                    $tieneEfectivo = true;
                    break;
                }
            }

            $venta->monto_recibido = $tieneEfectivo ? ($request->monto_recibido ?? null) : null;
            $venta->cambio = $tieneEfectivo ? ($request->cambio ?? null) : null;
            $venta->save();


            DB::commit();

            // ── Post-commit: cupón, jobs, caché, broadcasts ─────────────────────

            if ($cuponAplicado) {
                \App\Services\CuponService::registrarUso(
                    idCupon: $cuponAplicado->id_cupon,
                    idVenta: $venta->id_venta,
                    idNegocio: $user->id_negocio,
                    idUsuario: $user->id_usuario,
                    descuentoAplicado: $descuentoTotal,
                );
            }

            // ── Despachar jobs DESPUÉS del commit ───────────────────────────────
            $config     = CatalogService::getConfigNegocio($user->id_negocio);
            $debeCorreo = $config->entregaPorCorreo() && !empty($cliente->correo);
            $total = count($jobsPostVenta);

            foreach ($jobsPostVenta as $i => $job) {
                if ($debeCorreo && $i === $total - 1) {
                    $job->enviarCorreo = true;
                }
                dispatch($job);
            }

            foreach ($request->pagos as $pago) {
                VentaPago::create([
                    'id_venta' => $venta->id_venta,
                    'id_metodo' => $pago['id_metodo'],
                    'id_negocio' => $user->id_negocio,
                    'monto' => $pago['monto'],
                    'referencia' => $pago['referencia'] ?? null,
                ]);
            }

            // Persistir vendedor (opcional — si no se seleccionó no rompemos)
            if ($request->filled('id_personal')) {
                $personal = Personal::find($request->id_personal);
                if ($personal) {
                    VentaVendedor::create([
                        'id_venta' => $venta->id_venta,
                        'id_personal' => $personal->id_personal,
                        'nombre_snapshot' => $personal->nombre,
                    ]);
                }
            }


            // ── Invalidaciones ──────────────────────────────────────────────────
            foreach ($seriesParaInvalidar as $serie) {
                CatalogService::invalidateBicicleta($serie, $user->id_negocio);
            }
            CatalogService::invalidateBicicletasPorUsuario($user->id_usuario, $user->id_negocio);
            CatalogService::invalidateSeccion($user->id_usuario, $user->id_negocio);
            CatalogService::invalidateSeccion(null, $user->id_negocio);
            CatalogService::invalidateVentasByVendedor($user->id_negocio, $user->id_usuario);
            CatalogService::invalidateInventario($user->id_negocio, $user->id_usuario); // incrementVersion — cubre todo lo demás

            // ── Broadcasts ──────────────────────────────────────────────────────
            foreach ($eventosBicicleta as $ev) {
                event(new BicicletaActualizada(
                    numSerie: $ev['num_serie'],
                    idNegocio: $user->id_negocio,
                    idUsuario: $user->id_usuario,
                    nombreVendedor: $user->nombre_usuario,
                    modelo: $ev['modelo'],
                    voltaje: $ev['voltaje'],
                    color: $ev['color'],
                    status: $ev['status'],
                ));
            }

            event(new VentaRealizada(
                idVenta: $venta->id_venta,
                idNegocio: $user->id_negocio,
                idVendedor: $user->id_usuario,
                nombreVendedor: $user->nombre_usuario,
                nombreCliente: $cliente->nombre_cliente . ' ' . $cliente->apellido1,
                total: $totalVenta - $descuentoTotal,
                bicicletas: $bicicletasBroadcast,
            ));

            // ── Respuesta ───────────────────────────────────────────────────────
            if ($debeCorreo) {
                $mensajeExito = 'Venta registrada. Comprobante enviado al correo del cliente.';
            } elseif ($config->entregaPorTicket()) {
                session()->flash('auto_ticket', $venta->id_venta);
                $mensajeExito = 'Venta registrada. Generando ticket...';
            } else {
                $mensajeExito = 'Venta registrada correctamente.';
            }

            return redirect()
                ->route('ventas.show', $venta->id_venta)
                ->with('success', $mensajeExito);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al registrar venta', [
                'user' => $user->id_usuario,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return back()
                ->withInput()
                ->with('error', 'Error al registrar la venta: ' . $e->getMessage());
        }
    }

    /* =====================================================
     | SHOW
     ===================================================== */
    public function show(string $id_venta)
    {
        $user = auth()->user();
        if ($user->id_rol != 2)
            abort(403);

        $venta = Venta::with([
            'cliente',
            'detalles.producto',
            'detalles.bicicleta.modelo.marca',
            'detalles.bicicleta.voltaje',
            'detalles.bicicleta.color',
            'vendedor.personal',   // ← nuevo
            'pagos.metodo',        // ← nuevo
        ])
            ->where('id_negocio', $user->id_negocio)
            ->findOrFail($id_venta);

        $tieneGarantia = $venta->detalles
            ->filter(fn($d) => $d->bicicleta !== null)
            ->contains(function ($detalle) use ($user) {
                $idMarca = $detalle->bicicleta->modelo->id_marca ?? null;
                if (!$idMarca)
                    return false;
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
        if ($user->id_rol != 2)
            abort(403);

        $venta = Venta::with([
            'cliente',
            'negocio',
            'detalles.producto',
            'detalles.bicicleta.modelo.marca',
            'detalles.bicicleta.voltaje',
            'detalles.bicicleta.color',
            'vendedor.personal',   // ← nuevo
            'pagos.metodo',        // ← nuevo
        ])
            ->where('id_negocio', $user->id_negocio)
            ->findOrFail($id_venta);

        $bicicletas = $venta->detalles
            ->filter(fn($d) => $d->bicicleta !== null)
            ->values();

        if ($bicicletas->isEmpty()) {
            return back()->with('error', 'Esta venta no tiene bicicletas para generar póliza.');
        }

        $pdf = Pdf::loadView('vendedor.ventas.poliza', [
            'venta' => $venta,
            'cliente' => $venta->cliente,
            'negocio' => $venta->negocio,
            'bicicletas' => $bicicletas,
            'vendedor' => $venta->vendedor, // ← ahora es VentaVendedor, no $user
            'fecha' => now()->format('d/m/Y'),
            'pagos' => $venta->pagos,    // ← nuevo
        ])->setPaper('letter', 'landscape');

        return response()->make($pdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="poliza-' . $venta->id_venta . '.pdf"',
        ]);
    }

    public function ticket(string $id_venta)
    {
        $user = auth()->user();
        if ($user->id_rol != 2)
            abort(403);

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

        $pdf = Pdf::loadView('vendedor.ventas.ticket', [
            'venta' => $venta,
            'cliente' => $venta->cliente,
            'negocio' => $venta->negocio,
            'bicicletas' => $bicicletas,
            'vendedor' => $venta->vendedor, // ← ahora es VentaVendedor, no $user
            'fecha' => now()->format('d/m/Y'),
            'pagos' => $venta->pagos,    // ← nuevo
        ])->setPaper('letter', 'landscape');

        return response()->make($pdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="ticket-' . $venta->id_venta . '.pdf"',
        ]);
    }
}
