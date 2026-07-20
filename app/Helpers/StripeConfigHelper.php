<?php

namespace App\Helpers;

use App\Models\Portal;
use Illuminate\Support\Facades\Log;

class StripeConfigHelper
{
    /**
     * Get Stripe configuration for current tenant
     */
    public static function getConfig(): array
    {
        try {
            $portal = Portal::first();

            if ($portal && self::isTenantConfigured($portal)) {
                return [
                    'key' => $portal->payment_stripe_key,
                    'secret' => decrypt($portal->payment_stripe_secret ?? ''),
                    'allow_card' => (bool) $portal->payment_stripe_allow_card,
                    'allow_multibanco' => (bool) $portal->payment_stripe_allow_multibanco,
                    'percentage' => $portal->payment_percentage ?? 100,
                    'source' => 'tenant',
                ];
            }
        } catch (\Exception $e) {
            Log::warning('Failed to load tenant Stripe config', ['error' => $e->getMessage()]);
        }

        // Fallback to global config
        return [
            'key' => config('services.stripe.key'),
            'secret' => config('services.stripe.secret'),
            'allow_card' => config('services.stripe.allow_card', true),
            'allow_multibanco' => config('services.stripe.allow_multibanco', true),
            'percentage' => 100,
            'source' => 'global',
        ];
    }

    /**
     * Check if Stripe is configured for current tenant
     */
    public static function isConfigured(): bool
    {
        $config = self::getConfig();
        return !empty($config['key']) && !empty($config['secret']);
    }

    /**
     * Check if tenant-specific configuration exists
     */
    public static function isTenantConfigured(Portal $portal = null): bool
    {
        if (!$portal) {
            $portal = Portal::first();
        }

        if (!$portal) {
            return false;
        }

        return !empty($portal->payment_stripe_key) && !empty($portal->payment_stripe_secret);
    }

    /**
     * Get payment percentage for current tenant
     */
    public static function getPaymentPercentage(): int
    {
        try {
            $portal = Portal::first();
            return $portal?->payment_percentage ?? 100;
        } catch (\Exception $e) {
            Log::warning('Failed to load payment percentage', ['error' => $e->getMessage()]);
            return 100;
        }
    }

    /**
     * Get Stripe frontend key for current tenant
     */
    public static function getFrontendKey(): string
    {
        $config = self::getConfig();
        return $config['key'] ?? '';
    }

    /**
     * Check if specific payment method is enabled
     */
    public static function isPaymentMethodEnabled(string $method): bool
    {
        $config = self::getConfig();

        return match ($method) {
            'card' => $config['allow_card'] ?? true,
            'multibanco' => $config['allow_multibanco'] ?? true,
            'mbway' => $config['allow_multibanco'] ?? true, // MB WAY uses multibanco in Stripe
            default => false,
        };
    }

    /**
     * Validate Stripe configuration
     */
    public static function validate(): array
    {
        $errors = [];
        $config = self::getConfig();

        if (empty($config['key'])) {
            $errors[] = 'Stripe public key is not configured.';
        }

        if (empty($config['secret'])) {
            $errors[] = 'Stripe secret key is not configured.';
        }

        if (strlen($config['key'] ?? '') < 10) {
            $errors[] = 'Stripe public key appears to be invalid (too short).';
        }

        if (strlen($config['secret'] ?? '') < 10) {
            $errors[] = 'Stripe secret key appears to be invalid (too short).';
        }

        return [
            'valid' => count($errors) === 0,
            'errors' => $errors,
            'config_source' => $config['source'] ?? 'unknown',
        ];
    }
}
