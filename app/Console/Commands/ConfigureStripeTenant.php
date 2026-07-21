<?php

namespace App\Console\Commands;

use App\Models\Portal;
use App\Models\Tenant;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Stancl\Tenancy\Facades\Tenancy;
use Stripe\StripeClient;

class ConfigureStripeTenant extends Command
{
    protected $signature = 'stripe:configure-tenant {--tenant= : Tenant ID to initialize before saving config}';

    protected $description = 'Interactive wizard to configure Stripe credentials for the current tenant portal';

    public function handle(): int
    {
        $this->newLine();
        $this->info('🧾 Stripe tenant configuration wizard');
        $this->line('This command stores tenant Stripe credentials in the portal configuration.');

        if (!$this->initializeTenantFromOption()) {
            return self::FAILURE;
        }

        $portal = Portal::first() ?? new Portal();

        $publishableKey = $this->askForKey('Stripe Publishable Key (pk_...)', 'pk_', $portal->payment_stripe_key);
        $secretKey = $this->askForSecretKey('Stripe Secret Key (sk_...)');

        if (!$this->validateStripeConnection($secretKey)) {
            return self::FAILURE;
        }

        $paymentPercentage = (int) $this->ask(
            'Payment percentage to charge upfront (0-100)',
            (string) ($portal->payment_percentage ?? 50)
        );

        if ($paymentPercentage < 0 || $paymentPercentage > 100) {
            $this->error('Payment percentage must be between 0 and 100.');
            return self::FAILURE;
        }

        $allowCard = $this->confirm('Enable card payments?', (bool) ($portal->payment_stripe_allow_card ?? true));
        $allowMultibanco = $this->confirm('Enable Multibanco payments?', (bool) ($portal->payment_stripe_allow_multibanco ?? true));

        $portal->payment_stripe_key = $publishableKey;
        $portal->payment_stripe_secret = encrypt($secretKey);
        $portal->payment_stripe_allow_card = $allowCard;
        $portal->payment_stripe_allow_multibanco = $allowMultibanco;
        $portal->requires_payment = true;
        $portal->payment_percentage = $paymentPercentage;
        $portal->save();

        $this->newLine();
        $this->info('✅ Stripe tenant configuration saved successfully.');
        $this->line('Next steps:');
        $this->line('  1) Configure STRIPE_WEBHOOK_SECRET in environment variables.');
        $this->line('  2) Point Stripe webhook to: /booking/stripe/webhook');
        $this->line('  3) Validate flow with test card: 4242 4242 4242 4242');

        return self::SUCCESS;
    }

    private function initializeTenantFromOption(): bool
    {
        $tenantId = $this->option('tenant');

        if (!$tenantId) {
            return true;
        }

        $tenant = Tenant::find($tenantId);

        if (!$tenant) {
            $this->error("Tenant '{$tenantId}' not found.");
            return false;
        }

        Tenancy::initialize($tenant);

        $this->info("Tenant context initialized for: {$tenant->id}");

        return true;
    }

    private function askForKey(string $label, string $prefix, ?string $default = null): string
    {
        while (true) {
            $value = (string) $this->ask($label, $default);

            if (str_starts_with($value, $prefix)) {
                return $value;
            }

            $this->warn("Invalid format. Expected key prefix: {$prefix}");
        }
    }

    private function askForSecretKey(string $label): string
    {
        while (true) {
            $value = (string) $this->secret($label);

            if (str_starts_with($value, 'sk_')) {
                return $value;
            }

            $this->warn('Invalid format. Expected key prefix: sk_');
        }
    }

    private function validateStripeConnection(string $secretKey): bool
    {
        try {
            $client = new StripeClient($secretKey);
            $client->accounts->retrieve();

            $this->info('🔐 Stripe API credentials validated successfully.');

            return true;
        } catch (\Throwable $e) {
            Log::error('Stripe tenant configuration validation failed', [
                'tenant_id' => tenant()?->id,
                'error' => $e->getMessage(),
            ]);

            $this->error('Unable to validate Stripe credentials: ' . $e->getMessage());

            return false;
        }
    }
}
