<?php

use App\Http\Controllers\AgendaController;
use App\Http\Controllers\ClientsController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductsController;
use App\Http\Controllers\ProfessionalsController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ServicesController;
use Illuminate\Support\Facades\Route;


/*Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');*/

Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

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

    Route::get('/{id}/professionals', [ProfessionalsController::class, 'getProfessionalsByService']);
});

Route::prefix('professionals')->group(function () {
    Route::get('/', [ProfessionalsController::class, 'index'])->name('professionals.index');
    Route::get('/form', [ProfessionalsController::class, 'form'])->name('professionals.form');
    Route::get('/edit/{id}', [ProfessionalsController::class, 'edit'])->name('professionals.edit');

    Route::post('/store', [ProfessionalsController::class, 'store'])->name('professionals.store');
    Route::put('/update/{id}', [ProfessionalsController::class, 'update'])->name('professionals.update');

    Route::put('/{id}/services', [ProfessionalsController::class, 'updateServices'])->name('professionals.update.services');

});

Route::prefix('products')->group(function () {
    Route::get('/', [ProductsController::class, 'index'])->name('products.index');
    Route::get('/form', [ProductsController::class, 'form'])->name('products.form');
    Route::get('/edit/{id}', [ProductsController::class, 'edit'])->name('products.edit');

    Route::post('/store', [ProductsController::class, 'store'])->name('products.store');
    Route::put('/update/{id}', [ProductsController::class, 'update'])->name('products.update');
});


Route::prefix('agenda')->group(function () {
    Route::get('/', [AgendaController::class, 'index'])->name('agenda.index');
    Route::get('/form', [AgendaController::class, 'form'])->name('agenda.form');
    Route::get('/edit/{id}', [AgendaController::class, 'edit'])->name('agenda.edit');

    Route::post('/store', [AgendaController::class, 'store'])->name('agenda.store');
    Route::put('/update/{id}', [AgendaController::class, 'update'])->name('agenda.update');

    Route::get('/get-events', [AgendaController::class, 'getEvents'])->name('agenda.get-events');

});

require __DIR__ . '/auth.php';
