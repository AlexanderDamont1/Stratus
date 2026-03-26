<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RegistroController;
use App\Http\Controllers\RootController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Admin\SetupController;
use App\Http\Controllers\Admin\VendedorController;
use App\Http\Controllers\BicicletaController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\ModeloController;
use App\Http\Controllers\ColorController;
use App\Http\Controllers\VoltajeController;
use App\Http\Controllers\ModeloVoltajeController;
use App\Http\Controllers\MarcaController;       // ← nuevo
use App\Http\Controllers\PedidoController;
use App\Http\Controllers\EnlaceController;
use App\Events\NotificationEvent;

/*
|--------------------------------------------------------------------------
| PÚBLICAS
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/registro/{token}',  [RegistroController::class, 'show'])->name('registro.show');
Route::post('/registro/{token}', [RegistroController::class, 'store'])->name('registro.store');

/*
|--------------------------------------------------------------------------
| Autenticación
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function () {
    Route::get('/login',  [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store']);
});

Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');

/*
|--------------------------------------------------------------------------
| RUTAS PROTEGIDAS
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'single.session', 'force.setup'])->group(function () {

    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::get('/profile',    [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile',  [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::post('/admin/setup/completar', [SetupController::class, 'completar'])->name('admin.setup.completar');

    /*
    |--------------------------------------------------------------------------
    | Admin
    |--------------------------------------------------------------------------
    */
    Route::middleware('es.admin')->group(function () {
        Route::get('/admin/vendedores/create', [VendedorController::class, 'create'])->name('admin.vendedores.create');
        Route::post('/admin/vendedores', [VendedorController::class, 'store'])->name('admin.vendedores.store');
    });

    /*
    |--------------------------------------------------------------------------
    | Root
    |--------------------------------------------------------------------------
    */
    Route::middleware('es.root')->group(function () {
        Route::get('/root', [RootController::class, 'index'])->name('root.dashboard');
        Route::post('/root/links', [RootController::class, 'storeLink'])->name('root.links.store');
        Route::delete('/root/links/{link}', [RootController::class, 'destroyLink'])->name('root.links.destroy');
    });

    /*
    |--------------------------------------------------------------------------
    | Gestor (rol 5)
    |--------------------------------------------------------------------------
    */
    Route::middleware('gestor')->group(function () {

        Route::get('/gestor/dashboard', [EnlaceController::class, 'dashboard'])->name('gestor.dashboard');

        Route::prefix('gestor')->name('gestor.')->group(function () {
            Route::prefix('vehiculos')->name('vehiculos.')->group(function () {

                // Bicicletas
                Route::get('/bicicletas/crear', [BicicletaController::class, 'create'])->name('bicicletas.create');
                Route::post('/bicicletas', [BicicletaController::class, 'store'])->name('bicicletas.store');
                Route::get('/bicicletas/{id}/editar', [BicicletaController::class, 'edit'])->name('bicicletas.edit');
                Route::put('/bicicletas/{id}', [BicicletaController::class, 'update'])->name('bicicletas.update');
                Route::delete('/bicicletas/{num_serie}/pedido', [BicicletaController::class, 'destroyFromPedido'])->name('bicicletas.destroyFromPedido');
                Route::post('bicicletas/{num_serie}/status', [BicicletaController::class, 'updateStatus'])->name('bicicletas.status');
                Route::get('bicicletas/cliente/{id_cliente}', [BicicletaController::class, 'getByCliente'])->name('bicicletas.por-cliente');

                // Modelos
                Route::get('/modelos', [ModeloController::class, 'index'])->name('modelos.index');
                Route::get('/modelos/crear', [ModeloController::class, 'create'])->name('modelos.create');
                Route::post('/modelos', [ModeloController::class, 'store'])->name('modelos.store');
                Route::get('/modelos/{modelo}/editar', [ModeloController::class, 'edit'])->name('modelos.edit');
                Route::put('/modelos/{modelo}', [ModeloController::class, 'update'])->name('modelos.update');
                Route::delete('/modelos/{modelo}', [ModeloController::class, 'destroy'])->name('modelos.destroy'); // ← nuevo

                // Colores
                Route::get('/colores', [ColorController::class, 'index'])->name('colores.index');
                Route::get('/colores/crear', [ColorController::class, 'create'])->name('colores.create');
                Route::post('/colores', [ColorController::class, 'store'])->name('colores.store');
                Route::get('/colores/{color}/editar', [ColorController::class, 'edit'])->name('colores.edit');
                Route::put('/colores/{color}', [ColorController::class, 'update'])->name('colores.update');
                Route::delete('/colores/{color}', [ColorController::class, 'destroy'])->name('colores.destroy'); // ← nuevo

                // Voltajes
                Route::get('/voltajes', [VoltajeController::class, 'index'])->name('voltajes.index');
                Route::get('/voltajes/crear', [VoltajeController::class, 'create'])->name('voltajes.create');
                Route::post('/voltajes', [VoltajeController::class, 'store'])->name('voltajes.store');
                Route::get('/voltajes/{voltaje}/editar', [VoltajeController::class, 'edit'])->name('voltajes.edit');
                Route::put('/voltajes/{voltaje}', [VoltajeController::class, 'update'])->name('voltajes.update');
                Route::delete('/voltajes/{voltaje}', [VoltajeController::class, 'destroy'])->name('voltajes.destroy'); // ← nuevo

            });
        });

        Route::get('/pedidos/{id_pedido}/realizar', [PedidoController::class, 'realizar'])->name('pedidos.realizar');
        Route::get('/pedidos/{id_pedido}/pdf', [PedidoController::class, 'pdf'])->name('pedidos.pdf');

        Route::get('/bicicletas/{num_serie}', [BicicletaController::class, 'showApi'])->name('api.bicicletas.show');

        Route::patch('/enlaces/{id}/activar', [EnlaceController::class, 'activar'])->name('enlaces.activar');
        Route::get('/enlaces/pedidos', [EnlaceController::class, 'pedidosDeEnlaces'])->name('enlaces.pedidos');
        Route::post('/enlaces/aceptar', [EnlaceController::class, 'aceptar'])->name('enlaces.aceptar');

        Route::get('/pedidos/rapido/crear', [PedidoController::class, 'crearRapido'])->name('pedidos.rapido.crear');
        Route::post('/pedidos/rapido/pdf', [PedidoController::class, 'generarPdfRapido'])->name('pedidos.rapido.pdf');

        Route::post('/pedidos/{id_pedido}/status', [PedidoController::class, 'updateStatus'])->name('pedidos.status');
    });

    /*
    |--------------------------------------------------------------------------
    | Enlace (rol 1 y rol 5)
    |--------------------------------------------------------------------------
    */
    Route::middleware('enlace')->group(function () {

       // Route::get('/enlaces', [EnlaceController::class, 'index'])->name('enlaces.index');
        Route::post('/enlaces/generar', [EnlaceController::class, 'generar'])->name('enlaces.generar');
        Route::patch('/enlaces/{id}/cancelar', [EnlaceController::class, 'cancelar'])->name('enlaces.cancelar');

        Route::post('/pedidos/{id_pedido}/completar', [PedidoController::class, 'completarEntrega'])->name('pedidos.completar');
        Route::get('gestor/vehiculos/bicicletas', [BicicletaController::class, 'index'])->name('bicicletas.index');

        Route::get('/api/pedidos/{id}', function ($id) {
            $pedido = \App\Models\Pedido::with(['negocio', 'usuario', 'items.modelo', 'items.voltaje', 'items.color'])
                ->findOrFail($id);

            return [
                'id_pedido'  => $pedido->id_pedido,
                'negocio'    => $pedido->negocio->nombre_negocio ?? '—',
                'usuario'    => $pedido->usuario->nombre_usuario ?? '—',
                'status'     => $pedido->status_label,
                'status_num' => $pedido->status,
                'notas'      => $pedido->notas ?? '',
                'fecha'      => $pedido->created_at->format('d/m/Y H:i'),
                'updated_at' => $pedido->updated_at->format('d/m/Y H:i'),
                'items'      => $pedido->items->map(fn($i) => [
                    'id_modelo'  => $i->id_modelo,
                    'id_voltaje' => $i->id_voltaje,
                    'id_color'   => $i->id_color,
                    'modelo'     => $i->modelo->nombre_modelo ?? '—',
                    'voltaje'    => $i->voltaje->voltaje ?? '—',
                    'color'      => $i->color->color ?? '—',
                    'cantidad'   => $i->cantidad,
                ]),
            ];
        })->name('pedidos.api.get');
    });

    /*
    |--------------------------------------------------------------------------
    | Administrador (rol 1)
    |--------------------------------------------------------------------------
    */
    Route::middleware('administrador')->group(function () {

        Route::get('/Inicio', function () {
            $enlace = \App\Models\Enlace::where('id_usuario1', auth()->user()->id_usuario)
                ->whereIn('estado', ['pendiente', 'activo', 'cancelado'])
                ->with('usuarioDestino:id_usuario,nombre_usuario')
                ->first();

            return view('administrador.dashboard', ['enlace' => $enlace]);
        })->name('administrador.dashboard');


        // En web.php dentro del grupo administrador
        Route::get('/admin/catalogo/voltajes-disponibles/{modelo}', function (\App\Models\Modelo $modelo) {
            $user = auth()->user();
            if ($user->id_rol !== 1 || $modelo->id_negocio !== $user->id_negocio) abort(403);

            $asignados = \App\Models\ModeloVoltaje::where('id_modelo', $modelo->id_modelo)
                ->pluck('id_voltaje');

            return response()->json(
                \App\Models\Voltaje::where('id_negocio', $user->id_negocio)
                    ->whereNotIn('id_voltaje', $asignados)
                    ->orderBy('voltaje')
                    ->get(['id_voltaje', 'voltaje'])
            );
        })->name('admin.catalogo.voltajes.disponibles');

        Route::get('/pedidos/crear', [PedidoController::class, 'create'])->name('pedidos.create');
        Route::get('/pedidos/{id_pedido}/edit', [PedidoController::class, 'edit'])->name('pedidos.edit');
        Route::put('/pedidos/{id_pedido}', [PedidoController::class, 'update'])->name('pedidos.update');
        Route::delete('/pedidos/{id_pedido}', [PedidoController::class, 'destroy'])->name('pedidos.destroy');
        Route::get('/ver/{id_pedido}/token', [PedidoController::class, 'token'])->name('pedidos.token');

        // ── Marcas (exclusivo rol 1) ──────────────────────────────────────────
        Route::prefix('admin/catalogo')->name('admin.catalogo.')->middleware('prefijo.admin')->group(function () {

            Route::get('/marcas/crear', [MarcaController::class, 'create'])->name('marcas.create');
            Route::post('/marcas', [MarcaController::class, 'store'])->name('marcas.store');
            Route::get('/marcas/{marca}/editar', [MarcaController::class, 'edit'])->name('marcas.edit');
            Route::put('/marcas/{marca}', [MarcaController::class, 'update'])->name('marcas.update');
            Route::delete('/marcas/{marca}', [MarcaController::class, 'destroy'])->name('marcas.destroy');

            // Modelos, colores y voltajes del admin (mismo controlador, distinto prefijo)
            Route::get('/modelos', fn() => redirect()->route('admin.catalogo.index'));
            Route::get('/modelos/crear', [ModeloController::class, 'create'])->name('modelos.create');
            Route::post('/modelos', [ModeloController::class, 'store'])->name('modelos.store');
            Route::get('/modelos/{modelo}/editar', [ModeloController::class, 'edit'])->name('modelos.edit');
            Route::put('/modelos/{modelo}', [ModeloController::class, 'update'])->name('modelos.update');
            Route::delete('/modelos/{modelo}', [ModeloController::class, 'destroy'])->name('modelos.destroy');

            Route::get('/colores', fn() => redirect()->route('admin.catalogo.index'));
            Route::get('/colores/crear', [ColorController::class, 'create'])->name('colores.create');
            Route::post('/colores', [ColorController::class, 'store'])->name('colores.store');
            Route::get('/colores/{color}/editar', [ColorController::class, 'edit'])->name('colores.edit');
            Route::put('/colores/{color}', [ColorController::class, 'update'])->name('colores.update');
            Route::delete('/colores/{color}', [ColorController::class, 'destroy'])->name('colores.destroy');

            Route::get('/voltajes', [VoltajeController::class, 'index'])->name('voltajes.index');
            Route::get('/voltajes/crear', [VoltajeController::class, 'create'])->name('voltajes.create');
            Route::post('/voltajes', [VoltajeController::class, 'store'])->name('voltajes.store');
            Route::get('/voltajes/{voltaje}/editar', [VoltajeController::class, 'edit'])->name('voltajes.edit');
            Route::put('/voltajes/{voltaje}', [VoltajeController::class, 'update'])->name('voltajes.update');
            Route::delete('/voltajes/{voltaje}', [VoltajeController::class, 'destroy'])->name('voltajes.destroy');

            Route::post('/modelo-voltaje', [ModeloVoltajeController::class, 'store'])->name('modelo-voltaje.store');
            Route::delete('/modelo-voltaje/{id}', [ModeloVoltajeController::class, 'destroy'])->name('modelo-voltaje.destroy');

            Route::get('/', [MarcaController::class, 'catalogo'])->name('index');

            Route::get('/admin/catalogo/marca-card/{idMarca}', [MarcaController::class, 'card'])->name('admin.catalogo.marca.card')->middleware('auth');

            Route::post('/sugerir-hex', function (Request $request) {
                $request->validate(['nombre' => 'required|string|max:50']);

                $response = \Illuminate\Support\Facades\Http::withHeaders([
                    'Authorization' => 'Bearer ' . config('services.groq.key'),
                    'Content-Type'  => 'application/json',
                ])->post('https://api.groq.com/openai/v1/chat/completions', [
                    'model'      => 'llama-3.1-8b-instant', // gratis y rapidísimo
                    'max_tokens' => 10,
                    'messages'   => [
                        [
                            'role'    => 'system',
                            'content' => 'Eres un asistente que SOLO responde con colores hexadecimales en formato #RRGGBB. Sin explicaciones, sin texto extra, solo el hex.',
                        ],
                        [
                            'role'    => 'user',
                            'content' => "¿Qué color hexadecimal representa \"{$request->nombre}\"?",
                        ],
                    ],
                ]);

                $hex = trim($response->json('choices.0.message.content') ?? '');

                if (!preg_match('/^#[0-9A-Fa-f]{6}$/', $hex)) {
                    return response()->json(['hex' => null]);
                }

                return response()->json(['hex' => $hex]);})->name('admin.catalogo.sugerir-hex');
        });
    });

    /*
    |--------------------------------------------------------------------------
    | Ventas (rol 2)
    |--------------------------------------------------------------------------
    */
    Route::middleware('ventas')->group(function () {
        Route::get('vendedor/dashboard', [BicicletaController::class, 'showB'])->name('stock.index');
        Route::get('/bicicletas/qrv/{num_serie}', [BicicletaController::class, 'buscarPorSerieQr'])->name('bicicletas.qr');
        Route::post('/bicicletas/asignar-usuario', [BicicletaController::class, 'asignarUsuario'])->name('bicicletas.asignarUsuario');
    });

    /*
    |--------------------------------------------------------------------------
    | AJAX / API internas (accesibles para varios roles)
    |--------------------------------------------------------------------------
    */
    Route::get('/modelo-voltaje', [ModeloVoltajeController::class, 'modeloVoltaje'])->name('modelo-voltaje');
    Route::post('/modelo-voltaje', [ModeloVoltajeController::class, 'store'])->name('modelo-voltaje.store');
    Route::delete('/modelo-voltaje/{id}', [ModeloVoltajeController::class, 'destroy'])->name('modelo-voltaje.destroy');

    Route::get('/voltaje-por-modelo/{id_modelo}', [ModeloVoltajeController::class, 'voltajesPorModelo'])->name('voltajes.porModelo');
    Route::get('/colores-por-modelo/{id_modelo}', [BicicletaController::class, 'coloresPorModelo'])->name('colores.porModelo');

    // Productos
    Route::get('/Productos', [ProductoController::class, 'index'])->name('productos.index');
    Route::post('/Productos', [ProductoController::class, 'store'])->name('productos.store');
    Route::delete('/Productos/{id}', [ProductoController::class, 'destroy'])->name('productos.destroy');

    // Pedidos
    Route::get('/pedidos', [PedidoController::class, 'index'])->name('pedidos.index');
    Route::post('/pedidos', [PedidoController::class, 'store'])->name('pedidos.store');
    Route::get('/pedidos/{id_pedido}', [PedidoController::class, 'show'])->name('pedidos.show');
    Route::patch('/pedidos/{id_pedido}/status', [PedidoController::class, 'updateStatus'])->name('pedidos.status');

});

require __DIR__ . '/auth.php';