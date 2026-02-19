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


Route::get('/test-payment-setup', function() {
    $results = [];

    // 1. Verificar se Stripe SDK está instalado
    $results['stripe_sdk'] = class_exists('\Stripe\Stripe') ? '✅ Instalado' : '❌ NÃO INSTALADO - Execute: composer require stripe/stripe-php';

    // 2. Verificar variáveis de ambiente
    $results['stripe_key'] = config('services.stripe.key')
        ? '✅ ' . substr(config('services.stripe.key'), 0, 10) . '...'
        : '❌ FALTANDO em .env ou config/services.php';

    $results['stripe_secret'] = config('services.stripe.secret')
        ? '✅ ' . substr(config('services.stripe.secret'), 0, 10) . '...'
        : '❌ FALTANDO em .env ou config/services.php';

    // 3. Verificar se config/services.php tem array stripe
    $results['config_services'] = config('services.stripe')
        ? '✅ Configurado'
        : '❌ Adicione array "stripe" em config/services.php';

    // 4. Testar conexão com Stripe API
    if (class_exists('\Stripe\Stripe') && config('services.stripe.secret')) {
        try {
            \Stripe\Stripe::setApiKey(config('services.stripe.secret'));
            $balance = \Stripe\Balance::retrieve();
            $results['stripe_connection'] = '✅ Conectado à Stripe API';
        } catch (\Exception $e) {
            $results['stripe_connection'] = '❌ Erro: ' . $e->getMessage();
        }
    } else {
        $results['stripe_connection'] = '⏭️ Pulado (SDK ou secret não disponível)';
    }

    // 5. Verificar Model Service
    $results['service_model'] = class_exists('App\Models\Service')
        ? '✅ Existe'
        : '❌ Model Service não encontrado';

    // 6. Verificar se existe pelo menos um serviço
    if (class_exists('App\Models\Service')) {
        try {
            $count = \App\Models\Service::count();
            $results['services_count'] = $count > 0
                ? "✅ {$count} serviço(s) na base de dados"
                : '⚠️ Nenhum serviço encontrado na BD';
        } catch (\Exception $e) {
            $results['services_count'] = '❌ Erro ao contar serviços: ' . $e->getMessage();
        }
    }

    // 7. Verificar Controller
    $results['controller'] = class_exists('App\Http\Controllers\BookingPaymentController')
        ? '✅ BookingPaymentController existe'
        : '❌ BookingPaymentController NÃO ENCONTRADO';

    // 8. Verificar route
    $results['route'] = \Route::has('book.payment.intent')
        ? '✅ Route "book.payment.intent" registada'
        : '❌ Route NÃO registada - adicione ao routes/web.php';

    // HTML Output
    $html = '<html><head><title>Stripe Setup Test</title><style>
        body { font-family: monospace; padding: 2rem; background: #f5f5f5; }
        .result { padding: 1rem; margin: 0.5rem 0; background: white; border-left: 4px solid #ccc; }
        .result.success { border-color: #22c55e; }
        .result.error { border-color: #ef4444; }
        .result.warning { border-color: #f59e0b; }
        h1 { color: #333; }
        code { background: #e5e5e5; padding: 0.2rem 0.5rem; border-radius: 3px; }
    </style></head><body>';

    $html .= '<h1>🔍 Stripe Payment Setup Test</h1>';
    $html .= '<p>Teste de configuração do sistema de pagamentos. Resolva os erros ❌ antes de continuar.</p>';

    foreach ($results as $key => $value) {
        $class = 'result';
        if (str_starts_with($value, '✅')) $class .= ' success';
        elseif (str_starts_with($value, '❌')) $class .= ' error';
        elseif (str_starts_with($value, '⚠️')) $class .= ' warning';

        $html .= "<div class='$class'><strong>" . ucfirst(str_replace('_', ' ', $key)) . ":</strong> $value</div>";
    }

    $html .= '<hr><h2>📋 Checklist Rápido</h2>';
    $html .= '<ol>';
    $html .= '<li>Stripe SDK instalado? <code>composer require stripe/stripe-php</code></li>';
    $html .= '<li>.env tem STRIPE_KEY e STRIPE_SECRET?</li>';
    $html .= '<li>config/services.php tem array "stripe"?</li>';
    $html .= '<li><code>php artisan config:clear</code> executado?</li>';
    $html .= '<li>BookingPaymentController.php existe em app/Http/Controllers/?</li>';
    $html .= '<li>Route registada em routes/web.php?</li>';
    $html .= '</ol>';

    $html .= '<hr><h2>🧪 Próximo Teste</h2>';
    $html .= '<p>Se todos os checks acima estão ✅, testa criar um PaymentIntent:</p>';
    $html .= '<p><a href="/test-create-payment-intent">→ Testar criar PaymentIntent</a></p>';

    $html .= '</body></html>';

    return response($html);
});

// Teste 2: Tentar criar um PaymentIntent
Route::get('/test-create-payment-intent', function() {
    if (!class_exists('\Stripe\Stripe')) {
        return response()->json(['error' => 'Stripe SDK não instalado']);
    }

    if (!config('services.stripe.secret')) {
        return response()->json(['error' => 'STRIPE_SECRET não configurado']);
    }

    try {
        \Stripe\Stripe::setApiKey(config('services.stripe.secret'));

        $paymentIntent = \Stripe\PaymentIntent::create([
            'amount' => 1000, // 10.00 EUR
            'currency' => 'eur',
            'payment_method_types' => ['card', 'multibanco'],
            'metadata' => ['test' => 'true'],
        ]);

        return response()->json([
            'success' => true,
            'message' => '✅ PaymentIntent criado com sucesso!',
            'payment_intent_id' => $paymentIntent->id,
            'client_secret' => substr($paymentIntent->client_secret, 0, 20) . '...',
            'amount' => $paymentIntent->amount,
            'currency' => $paymentIntent->currency,
            'payment_method_types' => $paymentIntent->payment_method_types,
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'error' => $e->getMessage(),
            'type' => get_class($e),
        ], 500);
    }
});

// Teste 3: Simular o request real do wizard
Route::post('/test-booking-payment-intent', function(\Illuminate\Http\Request $request) {
    if (!class_exists('\Stripe\Stripe')) {
        return response()->json(['error' => 'Stripe SDK não instalado'], 500);
    }

    if (!config('services.stripe.secret')) {
        return response()->json(['error' => 'STRIPE_SECRET não configurado'], 500);
    }

    try {
        // Simular validação
        $serviceId = $request->input('service_id', 1);

        // Tentar buscar service
        if (!class_exists('App\Models\Service')) {
            return response()->json(['error' => 'Model Service não existe'], 500);
        }

        $service = \App\Models\Service::find($serviceId);
        if (!$service) {
            return response()->json(['error' => "Service ID {$serviceId} não encontrado"], 404);
        }

        $amount = (int) ($service->price * 100);

        \Stripe\Stripe::setApiKey(config('services.stripe.secret'));

        $paymentIntent = \Stripe\PaymentIntent::create([
            'amount' => $amount,
            'currency' => 'eur',
            'payment_method_types' => ['card', 'multibanco'],
            'metadata' => [
                'service_id' => $serviceId,
                'test' => 'true',
            ],
        ]);

        return response()->json([
            'success' => true,
            'client_secret' => $paymentIntent->client_secret,
            'payment_intent_id' => $paymentIntent->id,
        ]);

    } catch (\Exception $e) {
        \Log::error('Test Payment Intent Error', [
            'message' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
        ]);

        return response()->json([
            'error' => $e->getMessage(),
            'type' => get_class($e),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
        ], 500);
    }
});


require __DIR__ . '/auth.php';
