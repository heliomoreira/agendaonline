<?php

namespace Tests\Unit\Services;

use App\Models\Portal;
use App\Services\StripeService;
use Tests\TestCase;

class StripeServiceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config()->set('services.stripe.key', 'pk_global_123');
        config()->set('services.stripe.secret', 'sk_global_123');
        config()->set('services.stripe.allow_card', true);
        config()->set('services.stripe.allow_multibanco', true);
        config()->set('services.stripe.webhook_secret', 'whsec_global_123');
    }

    public function test_it_gets_tenant_specific_stripe_configuration(): void
    {
        $portal = new Portal([
            'payment_stripe_key' => 'pk_test_tenant',
            'payment_stripe_secret' => encrypt('sk_test_tenant'),
            'payment_stripe_allow_card' => true,
            'payment_stripe_allow_multibanco' => false,
        ]);

        $service = new TestableStripeService($portal);
        $config = $service->getConfig();

        $this->assertSame('pk_test_tenant', $config['key']);
        $this->assertSame('sk_test_tenant', $config['secret']);
        $this->assertTrue($config['allow_card']);
        $this->assertFalse($config['allow_multibanco']);
    }

    public function test_it_falls_back_to_global_configuration_when_portal_is_missing(): void
    {
        $service = new TestableStripeService(null);
        $config = $service->getConfig();

        $this->assertSame('pk_global_123', $config['key']);
        $this->assertSame('sk_global_123', $config['secret']);
    }

    public function test_it_falls_back_to_global_configuration_when_tenant_config_is_incomplete(): void
    {
        $portal = new Portal([
            'payment_stripe_key' => 'pk_test_tenant',
            'payment_stripe_secret' => null,
        ]);

        $service = new TestableStripeService($portal);
        $config = $service->getConfig();

        $this->assertSame('pk_global_123', $config['key']);
        $this->assertSame('sk_global_123', $config['secret']);
    }

    public function test_it_gets_payment_percentage_from_tenant_portal(): void
    {
        $portal = new Portal(['payment_percentage' => 35]);

        $service = new TestableStripeService($portal);

        $this->assertSame(35, $service->getPaymentPercentage());
    }

    public function test_it_uses_full_payment_percentage_when_portal_does_not_define_it(): void
    {
        $service = new TestableStripeService(null);

        $this->assertSame(100, $service->getPaymentPercentage());
    }

    public function test_it_validates_enabled_payment_method_from_tenant_configuration(): void
    {
        $portal = new Portal([
            'payment_stripe_key' => 'pk_test_tenant',
            'payment_stripe_secret' => encrypt('sk_test_tenant'),
            'payment_stripe_allow_card' => true,
            'payment_stripe_allow_multibanco' => false,
        ]);

        $service = new TestableStripeService($portal);

        $this->assertTrue($service->isPaymentMethodEnabled('card'));
        $this->assertFalse($service->isPaymentMethodEnabled('multibanco'));
    }

    public function test_it_returns_false_for_unknown_payment_methods(): void
    {
        $service = new TestableStripeService(null);

        $this->assertFalse($service->isPaymentMethodEnabled('mbway'));
    }

    public function test_it_uses_tenant_webhook_secret_when_available(): void
    {
        $portal = new Portal([
            'payment_stripe_webhook_secret' => encrypt('whsec_tenant_123'),
        ]);

        $service = new TestableStripeService($portal);

        $this->assertSame('whsec_tenant_123', $service->exposeWebhookSecret());
    }

    public function test_it_falls_back_to_global_webhook_secret_when_tenant_secret_is_missing(): void
    {
        $service = new TestableStripeService(new Portal());

        $this->assertSame('whsec_global_123', $service->exposeWebhookSecret());
    }
}

class TestableStripeService extends StripeService
{
    public function __construct(private ?Portal $portal)
    {
    }

    protected function getPortal(): ?Portal
    {
        return $this->portal;
    }

    public function exposeWebhookSecret(): string
    {
        return $this->getWebhookSecret();
    }
}
