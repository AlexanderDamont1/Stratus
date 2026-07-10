<?php

namespace App\Services;

use App\Models\Reparaciones;
use App\Models\ReparacionPieza;
use App\Models\ReparacionHistorial;
use App\Models\Cotizacion;
use App\Models\PiezaCatalogo;
use App\Services\StockService;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class ReparacionService
{
    const CACHE_TTL    = 300;
    const CACHE_PREFIX = 'rep:';

    // ── Cache helpers ─────────────────────────────────────────────────────────

    protected static function version(string $idNegocio): int
    {
        return CatalogService::getVersion($idNegocio);
    }

    protected static function remember(string $key, string $idNegocio, callable $cb): mixed
    {
        $v = self::version($idNegocio);
        return Cache::remember(
            self::CACHE_PREFIX . $key . ":v{$v}",
            self::CACHE_TTL,
            $cb
        );
    }

    protected static function invalidar(string $idNegocio): void
    {
        CatalogService::incrementVersion($idNegocio);
    }

    // ── Consultas ─────────────────────────────────────────────────────────────

    public static function get(string $id, string $idNegocio): ?Reparaciones
    {
        return self::remember("rep:{$id}", $idNegocio, fn() =>
            Reparaciones::with([
                'piezas.pieza',
                'historial.usuario',
                'cliente',
                'bicicleta',
                'cotizacion',
            ])
            ->where('id_negocio', $idNegocio)
            ->find($id)
        );
    }

    public static function listar(
        string  $idNegocio,
        string  $idSucursal,
        int     $page = 1,
        ?string $estado = null
    ): LengthAwarePaginator {
        $key = "lista:{$idSucursal}:estado:" . ($estado ?? 'todas') . ":p{$page}";

        return self::remember($key, $idNegocio, fn() =>
            Reparaciones::with(['cliente', 'bicicleta', 'cotizacion'])
                ->where('id_negocio', $idNegocio)
                ->where('id_usuario_sucursal', $idSucursal)
                ->when($estado, fn($q) => $q->where('estado', $estado))
                ->orderByDesc('created_at')
                ->paginate(15, ['*'], 'page', $page)
        );
    }

    // ── Crear ─────────────────────────────────────────────────────────────────

    /**
     * @param string|null $estadoInicial Normalmente null → 'recibida'. Se usa
     *        'en_revision' cuando la OT nace de un reclamo de garantía y
     *        necesita aprobación del admin antes de entrar al flujo normal.
     */
    public static function crear(
        array $datos,
        string $idNegocio,
        string $idUsuario,
        ?string $estadoInicial = null
    ): Reparaciones {
        if (!empty($datos['num_serie'])) {
            $bici = CatalogService::getBicicletaBySerie($datos['num_serie'], $idNegocio);

            if (!$bici) {
                abort(422, 'El número de serie no está registrado en el sistema.');
            }

            if ($bici->status != 2) {
                abort(422, 'Solo se pueden abrir órdenes de trabajo para unidades vendidas.');
            }
        }

        $estadoInicial = $estadoInicial ?? 'recibida';

        return DB::transaction(function () use ($datos, $idNegocio, $idUsuario, $estadoInicial) {

            $rep = Reparaciones::create([
                'id_negocio'          => $idNegocio,
                'id_usuario_sucursal' => $idUsuario,
                'num_serie'           => $datos['num_serie'] ?? null,
                'unidad_descripcion'  => $datos['unidad_descripcion'] ?? null,
                'id_cliente'          => $datos['id_cliente'] ?? null,
                'cliente_nombre'      => $datos['cliente_nombre'] ?? null,
                'cliente_email'       => $datos['cliente_email'] ?? null,
                'cliente_telefono'    => $datos['cliente_telefono'] ?? 'sin teléfono',
                'id_verificada'       => $datos['id_verificada'] ?? false,
                'tipo'                => $datos['tipo'],
                'problema_reportado'  => $datos['problema_reportado'],
                'notas_internas'      => $datos['notas_internas'] ?? null,
                'estado'              => $estadoInicial,
                'costo_reparacion'    => in_array($datos['tipo'], ['reparacion', 'mantenimiento'])
                                            ? ($datos['costo_reparacion'] ?? 0)
                                            : 0,
            ]);

            ReparacionHistorial::create([
                'id_reparacion'   => $rep->id_reparacion,
                'estado_anterior' => null,
                'estado_nuevo'    => $estadoInicial,
                'id_usuario'      => $idUsuario,
                'nota'            => $estadoInicial === 'en_revision'
                    ? 'OT creada automáticamente desde un reclamo de garantía — pendiente de aprobación.'
                    : 'OT creada',
            ]);

            self::invalidar($idNegocio);

            return $rep;
        });
    }

    // ── Buscar bicicleta ──────────────────────────────────────────────────────

    public static function buscarBicicleta(string $numSerie, string $idNegocio): ?array
    {
        $bici = CatalogService::getBicicletaBySerie($numSerie, $idNegocio);

        if (!$bici) return null;

        if ($bici->status != 2) {
            return ['error' => 'no_vendida', 'status' => $bici->status];
        }

        $cliente = CatalogService::getClienteByNumSerie($numSerie, $idNegocio);

        $garantias = \App\Models\BicicletaGarantia::with('garantiaDef')
            ->where('num_serie', $numSerie)
            ->whereIn('estado', ['vigente', 'por_vencer'])
            ->whereNull('id_reemplazada_por')
            ->get();

        return [
            'bicicleta' => [
                'num_serie'  => $bici->num_serie,
                'modelo'     => $bici->modelo?->nombre_modelo,
                'marca'      => $bici->modelo?->marca?->nombre_marca,
                'color'      => $bici->color?->color,
                'voltaje'    => $bici->voltaje?->voltaje,
                'status'     => $bici->status,
                'id_cliente' => $cliente?->id_cliente,
            ],
            'cliente' => $cliente ? [
                'id_cliente'     => $cliente->id_cliente,
                'nombre_cliente' => $cliente->nombre_cliente,
                'apellido1'      => $cliente->apellido1,
                'apellido2'      => $cliente->apellido2,
                'telefono'       => $cliente->telefono,
                'correo'         => $cliente->correo,
            ] : null,
            'garantias_activas' => $garantias->map(fn($g) => [
                'clave'     => $g->clave_componente,
                'nombre'    => $g->garantiaDef?->nombre_componente ?? $g->clave_componente,
                'expira_at' => $g->fecha_expiracion?->format('d/m/Y'),
            ])->values()->toArray(),
        ];
    }

    // ── Diagnóstico ───────────────────────────────────────────────────────────

    public static function guardarDiagnostico(
        Reparaciones $rep,
        string       $textoDiagnostico,
        array        $piezas,
        float        $costoManoObra,
        string       $idUsuario
    ): Reparaciones {
        return DB::transaction(function () use ($rep, $textoDiagnostico, $piezas, $costoManoObra, $idUsuario) {

            self::sincronizarPiezas($rep, $piezas, $costoManoObra);
            $rep->update(['diagnostico' => $textoDiagnostico]);

            if ($rep->estado === 'recibida') {
                $rep->avanzarEstado('diagnostico', $idUsuario, 'Diagnóstico ingresado');
            }

            self::invalidar($rep->id_negocio);

            return $rep->fresh(['piezas.pieza', 'cotizacion', 'historial']);
        });
    }

    public static function sincronizarPiezas(
        Reparaciones $rep,
        array        $piezas,
        ?float       $costoManoObra = null
    ): void {
        ReparacionPieza::where('id_reparacion', $rep->id_reparacion)->delete();

        $totalPiezas = 0;

        foreach ($piezas as $p) {
            $subtotal     = ($p['precio_unitario'] ?? 0) * ($p['cantidad'] ?? 1);
            $totalPiezas += $subtotal;

            ReparacionPieza::create([
                'id_reparacion'    => $rep->id_reparacion,
                'id_pieza'         => $p['id_pieza'] ?? null,
                'descripcion'      => $p['descripcion'] ?? null,
                'cantidad'         => $p['cantidad'] ?? 1,
                'precio_unitario'  => $p['precio_unitario'] ?? 0,
                'subtotal'         => $subtotal,
                'es_garantia'      => false,
                'stock_descontado' => false,
            ]);
        }

        $manoObra = $costoManoObra ?? (float) $rep->costo_mano_obra;

    
       $costoBase = in_array($rep->tipo, ['reparacion', 'mantenimiento'])
        ? (float) $rep->costo_reparacion
        : 0;
        $costoTotal = $costoBase + $manoObra + $totalPiezas;

        $rep->update([
            'costo_mano_obra' => $manoObra,
            'costo_piezas'    => $totalPiezas,
            'costo_total'     => $costoTotal,
        ]);
    }

    // ── Cotización ────────────────────────────────────────────────────────────

    public static function enviarCotizacion(
        Reparaciones $rep,
        string       $descripcionTrabajo,
        string       $idUsuario
    ): Cotizacion {
        return DB::transaction(function () use ($rep, $descripcionTrabajo, $idUsuario) {

            Cotizacion::where('id_reparacion', $rep->id_reparacion)
                ->whereNull('respuesta')
                ->delete();

            $piezasDetalle = $rep->piezas()->with('pieza')->get()
                ->map(fn($p) => [
                    'nombre'          => $p->pieza?->nombre ?? $p->descripcion ?? '—',
                    'cantidad'        => $p->cantidad,
                    'precio_unitario' => (float) $p->precio_unitario,
                    'subtotal'        => (float) $p->subtotal,
                ])->values()->toArray();

            $ahora = now();

            $cotizacion = Cotizacion::create([
                'id_reparacion'           => $rep->id_reparacion,
                'costo_mano_obra'         => $rep->costo_mano_obra,
                'costo_piezas'            => $rep->costo_piezas,
                'costo_total'             => $rep->costo_total,
                'costo_reparacion' => $rep->costo_reparacion,
                'descripcion_trabajo'     => $descripcionTrabajo,
                'piezas_detalle'          => $piezasDetalle,
                'enviado_at'              => $ahora,
                'expires_at'              => $ahora->copy()->addHours(12),
            ]);

            $rep->avanzarEstado('cotizacion_enviada', $idUsuario, 'Cotización enviada al cliente');

            self::invalidar($rep->id_negocio);

            // nombre correcto del Job
            \App\Jobs\EnviarCotizacion::dispatch(
                $cotizacion->id_cotizacion,
                $rep->id_negocio
            )->onQueue('emails');

            return $cotizacion;
        });
    }

    public static function procesarRespuestaToken(string $token, int $respuesta): Cotizacion
    {
        $cotizacion = Cotizacion::where('token', $token)->firstOrFail();

        if (!is_null($cotizacion->respuesta)) {
            abort(409, 'Esta cotización ya fue respondida.');
        }

        if ($cotizacion->expirada()) {
            abort(410, 'Este enlace ha expirado.');
        }

        $cotizacion->update([
            'respuesta'     => $respuesta,
            'respondido_at' => now(),
        ]);

        if ($respuesta === 1) {
            $rep = $cotizacion->reparacion;
            $rep->avanzarEstado(
                'en_proceso',
                $rep->id_usuario_sucursal,
                'Cliente aceptó la cotización por email'
            );
            self::invalidar($rep->id_negocio);
        }

        return $cotizacion->fresh();
    }

    public static function resolverManualmente(
        Reparaciones $rep,
        string       $decision,
        string       $idUsuario,
        ?string      $nota = null,
        array        $piezasAceptadas = []
    ): Reparaciones {
        return DB::transaction(function () use ($rep, $decision, $idUsuario, $nota, $piezasAceptadas) {

            $cotizacion = $rep->cotizacion;

            if ($cotizacion) {
                // Bug fix del servicio anterior: aceptar_parcial devolvía 0 incorrectamente.
                // El cliente SÍ acepta (parcialmente), por eso es 1.
                $respuesta = match ($decision) {
                    'aceptar',
                    'aceptar_parcial'    => 1,
                    'rechazar',
                    'solo_mantenimiento' => 0,
                    default              => abort(422, 'Decisión no reconocida'),
                };

                $updateCot = [
                    'resolucion_manual' => true,
                    'nota_resolucion'   => $nota,
                    'respondido_at'     => now(),
                    'respuesta'         => $respuesta,
                ];

                if ($decision === 'aceptar_parcial') {
                    $updateCot['piezas_aceptadas'] = $piezasAceptadas;
                }

                $cotizacion->update($updateCot);
            }

            $notaHistorial = $nota ?? match ($decision) {
                'aceptar'            => 'Cliente aceptó (resolución manual)',
                'aceptar_parcial'    => 'Cliente aceptó parcialmente (resolución manual)',
                'rechazar'           => 'Cliente rechazó (resolución manual)',
                'solo_mantenimiento' => 'Se procede solo con mantenimiento base',
                default              => 'Resolución manual',
            };

            // rechazar se queda en cotizacion_enviada — el trabajador
            // ve el teléfono del cliente y decide si cancela o negocia
            match ($decision) {
                'aceptar',
                'aceptar_parcial',
                'solo_mantenimiento' => $rep->avanzarEstado('en_proceso', $idUsuario, $notaHistorial),
                'rechazar'           => $rep->avanzarEstado('cotizacion_enviada', $idUsuario, $notaHistorial),
                default              => null,
            };

            if ($decision === 'aceptar_parcial' && !empty($piezasAceptadas)) {
                $piezasOrigen = $rep->piezas()->whereIn('id_pieza', $piezasAceptadas)->get();
                self::sincronizarPiezas($rep, $piezasOrigen->map(fn($p) => [
                    'id_pieza'        => $p->id_pieza,
                    'descripcion'     => $p->descripcion,
                    'cantidad'        => $p->cantidad,
                    'precio_unitario' => (float) $p->precio_unitario,
                ])->toArray(), (float) $rep->costo_mano_obra);
            }

           if ($decision === 'solo_mantenimiento') {
                ReparacionPieza::where('id_reparacion', $rep->id_reparacion)->delete();
                $rep->update([
                    'costo_piezas'    => 0,
                    'costo_mano_obra' => 0,
                    'costo_total'     => (float) $rep->costo_reparacion, // costo base de mantenimiento
                ]);
            }

            self::invalidar($rep->id_negocio);

            return $rep->fresh(['piezas.pieza', 'cotizacion', 'historial']);
        });
    }

    // ── Avanzar estado simple ─────────────────────────────────────────────────

    public static function avanzarEstado(
        string  $idRep,
        string  $nuevoEstado,
        string  $idUsuario,
        string  $idNegocio,
        ?string $nota = null
    ): Reparaciones {
        $rep = Reparaciones::where('id_negocio', $idNegocio)->findOrFail($idRep);

        self::validarTransicion($rep->estado, $nuevoEstado);

        $rep->avanzarEstado($nuevoEstado, $idUsuario, $nota);

        if ($nuevoEstado === 'lista' && !$rep->notificacion_enviada) {
            // nombre correcto del Job
            \App\Jobs\NotificarOtLista::dispatch($rep, $idNegocio)
                ->onQueue('emails');

            $rep->update([
                'notificacion_enviada'    => true,
                'notificacion_enviada_at' => now(),
            ]);
        }

        if ($nuevoEstado === 'entregada') {
            self::descontarStock($rep, $idNegocio);
        }

        self::invalidar($idNegocio);

        return $rep->fresh(['piezas', 'historial', 'cliente', 'cotizacion']);
    }

    // ── Stock ─────────────────────────────────────────────────────────────────

    public static function descontarStock(Reparaciones $rep, string $idNegocio): void
    {
        $piezasSinDescontar = ReparacionPieza::where('id_reparacion', $rep->id_reparacion)
            ->where('stock_descontado', false)
            ->whereNotNull('id_pieza')
            ->get();

        foreach ($piezasSinDescontar as $rp) {
            $pieza = PiezaCatalogo::find($rp->id_pieza);
            if (!$pieza) continue;

            StockService::registrarSalidaPorOT(
                pieza:        $pieza,
                cantidad:     $rp->cantidad,
                idReparacion: $rep->id_reparacion,
                idNegocio:    $idNegocio,
                idUsuario:    $rep->id_usuario_sucursal,
            );

            $rp->update(['stock_descontado' => true]);
        }

        $ids = ReparacionPieza::where('id_reparacion', $rep->id_reparacion)
            ->whereNotNull('id_pieza')
            ->pluck('id_pieza')
            ->toArray();

        $rep->update(['piezas_usadas' => $ids]);
}

    // ── Validar transición ────────────────────────────────────────────────────

    private static function validarTransicion(string $actual, string $nuevo): void
    {
        $permitidas = [
            // Solo alcanzable al crear una OT desde un reclamo de garantía.
            // El admin aprueba (→ recibida, entra al flujo normal) o rechaza (→ cancelada).
            'en_revision'        => ['recibida', 'cancelada'],
            'recibida'           => ['diagnostico', 'cancelada'],
            'diagnostico'        => ['cotizacion_enviada', 'en_proceso', 'cancelada'],
            'cotizacion_enviada' => ['en_proceso', 'cancelada'],
            'en_proceso'         => ['lista', 'cancelada'],
            'lista'              => ['entregada'],
            'entregada'          => [],
            'cancelada'          => [],
        ];

        if (!in_array($nuevo, $permitidas[$actual] ?? [])) {
            abort(422, "Transición no permitida: {$actual} → {$nuevo}");
        }
    }
}