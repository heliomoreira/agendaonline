<?php

use App\Http\Controllers\AgendaController;
use App\Http\Controllers\AvailabilityController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\ClientsController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductsController;
use App\Http\Controllers\ProfessionalsController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ServicesController;
use App\Http\Controllers\TenantController;
use Illuminate\Support\Facades\Route;


foreach (config('tenancy.central_domains') as $domain) {
    Route::domain($domain)->group(function () {
        Route::get('/', function () {
            return view('website.index');
        });
        Route::get('/signup', [TenantController::class, 'signup']);
        Route::post('/signup/create-tenant', [TenantController::class, 'createTenant']);

    });
}

Route::middleware(['web', 'tenant'])->group(function () {
    Route::get('/booking', [BookingController::class, 'index'])->name('book.index');
    Route::post('/book-slot', [BookingController::class, 'bookSlot'])->name('book.slot');
});

/*Route::get('/booking', [BookingController::class, 'index'])->name('book.index');
Route::post('/book-slot', [BookingController::class, 'bookSlot'])->name('book.slot');*/
//Route::get('/book-confirmation', [BookingController::class, 'bookConfirmation'])->name('book.confirmation');





require __DIR__ . '/auth.php';
