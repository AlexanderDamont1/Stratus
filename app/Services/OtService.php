<?php
// app/Services/OtService.php

namespace App\Services;

use App\Models\OrdenTrabajo;
use App\Models\OtHistorial;
use App\Models\OtPieza;
use App\Models\PiezaCatalogo;
use App\Models\GarantiaReclamo;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class OtService
{
    const CACHE_TTL    = 300; // 5 min — OTs cambian frecuentemente
    const CACHE_PREFIX = 'catalog:ot:';

    // ─── VERSIÓN (reutiliza la del tenant via CatalogService) ────────────────

    protected static function version(string $idNegocio): int
    {
        return CatalogService::getVersion($idNegocio);
    }

    protected static function key(string $k): string
    {
        return self::CACHE_PREFIX . $k;
    }

    protected static function remember(string $key, string $idNegocio, callable $cb)
    {
        $v = self::version($idNegocio);
        return Cache::remember(self::key($key) . ":v{$v}", self::CACHE_TTL, $cb);
    }

    // ─── INVALIDACIÓN ────────────────────────────────────────────────────────

    public static function invalidateOt(string $idOt, string $idNegocio): void
    {
        $v = self::version($idNegocio);
        Cache::forget(self::key("ot:{$idOt}") . ":v{$v}");
        Cache::forget(self::key("historial:{$idOt}") . ":v{$v}");
        // Invalida listas paginadas del negocio (páginas 1-3 son las más comunes)
        foreach (range(1, 3) as $p) {
            Cache::forget(self::key("lista:negocio:{$idNegocio}:page:{$p}") . ":v{$v}");
        }
        // Invalida listas por sucursal
        Cache::forget(self::key("lista:negocio:{$idNegocio}:todas") . ":v{$v}");
    }

    public static function invalidateLista(string $idNegocio, string $idSucursal): void
    {
        $v = self::version($idNegocio);
        foreach (range(1, 3) as $p) {
            Cache::forget(self::key("lista:sucursal:{$idSucursal}:page:{$p}") . ":v{$v}");
            Cache::forget(self::key("lista:negocio:{$idNegocio}:page:{$p}") . ":v{$v}");
        }
    }

    // ─── BÚSQUEDA DE BICICLETA POR NUM_SERIE ─────────────────────────────────
    // Reutiliza CatalogService — ya está cacheado ahí

    public static function buscarBicicleta(string $numSerie, string $idNegocio): ?array
    {
        $bici = CatalogService::getBicicletaBySerie($numSerie, $idNegocio);
        if (!$bici) return null;

        // Si la bici tiene cliente registrado (fue vendida en este tenant)
        $cliente = null;
        if ($bici->id_cliente) {
            $cliente = self::remember(
                "cliente:{$bici->id_cliente}",
                $idNegocio,
                fn() => \App\Models\Cliente::find($bici->id_cliente)
            );
        }

        return [
            'bicicleta' => [
                'num_serie'   => $bici->num_serie,
                'modelo'      => $bici->modelo?->nombre_modelo,
                'marca'       => $bici->modelo?->marca?->nombre_marca,
                'color'       => $bici->color?->color,
                'voltaje'     => $bici->voltaje?->voltaje,
                'status'      => $bici->status,
                'id_cliente'  => $bici->id_cliente,
            ],
            'cliente' => $cliente ? [
                'id_cliente'     => $cliente->id_cliente,
                'nombre_cliente' => $cliente->nombre_cliente,
                'apellido1'      => $cliente->apellido1,
                'apellido2'      => $cliente->apellido2,
                'telefono'       => $cliente->telefono,
                'correo'         => $cliente->correo,
            ] : null,
        ];
    }

    // ─── CREAR OT ─────────────────────────────────────────────────────────────

    public static function crearOt(array $datos, string $idNegocio, string $idUsuarioSucursal): OrdenTrabajo
    {
        return DB::transaction(function () use ($datos, $idNegocio, $idUsuarioSucursal) {

            // Si mandaron num_serie, verificar que exista en bicicletas del tenant
            // Si no existe → lo nulleamos y usamos bici_descripcion
            $numSerie = null;
            if (!empty($datos['num_serie'])) {
                $bici = CatalogService::getBicicletaBySerie($datos['num_serie'], $idNegocio);
                if ($bici) {
                    $numSerie = $datos['num_serie'];
                }
                // Si no existe en el sistema, se toma como descripción libre
                // bici_descripcion ya debe venir del front en ese caso
            }

            $ot = OrdenTrabajo::create([
                'id_negocio'           => $idNegocio,
                'id_usuario_sucursal'  => $idUsuarioSucursal,
                'num_serie'            => $numSerie,                          // ← null si no existe en BD
                'bici_descripcion'     => $datos['bici_descripcion'] ?? null,
                'id_cliente'           => $datos['id_cliente'] ?? null,
                'cliente_nombre'       => $datos['cliente_nombre'] ?? null,
                'cliente_email'        => $datos['cliente_email'] ?? null,
                'id_verificada'        => $datos['id_verificada'] ?? false,
                'tipo'                 => $datos['tipo'] ?? 'reparacion',
                'id_garantia_aprobada' => $datos['id_garantia_aprobada'] ?? null,
                'problema_reportado'   => $datos['problema_reportado'],
                'estado'               => 'recibida',
                'notas_internas'       => $datos['notas_internas'] ?? null,
            ]);

            if (!empty($datos['piezas'])) {
                self::sincronizarPiezas($ot, $datos['piezas']);
            }

            OtHistorial::create([
                'id_ot'        => $ot->id_ot,
                'estado_nuevo' => 'recibida',
                'id_usuario'   => $idUsuarioSucursal,
                'nota'         => 'OT creada',
            ]);

            self::invalidateLista($idNegocio, $idUsuarioSucursal);

            return $ot;
        });
    }

    // ─── CREAR OT DESDE GARANTÍA APROBADA ────────────────────────────────────

    public static function crearOtDesdeGarantia(string $idReclamo, string $idNegocio, string $idUsuarioSucursal): OrdenTrabajo
    {
        $reclamo = GarantiaReclamo::with('bicicletaGarantia')->findOrFail($idReclamo);

        // Buscar si la pieza del reclamo existe en el catálogo del tenant
        $pieza = PiezaCatalogo::where('id_negocio', $idNegocio)
            ->where('clave', $reclamo->clave_componente)
            ->first();

        $piezas = [];
        if ($pieza) {
            $piezas[] = [
                'id_pieza'       => $pieza->id_pieza,
                'descripcion'    => $pieza->nombre,
                'cantidad'       => 1,
                'precio_unitario'=> $pieza->precio_venta,
                'subtotal'       => $pieza->precio_venta,
                'es_garantia'    => true, // costo al cliente = $0
            ];
        }

        return self::crearOt([
            'num_serie'            => $reclamo->num_serie,
            'tipo'                 => 'garantia',
            'id_garantia_aprobada' => $idReclamo,
            'problema_reportado'   => $reclamo->motivo_reclamo ?? 'Garantía aprobada — ' . $reclamo->clave_componente,
            'notas_internas'       => 'Generada automáticamente desde reclamo ' . $idReclamo,
            'piezas'               => $piezas,
        ], $idNegocio, $idUsuarioSucursal);
    }

    // ─── AVANZAR ESTADO ───────────────────────────────────────────────────────

    public static function avanzarEstado(
        string  $idOt,
        string  $nuevoEstado,
        string  $idUsuario,
        string  $idNegocio,
        ?string $nota = null
    ): OrdenTrabajo {

        $ot = OrdenTrabajo::findOrFail($idOt);

        // Validar transición permitida
        self::validarTransicion($ot->estado, $nuevoEstado);

        $ot->avanzarEstado($nuevoEstado, $idUsuario, $nota);

         // Disparar notificación al cliente cuando queda lista
        if ($nuevoEstado === 'lista' && !$ot->notificacion_enviada) {
            self::despacharNotificacion($ot);
        }

        self::invalidateOt($idOt, $idNegocio);

        return $ot->fresh(['piezas', 'historial', 'cliente', 'bicicleta']);
    }

    // ─── PIEZAS ───────────────────────────────────────────────────────────────

    public static function sincronizarPiezas(OrdenTrabajo $ot, array $piezas): void
    {
        // Borrar las anteriores y recrear — simple y sin conflictos
        $ot->piezas()->delete();

        $totalPiezas = 0;

        foreach ($piezas as $p) {
            $subtotal = ($p['precio_unitario'] ?? 0) * ($p['cantidad'] ?? 1);
            $totalPiezas += $p['es_garantia'] ?? false ? 0 : $subtotal;

            OtPieza::create([
                'id_ot'           => $ot->id_ot,
                'id_pieza'        => $p['id_pieza'] ?? null,
                'descripcion'     => $p['descripcion'] ?? null,
                'cantidad'        => $p['cantidad'] ?? 1,
                'precio_unitario' => $p['precio_unitario'] ?? 0,
                'subtotal'        => $subtotal,
                'es_garantia'     => $p['es_garantia'] ?? false,
                'stock_descontado'=> false,
            ]);
        }

        // Actualizar costo_piezas en la OT
        $ot->update([
            'costo_piezas' => $totalPiezas,
            'costo_total'  => $ot->costo_mano_obra + $totalPiezas,
        ]);
    }

    // Descuenta stock al cerrar la OT (llamado en estado = entregada)
    public static function descontarStock(OrdenTrabajo $ot, string $idNegocio): void
    {
        foreach ($ot->piezas()->where('stock_descontado', false)->get() as $otPieza) {
            if (!$otPieza->id_pieza) continue;

            PiezaCatalogo::where('id_pieza', $otPieza->id_pieza)
                ->decrement('stock_actual', $otPieza->cantidad);

            $otPieza->update(['stock_descontado' => true]);

            // Invalida caché de piezas del tenant
            CatalogService::invalidatePiezas($idNegocio);
        }
    }

    // ─── CONSULTAS CACHEADAS ──────────────────────────────────────────────────

    public static function getOt(string $idOt, string $idNegocio): ?OrdenTrabajo
    {
        return self::remember("ot:{$idOt}", $idNegocio, fn() =>
            OrdenTrabajo::with(['piezas.pieza', 'historial.usuario', 'cliente', 'bicicleta', 'tecnico'])
                ->where('id_negocio', $idNegocio)
                ->find($idOt)
        );
    }

    public static function listarOts(string $idNegocio, string $idSucursal, int $page = 1, ?string $estado = null): \Illuminate\Pagination\LengthAwarePaginator
    {
        $cacheKey = "lista:sucursal:{$idSucursal}:estado:" . ($estado ?? 'todas') . ":page:{$page}";

        return self::remember($cacheKey, $idNegocio, fn() =>
            OrdenTrabajo::with(['cliente', 'bicicleta', 'tecnico'])
                ->where('id_negocio', $idNegocio)
                ->where('id_usuario_sucursal', $idSucursal)
                ->when($estado, fn($q) => $q->where('estado', $estado))
                ->orderByDesc('created_at')
                ->paginate(15, ['*'], 'page', $page)
        );
    }

    public static function getHistorial(string $idOt, string $idNegocio): \Illuminate\Support\Collection
    {
        return self::remember("historial:{$idOt}", $idNegocio, fn() =>
            OtHistorial::with('usuario')
                ->where('id_ot', $idOt)
                ->orderBy('created_at')
                ->get()
        );
    }

    // ─── HELPERS PRIVADOS ────────────────────────────────────────────────────

    private static function validarTransicion(string $actual, string $nuevo): void
    {
        $permitidas = [
            'recibida'              => ['diagnostico', 'mandado_fabrica', 'cancelada'],
            'diagnostico'           => ['esperando_aprobacion', 'en_proceso', 'mandado_fabrica', 'cancelada'],
            'esperando_aprobacion'  => ['en_proceso', 'cancelada'],
            'en_proceso'            => ['lista', 'cancelada'],
            'mandado_fabrica'       => ['lista', 'cancelada'],
            'lista'                 => ['entregada'],
            'entregada'             => [],
            'cancelada'             => [],
        ];

        if (!in_array($nuevo, $permitidas[$actual] ?? [])) {
            abort(422, "Transición no permitida: {$actual} → {$nuevo}");
        }
    }

private static function despacharNotificacion(OrdenTrabajo $ot): void
    {
        $email = $ot->cliente_email ?? $ot->cliente?->correo;
        if (empty($email)) return;
    
        \App\Jobs\EnviarNotificacionOtListaJob::dispatch($ot->id_ot, $ot->id_negocio)
            ->onQueue('emails');
    
        $ot->update([
            'notificacion_enviada'    => true,
            'notificacion_enviada_at' => now(),
        ]);
    }
}