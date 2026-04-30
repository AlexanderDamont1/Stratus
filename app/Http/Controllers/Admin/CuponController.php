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
use Illuminate\Support\Str;

class CuponController extends Controller
{
    // ── INDEX ─────────────────────────────────────────────────────────────
    public function index()
    {
        $user = Auth::user();
        if ($user->id_rol !== 1) abort(403);

        $cupones = CuponService::getCuponesByNegocio($user->id_negocio);

        $sucursales = CatalogService::getSucursalesByNegocio($user->id_negocio);
        $marcas     = CatalogService::getMarcasByNegocio($user->id_negocio);
        $modelos    = CatalogService::getModelosByNegocio($user->id_negocio);
        $voltajes   = CatalogService::getVoltajesByNegocio($user->id_negocio);
        $accesorios = \App\Models\Producto::where('id_negocio', $user->id_negocio)
        ->where('tipo', '1')   // ajusta según tu enum
        ->orderBy('nombre_producto')
        ->get(['id_producto', 'nombre_producto', 'precio']);


        return view('administrador.cupones.index', compact(
            'cupones', 'sucursales', 'marcas', 'modelos', 'voltajes', 'accesorios'));
    }

    // ── STORE ─────────────────────────────────────────────────────────────
   public function store(Request $request)
    {
        $user = Auth::user();
        if ($user->id_rol !== 1) abort(403);

        $validated = $this->validarFormulario($request, unique: true);

        DB::transaction(function () use ($validated, $user) {
            $cupon = Cupon::create([
                'id_negocio'         => $user->id_negocio,
                'codigo'             => strtoupper(trim($validated['codigo'])),
                'nombre'             => $validated['nombre'],
                'tipo_descuento'     => $validated['tipo_descuento'],
                'valor_descuento'    => $validated['valor_descuento'],
                'aplica_a'           => $validated['aplica_a'],
                'id_producto_gratis' => $validated['id_producto_gratis'] ?? null,
                'activo'             => true,
                'usos_maximos'       => $validated['usos_maximos'] ?? null,
                'fecha_inicio'       => $validated['fecha_inicio'] ?? null,
                'fecha_fin'          => $validated['fecha_fin'] ?? null,
            ]);

            $this->sincronizarReglas($cupon, $validated['reglas'] ?? []);
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

        $validated = $this->validarFormulario($request, unique: false, ignorar: $idCupon);

        DB::transaction(function () use ($cupon, $validated) {
            $cupon->update([
                'codigo'             => strtoupper(trim($validated['codigo'])),
                'nombre'             => $validated['nombre'],
                'tipo_descuento'     => $validated['tipo_descuento'],
                'valor_descuento'    => $validated['valor_descuento'],
                'aplica_a'           => $validated['aplica_a'],
                'id_producto_gratis' => $validated['id_producto_gratis'] ?? null,
                'usos_maximos'       => $validated['usos_maximos'] ?? null,
                'fecha_inicio'       => $validated['fecha_inicio'] ?? null,
                'fecha_fin'          => $validated['fecha_fin'] ?? null,
            ]);

            $this->sincronizarReglas($cupon, $validated['reglas'] ?? []);
        });

        CuponService::invalidar($idCupon, $cupon->id_negocio);

        return response()->json(['ok' => true, 'mensaje' => 'Cupón actualizado correctamente.']);
    }


    // ── TOGGLE ACTIVO ─────────────────────────────────────────────────────
    public function toggle(string $idCupon)
    {
        $user = Auth::user();
        if ($user->id_rol !== 1) abort(403);

        $cupon = Cupon::where('id_cupon', $idCupon)
            ->where('id_negocio', $user->id_negocio)
            ->firstOrFail();

        $cupon->update(['activo' => !$cupon->activo]);

        CuponService::invalidar($idCupon, $user->id_negocio);

        return response()->json(['ok' => true, 'activo' => $cupon->activo]);
    }

    // ── DESTROY ───────────────────────────────────────────────────────────
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

    // ── HELPERS PRIVADOS ──────────────────────────────────────────────────────
    private function validarFormulario(Request $request, bool $unique, string $ignorar = ''): array
    {
        $codigoRule = $unique
            ? 'required|string|max:30|unique:cupones,codigo'
            : "required|string|max:30|unique:cupones,codigo,{$ignorar},id_cupon";

        return $request->validate([
            'nombre'              => 'required|string|max:120',
            'codigo'              => $codigoRule,
            'tipo_descuento'      => 'required|in:porcentaje,monto_fijo',
            'valor_descuento'     => 'required|numeric|min:0',
            'aplica_a'            => 'required|in:total,producto',
            'id_producto_gratis'  => 'nullable|string|exists:productos,id_producto',
            'usos_maximos'        => 'nullable|integer|min:1',
            'fecha_inicio'        => 'nullable|date',
            'fecha_fin'           => 'nullable|date|after_or_equal:fecha_inicio',
            'reglas'              => 'required|array|min:1',    // sucursal obligatoria
            'reglas.*.tipo'       => 'required|in:modelo,marca,cantidad_minima,sucursal',
            'reglas.*.valor'      => 'nullable|string|max:100',
        ]);
    }

    private function sincronizarReglas(Cupon $cupon, array $reglas): void
    {
        // Borrar reglas viejas y recrear
        $cupon->reglas()->delete();

        foreach ($reglas as $regla) {
            $valor = ($regla['valor'] ?? '') === '' ? null : $regla['valor'];
            CuponRegla::create([
                'id_cupon' => $cupon->id_cupon,
                'tipo'     => $regla['tipo'],
                'valor'    => $valor,
            ]);
        }
    }
}