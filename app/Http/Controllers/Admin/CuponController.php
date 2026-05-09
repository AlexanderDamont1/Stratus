<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Cupon;
use App\Models\CuponRegla;
use App\Services\CatalogService;
use App\Services\CuponService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CuponController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        if ($user->id_rol !== 1) abort(403);

        $cupones    = CuponService::getCuponesByNegocio($user->id_negocio);
        $sucursales = CatalogService::getSucursalesByNegocio($user->id_negocio);
        $marcas     = CatalogService::getMarcasByNegocio($user->id_negocio);
        $modelos    = CatalogService::getModelosByNegocio($user->id_negocio);
        $voltajes   = CatalogService::getVoltajesByNegocio($user->id_negocio);
        $accesorios = \App\Models\Producto::where('id_negocio', $user->id_negocio)
            ->where('tipo', '1')
            ->orderBy('nombre_producto')
            ->get(['id_producto', 'nombre_producto', 'precio']);

        return view('administrador.cupones.index', compact(
            'cupones', 'sucursales', 'marcas', 'modelos', 'voltajes', 'accesorios'
        ));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        if ($user->id_rol !== 1) abort(403);

        $validated = $this->validarFormulario($request, unique: true);

        DB::transaction(function () use ($validated, $user) {
            $cupon = Cupon::create([
                'id_negocio'         => $user->id_negocio,
                'tipo_cupon'         => $validated['tipo_cupon'],
                'codigo'             => strtoupper(trim($validated['codigo'])),
                'nombre'             => $validated['nombre'],
                'mensaje_vendedor'   => $validated['mensaje_vendedor'] ?? null,
                'monto_minimo'       => $validated['monto_minimo'] ?? null,
                'tipo_descuento'     => $validated['tipo_descuento'] ?? null,
                'valor_descuento'    => $validated['valor_descuento'] ?? null,
                'aplica_a'           => $validated['aplica_a'] ?? 'total',
                'id_producto_gratis' => $validated['id_producto_gratis'] ?? null,
                'activo'             => true,
                'usos_maximos'       => $validated['usos_maximos'] ?? null,
                'fecha_inicio'       => $validated['fecha_inicio'] ?? null,
                'fecha_fin'          => $validated['fecha_fin'] ?? null,
            ]);

            $this->sincronizarReglas($cupon, $validated['reglas'] ?? [], $user->id_negocio);
        });

        CuponService::invalidar('', $user->id_negocio);

        return response()->json(['ok' => true, 'mensaje' => 'Cupón creado correctamente.']);
    }

    public function update(Request $request, string $idCupon)
    {
        $user = Auth::user();
        if ($user->id_rol !== 1) abort(403);

        $cupon = Cupon::where('id_cupon', $idCupon)
            ->where('id_negocio', $user->id_negocio)
            ->firstOrFail();

        // FIX: unique rule ignora el cupón actual para no disparar error de código duplicado
        $validated = $this->validarFormulario($request, unique: false, ignorar: $idCupon);

        DB::transaction(function () use ($cupon, $validated, $user) {
            // FIX: era Cupon::create() — creaba un registro nuevo con el mismo código
            $cupon->update([
                'tipo_cupon'         => $validated['tipo_cupon'],
                'codigo'             => strtoupper(trim($validated['codigo'])),
                'nombre'             => $validated['nombre'],
                'mensaje_vendedor'   => $validated['mensaje_vendedor'] ?? null,
                'monto_minimo'       => $validated['monto_minimo'] ?? null,
                'tipo_descuento'     => $validated['tipo_descuento'] ?? null,
                'valor_descuento'    => $validated['valor_descuento'] ?? null,
                'aplica_a'           => $validated['aplica_a'] ?? 'total',
                'id_producto_gratis' => $validated['id_producto_gratis'] ?? null,
                'usos_maximos'       => $validated['usos_maximos'] ?? null,
                'fecha_inicio'       => $validated['fecha_inicio'] ?? null,
                'fecha_fin'          => $validated['fecha_fin'] ?? null,
            ]);

            $this->sincronizarReglas($cupon, $validated['reglas'] ?? [], $user->id_negocio);
        });

        CuponService::invalidar($idCupon, $user->id_negocio);

        return response()->json(['ok' => true, 'mensaje' => 'Cupón actualizado correctamente.']);
    }

    public function toggle(string $idCupon)
    {
        $user = Auth::user();
        if ($user->id_rol !== 1) abort(403);

        $cupon = Cupon::where('id_cupon', $idCupon)
            ->where('id_negocio', $user->id_negocio)
            ->firstOrFail();

        $cupon->update(['activo' => !$cupon->activo]);
        CuponService::invalidar($idCupon, $user->id_negocio);

        return response()->json([
            'ok'     => true,
            'activo' => $cupon->activo,
            'vigente' => $cupon->estaVigente(),
        ]);
    }

    public function destroy(string $idCupon)
    {
        $user = Auth::user();
        if ($user->id_rol !== 1) abort(403);

        $cupon = Cupon::where('id_cupon', $idCupon)
            ->where('id_negocio', $user->id_negocio)
            ->firstOrFail();

        CuponService::invalidar($idCupon, $user->id_negocio);
        $cupon->delete();

        return response()->json(['ok' => true, 'mensaje' => 'Cupón eliminado.']);
    }

    public function modelosPorMarca(string $idMarca)
    {
        $user    = Auth::user();
        $modelos = CatalogService::getModelosByNegocio($user->id_negocio)
            ->where('id_marca', $idMarca)
            ->values()
            ->map(fn($m) => ['id' => $m->id_modelo, 'nombre' => $m->nombre_modelo]);

        return response()->json($modelos);
    }

    // ── PRIVADOS ──────────────────────────────────────────────────────────

    private function validarFormulario(Request $request, bool $unique, string $ignorar = ''): array
    {
        $codigoRule = $unique
            ? 'required|string|max:30|unique:cupones,codigo'
            : "required|string|max:30|unique:cupones,codigo,{$ignorar},id_cupon";

        $rules = [
            'tipo_cupon'         => 'required|in:1,2,3',
            'nombre'             => 'required|string|max:120',
            'codigo'             => $codigoRule,
            'mensaje_vendedor'   => 'nullable|string|max:255',
            'monto_minimo'       => 'nullable|numeric|min:0',
            'id_producto_gratis' => 'nullable|string|exists:productos,id_producto',
            'usos_maximos'       => 'nullable|integer|min:1',
            'fecha_inicio'       => 'nullable|date',
            'fecha_fin'          => 'nullable|date|after_or_equal:fecha_inicio',
            'reglas'             => 'required|array|min:1',
            'reglas.*.tipo'      => 'required|in:modelo,voltaje,marca,sucursal,monto_minimo',
            'reglas.*.valor'     => 'nullable|string|max:100',
        ];

        if ($request->tipo_cupon === '1') {
            $rules['tipo_descuento']  = 'required|in:porcentaje,monto_fijo';
            $rules['valor_descuento'] = 'required|numeric|min:0';
            $rules['aplica_a']        = 'required|in:total,producto';
        }

        if ($request->tipo_cupon === '2') {
            $rules['id_producto_gratis'] = 'required|string|exists:productos,id_producto';
            $rules['tipo_descuento']     = 'nullable';
            $rules['valor_descuento']    = 'nullable';
            $rules['aplica_a']           = 'nullable';
        }

        if ($request->tipo_cupon === '3') {
            $rules['tipo_descuento']  = 'nullable|in:porcentaje,monto_fijo';
            $rules['valor_descuento'] = 'nullable|numeric|min:0';
            $rules['aplica_a']        = 'nullable';
        }

        return $request->validate($rules);
    }

    private function sincronizarReglas(Cupon $cupon, array $reglas, string $idNegocio): void
    {
        $cupon->reglas()->delete();

        foreach ($reglas as $regla) {
            $valor = ($regla['valor'] ?? '') === '' ? null : $regla['valor'];
            CuponRegla::create([
                'id_cupon'   => $cupon->id_cupon,
                'id_negocio' => $idNegocio,
                'tipo'       => $regla['tipo'],
                'valor'      => $valor,
            ]);
        }
    }
}