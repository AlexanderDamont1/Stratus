<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\CatalogService;
use App\Services\BicicletaMovimientoService;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class MovimientoController extends Controller
{
    // ─── INDEX ───────────────────────────────────────────────────────────────

    public function index()
    {
        $user = auth()->user();

        if (!in_array($user->id_rol, [1, 2])) abort(403);

        // CatalogService::getMovimientosRecientes usa remember() con versión del
        // tenant — se invalida automáticamente con invalidateMovimientos().
        $recientes = CatalogService::getMovimientosRecientes($user->id_negocio)
            ->map(fn ($m) => [
                'id'               => $m->id_movimiento,
                'num_serie'        => $m->num_serie,
                'tipo_movimiento'  => $m->tipo_movimiento,
                'origen'           => $m->origen,
                'destino'          => $m->destino,
                'notas'            => $m->notas,
                'fecha_movimiento' => $m->fecha_movimiento->toDateTimeString(),
                'usuario'          => $m->usuario?->nombre_usuario,
            ]);

        return view('administrador.movimientos.index', compact('recientes'));
    }

    // ─── HISTORIAL EN TABLA (solo rol 1) ────────────────────────────────────

    public function tabla(Request $request)
    {
        $user = auth()->user();

        if ($user->id_rol !== 1) abort(403);

        // La vista es Alpine + fetch (igual que el tracking en vivo) — la
        // carga inicial del HTML solo trae el catálogo de sucursales (para el
        // selector), no el listado.
        if (!$request->wantsJson()) {
            $sucursales = CatalogService::getSucursalesByNegocio($user->id_negocio);
            return view('administrador.movimientos.tabla', compact('sucursales'));
        }

        $busqueda      = trim((string) $request->query('q', ''));
        $page          = (int) $request->query('page', 1);
        $idSucursal    = trim((string) $request->query('sucursal', '')) ?: null;
        [$desde, $hasta] = $this->rangoFechas($request);

        $items = CatalogService::getHistorialBicicletasTabla($user->id_negocio, $page, $busqueda ?: null, $desde, $hasta, $idSucursal);
        $stats = CatalogService::getHistorialBicicletasStats($user->id_negocio);

        return response()->json([
            'ok'           => true,
            'items'        => collect($items->items())->map(fn ($b) => [
                'num_serie'              => $b->num_serie,
                'marca'                  => $b->modelo?->marca?->nombre_marca,
                'modelo'                 => $b->modelo?->nombre_modelo,
                'color'                  => $b->color?->color,
                'voltaje'                => $b->voltaje?->voltaje,
                'created_at'             => $b->created_at?->toIso8601String(),
                'fecha_ingreso_sucursal' => $b->fecha_ingreso_sucursal?->toIso8601String(),
                'fecha_vendida'          => $b->fecha_vendida?->toIso8601String(),
            ])->values(),
            'current_page' => $items->currentPage(),
            'last_page'    => $items->lastPage(),
            'total'        => $items->total(),
            'stats'        => $stats,
        ]);
    }

    // ─── HISTORIAL EN TABLA — PDF del período seleccionado ──────────────────

    public function tablaPdf(Request $request)
    {
        $user = auth()->user();

        if ($user->id_rol !== 1) abort(403);

        $busqueda      = trim((string) $request->query('q', ''));
        $idSucursal    = trim((string) $request->query('sucursal', '')) ?: null;
        [$desde, $hasta] = $this->rangoFechas($request);

        $items = CatalogService::getHistorialBicicletasParaPdf($user->id_negocio, $busqueda ?: null, $desde, $hasta, $idSucursal);
        $sucursalNombre = $idSucursal
            ? CatalogService::getSucursalesByNegocio($user->id_negocio)->firstWhere('id_usuario', $idSucursal)?->nombre_usuario
            : null;

        $pdf = Pdf::loadView('administrador.movimientos.historial-pdf', [
            'items'          => $items,
            'desde'          => $desde,
            'hasta'          => $hasta,
            'sucursalNombre' => $sucursalNombre,
            'nombreNegocio'  => $user->negocio?->nombre_negocio ?? '—',
            'generadoEn'     => now()->format('d/m/Y H:i'),
        ])->setPaper('letter', 'portrait');

        $archivo = 'historial-vehiculos-' . now()->format('Ymd-His') . '.pdf';

        return response()->make($pdf->output(), 200, [
            'Content-Type'        => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $archivo . '"',
        ]);
    }

    // Lee ?desde=YYYY-MM-DD&hasta=YYYY-MM-DD y los normaliza (si vienen
    // invertidos, los intercambia en vez de devolver un rango vacío).
    private function rangoFechas(Request $request): array
    {
        $desde = trim((string) $request->query('desde', '')) ?: null;
        $hasta = trim((string) $request->query('hasta', '')) ?: null;

        if ($desde && $hasta && $desde > $hasta) {
            [$desde, $hasta] = [$hasta, $desde];
        }

        return [$desde, $hasta];
    }

    // ─── AUTOCOMPLETE ────────────────────────────────────────────────────────

    public function buscar(Request $request)
    {
        $user = auth()->user();
        $q    = trim($request->get('q', ''));

        if (strlen($q) < 2) return response()->json([]);

        // searchBicicletas usa remember() con versión del tenant (TTL 1h).
        $resultados = CatalogService::searchBicicletas($q, $user->id_negocio, 8);

        return response()->json($resultados);
    }

    // ─── HISTORIAL POR SERIE ─────────────────────────────────────────────────

    public function historial(string $numSerie)
    {
        $user = auth()->user();

        $bicicleta = CatalogService::getBicicletaBySerie($numSerie, $user->id_negocio);

        if (!$bicicleta || $bicicleta->id_negocio !== $user->id_negocio) {
            return response()->json(['error' => 'Serie no encontrada.'], 404);
        }

        // getHistorialMovimientos usa remember() con versión del tenant (TTL 10m).
        $movimientos = CatalogService::getHistorialMovimientos($numSerie, $user->id_negocio)
            ->map(fn ($m) => [
                'id'               => $m->id_movimiento,
                'num_serie'        => $m->num_serie,
                'tipo_movimiento'  => $m->tipo_movimiento,
                'origen'           => $m->origen,
                'destino'          => $m->destino,
                'notas'            => $m->notas,
                'fecha_movimiento' => $m->fecha_movimiento->toDateTimeString(),
                'usuario'          => $m->usuario?->nombre_usuario,
            ]);

        return response()->json([
            'movimientos' => $movimientos,
            'bicicleta'   => [
                'num_serie' => $bicicleta->num_serie,
                'marca'     => $bicicleta->modelo?->marca?->nombre_marca ?? '—',
                'modelo'    => $bicicleta->modelo?->nombre_modelo         ?? '—',
                'voltaje'   => $bicicleta->voltaje?->voltaje              ?? '—',
                'status'    => $bicicleta->status,
            ],
        ]);
    }
}