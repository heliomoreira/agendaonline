<?php

declare(strict_types=1);

use App\Http\Controllers\AccountSettingsController;
use App\Http\Controllers\AgendaController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\ClientsController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\PortalController;
use App\Http\Controllers\ProductsController;
use App\Http\Controllers\ProfessionalsController;
use App\Http\Controllers\ServicesController;
use App\Http\Controllers\TenantController;
use Illuminate\Support\Facades\Route;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;

/*
|--------------------------------------------------------------------------
| Tenant Routes
|--------------------------------------------------------------------------
|
| Here you can register the tenant routes for your application.
| These routes are loaded by the TenantRouteServiceProvider.
|
| Feel free to customize them however you want. Good luck!
|
*/

Route::middleware([
    'web',
    InitializeTenancyByDomain::class,
    PreventAccessFromCentralDomains::class,
])->group(function () {

    Route::get('/', [TenantController::class, 'index'])->name('tenant.index');

    Route::get('/admin', [DashboardController::class, 'index'])->middleware('auth')->name('dashboard');

    Route::prefix('admin')->group(function () {
        /*
        * Admin Auth routes
         */
        Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
        Route::post('/login', [LoginController::class, 'login']);
        Route::post('/logout', function () {
            Auth::logout();
            request()->session()->invalidate();
            request()->session()->regenerateToken();

            return redirect()->route('login');
        })->name('logout');

        /*
        * Admin Application routes
        */
        Route::middleware(['auth'])->group(function () {
            // Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

            /* Clients */
            Route::prefix('/clients')->group(function () {
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

                //Route::get('/{id}/professionals', [ProfessionalsController::class, 'getProfessionalsByService']);
            });

            Route::prefix('professionals')->group(function () {
                Route::get('/', [ProfessionalsController::class, 'index'])->name('professionals.index');
                Route::get('/form', [ProfessionalsController::class, 'form'])->name('professionals.form');
                Route::get('/edit/{id}', [ProfessionalsController::class, 'edit'])->name('professionals.edit');

                Route::post('/store', [ProfessionalsController::class, 'store'])->name('professionals.store');
                Route::put('/update/{id}', [ProfessionalsController::class, 'update'])->name('professionals.update');

                Route::put('/{id}/services', [ProfessionalsController::class, 'updateServices'])->name('professionals.update.services');
                Route::put('/{id}/working-hours', [ProfessionalsController::class, 'saveWorkingHours'])->name('professionals.save.working-hours');

                Route::post('/{id}/save-unavailability', [ProfessionalsController::class, 'saveUnavailability'])->name('professionals.unavailability.store');
                Route::delete('/{id}/remove-unavailability', [ProfessionalsController::class, 'removeUnavailability'])->name('professionals.unavailability.remove');

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

            Route::prefix('account-settings')->group(function () {
                Route::get('/', [AccountSettingsController::class, 'index'])->name('account-settings.index');
                Route::put('/update', [AccountSettingsController::class, 'updateAccount'])->name('account-settings.updateAccount');

            });

            Route::prefix('portal')->group(function () {
                Route::get('/', [PortalController::class, 'index'])->name('portal.index');
                Route::put('/update/{id}', [PortalController::class, 'update'])->name('portal.update');
            });

            Route::prefix('locations')->group(function () {
                Route::get('/', [LocationController::class, 'index'])->name('locations.index');
                Route::get('/form', [LocationController::class, 'form'])->name('locations.form');
                Route::POST('/store', [LocationController::class, 'store'])->name('locations.store');
                Route::PUT('/update/{id}', [LocationController::class, 'update'])->name('locations.update');
                Route::get('/edit/{id}', [LocationController::class, 'edit'])->name('locations.edit');
            });
        });
    });

    /*
     * Public routes
     */
    Route::get('/services/{id}/professionals', [ProfessionalsController::class, 'getProfessionalsByService']);
    Route::get('/available-slots', [BookingController::class, 'getAvailableSlots']);
    //Route::get('/booking', [BookingController::class, 'index'])->name('book.index');
    Route::post('/book-slot', [BookingController::class, 'bookSlot'])->name('book.slot');


});
