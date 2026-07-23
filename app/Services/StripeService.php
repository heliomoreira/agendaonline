<?php

namespace App\Services;

use App\Models\Portal;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Log;
use RuntimeException;
use Stripe\Exception\ApiErrorException;
use Stripe\PaymentIntent;
use Stripe\Stripe;
use Stripe\Webhook;

class StripeService
{
    private ?Portal $portal;

    public function __construct()
    {
        $this->portal = Portal::first();
    }

    /**
     * Returns true only when both tenant key and secret are present and decryptable.
     * No fallback to env values.
     */
    public function isConfigured(): bool
    {
        if (empty($this->portal?->payment_stripe_key) || empty($this->portal?->payment_stripe_secret)) {
            return false;
        }

        try {
            $this->resolveSecret();
            return true;
        } catch (RuntimeException) {
            return false;
        }
    }

    /**
     * Retrieve a Stripe PaymentIntent using tenant-only credentials.
     *
     * @throws RuntimeException when tenant is not properly configured
     * @throws ApiErrorException when Stripe API call fails
     */
    public function retrievePaymentIntent(string $paymentIntentId): PaymentIntent
    {
        $this->configure();

        return PaymentIntent::retrieve($paymentIntentId);
    }

    /**
     * Create a Stripe PaymentIntent using tenant-only credentials.
     *
     * @throws RuntimeException when tenant is not properly configured
     * @throws ApiErrorException when Stripe API call fails
     */
    public function createPaymentIntent(
        float $amount,
        string $currency,
        array $metadata = [],
        string $description = '',
        ?string $receiptEmail = null
    ): array {
        $this->configure();

        $paymentMethods = $this->resolvePaymentMethods();

        $params = [
            'amount'               => (int) round($amount * 100),
            'currency'             => $currency,
            'payment_method_types' => $paymentMethods,
            'metadata'             => $metadata,
        ];

        if (!empty($description)) {
            $params['description'] = $description;
        }

        if (!empty($receiptEmail)) {
            $params['receipt_email'] = $receiptEmail;
        }

        $intent = PaymentIntent::create($params);

        return [
            'client_secret'     => $intent->client_secret,
            'payment_intent_id' => $intent->id,
            'payment_methods'   => $paymentMethods,
        ];
    }

    /**
     * Verify a Stripe webhook signature using tenant-only credentials.
     *
     * @throws RuntimeException when tenant is not properly configured
     * @throws \Stripe\Exception\SignatureVerificationException
     */
    public function verifyWebhook(string $payload, string $sigHeader): \Stripe\Event
    {
        $this->configure();

        $webhookSecret = $this->resolveSecret();

        return Webhook::constructEvent($payload, $sigHeader, $webhookSecret);
    }

    /**
     * Return tenant payment percentage (0–100).
     */
    public function getPaymentPercentage(): float
    {
        return (float) ($this->portal?->payment_percentage ?? 100);
    }

    /**
     * Return the tenant Stripe publishable key (for frontend use).
     * Returns null when not configured – callers must handle the null.
     */
    public function getPublishableKey(): ?string
    {
        return $this->portal?->payment_stripe_key ?: null;
    }

    // -------------------------------------------------------------------------
    // Private helpers
    // -------------------------------------------------------------------------

    /**
     * Configure the Stripe library with the tenant secret key.
     *
     * @throws RuntimeException
     */
    private function configure(): void
    {
        Stripe::setApiKey($this->resolveSecret());
    }

    /**
     * Decrypt and return the tenant Stripe secret.
     *
     * @throws RuntimeException when credentials are missing or cannot be decrypted
     */
    private function resolveSecret(): string
    {
        if (empty($this->portal?->payment_stripe_secret)) {
            throw new RuntimeException(
                'Stripe não configurado: secret em falta para este tenant.'
            );
        }

        try {
            return decrypt($this->portal->payment_stripe_secret);
        } catch (DecryptException) {
            Log::error('Stripe secret inválido para este tenant – não é possível desencriptar.', [
                'tenant_id' => tenant()?->id,
            ]);

            throw new RuntimeException(
                'Credenciais Stripe inválidas: o secret do tenant não pode ser desencriptado. ' .
                'Por favor, insira novamente o Stripe Secret no painel de configuração.'
            );
        }
    }

    /**
     * Resolve configured payment methods from tenant flags.
     */
    private function resolvePaymentMethods(): array
    {
        $methods = [];

        if ((int) ($this->portal?->payment_stripe_allow_card ?? 1) === 1) {
            $methods[] = 'card';
        }

        if ((int) ($this->portal?->payment_stripe_allow_multibanco ?? 0) === 1) {
            $methods[] = 'multibanco';
        }

        if (empty($methods)) {
            // Always allow at least card to prevent empty payment_method_types
            $methods[] = 'card';
        }

        return $methods;
    }
}
