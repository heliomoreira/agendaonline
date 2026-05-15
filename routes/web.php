<?php

use App\Http\Controllers\AgendaController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\ClientAuthController;
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
            return view('front.website.index');
        });
        Route::get('/signup', [TenantController::class, 'signup']);
        Route::post('/signup/create-tenant', [TenantController::class, 'createTenant']);
        Route::get('/signup/account-created', [TenantController::class, 'createTenant']);

        //Route::get('/signup', [TenantController::class, 'showRegistrationForm'])->name('register');

// Processar registo
        Route::post('/signup/create-tenant', [TenantController::class, 'createTenant'])->name('register.create');
    });
}


Route::prefix('client')->name('client.')->group(function () {

    // Guest routes
    Route::middleware('guest:client')->group(function () {
        Route::get('/login', [ClientAuthController::class, 'showLogin'])->name('login');
        Route::post('/login', [ClientAuthController::class, 'login']);
        Route::get('/register', [ClientAuthController::class, 'showRegister'])->name('register');
        Route::post('/register', [ClientAuthController::class, 'register']);

        Route::get('/forgot-password', [ClientAuthController::class, 'showForgotPassword'])->name('password.request');
        Route::post('/forgot-password', [ClientAuthController::class, 'sendResetLink'])->name('password.email');
        Route::get('/reset-password/{token}', [ClientAuthController::class, 'showResetPassword'])->name('password.reset');
        Route::post('/reset-password', [ClientAuthController::class, 'resetPassword'])->name('password.update');
    });

    // Authenticated routes
    Route::middleware('auth:client')->group(function () {
        Route::get('/dashboard', [ClientDashboardController::class, 'index'])->name('dashboard');
        Route::get('/bookings', [ClientDashboardController::class, 'bookings'])->name('bookings');
        Route::get('/bookings/{booking}', [ClientDashboardController::class, 'show'])->name('bookings.show');
        Route::post('/bookings/{booking}/cancel', [ClientDashboardController::class, 'cancel'])->name('bookings.cancel');

        Route::get('/profile', [ClientProfileController::class, 'edit'])->name('profile.edit');
        Route::put('/profile', [ClientProfileController::class, 'update'])->name('profile.update');
        Route::put('/profile/password', [ClientProfileController::class, 'updatePassword'])->name('profile.password');

        Route::post('/logout', [ClientAuthController::class, 'logout'])->name('logout');
    });
});

// Email verification
Route::get('/client/verify/{id}/{hash}', [ClientAuthController::class, 'verify'])
    ->middleware(['signed'])
    ->name('client.verification.verify');


require __DIR__ . '/auth.php';
