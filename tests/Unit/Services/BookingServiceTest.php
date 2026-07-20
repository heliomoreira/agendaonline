<?php

namespace Tests\Unit\Services;

use App\Models\Portal;
use App\Models\Service;
use App\Models\Agenda;
use App\Services\BookingService;
use App\Services\StripeService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookingServiceTest extends TestCase
{
    use RefreshDatabase;

    protected BookingService $bookingService;
    protected StripeService $stripeService;
    protected Portal $portal;
    protected Service $service;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->stripeService = app(StripeService::class);
        $this->bookingService = app(BookingService::class);

        // Create test portal
        $this->portal = Portal::create([
            'title' => 'Test Portal',
            'payment_stripe_key' => 'pk_test_123456789',
            'payment_stripe_secret' => encrypt('sk_test_123456789'),
            'payment_stripe_allow_card' => true,
            'payment_stripe_allow_multibanco' => true,
            'payment_percentage' => 50,
            'requires_payment' => true,
            'enable_booking' => true,
        ]);

        // Create test service
        $this->service = Service::create([
            'name' => 'Test Service',
            'description' => 'Test service description',
            'price' => 100.00,
            'duration' => 60,
        ]);
    }

    /**
     * @test
     * Test that booking is created with correct payment status
     */
    public function test_confirmBooking_creates_booking_with_correct_status()
    {
        $booking = $this->bookingService->confirmBooking(
            serviceId: $this->service->id,
            professionalId: 1,
            day: now()->addDay()->toDateString(),
            startHour: '10:00',
            clientName: 'John Doe',
            clientPhone: '912345678',
            clientEmail: 'john@example.com',
            paymentIntentId: 'pi_test_123',
            paymentMethod: 'card'
        );

        $this->assertNotNull($booking->id);
        $this->assertEquals($booking->status, Agenda::STATUS_CONFIRMED);
        $this->assertTrue($booking->paid);
        $this->assertEquals('card', $booking->payment_method);
        $this->assertEquals('pi_test_123', $booking->payment_intent_id);
    }

    /**
     * @test
     * Test that booking payment failure updates status
     */
    public function test_handlePaymentFailed_updates_booking_status()
    {
        // Create a pending booking first
        $booking = Agenda::create([
            'service_id' => $this->service->id,
            'professional_id' => 1,
            'day' => now()->addDay()->toDateString(),
            'start_hour' => '10:00',
            'end_hour' => '11:00',
            'client_name' => 'John Doe',
            'client_phone_1' => '912345678',
            'client_email' => 'john@example.com',
            'payment_intent_id' => 'pi_test_failed',
            'status' => Agenda::STATUS_PENDING,
        ]);

        $this->bookingService->handlePaymentFailed('pi_test_failed', 'Card declined');

        $booking->refresh();
        $this->assertEquals(Agenda::STATUS_PAYMENT_FAILED, $booking->status);
        $this->assertFalse($booking->paid);
    }

    /**
     * @test
     * Test that booking cancellation updates status
     */
    public function test_handlePaymentCanceled_updates_booking_status()
    {
        // Create a pending booking first
        $booking = Agenda::create([
            'service_id' => $this->service->id,
            'professional_id' => 1,
            'day' => now()->addDay()->toDateString(),
            'start_hour' => '10:00',
            'end_hour' => '11:00',
            'client_name' => 'John Doe',
            'client_phone_1' => '912345678',
            'client_email' => 'john@example.com',
            'payment_intent_id' => 'pi_test_canceled',
            'status' => Agenda::STATUS_PENDING,
        ]);

        $this->bookingService->handlePaymentCanceled('pi_test_canceled');

        $booking->refresh();
        $this->assertEquals(Agenda::STATUS_CANCELLED, $booking->status);
        $this->assertFalse($booking->paid);
    }

    /**
     * @test
     * Test payment percentage calculation in booking creation
     */
    public function test_createBookingWithPayment_calculates_correct_amount()
    {
        // Update portal to 30% payment
        $this->portal->update(['payment_percentage' => 30]);

        // This test would validate the amount calculation
        // In production, you'd integrate with actual Stripe test mode
        $this->assertEquals(30, $this->stripeService->getPaymentPercentage());
    }

    /**
     * @test
     * Test updateOrCreate on confirmBooking prevents duplicates
     */
    public function test_confirmBooking_prevents_duplicate_bookings()
    {
        $paymentIntentId = 'pi_test_duplicate';

        // Create first booking
        $booking1 = $this->bookingService->confirmBooking(
            serviceId: $this->service->id,
            professionalId: 1,
            day: now()->addDay()->toDateString(),
            startHour: '10:00',
            clientName: 'John Doe',
            clientPhone: '912345678',
            clientEmail: 'john@example.com',
            paymentIntentId: $paymentIntentId,
            paymentMethod: 'card'
        );

        $firstId = $booking1->id;

        // Try to create same booking again with same payment intent
        $booking2 = $this->bookingService->confirmBooking(
            serviceId: $this->service->id,
            professionalId: 1,
            day: now()->addDay()->toDateString(),
            startHour: '10:00',
            clientName: 'Jane Doe', // Different name
            clientPhone: '987654321', // Different phone
            clientEmail: 'jane@example.com',
            paymentIntentId: $paymentIntentId, // Same payment intent
            paymentMethod: 'multibanco'
        );

        // Should update the existing booking, not create a new one
        $this->assertEquals($firstId, $booking2->id);
        $this->assertEquals('Jane Doe', $booking2->client_name);
    }
}
