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
use App\Http\Controllers\MarcaController;
use App\Http\Controllers\PedidoController;
use App\Http\Controllers\EnlaceController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\VentaController;
use App\Http\Controllers\MovimientoController;
use App\Http\Controllers\GarantiaController;
use App\Http\Controllers\AdminGarantiaController;
use App\Http\Controllers\TrialController;
use App\Http\Controllers\EmailVerificationController;
use App\Http\Controllers\GoogleAuthController;

/*
|--------------------------------------------------------------------------
| PÚBLICAS
|--------------------------------------------------------------------------
*/



Route::get('/', fn () => view('welcome'));

//Formulario de contacto
Route::post('/contact', [ContactController::class, 'send'])
    ->name('contact.send')
    ->middleware('throttle:5,1'); // máx 5 envíos por minuto por IP

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


    // ── Google OAuth ──────────────────────────────────────
Route::get('/auth/google/login',              [GoogleAuthController::class, 'redirectLogin'])->name('google.login');
Route::get('/auth/google/callback',           [GoogleAuthController::class, 'callbackLogin'])->name('google.callback');
Route::get('/auth/google/registro/{token}',   [GoogleAuthController::class, 'redirectRegistro'])->name('google.registro.redirect');
Route::get('/auth/google/registro/callback',  [GoogleAuthController::class, 'callbackRegistro'])->name('google.registro.callback');
Route::get('/registro/google/negocio',        [GoogleAuthController::class, 'formNegocio'])->name('registro.google.negocio');
Route::post('/registro/google/negocio',       [GoogleAuthController::class, 'storeNegocio'])->name('registro.google.negocio.store');

/*
|--------------------------------------------------------------------------
| PROTEGIDAS
|--------------------------------------------------------------------------
*/


Route::middleware(['auth', 'email.verificado'])->group(function () {
    Route::get('/trial/expirado',       [TrialController::class, 'expirado'])->name('trial.expirado');
    Route::get('/suscripcion/expirada', [TrialController::class, 'suscripcionExpirada'])->name('suscripcion.expirada');

     Route::get('/verificacion-pendiente', function () {
        if (auth()->user()->emailVerificado()) {
            return redirect()->route('dashboard');
        }
        return view('auth.verificacion-pendiente');
    })->name('verificacion.pendiente');

    Route::post('/reenviar-verificacion', [EmailVerificationController::class, 'reenviar'])
        ->name('verificacion.reenviar')
        ->middleware('throttle:3,1');
});






Route::middleware(['auth', 'single.session', 'force.setup', 'trial.expirado'])->group(function () {

    Route::get('/dashboard', fn () => view('dashboard'))->name('dashboard');

    Route::get('/profile',    [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile',  [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/modelo-voltaje', [ModeloVoltajeController::class, 'modeloVoltaje'])->name('modelo-voltaje');

    Route::post('/admin/setup/completar', [SetupController::class, 'completar'])->name('admin.setup.completar');

    /*
    |----------------------------------------------------------------------
    | AJAX compartidas (todos los roles autenticados)
    | Registradas UNA sola vez aquí arriba para evitar duplicados.
    |----------------------------------------------------------------------
    */
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
            'items'      => $pedido->items->map(fn ($i) => [
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

    Route::get('/voltaje-por-modelo/{id_modelo}', [ModeloVoltajeController::class, 'voltajesPorModelo'])->name('voltajes.porModelo');
    Route::get('/colores-por-modelo/{id_modelo}', [BicicletaController::class, 'coloresPorModelo'])->name('colores.porModelo');

    

    /*
    |----------------------------------------------------------------------
    | Admin (rol 1)
    |----------------------------------------------------------------------
    */
    Route::middleware('administrador')->group(function () {

        Route::get('/Inicio', function () {
            $enlace = \App\Models\Enlace::where('id_usuario1', auth()->user()->id_usuario)
                ->whereIn('estado', ['pendiente', 'activo', 'cancelado'])
                ->with('usuarioDestino:id_usuario,nombre_usuario')
                ->first();
            return view('administrador.dashboard', ['enlace' => $enlace]);
        })->name('administrador.dashboard');

        // Vendedores
        Route::get('/admin/vendedores/create', [VendedorController::class, 'create'])->name('admin.vendedores.create');
        Route::post('/admin/vendedores',        [VendedorController::class, 'store'])->name('admin.vendedores.store');

        // Bicicletas admin
        Route::get('/admin/stock-seccion',        [BicicletaController::class, 'stockSeccion'])->name('bicicletas.stock.seccion');
        Route::get('/admin/bicicletas/crear',     [BicicletaController::class, 'create'])->name('admin.bicicletas.create');
        Route::post('/admin/bicicletas',          [BicicletaController::class, 'store'])->name('admin.bicicletas.store');
        Route::post('/admin/bicicletas/masivo',   [BicicletaController::class, 'storeMasivo'])->name('admin.bicicletas.storeMasivo');

        // Pedidos (solo admin puede crear/editar/eliminar pedidos)
        Route::get('/pedidos/crear',              [PedidoController::class, 'create'])->name('pedidos.create');
        Route::post('/pedidos',                   [PedidoController::class, 'store'])->name('pedidos.store');
        Route::get('/pedidos/{id_pedido}/edit',   [PedidoController::class, 'edit'])->name('pedidos.edit');
        Route::put('/pedidos/{id_pedido}',        [PedidoController::class, 'update'])->name('pedidos.update');
        Route::delete('/pedidos/{id_pedido}',     [PedidoController::class, 'destroy'])->name('pedidos.destroy');
        Route::get('/ver/{id_pedido}/token',      [PedidoController::class, 'token'])->name('pedidos.token');



        Route::prefix('admin/movimientos')->name('admin.movimientos.')->middleware(['modulo:tracking'])->group(function () {
                Route::get('/',                  [MovimientoController::class, 'index'])->name('index');
                Route::get('/historial/{serie}', [MovimientoController::class, 'historial'])->name('historial');
                Route::get('/buscar',            [MovimientoController::class, 'buscar'])->name('buscar');
            });

        Route::prefix('admin/garantias')->name('admin.garantias.')->group(function () {
        Route::get('/',                          [AdminGarantiaController::class, 'index'])         ->name('index');
        Route::get('/reclamos',                  [AdminGarantiaController::class, 'reclamos'])      ->name('reclamos');
        Route::get('/marcas',                    [AdminGarantiaController::class, 'marcas'])        ->name('marcas');
        Route::get('/marcas/{idMarca}',          [AdminGarantiaController::class, 'editarMarca'])   ->name('marcas.editar');
        Route::post('/marcas/{idMarca}/pdf',     [AdminGarantiaController::class, 'subirPdf'])      ->name('marcas.pdf');
        Route::post('/marcas/{idMarca}/activar', [AdminGarantiaController::class, 'activar'])       ->name('marcas.activar');
        Route::post('/componentes',              [AdminGarantiaController::class, 'guardarDefs'])   ->name('componentes.guardar');
        Route::delete('/componentes/{id}',       [AdminGarantiaController::class, 'borrarDef'])     ->name('componentes.borrar');
        Route::patch('/reclamo/{id}/estado',     [AdminGarantiaController::class, 'estadoReclamo'])->name('reclamo.estado');
        Route::post('/reclamo/{id}/reemplazo',   [AdminGarantiaController::class, 'reemplazo'])    ->name('reclamo.reemplazo');
        Route::post('/politica', [AdminGarantiaController::class, 'guardarPolitica'])->name('politica');
    });


        // ── Productos (admin) ──────────────────────────────────────────────
        Route::prefix('productos')->name('admin.productos.')->group(function () {
            Route::get('/',                           [ProductoController::class, 'index'])->name('index');
            Route::post('/accesorio',                 [ProductoController::class, 'storeAccesorio'])->name('storeAccesorio');
            Route::post('/bicicleta',                 [ProductoController::class, 'storeBicicleta'])->name('storeBicicleta');
            Route::put('/{id}',                       [ProductoController::class, 'update'])->name('update');
            Route::delete('/{id}',                    [ProductoController::class, 'destroy'])->name('destroy');
            // AJAX: voltajes disponibles (ya excluye los usados en el controlador)
            Route::get('/voltajes/{idModelo}',        [ProductoController::class, 'voltajesPorModelo'])->name('voltajes');
            // AJAX: modelos de una marca
            Route::get('/modelos-por-marca/{idMarca}',[ProductoController::class, 'modelosPorMarca'])->name('modelosPorMarca');
            Route::post('/filtro-sucursal', [ProductoController::class, 'setFiltroSucursal'])->name('filtroSucursal');
        });

        // ── Catálogo admin ────────────────────────────────────────────────────
        Route::prefix('admin/catalogo')->name('admin.catalogo.')->middleware('prefijo.admin')->group(function () {

            Route::get('/', [MarcaController::class, 'catalogo'])->name('index');
            Route::get('/marca-card/{idMarca}', [MarcaController::class, 'card'])->name('marca.card');

            // Marcas
            Route::get('/marcas/crear',          [MarcaController::class, 'create'])->name('marcas.create');
            Route::post('/marcas',               [MarcaController::class, 'store'])->name('marcas.store');
            Route::get('/marcas/{marca}/editar', [MarcaController::class, 'edit'])->name('marcas.edit');
            Route::put('/marcas/{marca}',        [MarcaController::class, 'update'])->name('marcas.update');
            Route::delete('/marcas/{marca}',     [MarcaController::class, 'destroy'])->name('marcas.destroy');

            // Modelos
            Route::get('/modelos',               fn () => redirect()->route('admin.catalogo.index'));
            Route::get('/modelos/crear',         [ModeloController::class, 'create'])->name('modelos.create');
            Route::post('/modelos',              [ModeloController::class, 'store'])->name('modelos.store');
            Route::get('/modelos/{modelo}/editar',[ModeloController::class, 'edit'])->name('modelos.edit');
            Route::put('/modelos/{modelo}',      [ModeloController::class, 'update'])->name('modelos.update');
            Route::delete('/modelos/{modelo}',   [ModeloController::class, 'destroy'])->name('modelos.destroy');

            // Colores
            Route::get('/colores',               fn () => redirect()->route('admin.catalogo.index'));
            Route::get('/colores/crear',         [ColorController::class, 'create'])->name('colores.create');
            Route::post('/colores',              [ColorController::class, 'store'])->name('colores.store');
            Route::get('/colores/{color}/editar',[ColorController::class, 'edit'])->name('colores.edit');
            Route::put('/colores/{color}',       [ColorController::class, 'update'])->name('colores.update');
            Route::delete('/colores/{color}',    [ColorController::class, 'destroy'])->name('colores.destroy');

            // Voltajes
            Route::get('/voltajes',              [VoltajeController::class, 'index'])->name('voltajes.index');
            Route::get('/voltajes/crear',        [VoltajeController::class, 'create'])->name('voltajes.create');
            Route::post('/voltajes',             [VoltajeController::class, 'store'])->name('voltajes.store');
            Route::get('/voltajes/{voltaje}/editar',[VoltajeController::class, 'edit'])->name('voltajes.edit');
            Route::put('/voltajes/{voltaje}',    [VoltajeController::class, 'update'])->name('voltajes.update');
            Route::delete('/voltajes/{voltaje}', [VoltajeController::class, 'destroy'])->name('voltajes.destroy');

             Route::post('/modelo-voltaje',       [ModeloVoltajeController::class, 'store'])->name('modelo-voltaje.store');
            Route::delete('/modelo-voltaje/{id}',[ModeloVoltajeController::class, 'destroy'])->name('modelo-voltaje.destroy');

            // Voltajes disponibles para asignar a modelo (distintos de los de productos)
            Route::get('/voltajes-disponibles/{modelo}', function (\App\Models\Modelo $modelo) {
                $user = auth()->user();
                if ($user->id_rol !== 1 || $modelo->id_negocio !== $user->id_negocio) abort(403);

                $asignados = \App\Models\ModeloVoltaje::where('id_modelo', $modelo->id_modelo)->pluck('id_voltaje');

                return response()->json(
                    \App\Models\Voltaje::where('id_negocio', $user->id_negocio)
                        ->whereNotIn('id_voltaje', $asignados)
                        ->orderBy('voltaje')
                        ->get(['id_voltaje', 'voltaje'])
                );
            })->name('voltajes.disponibles');

           

            // Sugerir hex por IA
            Route::post('/sugerir-hex', function (Request $request) {
                $request->validate(['nombre' => 'required|string|max:50']);

                $response = \Illuminate\Support\Facades\Http::withHeaders([
                    'Authorization' => 'Bearer ' . config('services.groq.key'),
                    'Content-Type'  => 'application/json',
                ])->post('https://api.groq.com/openai/v1/chat/completions', [
                    'model'      => 'llama-3.1-8b-instant',
                    'max_tokens' => 10,
                    'messages'   => [
                        ['role' => 'system', 'content' => 'Eres un asistente que SOLO responde con colores hexadecimales en formato #RRGGBB. Sin explicaciones, sin texto extra, solo el hex.'],
                        ['role' => 'user',   'content' => "¿Qué color hexadecimal representa \"{$request->nombre}\"?"],
                    ],
                ]);

                $hex = trim($response->json('choices.0.message.content') ?? '');

                return response()->json([
                    'hex' => preg_match('/^#[0-9A-Fa-f]{6}$/', $hex) ? $hex : null,
                ]);
            })->name('sugerir-hex');
        });

        
    });

    /*
    |----------------------------------------------------------------------
    | Gestor (rol 5)
    |----------------------------------------------------------------------
    */
    Route::middleware('gestor')->group(function () {

        Route::get('/gestor/dashboard', [EnlaceController::class, 'dashboard'])->name('gestor.dashboard');

        Route::prefix('gestor')->name('gestor.')->group(function () {
            Route::prefix('vehiculos')->name('vehiculos.')->group(function () {

                // Bicicletas
                Route::post('/bicicletas',                       [BicicletaController::class, 'store'])->name('bicicletas.store');
                Route::put('/bicicletas/{id}',                   [BicicletaController::class, 'update'])->name('bicicletas.update');
                Route::delete('/bicicletas/{num_serie}/pedido',  [BicicletaController::class, 'destroyFromPedido'])->name('bicicletas.destroyFromPedido');
                Route::get('bicicletas/cliente/{id_cliente}',    [BicicletaController::class, 'getByCliente'])->name('bicicletas.por-cliente');
                

                // Modelos
                Route::get('/modelos/crear',          [ModeloController::class, 'create'])->name('modelos.create');
                Route::get('/modelos',                [ModeloController::class, 'index'])->name('modelos.index');
                Route::post('/modelos',               [ModeloController::class, 'store'])->name('modelos.store');
                Route::get('/modelos/{modelo}/editar',[ModeloController::class, 'edit'])->name('modelos.edit');
                Route::put('/modelos/{modelo}',       [ModeloController::class, 'update'])->name('modelos.update');
                Route::delete('/modelos/{modelo}',    [ModeloController::class, 'destroy'])->name('modelos.destroy');

                // Colores
                Route::get('/colores/crear',          [ColorController::class, 'create'])->name('colores.create');
                Route::get('/colores',                [ColorController::class, 'index'])->name('colores.index');
                Route::post('/colores',               [ColorController::class, 'store'])->name('colores.store');
                Route::get('/colores/{color}/editar', [ColorController::class, 'edit'])->name('colores.edit');
                Route::put('/colores/{color}',        [ColorController::class, 'update'])->name('colores.update');
                Route::delete('/colores/{color}',     [ColorController::class, 'destroy'])->name('colores.destroy');

                // Voltajes
                Route::get('/voltajes',               [VoltajeController::class, 'index'])->name('voltajes.index');
                Route::get('/voltajes/crear',         [VoltajeController::class, 'create'])->name('voltajes.create');
                Route::post('/voltajes',              [VoltajeController::class, 'store'])->name('voltajes.store');
                Route::get('/voltajes/{voltaje}/editar',[VoltajeController::class, 'edit'])->name('voltajes.edit');
                Route::put('/voltajes/{voltaje}',     [VoltajeController::class, 'update'])->name('voltajes.update');
                Route::delete('/voltajes/{voltaje}',  [VoltajeController::class, 'destroy'])->name('voltajes.destroy');
            });
        });

        Route::get('/pedidos/{id_pedido}/realizar',    [PedidoController::class, 'realizar'])->name('pedidos.realizar');
        Route::get('/pedidos/{id_pedido}/pdf',         [PedidoController::class, 'pdf'])->name('pedidos.pdf');
        Route::post('/pedidos/{id_pedido}/completar',  [PedidoController::class, 'completarEntrega'])->name('pedidos.completar');
        Route::post('/pedidos/{id_pedido}/status',     [PedidoController::class, 'updateStatus'])->name('pedidos.status.post');

        Route::get('/bicicletas/{num_serie}',          [BicicletaController::class, 'showApi'])->name('api.bicicletas.show');

        Route::patch('/enlaces/{id}/activar',          [EnlaceController::class, 'activar'])->name('enlaces.activar');
        Route::get('/enlaces/pedidos',                 [EnlaceController::class, 'pedidosDeEnlaces'])->name('enlaces.pedidos');
        Route::post('/enlaces/aceptar',                [EnlaceController::class, 'aceptar'])->name('enlaces.aceptar');

        Route::get('/pedidos/rapido/crear',            [PedidoController::class, 'crearRapido'])->name('pedidos.rapido.crear');
        Route::post('/pedidos/rapido/pdf',             [PedidoController::class, 'generarPdfRapido'])->name('pedidos.rapido.pdf');
    });

    /*
    |----------------------------------------------------------------------
    | Enlace (rol 1 y rol 5)
    |----------------------------------------------------------------------
    */
    Route::middleware('enlace')->group(function () {
        Route::post('/enlaces/generar',          [EnlaceController::class, 'generar'])->name('enlaces.generar');
        Route::patch('/enlaces/{id}/cancelar',   [EnlaceController::class, 'cancelar'])->name('enlaces.cancelar');
        Route::get('gestor/vehiculos/bicicletas',[BicicletaController::class, 'index'])->name('bicicletas.index');
        Route::post('/modelo-voltaje',       [ModeloVoltajeController::class, 'store'])->name('modelo-voltaje.store');
        Route::delete('/modelo-voltaje/{id}',[ModeloVoltajeController::class, 'destroy'])->name('modelo-voltaje.destroy');
        Route::get('/pedidos',                          [PedidoController::class, 'index'])->name('pedidos.index');
        Route::get('/pedidos/{id_pedido}',              [PedidoController::class, 'show'])->name('pedidos.show');
        Route::patch('/pedidos/{id_pedido}/status',     [PedidoController::class, 'updateStatus'])->name('pedidos.status');

    });

    /*
    |----------------------------------------------------------------------
    | Ventas (rol 2)
    |----------------------------------------------------------------------
    */
    Route::middleware('ventas')->group(function () {
        Route::get('vendedor/dashboard',                      [BicicletaController::class, 'showB'])->name('stock.index');
        Route::get('/bicicletas/qrv/{num_serie}',            [BicicletaController::class, 'buscarPorSerieQr'])->name('bicicletas.qr');
        Route::post('/bicicletas/asignar-usuario',           [BicicletaController::class, 'asignarUsuario'])->name('bicicletas.asignarUsuario');

        // ── Productos (sucursal) ──────────────────────────────────────────────
        // Mismo controlador que admin pero bajo /sucursal/productos
       Route::prefix('sucursal/productos')->name('sucursal.productos.')->group(function () {
            Route::get('/',                            [ProductoController::class, 'index'])->name('index');
            Route::post('/accesorio',                  [ProductoController::class, 'storeAccesorio'])->name('storeAccesorio');
            Route::post('/bicicleta',                  [ProductoController::class, 'storeBicicleta'])->name('storeBicicleta');
            Route::put('/{id}',                        [ProductoController::class, 'update'])->name('update');
            Route::delete('/{id}',                     [ProductoController::class, 'destroy'])->name('destroy');
            Route::get('/voltajes/{idModelo}',         [ProductoController::class, 'voltajesPorModelo'])->name('voltajes');
            Route::get('/modelos-por-marca/{idMarca}', [ProductoController::class, 'modelosPorMarca'])->name('modelosPorMarca');
            Route::patch('/inventario/{idInventario}/cantidad', [ProductoController::class, 'updateCantidadAccesorio'])->name('updateCantidad'); // ← nuevo
        });

        Route::prefix('garantias')->name('garantias.')->group(function () {
        Route::get('/',                      [GarantiaController::class, 'index'])   ->name('index');
        Route::get('/buscar',                [GarantiaController::class, 'buscar'])  ->name('buscar');   // AJAX
        Route::get('/bici/{numSerie}',       [GarantiaController::class, 'show'])    ->name('show');
        Route::post('/reclamo',              [GarantiaController::class, 'reclamo']) ->name('reclamo');
        Route::patch('/reclamo/{id}/estado', [GarantiaController::class, 'estado'])  ->name('estado');
    });

        Route::prefix('sucursal/ventas')->name('ventas.')->group(function () {
    Route::get('/',                 [VentaController::class, 'index'])->name('index');
    Route::get('/crear',            [VentaController::class, 'create'])->name('create');
    Route::post('/',                [VentaController::class, 'store'])->name('store');
    Route::get('/buscar-serie',     [VentaController::class, 'buscarSerie'])->name('buscar-serie');
    Route::get('/{id}',             [VentaController::class, 'show'])->name('show');
    Route::get('/{id}/poliza',      [VentaController::class, 'poliza'])->name('poliza');
    Route::get('/{id}/ticket',      [VentaController::class, 'ticket'])->name('ticket');
});

        
    });

    /*
    |----------------------------------------------------------------------
    | Root
    |----------------------------------------------------------------------
    */
    Route::middleware('es.root')->group(function () {
        Route::get('/root',                                    [RootController::class, 'index'])->name('root.dashboard');
        Route::post('/root/links',                             [RootController::class, 'storeLink'])->name('root.links.store');
        Route::delete('/root/links/{link}',                    [RootController::class, 'destroyLink'])->name('root.links.destroy');
        Route::post('/root/negocios/{id}/activar',             [RootController::class, 'activarSuscripcion'])->name('root.negocios.activar');
        Route::post('/root/negocios/{id}/suspender',           [RootController::class, 'suspender'])->name('root.negocios.suspender');

        // ✅ Módulos
        Route::get('/root/negocios/{id}/modulos',        [RootController::class, 'modulos'])->name('root.modulos');
        Route::post('/root/modulos/toggle',              [RootController::class, 'toggleModulo'])->name('root.modulos.toggle');
    });

   

});

require __DIR__ . '/auth.php';