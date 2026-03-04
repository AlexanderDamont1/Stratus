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
use App\Events\NotificationEvent;

/*
|--------------------------------------------------------------------------
| PÚBLICAS
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
});

        Route::get('/test-websocket', function () {
    return view('test-websocket');
});

Route::post('/send-websocket-test', function (Request $request) {
    $userId = $request->user_id ?? 1;
    $message = $request->message ?? 'Mensaje de prueba';
    
    event(new NotificationEvent($userId, $message));
    
    return response()->json(['success' => true, 'message' => 'Evento enviado']);
})->name('websocket.test');

/*
|--------------------------------------------------------------------------
| Registro por link (público)
|--------------------------------------------------------------------------
*/

Route::get('/registro/{token}',  [RegistroController::class, 'show'])
    ->name('registro.show');

Route::post('/registro/{token}', [RegistroController::class, 'store'])
    ->name('registro.store');

/*
|--------------------------------------------------------------------------
| Autenticación
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function () {
    Route::get('/login',  [AuthenticatedSessionController::class, 'create'])
        ->name('login');

    Route::post('/login', [AuthenticatedSessionController::class, 'store']);
});

Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');

/*
|--------------------------------------------------------------------------
| RUTAS PROTEGIDAS
| Middleware:
| - auth
| - single.session
| - force.setup  (inyecta modal si rol = 44)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'single.session', 'force.setup'])->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Dashboard
    |--------------------------------------------------------------------------
    */
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');


    /*
    |--------------------------------------------------------------------------
    | Perfil (Breeze compatible)
    |--------------------------------------------------------------------------
    */
    Route::get('/profile',    [ProfileController::class, 'edit'])
        ->name('profile.edit');

    Route::patch('/profile',  [ProfileController::class, 'update'])
        ->name('profile.update');

    Route::delete('/profile', [ProfileController::class, 'destroy'])
        ->name('profile.destroy');


    /*
    |--------------------------------------------------------------------------
    | Setup obligatorio (ROL 44 → 1)
    |--------------------------------------------------------------------------
    */
    Route::post('/admin/setup/completar', [SetupController::class, 'completar'])
        ->name('admin.setup.completar');


    /*
    |--------------------------------------------------------------------------
    | Vendedores (Solo Admin rol = 1)
    |--------------------------------------------------------------------------
    */
    Route::middleware('es.admin')->group(function () {

        Route::get('/admin/vendedores/create', [VendedorController::class, 'create'])
            ->name('admin.vendedores.create');

        Route::post('/admin/vendedores', [VendedorController::class, 'store'])
            ->name('admin.vendedores.store');
    });


    /*
    |--------------------------------------------------------------------------
    | ROOT (Solo id_rol = 0)
    |--------------------------------------------------------------------------
    */
    Route::middleware('es.root')->group(function () {

        Route::get('/root', [RootController::class, 'index'])
            ->name('root.dashboard');

        Route::post('/root/links', [RootController::class, 'storeLink'])
            ->name('root.links.store');

        Route::delete('/root/links/{link}', [RootController::class, 'destroyLink'])
            ->name('root.links.destroy');

    
    });



    /*
    |--------------------------------------------------------------------------
    | ROOT (Solo id_rol = 0)
    |--------------------------------------------------------------------------
    */
    Route::middleware('es.gestor')->group(function () {

            Route::get('/gestor', function () {
                return view('gestor.dashboard');
            })->name('gestor.dashboard');

    });

});

require __DIR__.'/auth.php';