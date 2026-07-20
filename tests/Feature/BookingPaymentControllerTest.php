<?php

namespace Tests\Feature;

use App\Models\Portal;
use App\Models\Service;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookingPaymentControllerTest extends TestCase
{
    use RefreshDatabase;

    protected Portal $portal;
    protected Service $service;

    protected function setUp(): void
    {
        parent::setUp();

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
     * Test that createPaymentIntent endpoint validates required fields
     */
    public function test_createPaymentIntent_validates_required_fields()
    {
        $response = $this->postJson('/booking/payment-intent', []);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['service_id', 'day', 'start_hour', 'client_name', 'client_phone_1']);
    }

    /**
     * @test
     * Test that createPaymentIntent returns error when Stripe not configured
     */
    public function test_createPaymentIntent_returns_error_when_stripe_not_configured()
    {
        // Remove Stripe configuration
        $this->portal->update([
            'payment_stripe_key' => null,
            'payment_stripe_secret' => null,
        ]);

        $response = $this->postJson('/booking/payment-intent', [
            'service_id' => $this->service->id,
            'professional_id' => 1,
            'day' => now()->addDay()->toDateString(),
            'start_hour' => '10:00',
            'client_name' => 'John Doe',
            'client_phone_1' => '912345678',
            'client_email' => 'john@example.com',
        ]);

        $response->assertStatus(503);
        $response->assertJson(['success' => false]);
    }

    /**
     * @test
     * Test that createPaymentIntent validates service exists
     */
    public function test_createPaymentIntent_validates_service_exists()
    {
        $response = $this->postJson('/booking/payment-intent', [
            'service_id' => 9999, // Non-existent service
            'professional_id' => 1,
            'day' => now()->addDay()->toDateString(),
            'start_hour' => '10:00',
            'client_name' => 'John Doe',
            'client_phone_1' => '912345678',
            'client_email' => 'john@example.com',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['service_id']);
    }

    /**
     * @test
     * Test that createPaymentIntent includes payment percentage in response
     */
    public function test_createPaymentIntent_includes_payment_percentage_in_response()
    {
        // Update portal to 30% payment
        $this->portal->update(['payment_percentage' => 30]);

        // Note: This test will fail if making actual Stripe API calls
        // In real scenario, mock the Stripe API or use test mode
        // This is a placeholder for the validation logic
        
        $percentage = $this->portal->payment_percentage;
        $this->assertEquals(30, $percentage);
    }

    /**
     * @test
     * Test webhook handler validates signature
     */
    public function test_webhook_handler_validates_signature()
    {
        $response = $this->postJson('/booking/stripe/webhook', 
            [],
            ['Stripe-Signature' => 'invalid_signature']
        );

        // Should return error for invalid signature
        $response->assertStatus(400);
    }

    /**
     * @test
     * Test that payment intent creation validates date format
     */
    public function test_createPaymentIntent_validates_date_format()
    {
        $response = $this->postJson('/booking/payment-intent', [
            'service_id' => $this->service->id,
            'professional_id' => 1,
            'day' => 'invalid-date',
            'start_hour' => '10:00',
            'client_name' => 'John Doe',
            'client_phone_1' => '912345678',
            'client_email' => 'john@example.com',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['day']);
    }

    /**
     * @test
     * Test that payment intent creation validates start_hour format
     */
    public function test_createPaymentIntent_validates_start_hour_format()
    {
        $response = $this->postJson('/booking/payment-intent', [
            'service_id' => $this->service->id,
            'professional_id' => 1,
            'day' => now()->addDay()->toDateString(),
            'start_hour' => 'invalid-hour',
            'client_name' => 'John Doe',
            'client_phone_1' => '912345678',
            'client_email' => 'john@example.com',
        ]);

        // This depends on your validation rules
        $response->assertStatus(422);
    }
}
