<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RegistroController;
use App\Http\Controllers\RootController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Pública
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
});

/*
|--------------------------------------------------------------------------
| Registro por link (pública — sin auth)
| El link valida todo: existencia, expiración y uso único
|--------------------------------------------------------------------------
*/

Route::get('/registro/{token}',  [RegistroController::class, 'show'])->name('registro.show');
Route::post('/registro/{token}', [RegistroController::class, 'store'])->name('registro.store');

/*
|--------------------------------------------------------------------------
| Dashboard post-login
|--------------------------------------------------------------------------
*/

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth'])->name('dashboard');

/*
|--------------------------------------------------------------------------
| Perfil (Breeze)
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {
    Route::get('/profile',    [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile',  [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

/*
|--------------------------------------------------------------------------
| Root — solo id_rol = 0
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'es.root'])->group(function () {

    Route::get('/root',                              [RootController::class, 'index'])->name('root.dashboard');
    Route::post('/root/links',                       [RootController::class, 'storeLink'])->name('root.links.store');
    Route::delete('/root/links/{link}',              [RootController::class, 'destroyLink'])->name('root.links.destroy');

});

require __DIR__ . '/auth.php';