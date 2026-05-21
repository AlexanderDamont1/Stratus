<?php

namespace App\Http\Controllers\Root;

use App\Http\Controllers\Controller;
use App\Services\NovedadService;
use Illuminate\Http\Request;

class NovedadController extends Controller
{

    public function index()
    {
        return response()->json([
            'ok'       => true,
            'novedades' => NovedadService::all(),
        ]);
    }
    
    public function store(Request $request)
    {
        $data = $request->validate([
            'tipo'    => 'required|in:info,warning,success,danger',
            'titulo'  => 'required|string|max:80',
            'mensaje' => 'required|string|max:200',
        ]);

        NovedadService::create($data);

        return response()->json(['ok' => true, 'novedades' => NovedadService::all()]);
    }

    public function update(Request $request, string $id)
    {
        $data = $request->validate([
            'tipo'    => 'sometimes|in:info,warning,success,danger',
            'titulo'  => 'sometimes|string|max:80',
            'mensaje' => 'sometimes|string|max:200',
            'activa'  => 'sometimes|boolean',
        ]);

        NovedadService::update($id, $data);

        return response()->json(['ok' => true, 'novedades' => NovedadService::all()]);
    }

    public function destroy(string $id)
    {
        NovedadService::delete($id);

        return response()->json(['ok' => true, 'novedades' => NovedadService::all()]);
    }

    public function reorder(Request $request)
    {
        $request->validate(['ids' => 'required|array']);
        NovedadService::reorder($request->ids);

        return response()->json(['ok' => true]);
    }
}