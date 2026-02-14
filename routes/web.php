<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RootController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth'])->name('dashboard');


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

//DASHBOARD ROOT
Route::middleware(['auth'])->group(function () {

    Route::get('/root', [RootController::class, 'index'])->name('root.dashboard');

    Route::post('/root/links', [RootController::class, 'storeLink'])->name('root.links.store');

    Route::delete('/root/links/{link}', [RootController::class, 'destroyLink'])->name('root.links.destroy');

    Route::get('/root/negocios', [RootController::class, 'negocios'])->name('root.negocios');

});


require __DIR__.'/auth.php';
