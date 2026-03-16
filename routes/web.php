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
    | Gestor
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
                Route::delete('/bicicletas/{num_serie}/pedido', [BicicletaController::class, 'destroyFromPedido'])->name('bicicletas.destroyFromPedido');;
                Route::post('bicicletas/{num_serie}/status', [BicicletaController::class, 'updateStatus'])->name('bicicletas.status');
                Route::get('bicicletas/cliente/{id_cliente}', [BicicletaController::class, 'getByCliente'])->name('bicicletas.por-cliente');

                // Modelos
                Route::get('/modelos', [ModeloController::class, 'index'])->name('modelos.index');
                Route::get('/modelos/crear', [ModeloController::class, 'create'])->name('modelos.create');
                Route::post('/modelos', [ModeloController::class, 'store'])->name('modelos.store');
                Route::get('/modelos/{id}/editar', [ModeloController::class, 'edit'])->name('modelos.edit');
                Route::put('/modelos/{id}', [ModeloController::class, 'update'])->name('modelos.update');
            

                // Colores
                Route::get('/colores', [ColorController::class, 'index'])->name('colores.index');
                Route::get('/colores/crear', [ColorController::class, 'create'])->name('colores.create');
                Route::post('/colores', [ColorController::class, 'store'])->name('colores.store');
                Route::get('/colores/{id}/editar', [ColorController::class, 'edit'])->name('colores.edit');
                Route::put('/colores/{id}', [ColorController::class, 'update'])->name('colores.update');
             

                // Voltajes
                Route::get('/voltajes', [VoltajeController::class, 'index'])->name('voltajes.index');
                Route::get('/voltajes/crear', [VoltajeController::class, 'create'])->name('voltajes.create');
                Route::post('/voltajes', [VoltajeController::class, 'store'])->name('voltajes.store');
                Route::get('/voltajes/{id}/editar', [VoltajeController::class, 'edit'])->name('voltajes.edit');
                Route::put('/voltajes/{id}', [VoltajeController::class, 'update'])->name('voltajes.update');
               
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






    Route::middleware('enlace')->group(function () {

        // Enlaces
        Route::get('/enlaces', [EnlaceController::class, 'index'])->name('enlaces.index');
        Route::post('/enlaces/generar', [EnlaceController::class, 'generar'])->name('enlaces.generar');
        Route::patch('/enlaces/{id}/cancelar', [EnlaceController::class, 'cancelar'])->name('enlaces.cancelar');

        Route::post('/pedidos/{id_pedido}/completar', [PedidoController::class, 'completarEntrega'])->name('pedidos.completar');
        Route::get('gestor/vehiculos/bicicletas', [BicicletaController::class, 'index'])->name('bicicletas.index');



        Route::get('/api/pedidos/{id}', function ($id) {
            $pedido = \App\Models\Pedido::with(['negocio', 'usuario', 'items.modelo', 'items.voltaje', 'items.color'])
                ->findOrFail($id);

            return [
                'id_pedido' => $pedido->id_pedido,
                'negocio' => $pedido->negocio->nombre_negocio ?? '—',
                'usuario' => $pedido->usuario->nombre_usuario ?? '—',
                'status' => $pedido->status_label,
                'status_num' => $pedido->status,
                'notas' => $pedido->notas ?? '',
                'fecha' => $pedido->created_at->format('d/m/Y H:i'),
                'updated_at' => $pedido->updated_at->format('d/m/Y H:i'),
                'items' => $pedido->items->map(fn($i) => [
                    'id_modelo' => $i->id_modelo,
                    'id_voltaje' => $i->id_voltaje,
                    'id_color' => $i->id_color,
                    'modelo' => $i->modelo->nombre_modelo ?? '—',
                    'voltaje' => $i->voltaje->voltaje ?? '—',
                    'color' => $i->color->color ?? '—',
                    'cantidad' => $i->cantidad,
                ]),
            ];
        })->name('pedidos.api.get');
    });




    Route::middleware('administrador')->group(function () {

        Route::get('/Inicio', function () {
            $enlace = \App\Models\Enlace::where('id_usuario1', auth()->user()->id_usuario)
                ->whereIn('estado', ['pendiente', 'activo'])
                ->with('usuarioDestino:id_usuario,nombre_usuario')
                ->first();

            return view('administrador.dashboard', ['enlace' => $enlace]);
        })->name('administrador.dashboard');

        Route::get('/pedidos/crear', [PedidoController::class, 'create'])->name('pedidos.create');
        Route::get('/pedidos/{id_pedido}/edit', [PedidoController::class, 'edit'])->name('pedidos.edit');
        Route::put('/pedidos/{id_pedido}',      [PedidoController::class, 'update'])->name('pedidos.update');
        Route::delete('/pedidos/{id_pedido}', [PedidoController::class, 'destroy'])->name('pedidos.destroy');
        Route::get('/ver/{id_pedido}/token', [PedidoController::class, 'token'])->name('pedidos.token');
    });









    // Modelo-Voltaje
    Route::get('/modelo-voltaje', [ModeloVoltajeController::class, 'modeloVoltaje'])->name('modelo-voltaje');
    Route::post('/modelo-voltaje', [ModeloVoltajeController::class, 'store'])->name('modelo-voltaje.store');
    Route::delete('/modelo-voltaje/{id}', [ModeloVoltajeController::class, 'destroy'])->name('modelo-voltaje.destroy');

    // AJAX: voltajes por modelo
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




    /*
    |--------------------------------------------------------------------------
    | WebSocket test
    |--------------------------------------------------------------------------
    */
    Route::get('/test-websocket', function () {
        return view('test-websocket');
    });

    Route::post('/send-websocket-test', function (Request $request) {
        $userId  = $request->user_id ?? 1;
        $message = $request->message ?? 'Mensaje de prueba';
        event(new NotificationEvent($userId, $message));
        return response()->json(['success' => true, 'message' => 'Evento enviado']);
    })->name('websocket.test');
});

require __DIR__ . '/auth.php';
