<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Producto;
use App\Models\ProductoModelo;
use App\Services\CatalogService;

class ProductoController extends Controller
{
    public function index()
    {
        $user      = Auth::user();
        $idNegocio = $user->id_negocio;
        $esRol1    = $user->id_rol === 1;

        $productos = CatalogService::getProductosConRelaciones(
            $idNegocio,
            $esRol1 ? null : $user->id_usuario
        );

        $bicicletas = $productos
            ->where('tipo', '2')
            ->groupBy(fn($p) => $p->productoModelo?->first()?->id_modelo ?? $p->id_producto);

        $accesorios = $productos->where('tipo', '1')->values();

        $sucursales = $esRol1
            ? CatalogService::getSucursalesByNegocio($idNegocio)
            : collect();

        $marcas  = CatalogService::getMarcasByNegocio($idNegocio);
        $modelos = CatalogService::getModelosByNegocio($idNegocio);

        // ── Inventario ──────────────────────────────────────────
        if ($esRol1) {
            // Admin: inventario de todas las sucursales agrupado por id_producto_modelo
            $inventario = CatalogService::getInventarioByNegocio($idNegocio)
                ->groupBy('id_producto_modelo');
        } else {
            // Sucursal: solo su propio inventario, indexado por id_producto_modelo
            $inventario = CatalogService::getInventarioBySucursal($idNegocio, $user->id_usuario)
                ->keyBy('id_producto_modelo');
        }

        return view('productos.index', compact(
            'bicicletas',
            'accesorios',
            'sucursales',
            'marcas',
            'modelos',
            'esRol1',
            'inventario'
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

        CatalogService::invalidateProducto($idProducto, $idNegocio);
        CatalogService::invalidateProductosConRelaciones($idNegocio, $idUsuario);

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
    // GET /productos/modelos-por-marca/{idMarca}
    public function modelosPorMarca($idMarca)
    {
        $idNegocio = Auth::user()->id_negocio;

        $modelos = CatalogService::getModelosByMarca($idMarca, $idNegocio);

        return response()->json($modelos);
    }

    // ── VOLTAJES DISPONIBLES POR MODELO (excluye los ya asignados a precio) ──
    public function voltajesPorModelo($idModelo)
    {
        $user      = Auth::user();
        $idNegocio = $user->id_negocio;
        $idUsuario = $user->id_rol === 1 ? null : $user->id_usuario;

        // Voltajes del catálogo para este modelo
        $voltajes = CatalogService::getVoltajesByModelo($idModelo, $idNegocio);

        // IDs de voltajes que ya tienen un producto/precio asignado para este modelo
        // en este negocio (y sucursal si aplica)
        $usadosQuery = ProductoModelo::where('id_modelo', $idModelo)
            ->where('id_negocio', $idNegocio);

        if ($idUsuario) {
            $usadosQuery->where('id_usuario', $idUsuario);
        }

        $voltajesUsados = $usadosQuery->pluck('id_voltaje')->toArray();

        // Filtrar los ya usados
        $voltajesDisponibles = $voltajes->reject(
            fn($v) => in_array($v->id_voltaje, $voltajesUsados)
        )->values();

        return response()->json($voltajesDisponibles);
    }
}
