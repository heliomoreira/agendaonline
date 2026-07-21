<?php

namespace App\Services;

use App\Models\Portal;
use Illuminate\Support\Facades\Log;
use Stripe\Stripe;
use Stripe\PaymentIntent;
use Stripe\Webhook;
use Stripe\Exception\SignatureVerificationException;
use UnexpectedValueException;

class StripeService
{
    /**
     * Get Stripe configuration for current tenant
     * Falls back to global config if tenant-specific is not set
     */
    public function getConfig(): array
    {
        $portal = $this->getPortal();

        if (!$portal || !$this->isConfigured($portal)) {
            // Fallback to global config from .env
            return [
                'key' => config('services.stripe.key'),
                'secret' => config('services.stripe.secret'),
                'allow_card' => config('services.stripe.allow_card', true),
                'allow_multibanco' => config('services.stripe.allow_multibanco', true),
            ];
        }

        return [
            'key' => $portal->payment_stripe_key,
            'secret' => decrypt($portal->payment_stripe_secret ?? ''),
            'allow_card' => (bool) $portal->payment_stripe_allow_card,
            'allow_multibanco' => (bool) $portal->payment_stripe_allow_multibanco,
        ];
    }

    /**
     * Check if Stripe is properly configured for current tenant
     */
    public function isConfigured(Portal $portal = null): bool
    {
        $portal = $portal ?? $this->getPortal();

        if (!$portal) {
            return (bool) config('services.stripe.secret');
        }

        return !empty($portal->payment_stripe_key) && !empty($portal->payment_stripe_secret);
    }

    /**
     * Get payment percentage for current tenant
     */
    public function getPaymentPercentage(): int
    {
        $portal = $this->getPortal();
        return $portal?->payment_percentage ?? 100;
    }

    /**
     * Create a Payment Intent for the tenant
     */
    public function createPaymentIntent(
        float $amount,
        string $currency = 'eur',
        array $metadata = [],
        string $description = '',
        string $receiptEmail = ''
    ): array {
        $config = $this->getConfig();

        if (empty($config['secret'])) {
            Log::error('Stripe not configured for tenant', [
                'tenant' => tenant()?->id,
                'domain' => tenant()?->domains->first()?->domain,
            ]);
            throw new \Exception('Stripe payment processing is not configured for this tenant.');
        }

        try {
            Stripe::setApiKey($config['secret']);

            // Determine payment methods based on configuration
            $paymentMethods = ['card'];
            if ($config['allow_multibanco']) {
                $paymentMethods[] = 'multibanco';
            }

            // Create the payment intent
            $paymentIntent = PaymentIntent::create([
                'amount' => (int) ($amount * 100), // Convert to cents
                'currency' => $currency,
                'payment_method_types' => $paymentMethods,
                'metadata' => array_merge($metadata, [
                    'tenant_id' => tenant()?->id,
                    'tenant_domain' => tenant()?->domains->first()?->domain,
                ]),
                'description' => $description,
                'receipt_email' => $receiptEmail,
            ]);

            Log::info('Payment Intent created', [
                'payment_intent_id' => $paymentIntent->id,
                'tenant_id' => tenant()?->id,
                'amount' => $amount,
            ]);

            return [
                'client_secret' => $paymentIntent->client_secret,
                'payment_intent_id' => $paymentIntent->id,
                'amount' => $amount,
                'payment_methods' => $paymentMethods,
            ];
        } catch (\Exception $e) {
            Log::error('Stripe Payment Intent creation failed', [
                'tenant_id' => tenant()?->id,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Retrieve a Payment Intent
     */
    public function retrievePaymentIntent(string $paymentIntentId): ?PaymentIntent
    {
        $config = $this->getConfig();

        if (empty($config['secret'])) {
            return null;
        }

        try {
            Stripe::setApiKey($config['secret']);
            return PaymentIntent::retrieve($paymentIntentId);
        } catch (\Exception $e) {
            Log::error('Failed to retrieve Payment Intent', [
                'payment_intent_id' => $paymentIntentId,
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * Verify webhook signature
     * Works with tenant-specific webhook secrets if configured
     */
    public function verifyWebhook(string $payload, string $signature, Portal $portal = null)
    {
        $portal = $portal ?? $this->getPortal();
        $secret = $this->getWebhookSecret($portal);

        if (empty($secret)) {
            Log::error('Webhook secret not configured', [
                'tenant_id' => tenant()?->id,
            ]);
            throw new \Exception('Webhook secret not configured for this tenant.');
        }

        try {
            return Webhook::constructEvent($payload, $signature, $secret);
        } catch (UnexpectedValueException $e) {
            Log::error('Webhook payload invalid', ['error' => $e->getMessage()]);
            throw new \Exception('Invalid webhook payload.');
        } catch (SignatureVerificationException $e) {
            Log::error('Webhook signature invalid', ['error' => $e->getMessage()]);
            throw new \Exception('Invalid webhook signature.');
        }
    }

    /**
     * Get webhook secret for tenant
     * Falls back to global config if not set
     */
    protected function getWebhookSecret(Portal $portal = null): string
    {
        $portal = $portal ?? $this->getPortal();

        // Check if tenant has custom webhook secret (future enhancement)
        if ($portal && !empty($portal->payment_stripe_webhook_secret ?? null)) {
            return decrypt($portal->payment_stripe_webhook_secret);
        }

        // Fall back to global webhook secret
        return config('services.stripe.webhook_secret', '');
    }

    /**
     * Get current tenant's Portal model
     */
    protected function getPortal(): ?Portal
    {
        try {
            return Portal::first();
        } catch (\Exception $e) {
            Log::warning('Failed to retrieve Portal', ['error' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Get Stripe frontend key for current tenant
     */
    public function getFrontendKey(): string
    {
        $config = $this->getConfig();
        return $config['key'] ?? '';
    }

    /**
     * Check if specific payment method is enabled
     */
    public function isPaymentMethodEnabled(string $method): bool
    {
        $config = $this->getConfig();

        return match ($method) {
            'card' => $config['allow_card'] ?? true,
            'multibanco' => $config['allow_multibanco'] ?? true,
            default => false,
        };
    }
}
