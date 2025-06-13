<?php

use App\Http\Controllers\ClientsController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

/*Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');*/

Route::get('/dashboard', function () {
    return view('dashboard');
})->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});


Route::prefix('clients')->group(function () {
    Route::get('/', [ClientsController::class, 'index'])->name('clients.index');
    Route::get('/form', [ClientsController::class, 'form'])->name('clients.form');
    Route::get('/edit/{id}', [ClientsController::class, 'edit'])->name('clients.edit');

    Route::post('/store', [ClientsController::class, 'store'])->name('clients.store');
    Route::put('/update/{id}', [ClientsController::class, 'update'])->name('clients.update');
});

require __DIR__.'/auth.php';
