<?php

namespace App\Services;

use App\Models\Portal;
use Illuminate\Support\Facades\Log;
use RuntimeException;
use Stripe\Event;
use Stripe\Exception\SignatureVerificationException;
use Stripe\PaymentIntent;
use Stripe\Stripe;
use Stripe\Webhook;

/**
 * Resolves Stripe credentials and behaviour from the current tenant's
 * Portal settings, falling back to the global .env config only when the
 * tenant hasn't configured its own Stripe account.
 */
class StripeService
{
    private ?Portal $portal = null;
    private bool $portalLoaded = false;

    private function portal(): ?Portal
    {
        if (!$this->portalLoaded) {
            $this->portal = Portal::first();
            $this->portalLoaded = true;
        }

        return $this->portal;
    }

    public function getPublishableKey(): ?string
    {
        return $this->portal()?->payment_stripe_key ?: config('services.stripe.key');
    }

    public function getSecretKey(): ?string
    {
        return $this->portal()?->payment_stripe_secret ?: config('services.stripe.secret');
    }

    public function getWebhookSecret(): ?string
    {
        return $this->portal()?->payment_stripe_webhook_secret ?: config('services.stripe.webhook_secret');
    }

    public function getCurrency(): string
    {
        return $this->portal()?->payment_currency ?: config('services.stripe.currency', 'eur');
    }

    public function getPaymentPercentage(): int
    {
        return (int) ($this->portal()?->payment_percentage ?: 100);
    }

    public function isConfigured(): bool
    {
        return filled($this->getPublishableKey()) && filled($this->getSecretKey());
    }

    /**
     * @return string[] Stripe payment_method_types allowed for this tenant.
     */
    public function getAllowedPaymentMethods(): array
    {
        $portal = $this->portal();
        $methods = [];

        if (!$portal || $portal->payment_stripe_allow_card) {
            $methods[] = 'card';
        }

        if ($portal?->payment_stripe_allow_multibanco) {
            $methods[] = 'multibanco';
            $methods[] = 'mb_way';
        }

        return $methods ?: ['card'];
    }

    public function createPaymentIntent(
        float $amount,
        ?string $currency = null,
        array $metadata = [],
        ?string $description = null,
        ?string $receiptEmail = null
    ): array {
        if (!$this->isConfigured()) {
            throw new RuntimeException('Stripe não está configurado para este portal.');
        }

        Stripe::setApiKey($this->getSecretKey());

        $paymentMethods = $this->getAllowedPaymentMethods();

        $paymentIntent = PaymentIntent::create(array_filter([
            'amount' => (int) round($amount * 100),
            'currency' => $currency ?: $this->getCurrency(),
            'payment_method_types' => $paymentMethods,
            'description' => $description,
            'receipt_email' => $receiptEmail ?: null,
            'metadata' => array_merge($metadata, [
                'tenant_id' => (string) tenant('id'),
                'tenant_domain' => (string) tenant()?->domains->first()?->domain,
            ]),
        ]));

        return [
            'client_secret' => $paymentIntent->client_secret,
            'payment_intent_id' => $paymentIntent->id,
            'payment_methods' => $paymentMethods,
        ];
    }

    public function retrievePaymentIntent(string $paymentIntentId): PaymentIntent
    {
        if (!$this->isConfigured()) {
            throw new RuntimeException('Stripe não está configurado para este portal.');
        }

        Stripe::setApiKey($this->getSecretKey());

        return PaymentIntent::retrieve($paymentIntentId);
    }

    public function verifyWebhook(string $payload, ?string $signature): Event
    {
        $webhookSecret = $this->getWebhookSecret();

        if (!$signature || !$webhookSecret) {
            throw new RuntimeException('Webhook do Stripe não está configurado para este portal.');
        }

        try {
            return Webhook::constructEvent($payload, $signature, $webhookSecret);
        } catch (SignatureVerificationException $e) {
            Log::warning('Stripe webhook signature verification failed', [
                'tenant_id' => tenant('id'),
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }
}
