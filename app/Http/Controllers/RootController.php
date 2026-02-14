<?php


namespace App\Http\Controllers;

use App\Models\Negocio;
use App\Models\RegistroLink;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class RootController extends Controller
{
    public function index()
    {
        $links = RegistroLink::latest()->paginate(10);
        $negocios = Negocio::latest()->paginate(10);

        return view('root.dashboard', compact('links', 'negocios'));
    }

    public function storeLink(Request $request)
    {
        $request->validate([
            'max_users' => 'required|integer|min:1'
        ]);

        RegistroLink::create([
            'token' => Str::uuid(),
            'max_users' => $request->max_users,
            'usado' => false,
        ]);

        return back()->with('success', 'Link creado correctamente');
    }

    public function destroyLink(RegistroLink $link)
    {
        $link->delete();

        return back()->with('success', 'Link eliminado');
    }
}
