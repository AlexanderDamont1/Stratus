<?php
// app/Services/RoboService.php

namespace App\Services;

use App\Jobs\EnviarConfirmacionRoboJob;
use App\Jobs\EnviarRoboConfirmadoJob;
use App\Jobs\EnviarVehiculoEncontradoJob;
use App\Models\ReporteRobo;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class RoboService
{
    const TTL_REPORTE = 300;
    const TTL_SET     = 300;
    const KEY_SET     = 'robos:activos';

    // ────────────────────────────────────────────────────────────────────────
    // CONSULTA CROSS-TENANT — O(1) en Redis
    // ────────────────────────────────────────────────────────────────────────

    public static function estaReportada(string $numSerie): bool
    {
        $set = Cache::get(self::KEY_SET, []);
        if (in_array($numSerie, $set, true)) return true;

        $existe = ReporteRobo::where('num_serie', $numSerie)
            ->whereIn('estado', ['pendiente', 'confirmado'])
            ->exists();

        if ($existe) self::recargarSetGlobal();

        return $existe;
    }

    public static function getReporteActivo(string $numSerie): ?ReporteRobo
    {
        return Cache::remember(
            "robos:serie:{$numSerie}",
            self::TTL_REPORTE,
            fn () => ReporteRobo::with([
                'cliente',
                'bicicleta.modelo.marca',
                'bicicleta.voltaje',
                'bicicleta.color',
                'negocioOrigen',
                'negocioReporta',
                'negocioEncontrado',
            ])
            ->where('num_serie', $numSerie)
            ->whereIn('estado', ['pendiente', 'confirmado'])
            ->latest()
            ->first()
        );
    }

    // ────────────────────────────────────────────────────────────────────────
    // LEVANTAR REPORTE
    // ────────────────────────────────────────────────────────────────────────

    public static function levantarReporte(
        string $numSerie,
        string $idNegocioOrigen,
        string $idNegocioReporta,
        string $idCliente,
        ?string $notas = null,
    ): ReporteRobo {
        // Si ya hay uno activo, devolver el existente
        if (self::estaReportada($numSerie)) {
            return self::getReporteActivo($numSerie);
        }

        $reporte = ReporteRobo::create([
            'num_serie'          => $numSerie,
            'id_negocio_origen'  => $idNegocioOrigen,
            'id_negocio_reporta' => $idNegocioReporta,
            'id_cliente'         => $idCliente,
            'estado'             => 'pendiente',
            'token_confirmacion' => Str::random(64),
            'token_expires_at'   => now()->addHours(48),
            'notas'              => $notas,
        ]);

        self::invalidarSerie($numSerie);

        // Job — manda correo al cliente para confirmar
        dispatch(new EnviarConfirmacionRoboJob($reporte->id_reporte));

        return $reporte;
    }

    // ────────────────────────────────────────────────────────────────────────
    // CONFIRMAR ROBO (cliente hace click en el link)
    // ────────────────────────────────────────────────────────────────────────

    public static function confirmarReporte(string $token): ?ReporteRobo
    {
        $reporte = ReporteRobo::where('token_confirmacion', $token)
            ->where('estado', 'pendiente')
            ->where('token_expires_at', '>', now())
            ->first();

        if (!$reporte) return null;

        $reporte->update([
            'estado'             => 'confirmado',
            'confirmado_at'      => now(),
            'token_confirmacion' => null,
            'token_expires_at'   => null,
        ]);

        // Agregar al set global — cualquier sucursal lo detecta ahora
        self::agregarAlSetGlobal($reporte->num_serie);
        self::invalidarSerie($reporte->num_serie);

        // Job — correo de confirmación al cliente
        dispatch(new EnviarRoboConfirmadoJob($reporte->id_reporte));

        return $reporte;
    }

    // ────────────────────────────────────────────────────────────────────────
    // MARCAR ENCONTRADO (sucursal detecta la bici al escanearla)
    // ────────────────────────────────────────────────────────────────────────

    public static function marcarEncontrado(
        string $numSerie,
        string $idNegocioEncontrado,
    ): ?ReporteRobo {
        $reporte = ReporteRobo::where('num_serie', $numSerie)
            ->where('estado', 'confirmado')
            ->latest()
            ->first();

        if (!$reporte) return null;

        $reporte->update([
            'estado'                => 'encontrado',
            'encontrado_at'         => now(),
            'id_negocio_encontrado' => $idNegocioEncontrado,
        ]);

        // Quitar del set global
        self::quitarDelSetGlobal($numSerie);
        self::invalidarSerie($numSerie);

        // Job — correo al cliente avisando que su bici fue encontrada
        dispatch(new EnviarVehiculoEncontradoJob($reporte->id_reporte));

        return $reporte;
    }

    // ────────────────────────────────────────────────────────────────────────
    // HELPERS DE CACHÉ GLOBAL
    // ────────────────────────────────────────────────────────────────────────

    private static function agregarAlSetGlobal(string $numSerie): void
    {
        $set   = Cache::get(self::KEY_SET, []);
        $set[] = $numSerie;
        Cache::put(self::KEY_SET, array_unique($set), self::TTL_SET);
    }

    private static function quitarDelSetGlobal(string $numSerie): void
    {
        $set = Cache::get(self::KEY_SET, []);
        Cache::put(
            self::KEY_SET,
            array_values(array_filter($set, fn($s) => $s !== $numSerie)),
            self::TTL_SET
        );
    }

    private static function invalidarSerie(string $numSerie): void
    {
        Cache::forget("robos:serie:{$numSerie}");
    }

    private static function recargarSetGlobal(): void
    {
        $series = ReporteRobo::whereIn('estado', ['pendiente', 'confirmado'])
            ->pluck('num_serie')
            ->toArray();

        Cache::put(self::KEY_SET, $series, self::TTL_SET);
    }
}