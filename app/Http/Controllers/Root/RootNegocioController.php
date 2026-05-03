<?php

namespace App\Http\Controllers\Root;

use App\Http\Controllers\Controller;
use App\Jobs\EliminarNegocioJob;
use App\Models\Negocio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class RootNegocioController extends Controller
{
    private const LOG_DISK      = 'local';
    private const LOG_DIR       = 'logs/negocios';
    private const LOG_EXTENSION = '.log';
    private const LOG_PATTERN   = '/^deletion_(ERROR_)?[A-Za-z0-9]+_\d{8}_\d{6}\.log$/';

    // ─────────────────────────────────────────────────────────────
    // DESTROY
    // ─────────────────────────────────────────────────────────────
    public function destroy(Request $request, string $idNegocio)
    {
        if (!preg_match('/^[A-Za-z0-9_\-]+$/', $idNegocio)) {
            abort(400, 'ID de negocio inválido.');
        }

        $negocio = Negocio::findOrFail($idNegocio);

        $request->validate([
            'confirmacion' => ['required', 'string', 'in:ELIMINAR'],
        ], [
            'confirmacion.in' => 'Debes escribir exactamente "ELIMINAR" para confirmar.',
        ]);

        $lockKey = "eliminando_negocio_{$negocio->id_negocio}";

        if (Cache::has($lockKey)) {
            return back()->withErrors([
                'confirmacion' => "Ya existe un proceso de eliminación en curso para \"{$negocio->nombre_negocio}\". Espera a que termine.",
            ]);
        }

        Cache::put($lockKey, true, now()->addMinutes(10));

        Log::channel('daily')->warning(
            "ROOT ELIMINAR NEGOCIO: {$negocio->nombre_negocio} ({$negocio->id_negocio}) " .
            "— Solicitado por usuario " . Auth::id() .
            " desde IP " . $request->ip()
        );

        EliminarNegocioJob::dispatch(
            idNegocio:     $negocio->id_negocio,
            nombreNegocio: $negocio->nombre_negocio,
            rootUsuarioId: Auth::id(),
        );

        return back()->with(
            'success',
            "El negocio \"{$negocio->nombre_negocio}\" está siendo eliminado en segundo plano. Revisa los logs para el reporte."
        );
    }

    // ─────────────────────────────────────────────────────────────
    // LOGS — listado
    // ─────────────────────────────────────────────────────────────
    public function logs()
    {
        $archivos = $this->obtenerArchivos();
        return view('root.logs-eliminacion', compact('archivos'));
    }

    // ─────────────────────────────────────────────────────────────
    // LOGS — SSE stream
    // ─────────────────────────────────────────────────────────────
    public function logsStream()
{
    // Deshabilitar time limit y buffer
    set_time_limit(0);
    ini_set('output_buffering', 'off');
    ini_set('zlib.output_compression', false);

    return response()->stream(function () {
        // Limpiar cualquier buffer previo
        while (ob_get_level() > 0) {
            ob_end_clean();
        }

        $ultimaFecha = 0;

        while (true) {
            if (connection_aborted()) break;

            try {
                $archivos         = $this->obtenerArchivos();
                $fechaMasReciente = $archivos->first()['fecha'] ?? 0;

                if ($fechaMasReciente > $ultimaFecha) {
                    $ultimaFecha = $fechaMasReciente;
                    echo "data: " . json_encode($archivos->values()) . "\n\n";
                }

                echo ": heartbeat\n\n";

                if (ob_get_level() > 0) ob_flush();
                flush();

            } catch (\Throwable $e) {
                echo "data: " . json_encode(['error' => $e->getMessage()]) . "\n\n";
                flush();
                break;
            }

            sleep(5);
        }
    }, 200, [
        'Content-Type'      => 'text/event-stream',
        'Cache-Control'     => 'no-cache',
        'X-Accel-Buffering' => 'no',
        'Connection'        => 'keep-alive',
    ]);
}

    // ─────────────────────────────────────────────────────────────
    // VER LOG — contenido individual
    // ─────────────────────────────────────────────────────────────
    public function verLog(Request $request)
    {
        $request->validate([
            'archivo' => [
                'required',
                'string',
                'max:100',
                'regex:' . self::LOG_PATTERN,
            ],
        ], [
            'archivo.regex' => 'Nombre de archivo inválido.',
        ]);

        $nombre = basename($request->archivo);
        $path   = self::LOG_DIR . '/' . $nombre;

        if (!str_ends_with($nombre, self::LOG_EXTENSION)) {
            abort(400, 'Tipo de archivo no permitido.');
        }

        if (!Storage::disk(self::LOG_DISK)->exists($path)) {
            abort(404);
        }

        $tamaño = Storage::disk(self::LOG_DISK)->size($path);
        if ($tamaño > 2 * 1024 * 1024) {
            abort(413, 'El archivo es demasiado grande para visualizarse.');
        }

        $contenido = Storage::disk(self::LOG_DISK)->get($path);

        return view('root.ver-log', compact('contenido', 'nombre'));
    }

    // ─────────────────────────────────────────────────────────────
    // HELPER privado — leer y mapear archivos
    // ─────────────────────────────────────────────────────────────
    private function obtenerArchivos()
    {
        return collect(Storage::disk(self::LOG_DISK)->files(self::LOG_DIR))
            ->filter(fn($path) => str_ends_with($path, self::LOG_EXTENSION))
            ->map(function ($path) {
                $nombre = basename($path);
                if (!preg_match(self::LOG_PATTERN, $nombre)) return null;

                return [
                    'nombre'   => $nombre,
                    'tamaño'   => Storage::disk(self::LOG_DISK)->size($path),
                    'fecha'    => Storage::disk(self::LOG_DISK)->lastModified($path),
                    'es_error' => str_contains($nombre, 'ERROR'),
                ];
            })
            ->filter()
            ->sortByDesc('fecha')
            ->values();
    }
}