<?php

use App\Http\Controllers\AgendaController;
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
            return view('front.website.index');
        });
        Route::get('/signup', [TenantController::class, 'signup']);
        Route::post('/signup/create-tenant', [TenantController::class, 'createTenant']);
        Route::get('/signup/account-created', [TenantController::class, 'createTenant']);

    });
}

require __DIR__ . '/auth.php';
