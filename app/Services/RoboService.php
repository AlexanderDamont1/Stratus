<?php

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

    // ── Consultas ────────────────────────────────────────────────────────────

    public static function estaReportada(string $numSerie): bool
    {
        $set = Cache::get(self::KEY_SET, []);
        if (in_array($numSerie, $set, true)) return true;

        $existe = ReporteRobo::where('num_serie', $numSerie)
            ->whereIn('estado', [
                ReporteRobo::PENDIENTE,
                ReporteRobo::CONFIRMADO,
                ReporteRobo::EN_CUSTODIA,
            ])
            ->exists();

        if ($existe) self::recargarSetGlobal();

        return $existe;
    }

    public static function getReporteActivo(string $numSerie): ?ReporteRobo
    {
        return Cache::remember(
            "robos:serie:{$numSerie}",
            self::TTL_REPORTE,
            fn() => ReporteRobo::with([
                'cliente',
                'bicicleta.modelo.marca',
                'bicicleta.voltaje',
                'bicicleta.color',
                'negocioOrigen',
                'negocioReporta',
                'negocioEncontrado',
            ])
            ->where('num_serie', $numSerie)
            ->whereIn('estado', [
                ReporteRobo::PENDIENTE,
                ReporteRobo::CONFIRMADO,
                ReporteRobo::EN_CUSTODIA,
            ])
            ->latest()
            ->first()
        );
    }

    public static function getEnCustodia(string $idNegocio): \Illuminate\Support\Collection
    {
        return Cache::remember(
            "robos:custodia:{$idNegocio}",
            self::TTL_REPORTE,
            fn() => ReporteRobo::with([
                'cliente',
                'bicicleta.modelo.marca',
                'negocioReporta',
            ])
            ->where('id_negocio_encontrado', $idNegocio)
            ->where('estado', ReporteRobo::EN_CUSTODIA)
            ->latest('encontrado_at')
            ->get()
        );
    }

    // ── Acciones ─────────────────────────────────────────────────────────────

    public static function levantarReporte(
        string  $numSerie,
        string  $idNegocioOrigen,
        string  $idNegocioReporta,
        string  $idCliente,
        ?string $notas = null,
    ): ReporteRobo {
        if (self::estaReportada($numSerie)) {
            return self::getReporteActivo($numSerie);
        }

        $reporte = ReporteRobo::create([
            'num_serie'          => $numSerie,
            'id_negocio_origen'  => $idNegocioOrigen,
            'id_negocio_reporta' => $idNegocioReporta,
            'id_cliente'         => $idCliente,
            'estado'             => ReporteRobo::PENDIENTE,
            'token_confirmacion' => Str::random(64),
            'token_expires_at'   => now()->addHours(48),
            'notas'              => $notas,
        ]);

        self::invalidarSerie($numSerie);
        dispatch(new EnviarConfirmacionRoboJob($reporte->id_reporte));

        return $reporte;
    }

    public static function confirmarReporte(string $token): ?ReporteRobo
    {
        $reporte = ReporteRobo::where('token_confirmacion', $token)
            ->where('estado', ReporteRobo::PENDIENTE)
            ->where('token_expires_at', '>', now())
            ->first();

        if (!$reporte) return null;

        $reporte->update([
            'estado'             => ReporteRobo::CONFIRMADO,
            'confirmado_at'      => now(),
            'token_confirmacion' => null,
            'token_expires_at'   => null,
        ]);

        self::agregarAlSetGlobal($reporte->num_serie);
        self::invalidarSerie($reporte->num_serie);
        dispatch(new EnviarRoboConfirmadoJob($reporte->id_reporte));

        return $reporte;
    }

    public static function marcarEncontrado(
        string  $numSerie,
        string  $idNegocioEncontrado,
        ?string $idUsuarioEncontrado = null,
    ): ?ReporteRobo {
        $reporte = ReporteRobo::where('num_serie', $numSerie)
            ->where('estado', ReporteRobo::CONFIRMADO)
            ->latest()
            ->first();

        if (!$reporte) return null;

        $reporte->update([
            'estado'                 => ReporteRobo::EN_CUSTODIA,
            'encontrado_at'          => now(),
            'id_negocio_encontrado'  => $idNegocioEncontrado,
            'id_usuario_encontrado'  => $idUsuarioEncontrado,
        ]);

        // Se queda en el set global — sigue bloqueado hasta entrega física
        self::invalidarSerie($numSerie);
        Cache::forget("robos:custodia:{$idNegocioEncontrado}");
        dispatch(new EnviarVehiculoEncontradoJob($reporte->id_reporte));

        return $reporte;
    }

    public static function cerrarReporte(string $idReporte, string $idNegocio): ?ReporteRobo
    {
        $reporte = ReporteRobo::where('id_reporte', $idReporte)
            ->where('id_negocio_encontrado', $idNegocio)
            ->where('estado', ReporteRobo::EN_CUSTODIA)
            ->first();

        if (!$reporte) return null;

        $reporte->update([
            'estado'       => ReporteRobo::CERRADO,
            'entregado_at' => now(),
        ]);

        self::quitarDelSetGlobal($reporte->num_serie);
        self::invalidarSerie($reporte->num_serie);
        Cache::forget("robos:custodia:{$idNegocio}");

        return $reporte;
    }

    // ── Cache helpers ────────────────────────────────────────────────────────

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
        $series = ReporteRobo::whereIn('estado', [
            ReporteRobo::PENDIENTE,
            ReporteRobo::CONFIRMADO,
            ReporteRobo::EN_CUSTODIA,
        ])
        ->pluck('num_serie')
        ->toArray();

        Cache::put(self::KEY_SET, $series, self::TTL_SET);
    }
}