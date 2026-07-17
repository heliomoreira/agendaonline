<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Stancl\Tenancy\Exceptions\TenantCouldNotBeIdentifiedOnDomainException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Middleware\EnsurePortalActive;


return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->redirectGuestsTo(function (Request $request) {
            if ($request->is('cliente') || $request->is('cliente/*')) {
                return route('client.login');
            }

            return route('login');
        });

        $middleware->alias([
            'portal.active' => EnsurePortalActive::class,
        ]);

        $middleware->redirectUsersTo(function (Request $request) {
            if ($request->is('cliente') || $request->is('cliente/*')) {
                return route('client.dashboard');
            }

            return route('dashboard');
        });
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (TenantCouldNotBeIdentifiedOnDomainException $e, $request) {
            return redirect()->away(config('app.url'));
        });
    })->create();
