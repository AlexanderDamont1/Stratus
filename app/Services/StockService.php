<?php

namespace App\Services;

use App\Models\PiezaCatalogo;
use App\Models\PiezaMovimiento;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class StockService
{
    // ── Consultas (delegan a CatalogService) ──────────────────────────────────

    /**
     * Lista paginada con filtros. Proxy a CatalogService para que los
     * controllers no tengan que saber cuál servicio usar.
     */
    public static function listar(
        string  $idNegocio,
        int     $page = 1,
        ?string $busqueda = null,
        ?string $categoria = null,
        bool    $soloStockBajo = false
    ): LengthAwarePaginator {
        return CatalogService::getPiezasPaginadas($idNegocio, $page, $busqueda, $categoria, $soloStockBajo);
    }

    public static function get(string $idPieza, string $idNegocio): ?PiezaCatalogo
    {
        return CatalogService::getPiezaById($idPieza, $idNegocio);
    }

    public static function categorias(string $idNegocio): array
    {
        return CatalogService::getCategoriasPiezas($idNegocio);
    }

    public static function buscarParaDiagnostico(
        string  $idNegocio,
        string  $busqueda,
        ?string $idModelo = null
    ): array {
        return CatalogService::buscarPiezasParaDiagnostico($idNegocio, $busqueda, $idModelo);
    }

    public static function historial(
        string $idPieza,
        string $idNegocio,
        int    $page = 1
    ): LengthAwarePaginator {
        return CatalogService::getHistorialPieza($idPieza, $page);
    }

    // ── Crear pieza ───────────────────────────────────────────────────────────

    public static function crear(array $datos, string $idNegocio): PiezaCatalogo
    {
        $pieza = PiezaCatalogo::create([
            'id_negocio'          => $idNegocio,
            'nombre'              => $datos['nombre'],
            'clave'               => $datos['clave'],
            'categoria'           => $datos['categoria'] ?? null,
            'marca_pieza'         => $datos['marca_pieza'] ?? null,
            'modelos_compatibles' => $datos['modelos_compatibles'] ?? null,
            'voltaje_compatible'  => $datos['voltaje_compatible'] ?? null,
            'descripcion'         => $datos['descripcion'] ?? null,
            'precio_costo'        => $datos['precio_costo'] ?? 0,
            'precio_venta'        => $datos['precio_venta'] ?? 0,
            'stock_actual'        => $datos['stock_inicial'] ?? 0,
            'stock_minimo'        => $datos['stock_minimo'] ?? 0,
            'serializable'        => $datos['serializable'] ?? false,
        ]);

        if (($datos['stock_inicial'] ?? 0) > 0) {
            self::registrarMovimiento(
                pieza: $pieza,
                idNegocio: $idNegocio,
                idUsuario: $datos['id_usuario'],
                tipo: 'entrada',
                cantidad: (int) $datos['stock_inicial'],
                stockAntes: 0,
                nota: 'Stock inicial al crear pieza',
            );
        }

        CatalogService::invalidatePiezas($idNegocio);

        return $pieza;
    }

    // ── Editar pieza ──────────────────────────────────────────────────────────

    public static function editar(PiezaCatalogo $pieza, array $datos): PiezaCatalogo
    {
        $pieza->update([
            'nombre'              => $datos['nombre'],
            'clave'               => $datos['clave'],
            'categoria'           => $datos['categoria'] ?? null,
            'marca_pieza'         => $datos['marca_pieza'] ?? null,
            'modelos_compatibles' => $datos['modelos_compatibles'] ?? null,
            'voltaje_compatible'  => $datos['voltaje_compatible'] ?? null,
            'descripcion'         => $datos['descripcion'] ?? null,
            'precio_costo'        => $datos['precio_costo'] ?? $pieza->precio_costo,
            'precio_venta'        => $datos['precio_venta'] ?? $pieza->precio_venta,
            'stock_minimo'        => $datos['stock_minimo'] ?? $pieza->stock_minimo,
            'serializable'        => $datos['serializable'] ?? $pieza->serializable,
        ]);

        CatalogService::invalidatePiezas($pieza->id_negocio);

        return $pieza->fresh();
    }

    // ── Entrada de stock ──────────────────────────────────────────────────────

    public static function registrarEntrada(
        PiezaCatalogo $pieza,
        int           $cantidad,
        string        $idUsuario,
        ?string       $nota = null
    ): PiezaMovimiento {
        if ($cantidad <= 0) abort(422, 'La cantidad debe ser mayor a 0.');

        return DB::transaction(function () use ($pieza, $cantidad, $idUsuario, $nota) {
            $stockAntes = $pieza->stock_actual;

            $pieza->increment('stock_actual', $cantidad);
            $pieza->refresh();

            $mov = self::registrarMovimiento(
                pieza: $pieza,
                idNegocio: $pieza->id_negocio,
                idUsuario: $idUsuario,
                tipo: 'entrada',
                cantidad: $cantidad,
                stockAntes: $stockAntes,
                nota: $nota,
            );

            CatalogService::invalidatePiezas($pieza->id_negocio);

            return $mov;
        });
    }

    // ── Salida por OT (llamado desde ReparacionService::descontarStock) ───────

    public static function registrarSalidaPorOT(
        PiezaCatalogo $pieza,
        int           $cantidad,
        string        $idReparacion,
        string        $idNegocio,
        string        $idUsuario
    ): void {
        $stockAntes = $pieza->stock_actual;

        // No bloqueamos stock negativo — se permite pero queda registrado
        PiezaCatalogo::where('id_pieza', $pieza->id_pieza)
            ->decrement('stock_actual', $cantidad);

        $pieza->refresh();

        self::registrarMovimiento(
            pieza: $pieza,
            idNegocio: $idNegocio,
            idUsuario: $idUsuario,
            tipo: 'salida',
            cantidad: $cantidad,
            stockAntes: $stockAntes,
            nota: "Salida por OT {$idReparacion}",
            idReparacion: $idReparacion,
        );
    }

    // ── Privado: registrar movimiento ─────────────────────────────────────────

    private static function registrarMovimiento(
        PiezaCatalogo $pieza,
        string        $idNegocio,
        string        $idUsuario,
        string        $tipo,
        int           $cantidad,
        int           $stockAntes,
        ?string       $nota = null,
        ?string       $idReparacion = null,
    ): PiezaMovimiento {
        return PiezaMovimiento::create([
            'id_pieza'      => $pieza->id_pieza,
            'id_negocio'    => $idNegocio,
            'id_usuario'    => $idUsuario,
            'id_reparacion' => $idReparacion,
            'tipo'          => $tipo,
            'cantidad'      => $cantidad,
            'stock_antes'   => $stockAntes,
            'stock_despues' => $pieza->stock_actual,
            'nota'          => $nota,
        ]);
    }
}
