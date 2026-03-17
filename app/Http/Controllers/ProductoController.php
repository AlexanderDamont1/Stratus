<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Services\CatalogService;
use Illuminate\Http\Request;

class ProductoController extends Controller
{
    /**
     * Lista paginada de productos.
     */
    public function index()
    {
        $productos = Producto::with('negocio')->paginate(10);
        $negocios = CatalogService::getNegocios();

        return view('productos.index', compact('productos', 'negocios'));
    }

    /**
     * Muestra formulario de creación.
     */
    public function create()
    {
        $negocios = CatalogService::getNegocios();
        return view('productos.create', compact('negocios'));
    }

    /**
     * Guarda un nuevo producto.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nombre_producto' => 'required|string|max:255|unique:productos,nombre_producto',
        ]);

        $producto = Producto::create([
            'id_producto'     => 'PDT' . str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT),
            'id_negocio'      => auth()->user()->id_negocio,
            'nombre_producto' => $request->nombre_producto,
        ]);

        // Invalidar caché del producto y listas asociadas
        CatalogService::invalidateProducto($producto->id_producto, $producto->id_negocio);

        return redirect()->route('productos.index')
            ->with('success', 'Producto creado correctamente.');
    }

    /**
     * Muestra formulario de edición.
     */
    public function edit(Producto $producto)
    {
        $negocios = CatalogService::getNegocios();
        return view('productos.edit', compact('producto', 'negocios'));
    }

    /**
     * Actualiza un producto existente.
     */
    public function update(Request $request, Producto $producto)
    {
        $request->validate([
            'nombre_producto' => 'required|string|max:255|unique:productos,nombre_producto,' . $producto->id_producto . ',id_producto',
            'id_negocio'      => 'required|exists:negocios,id_negocio',
        ]);

        $oldNegocio = $producto->id_negocio;
        $producto->update([
            'id_negocio'      => $request->id_negocio,
            'nombre_producto' => $request->nombre_producto,
        ]);

        // Invalidar caché del producto (tanto para el negocio anterior como para el nuevo si cambió)
        CatalogService::invalidateProducto($producto->id_producto, $oldNegocio);
        if ($oldNegocio != $request->id_negocio) {
            CatalogService::invalidateProducto($producto->id_producto, $request->id_negocio);
        }

        return redirect()->route('productos.index')
            ->with('success', 'Producto actualizado correctamente.');
    }

    /**
     * Elimina un producto.
     */
    public function destroy(Producto $producto)
    {
        $id = $producto->id_producto;
        $idNegocio = $producto->id_negocio;
        $producto->delete();

        CatalogService::invalidateProducto($id, $idNegocio);

        return redirect()->route('productos.index')
            ->with('success', 'Producto eliminado correctamente.');
    }
}