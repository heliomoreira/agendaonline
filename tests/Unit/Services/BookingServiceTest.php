<?php

namespace Tests\Unit\Services;

use App\Services\BookingService;
use App\Services\StripeService;
use Mockery;
use Tests\TestCase;

class BookingServiceTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();

        parent::tearDown();
    }

    public function test_it_calculates_payment_amount_using_tenant_percentage(): void
    {
        $serviceModel = new \App\Models\Service([
            'id' => 10,
            'name' => 'Consulta',
            'price' => 100,
        ]);

        $serviceAlias = Mockery::mock('alias:App\\Models\\Service');
        $serviceAlias->shouldReceive('findOrFail')->once()->with(10)->andReturn($serviceModel);

        $stripe = Mockery::mock(StripeService::class);
        $stripe->shouldReceive('getPaymentPercentage')->once()->andReturn(30);
        $stripe->shouldReceive('createPaymentIntent')
            ->once()
            ->withArgs(function (float $amount, string $currency, array $metadata) {
                return $amount === 30.0
                    && $currency === 'eur'
                    && $metadata['payment_percentage'] === 30;
            })
            ->andReturn([
                'client_secret' => 'secret_123',
                'payment_intent_id' => 'pi_123',
                'amount' => 30.0,
                'payment_methods' => ['card'],
            ]);

        $bookingService = new BookingService($stripe);

        $result = $bookingService->createBookingWithPayment(
            10,
            5,
            '2026-08-01',
            '10:00',
            'Client Name',
            '912345678',
            'client@example.com'
        );

        $this->assertSame(30.0, $result['amount']);
        $this->assertSame(100.0, $result['full_amount']);
        $this->assertSame(30, $result['percentage']);
    }

    public function test_it_confirms_booking_using_update_or_create_to_prevent_duplicates(): void
    {
        $agendaAlias = Mockery::mock('alias:App\\Models\\Agenda');
        $agendaAlias->shouldReceive('updateOrCreate')
            ->once()
            ->andReturn(new \App\Models\Agenda(['id' => 1]));

        $stripe = Mockery::mock(StripeService::class);
        $bookingService = new BookingService($stripe);

        $booking = $bookingService->confirmBooking(
            10,
            5,
            '2026-08-01',
            '10:00',
            'Client Name',
            '912345678',
            null,
            'pi_123',
            'card'
        );

        $this->assertInstanceOf(\App\Models\Agenda::class, $booking);
    }

    public function test_it_handles_payment_success_via_confirmation_workflow(): void
    {
        $stripe = Mockery::mock(StripeService::class);
        $stripe->shouldReceive('retrievePaymentIntent')->once()->with('pi_success')->andReturn((object) [
            'metadata' => new class {
                public function toArray(): array
                {
                    return [
                        'service_id' => 10,
                        'professional_id' => 5,
                        'day' => '2026-08-01',
                        'start_hour' => '10:00',
                        'client_name' => 'Client Name',
                        'client_phone' => '912345678',
                        'client_email' => 'client@example.com',
                    ];
                }
            },
            'payment_method_types' => ['card'],
        ]);

        $bookingService = Mockery::mock(BookingService::class, [$stripe])->makePartial();
        $bookingService->shouldReceive('confirmBooking')->once()->andReturn(new \App\Models\Agenda(['id' => 1]));

        $booking = $bookingService->handlePaymentSuccess('pi_success');

        $this->assertInstanceOf(\App\Models\Agenda::class, $booking);
    }

    public function test_it_throws_when_payment_success_handler_cannot_find_payment_intent(): void
    {
        $stripe = Mockery::mock(StripeService::class);
        $stripe->shouldReceive('retrievePaymentIntent')->once()->with('pi_missing')->andReturn(null);

        $bookingService = new BookingService($stripe);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Payment intent not found: pi_missing');

        $bookingService->handlePaymentSuccess('pi_missing');
    }

    public function test_it_marks_booking_as_payment_failed(): void
    {
        $booking = Mockery::mock();
        $booking->shouldReceive('update')->once()->with([
            'status' => 'payment_failed',
            'paid' => false,
        ]);

        $query = Mockery::mock();
        $query->shouldReceive('first')->once()->andReturn($booking);

        $agendaAlias = Mockery::mock('alias:App\\Models\\Agenda');
        $agendaAlias->shouldReceive('where')->once()->with('payment_intent_id', 'pi_fail')->andReturn($query);

        $stripe = Mockery::mock(StripeService::class);
        $bookingService = new BookingService($stripe);

        $bookingService->handlePaymentFailed('pi_fail', 'card_declined');

        $this->assertTrue(true);
    }

    public function test_it_marks_booking_as_canceled_when_payment_is_canceled(): void
    {
        $booking = Mockery::mock();
        $booking->shouldReceive('update')->once()->with([
            'status' => 'cancelled',
            'paid' => false,
        ]);

        $query = Mockery::mock();
        $query->shouldReceive('first')->once()->andReturn($booking);

        $agendaAlias = Mockery::mock('alias:App\\Models\\Agenda');
        $agendaAlias->shouldReceive('where')->once()->with('payment_intent_id', 'pi_cancel')->andReturn($query);

        $stripe = Mockery::mock(StripeService::class);
        $bookingService = new BookingService($stripe);

        $bookingService->handlePaymentCanceled('pi_cancel');

        $this->assertTrue(true);
    }
}
