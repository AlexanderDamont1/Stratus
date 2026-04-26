<?php

namespace App\Http\Controllers;

use App\Models\Negocio;
use App\Models\RegistroLink;
use App\Services\ModuloService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Events\NegocioExpirado;
use App\Events\NegocioActivado;
use App\Events\StatsActualizadas;

class RootController extends Controller
{
    public function index()
    {
        $links    = RegistroLink::latest()->paginate(10);
        $negocios = Negocio::with('admin')->latest()->paginate(10);

        $rawStats = Negocio::selectRaw("
            SUM(negocio_status = 'trial')                as en_trial,
            SUM(negocio_status = 'activo')               as activos,
            SUM(negocio_status = 'trial_expirado')       as trial_expirado,
            SUM(negocio_status = 'suscripcion_expirada') as suscripcion_expirada,
            SUM(negocio_status = 'suspendido')           as suspendidos
        ")->first();

        $stats = [
            'en_trial'             => (int) $rawStats->en_trial,
            'activos'              => (int) $rawStats->activos,
            'trial_expirado'       => (int) $rawStats->trial_expirado,
            'suscripcion_expirada' => (int) $rawStats->suscripcion_expirada,
            'suspendidos'          => (int) $rawStats->suspendidos,
        ];

        return view('root.dashboard', compact('links', 'negocios', 'stats'));
    }

    public function storeLink(Request $request)
    {
        $request->validate([
            'max_users' => ['required', 'integer', 'min:1', 'max:100'],
        ]);

        RegistroLink::create([
            'token'      => Str::uuid(),
            'max_users'  => $request->max_users,
            'usado'      => false,
            'expires_at' => now()->addHours(24),
        ]);

        return back()->with('success', 'Link creado. Expira en 24 horas.');
    }

    public function destroyLink(RegistroLink $link)
    {
        $link->delete();
        return back()->with('success', 'Link eliminado correctamente.');
    }

    // ── Activar suscripción desde root ────────────────────
        public function activarSuscripcion(Request $request, string $idNegocio)
    {
        $request->validate([
            'dias' => 'required|integer|min:1|max:365',
        ]);

        $negocio = Negocio::findOrFail($idNegocio);
        $negocio->activarSuscripcion($request->dias);
        $negocio->refresh();

        // Notificar en tiempo real al root (otras pestañas) y stats
        broadcast(new NegocioActivado($negocio));
        broadcast(new StatsActualizadas());

        return response()->json([
            'ok'      => true,
            'mensaje' => "Suscripción activada por {$request->dias} días.",
            'hasta'   => $negocio->subscribed_until->format('d/m/Y'),
        ]);
    }

    public function suspender(string $idNegocio)
    {
        $negocio = Negocio::findOrFail($idNegocio);
        $negocio->suspender();

        broadcast(new NegocioExpirado($negocio, 'suspendido'));
        broadcast(new StatsActualizadas());

        return response()->json(['ok' => true]);
    }

    public function modulos(string $idNegocio)
    {
        $negocio = Negocio::findOrFail($idNegocio);
        $estado  = ModuloService::getEstadoCompleto($idNegocio);

        return view('root.modulos', compact('negocio', 'estado'));
    }

    public function toggleModulo(Request $request)
    {
        $request->validate([
            'id_negocio' => 'required|string|exists:negocios,id_negocio',
            'id_modulo'  => 'required|string|exists:modulos,id_modulo',
            'id_rol'     => 'required|integer|in:1,2,5',
            'activo'     => 'required|boolean',
        ]);

        ModuloService::toggle(
            $request->id_negocio,
            $request->id_modulo,
            $request->id_rol,
            $request->activo
        );

        return response()->json(['ok' => true]);
    }
}