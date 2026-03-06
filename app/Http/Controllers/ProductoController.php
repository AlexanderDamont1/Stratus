<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Producto;
use App\Models\Negocio;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProductoController extends Controller
{
    public function index()
{
    $productos = Producto::with('negocio')->paginate(10);
    $negocios = Negocio::all();

    return view('productos.index', compact('productos', 'negocios'));
}

    public function create()
    {
        $negocios = Negocio::orderBy('nombre')->get();
        return view('productos.create', compact('negocios'));
    }

    public function store(Request $request)
{
    $request->validate([
        'nombre_producto' => 'required|string|max:255|unique:productos,nombre_producto',
    ]);

    Producto::create([
        'id_producto' => 'PDT' . str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT),
        'id_negocio'      => auth()->user()->id_negocio, // ✅ del usuario autenticado
        'nombre_producto' => $request->nombre_producto,
    ]);

    return redirect()->route('productos.index')
        ->with('success', 'Producto creado correctamente.');
}

    public function edit(Producto $producto)
    {
        $negocios = Negocio::orderBy('nombre')->get();
        return view('productos.edit', compact('producto', 'negocios'));
    }

    public function update(Request $request, Producto $producto)
    {
        $request->validate([
            'nombre_producto' => 'required|string|max:255|unique:productos,nombre_producto,' . $producto->id_producto . ',id_producto',
            'id_negocio'      => 'required|exists:negocios,id_negocio',
        ]);

        $producto->update([
            'id_negocio'      => $request->id_negocio,
            'nombre_producto' => $request->nombre_producto,
        ]);

        return redirect()->route('productos.index')
            ->with('success', 'Producto actualizado correctamente.');
    }

    public function destroy(Producto $producto)
    {
        $producto->delete();

        return redirect()->route('productos.index')
            ->with('success', 'Producto eliminado correctamente.');
    }
}