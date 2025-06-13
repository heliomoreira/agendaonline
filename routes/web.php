<?php

use App\Http\Controllers\ClientsController;
use App\Http\Controllers\ProfessionalsController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ServicesController;
use Illuminate\Support\Facades\Route;


/*Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');*/

Route::get('/', function () {
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

Route::prefix('services')->group(function () {
    Route::get('/', [ServicesController::class, 'index'])->name('services.index');
    Route::get('/form', [ServicesController::class, 'form'])->name('services.form');
    Route::get('/edit/{id}', [ServicesController::class, 'edit'])->name('services.edit');

    Route::post('/store', [ServicesController::class, 'store'])->name('services.store');
    Route::put('/update/{id}', [ServicesController::class, 'update'])->name('services.update');
});

Route::prefix('professionals')->group(function () {
    Route::get('/', [ProfessionalsController::class, 'index'])->name('professionals.index');
    Route::get('/form', [ProfessionalsController::class, 'form'])->name('professionals.form');
    Route::get('/edit/{id}', [ProfessionalsController::class, 'edit'])->name('professionals.edit');

    Route::post('/store', [ProfessionalsController::class, 'store'])->name('professionals.store');
    Route::put('/update/{id}', [ProfessionalsController::class, 'update'])->name('professionals.update');
});

require __DIR__ . '/auth.php';
