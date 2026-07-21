<?php

namespace Tests\Feature;

use App\Http\Controllers\BookingPaymentController;
use App\Services\BookingService;
use App\Services\StripeService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Mockery;
use Tests\TestCase;

class BookingPaymentControllerTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();

        parent::tearDown();
    }

    public function test_create_payment_intent_returns_503_when_stripe_is_not_configured(): void
    {
        $stripe = Mockery::mock(StripeService::class);
        $stripe->shouldReceive('isConfigured')->once()->andReturn(false);

        $booking = Mockery::mock(BookingService::class);

        $controller = new BookingPaymentController($stripe, $booking);
        $request = $this->mockValidatedPaymentRequest();

        $response = $controller->createPaymentIntent($request);

        $this->assertSame(503, $response->getStatusCode());
    }

    public function test_create_payment_intent_returns_500_when_service_is_missing(): void
    {
        $serviceAlias = Mockery::mock('alias:App\\Models\\Service');
        $serviceAlias->shouldReceive('findOrFail')->once()->with(10)->andThrow(new ModelNotFoundException());

        $stripe = Mockery::mock(StripeService::class);
        $stripe->shouldReceive('isConfigured')->once()->andReturn(true);
        $stripe->shouldReceive('getPaymentPercentage')->never();

        $booking = Mockery::mock(BookingService::class);

        $controller = new BookingPaymentController($stripe, $booking);
        $request = $this->mockValidatedPaymentRequest();

        $response = $controller->createPaymentIntent($request);

        $this->assertSame(500, $response->getStatusCode());
    }

    public function test_create_payment_intent_includes_tenant_percentage_in_response(): void
    {
        $serviceAlias = Mockery::mock('alias:App\\Models\\Service');
        $serviceAlias->shouldReceive('findOrFail')->once()->with(10)->andReturn((object) [
            'price' => 120,
            'name' => 'Consulta',
        ]);

        $stripe = Mockery::mock(StripeService::class);
        $stripe->shouldReceive('isConfigured')->once()->andReturn(true);
        $stripe->shouldReceive('getPaymentPercentage')->once()->andReturn(25);
        $stripe->shouldReceive('createPaymentIntent')->once()->andReturn([
            'client_secret' => 'secret_123',
            'payment_intent_id' => 'pi_123',
            'payment_methods' => ['card', 'multibanco'],
        ]);

        $booking = Mockery::mock(BookingService::class);

        $controller = new BookingPaymentController($stripe, $booking);
        $request = $this->mockValidatedPaymentRequest();

        $response = $controller->createPaymentIntent($request);
        $payload = $response->getData(true);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame(25, $payload['percentage']);
        $this->assertSame(30.0, $payload['amount']);
    }

    public function test_create_payment_intent_returns_500_when_stripe_creation_fails(): void
    {
        $serviceAlias = Mockery::mock('alias:App\\Models\\Service');
        $serviceAlias->shouldReceive('findOrFail')->once()->with(10)->andReturn((object) [
            'price' => 100,
            'name' => 'Consulta',
        ]);

        $stripe = Mockery::mock(StripeService::class);
        $stripe->shouldReceive('isConfigured')->once()->andReturn(true);
        $stripe->shouldReceive('getPaymentPercentage')->once()->andReturn(50);
        $stripe->shouldReceive('createPaymentIntent')->once()->andThrow(new \Exception('Stripe error'));

        $booking = Mockery::mock(BookingService::class);

        $controller = new BookingPaymentController($stripe, $booking);
        $request = $this->mockValidatedPaymentRequest();

        $response = $controller->createPaymentIntent($request);

        $this->assertSame(500, $response->getStatusCode());
    }

    public function test_handle_webhook_returns_400_for_invalid_signature(): void
    {
        $stripe = Mockery::mock(StripeService::class);
        $stripe->shouldReceive('verifyWebhook')->once()->andThrow(new \Exception('Invalid webhook signature.'));

        $booking = Mockery::mock(BookingService::class);

        $controller = new BookingPaymentController($stripe, $booking);
        $request = Request::create('/booking/stripe/webhook', 'POST', [], [], [], [], '{}');

        $response = $controller->handleWebhook($request);

        $this->assertSame(400, $response->getStatusCode());
    }

    public function test_handle_webhook_delegates_success_event_to_booking_service(): void
    {
        $stripe = Mockery::mock(StripeService::class);
        $stripe->shouldReceive('verifyWebhook')->once()->andReturn($this->makeStripeEvent('payment_intent.succeeded', 'pi_success'));

        $booking = Mockery::mock(BookingService::class);
        $booking->shouldReceive('handlePaymentSuccess')->once()->with('pi_success');

        $controller = new BookingPaymentController($stripe, $booking);
        $request = Request::create('/booking/stripe/webhook', 'POST', [], [], [], [], '{}');

        $response = $controller->handleWebhook($request);

        $this->assertSame(200, $response->getStatusCode());
    }

    public function test_handle_webhook_delegates_payment_failed_event_to_booking_service(): void
    {
        $stripe = Mockery::mock(StripeService::class);
        $stripe->shouldReceive('verifyWebhook')->once()->andReturn($this->makeStripeEvent('payment_intent.payment_failed', 'pi_failed', 'card_declined'));

        $booking = Mockery::mock(BookingService::class);
        $booking->shouldReceive('handlePaymentFailed')->once()->with('pi_failed', 'card_declined');

        $controller = new BookingPaymentController($stripe, $booking);
        $request = Request::create('/booking/stripe/webhook', 'POST', [], [], [], [], '{}');

        $response = $controller->handleWebhook($request);

        $this->assertSame(200, $response->getStatusCode());
    }

    private function mockValidatedPaymentRequest(): Request
    {
        $request = Request::create('/booking/payment-intent', 'POST', [
            'service_id' => 10,
            'professional_id' => 5,
            'day' => '2026-08-01',
            'start_hour' => '10:00',
            'client_name' => 'Client Name',
            'client_phone_1' => '912345678',
            'client_email' => 'client@example.com',
        ]);

        $mock = Mockery::mock($request)->makePartial();
        $mock->shouldReceive('validate')->once()->andReturn($request->all());

        return $mock;
    }

    private function makeStripeEvent(string $type, string $paymentIntentId, string $failureMessage = ''): object
    {
        return (object) [
            'type' => $type,
            'data' => (object) [
                'object' => (object) [
                    'id' => $paymentIntentId,
                    'last_payment_error' => (object) ['message' => $failureMessage],
                ],
            ],
        ];
    }
}
