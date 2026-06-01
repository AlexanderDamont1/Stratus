<?php

namespace App\Http\Controllers;

use App\Events\StockActualizado;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Producto;
use App\Models\ProductoModelo;
use App\Services\CatalogService;

class ProductoController extends Controller
{
    public function index(Request $request)
    {
        $user      = Auth::user();
        $idNegocio = $user->id_negocio;
        $esRol1    = $user->id_rol === 1;

        $idSucursalFiltro = $esRol1
            ? session('productos.sucursal_filtro')
            : $user->id_usuario;

        $productos = CatalogService::getProductosConRelaciones(
            $idNegocio,
            $idSucursalFiltro
        );

        if ($esRol1 && $idSucursalFiltro) {
            $productos = $productos->filter(function ($producto) {
                if ($producto->tipo === '1') return true;
                return $producto->productoModelo->contains('activo', true);
            });
        }

        $bicicletas = $productos
            ->where('tipo', '2')
            ->groupBy(fn($p) => $p->productoModelo?->first()?->id_modelo ?? $p->id_producto);

        $accesorios = $productos->where('tipo', '1')->values();

        $sucursales = $esRol1
            ? CatalogService::getSucursalesByNegocio($idNegocio)
            : collect();

        $marcas  = CatalogService::getMarcasByNegocio($idNegocio);
        $modelos = CatalogService::getModelosByNegocio($idNegocio);

        if ($esRol1 && $idSucursalFiltro) {
            $inventario = CatalogService::getInventarioBySucursal($idNegocio, $idSucursalFiltro)
                ->keyBy('id_producto_modelo');
        } elseif ($esRol1) {
            $inventario = CatalogService::getInventarioByNegocio($idNegocio)
                ->groupBy('id_producto_modelo');
        } else {
            $inventario = CatalogService::getInventarioBySucursal($idNegocio, $user->id_usuario)
                ->keyBy('id_producto_modelo');
        }

        if (!$esRol1) {
            $inventarioAccesorios = CatalogService::getInventarioBySucursal($idNegocio, $user->id_usuario)
                ->whereNull('id_producto_modelo')
                ->keyBy('id_producto');
        } elseif ($idSucursalFiltro) {
            $inventarioAccesorios = CatalogService::getInventarioBySucursal($idNegocio, $idSucursalFiltro)
                ->whereNull('id_producto_modelo')
                ->keyBy('id_producto');
        } else {
            $inventarioAccesorios = CatalogService::getInventarioByNegocio($idNegocio)
                ->whereNull('id_producto_modelo')
                ->keyBy('id_producto');
        }

        $coloresPorModelo = [];
        $idModelos = $bicicletas->keys()->toArray();

        if (count($idModelos)) {
            $coloresEnStock = CatalogService::getColoresEnStockPorModelos(
                $idModelos,
                $idNegocio,
                $idSucursalFiltro ?? ($esRol1 ? null : $user->id_usuario)
            );

            foreach ($idModelos as $idModelo) {
                $coloresPorModelo[$idModelo] = ($coloresEnStock[$idModelo] ?? collect())
                    ->map(function ($bici) {
                        $partes = explode('|', $bici->color->color ?? '');
                        $nombre = trim($partes[0]);
                        $hexes  = isset($partes[1])
                            ? array_map('trim', explode('/', $partes[1]))
                            : ['#cccccc'];
                        return ['nombre' => $nombre, 'hex' => $hexes];
                    })->values()->toArray();
            }
        }

        return view('productos.index', compact(
            'bicicletas',
            'accesorios',
            'sucursales',
            'marcas',
            'modelos',
            'esRol1',
            'inventario',
            'inventarioAccesorios',
            'coloresPorModelo',
            'idSucursalFiltro',
        ));
    }

    // ── STORE ACCESORIO (tipo 1) ──
    public function storeAccesorio(Request $request)
    {
        $user      = Auth::user();
        $idNegocio = $user->id_negocio;
        $esRol1    = $user->id_rol === 1;

        $rules = [
            'nombre_producto' => 'required|string|max:255',
            'precio'          => 'required|min:0',
        ];

        if ($esRol1) {
            $rules['id_usuario'] = 'required|exists:usuarios,id_usuario';
        }

        $request->validate($rules);

        $idUsuario  = $esRol1 ? $request->id_usuario : $user->id_usuario;
        $idProducto = 'PDT' . str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);

        Producto::create([
            'id_producto'     => $idProducto,
            'id_negocio'      => $idNegocio,
            'id_usuario'      => $idUsuario,
            'nombre_producto' => $request->nombre_producto,
            'precio'          => $request->precio,
            'tipo'            => '1',
        ]);

        \App\Models\Inventario::create([
            'id_inventario'      => 'INV' . strtoupper(substr(md5(uniqid()), 0, 12)),
            'id_producto_modelo' => null,
            'id_producto'        => $idProducto,
            'id_negocio'         => $idNegocio,
            'id_usuario'         => $idUsuario,
            'cantidad'           => 0,
            'stock_minimo'       => 3,
        ]);

        CatalogService::invalidateProducto($idProducto, $idNegocio);
        CatalogService::invalidateProductosConRelaciones($idNegocio, $idUsuario);
        CatalogService::invalidateInventario($idNegocio, $idUsuario);

        return response()->json(['message' => 'Accesorio creado correctamente.']);
    }

    // ── STORE BICICLETA (tipo 2) ──
    public function storeBicicleta(Request $request)
    {
        $user      = Auth::user();
        $idNegocio = $user->id_negocio;
        $esRol1    = $user->id_rol === 1;

        $rules = [
            'id_modelo'  => 'required|exists:modelos,id_modelo',
            'id_voltaje' => 'required|exists:voltajes,id_voltaje',
            'precio'     => 'required|min:0',
        ];

        if ($esRol1) {
            $rules['id_usuario'] = 'required|exists:usuarios,id_usuario';
        }

        $request->validate($rules);

        $idUsuario  = $esRol1 ? $request->id_usuario : $user->id_usuario;
        $modelo     = CatalogService::getModeloById($request->id_modelo);
        $idProducto = 'PDT' . str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);

        Producto::create([
            'id_producto'     => $idProducto,
            'id_negocio'      => $idNegocio,
            'id_usuario'      => $idUsuario,
            'nombre_producto' => $modelo->nombre_modelo ?? $request->id_modelo,
            'precio'          => $request->precio,
            'tipo'            => '2',
        ]);

        ProductoModelo::create([
            'id_producto_modelo' => 'PM' . str_pad(rand(0, 9999999), 7, '0', STR_PAD_LEFT),
            'id_producto'        => $idProducto,
            'id_negocio'         => $idNegocio,
            'id_usuario'         => $idUsuario,
            'id_modelo'          => $request->id_modelo,
            'id_voltaje'         => $request->id_voltaje,
            'activo'             => true,
        ]);

        CatalogService::invalidateProducto($idProducto, $idNegocio);
        CatalogService::invalidateProductosConRelaciones($idNegocio, $idUsuario);

        return response()->json(['message' => 'Bicicleta creada correctamente.']);
    }

    public function update(Request $request, $id)
    {
        $user      = Auth::user();
        $idNegocio = $user->id_negocio;
        $idUsuario = $user->id_usuario;

        $producto = Producto::where('id_producto', $id)
            ->where('id_negocio', $idNegocio)
            ->firstOrFail();

        $request->validate([
            'nombre_producto' => 'required|string|max:255',
            'precio'          => 'required|min:0',
        ]);

        $producto->update([
            'nombre_producto' => $request->nombre_producto,
            'precio'          => $request->precio,
        ]);

        CatalogService::invalidateProducto($id, $idNegocio);
        CatalogService::invalidateProductosConRelaciones($idNegocio, $idUsuario);

        return response()->json(['message' => 'Producto actualizado correctamente.']);
    }

    public function destroy($id)
    {
        $user      = Auth::user();
        $idNegocio = $user->id_negocio;
        $idUsuario = $user->id_usuario;

        $producto = Producto::where('id_producto', $id)
            ->where('id_negocio', $idNegocio)
            ->firstOrFail();

        $producto->delete();

        CatalogService::invalidateProducto($id, $idNegocio);
        CatalogService::invalidateProductosConRelaciones($idNegocio, $idUsuario);

        return response()->json(['message' => 'Producto eliminado correctamente.']);
    }

    // ── MODELOS POR MARCA ──
    public function modelosPorMarca($idMarca)
    {
        $idNegocio = Auth::user()->id_negocio;

        $modelos = CatalogService::getModelosByMarca($idMarca, $idNegocio);

        return response()->json($modelos);
    }

    // ── VOLTAJES DISPONIBLES POR MODELO ──
    public function voltajesPorModelo($idModelo)
    {
        $user      = Auth::user();
        $idNegocio = $user->id_negocio;
        $idUsuario = $user->id_rol === 1 ? null : $user->id_usuario;

        $voltajes = CatalogService::getVoltajesByModelo($idModelo, $idNegocio);

        $usadosQuery = ProductoModelo::where('id_modelo', $idModelo)
            ->where('id_negocio', $idNegocio);

        if ($idUsuario) {
            $usadosQuery->where('id_usuario', $idUsuario);
        }

        $voltajesUsados = $usadosQuery->pluck('id_voltaje')->toArray();

        $voltajesDisponibles = $voltajes->reject(
            fn($v) => in_array($v->id_voltaje, $voltajesUsados)
        )->values();

        return response()->json($voltajesDisponibles);
    }

    public function setFiltroSucursal(Request $request)
    {
        $idSucursal = $request->input('sucursal');

        session(['productos.sucursal_filtro' => $idSucursal ?: null]);

        return response()->json(['ok' => true]);
    }

    // ── ACTUALIZAR CANTIDAD ACCESORIO ──
    public function updateCantidadAccesorio(Request $request, string $idInventario)
    {
        $user      = Auth::user();
        $idNegocio = $user->id_negocio;

        $request->validate([
            'cantidad' => 'required|integer|min:0',
        ]);

        $inventario = \App\Models\Inventario::where('id_inventario', $idInventario)
            ->where('id_negocio', $idNegocio)
            ->where('id_usuario', $user->id_usuario)
            ->whereNull('id_producto_modelo')
            ->firstOrFail();

        $inventario->update(['cantidad' => $request->cantidad]);

        CatalogService::invalidateInventario($idNegocio, $user->id_usuario);

        // ── Notificar stock actualizado al vendedor via Reverb ────────────
        event(new StockActualizado(
            idNegocio:  $idNegocio,
            idUsuario:  $user->id_usuario,
            accesorios: [[
                'id_producto' => $inventario->id_producto,
                'stock'       => (int) $request->cantidad,
            ]],
        ));

        return response()->json(['ok' => true]);
    }
}