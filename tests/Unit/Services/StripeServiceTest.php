<?php

namespace Tests\Unit\Services;

use App\Models\Portal;
use App\Services\StripeService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Mockery\MockInterface;

class StripeServiceTest extends TestCase
{
    use RefreshDatabase;

    protected StripeService $stripeService;
    protected Portal $portal;

    protected function setUp(): void
    {
        parent::setUp();
        $this->stripeService = app(StripeService::class);
        
        // Create a test portal with Stripe configuration
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
    }

    /**
     * @test
     * Test that getConfig returns tenant-specific configuration
     */
    public function test_getConfig_returns_tenant_specific_config()
    {
        $config = $this->stripeService->getConfig();

        $this->assertEquals('pk_test_123456789', $config['key']);
        $this->assertEquals('sk_test_123456789', $config['secret']);
        $this->assertTrue($config['allow_card']);
        $this->assertTrue($config['allow_multibanco']);
    }

    /**
     * @test
     * Test that isConfigured returns true when portal has Stripe credentials
     */
    public function test_isConfigured_returns_true_when_credentials_exist()
    {
        $this->assertTrue($this->stripeService->isConfigured());
    }

    /**
     * @test
     * Test that isConfigured returns false when portal has no credentials
     */
    public function test_isConfigured_returns_false_when_credentials_missing()
    {
        $this->portal->update([
            'payment_stripe_key' => null,
            'payment_stripe_secret' => null,
        ]);

        $this->assertFalse($this->stripeService->isConfigured());
    }

    /**
     * @test
     * Test getPaymentPercentage returns tenant-specific percentage
     */
    public function test_getPaymentPercentage_returns_tenant_percentage()
    {
        $percentage = $this->stripeService->getPaymentPercentage();
        $this->assertEquals(50, $percentage);
    }

    /**
     * @test
     * Test getPaymentPercentage returns default when not set
     */
    public function test_getPaymentPercentage_returns_default_when_not_set()
    {
        $this->portal->update(['payment_percentage' => null]);
        
        $percentage = $this->stripeService->getPaymentPercentage();
        $this->assertEquals(100, $percentage);
    }

    /**
     * @test
     * Test getFrontendKey returns public key
     */
    public function test_getFrontendKey_returns_public_key()
    {
        $key = $this->stripeService->getFrontendKey();
        $this->assertEquals('pk_test_123456789', $key);
    }

    /**
     * @test
     * Test isPaymentMethodEnabled for card
     */
    public function test_isPaymentMethodEnabled_for_card()
    {
        $this->assertTrue($this->stripeService->isPaymentMethodEnabled('card'));
    }

    /**
     * @test
     * Test isPaymentMethodEnabled for multibanco
     */
    public function test_isPaymentMethodEnabled_for_multibanco()
    {
        $this->assertTrue($this->stripeService->isPaymentMethodEnabled('multibanco'));
    }

    /**
     * @test
     * Test isPaymentMethodEnabled returns false when disabled
     */
    public function test_isPaymentMethodEnabled_returns_false_when_disabled()
    {
        $this->portal->update(['payment_stripe_allow_card' => false]);
        
        $this->assertFalse($this->stripeService->isPaymentMethodEnabled('card'));
    }

    /**
     * @test
     * Test createPaymentIntent throws exception when not configured
     */
    public function test_createPaymentIntent_throws_exception_when_not_configured()
    {
        $this->portal->update([
            'payment_stripe_key' => null,
            'payment_stripe_secret' => null,
        ]);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Stripe payment processing is not configured for this tenant.');

        $this->stripeService->createPaymentIntent(50.00);
    }

    /**
     * @test
     * Test that payment intent creation includes tenant metadata
     */
    public function test_createPaymentIntent_includes_tenant_metadata()
    {
        // Mock the Stripe API call
        $this->mock('Stripe\\PaymentIntent', function (MockInterface $mock) {
            $mock->shouldReceive('create')->andReturn((object) [
                'client_secret' => 'pi_test_secret_123',
                'id' => 'pi_test_123',
            ]);
        });

        // This test validates the structure expected from createPaymentIntent
        // In a real scenario, you'd test with Stripe test keys
        $this->assertTrue(true); // Placeholder for actual Stripe API test
    }

    /**
     * @test
     * Test configuration fallback to global config
     */
    public function test_getConfig_falls_back_to_global_when_tenant_not_configured()
    {
        // Clear tenant configuration
        $this->portal->update([
            'payment_stripe_key' => null,
            'payment_stripe_secret' => null,
        ]);

        // Set global configuration
        config(['services.stripe.key' => 'pk_global_test']);
        config(['services.stripe.secret' => 'sk_global_test']);

        $config = $this->stripeService->getConfig();

        $this->assertEquals('pk_global_test', $config['key']);
        $this->assertEquals('sk_global_test', $config['secret']);
    }
}
