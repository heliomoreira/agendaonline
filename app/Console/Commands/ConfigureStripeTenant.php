<?php

namespace App\Console\Commands;

use App\Models\Portal;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class ConfigureStripeTenant extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'stripe:configure-tenant';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Configure Stripe credentials for a tenant in an interactive way';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->line('');
        $this->info('╔══════════════════════════════════════════════════════╗');
        $this->info('║      Stripe Multi-Tenant Configuration Wizard         ║');
        $this->info('╚══════════════════════════════════════════════════════╝');
        $this->line('');

        // Get portal
        $portal = Portal::first();
        if (!$portal) {
            $this->error('❌ No portal found. Please create a portal first.');
            return self::FAILURE;
        }

        $this->info("📌 Configuring Stripe for: <info>{$portal->title}</info>");
        $this->line('');

        // Display current configuration
        if ($portal->payment_stripe_key) {
            $this->comment('Current Configuration:');
            $this->line("  • Stripe Key (Public): {$this->maskString($portal->payment_stripe_key)}");
            $this->line("  • Stripe Secret: " . ($portal->payment_stripe_secret ? 'Configured' : 'Not configured'));
            $this->line("  • Payment Percentage: {$portal->payment_percentage}%");
            $this->line("  • Allow Card: " . ($portal->payment_stripe_allow_card ? 'Yes' : 'No'));
            $this->line("  • Allow Multibanco/MBWay: " . ($portal->payment_stripe_allow_multibanco ? 'Yes' : 'No'));
            $this->line('');

            if (!$this->confirm('Do you want to update the current configuration?', false)) {
                $this->info('ℹ️  Configuration skipped.');
                return self::SUCCESS;
            }
        }

        // Step 1: Stripe Public Key
        $this->line('');
        $this->info('Step 1️⃣  - Stripe Public Key (Publishable Key)');
        $this->comment('Find this at: https://dashboard.stripe.com/apikeys');
        $stripeKey = $this->askForStripeKey('Enter your Stripe public key (starts with pk_):');

        // Step 2: Stripe Secret Key
        $this->line('');
        $this->info('Step 2️⃣  - Stripe Secret Key');
        $this->comment('⚠️  Keep this secret and never share it!');
        $stripeSecret = $this->askForStripeSecret('Enter your Stripe secret key (starts with sk_):');

        // Step 3: Payment Percentage
        $this->line('');
        $this->info('Step 3️⃣  - Payment Percentage');
        $this->comment('What percentage of the service price should be paid upfront?');
        $paymentPercentage = $this->askForPercentage(
            'Enter payment percentage (0-100):',
            $portal->payment_percentage ?? 50
        );

        // Step 4: Payment Methods
        $this->line('');
        $this->info('Step 4️⃣  - Payment Methods');
        $allowCard = $this->confirm('Allow credit/debit card payments?', true);
        $allowMultibanco = $this->confirm('Allow Multibanco/MB WAY payments?', true);

        if (!$allowCard && !$allowMultibanco) {
            $this->error('❌ You must enable at least one payment method!');
            return self::FAILURE;
        }

        // Step 5: Confirmation
        $this->line('');
        $this->info('Summary of Configuration:');
        $this->line("  • Stripe Key: {$this->maskString($stripeKey)}");
        $this->line("  • Stripe Secret: {$this->maskString($stripeSecret)}");
        $this->line("  • Payment Percentage: {$paymentPercentage}%");
        $this->line("  • Allow Card: " . ($allowCard ? 'Yes ✓' : 'No ✗'));
        $this->line("  • Allow Multibanco/MBWay: " . ($allowMultibanco ? 'Yes ✓' : 'No ✗'));
        $this->line('');

        if (!$this->confirm('Save this configuration?')) {
            $this->info('ℹ️  Configuration cancelled.');
            return self::SUCCESS;
        }

        // Validate Stripe credentials with Stripe API
        if (!$this->validateStripeCredentials($stripeKey, $stripeSecret)) {
            $this->error('❌ Invalid Stripe credentials. Please check and try again.');
            return self::FAILURE;
        }

        // Save configuration
        try {
            $portal->update([
                'payment_stripe_key' => $stripeKey,
                'payment_stripe_secret' => encrypt($stripeSecret),
                'payment_percentage' => $paymentPercentage,
                'payment_stripe_allow_card' => $allowCard,
                'payment_stripe_allow_multibanco' => $allowMultibanco,
            ]);

            $this->line('');
            $this->info('✅ Stripe configuration saved successfully!');
            $this->line('');
            $this->comment('Next steps:');
            $this->line('  1. Configure webhook in Stripe Dashboard');
            $this->line('     Endpoint URL: ' . url('/booking/stripe/webhook'));
            $this->line('');
            $this->line('  2. Add webhook events:');
            $this->line('     • payment_intent.succeeded');
            $this->line('     • payment_intent.payment_failed');
            $this->line('     • payment_intent.canceled');
            $this->line('');
            $this->line('  3. Test the booking flow in your application');
            $this->line('');

            return self::SUCCESS;
        } catch (\Exception $e) {
            $this->error('❌ Error saving configuration: ' . $e->getMessage());
            return self::FAILURE;
        }
    }

    /**
     * Ask for Stripe public key with validation
     */
    protected function askForStripeKey(string $question): string
    {
        while (true) {
            $key = $this->secret($question);

            if (empty($key)) {
                $this->error('❌ Stripe key cannot be empty.');
                continue;
            }

            if (!str_starts_with($key, 'pk_')) {
                $this->error('❌ Stripe public key must start with "pk_"');
                continue;
            }

            if (strlen($key) < 20) {
                $this->error('❌ Stripe public key appears to be invalid (too short).');
                continue;
            }

            return $key;
        }
    }

    /**
     * Ask for Stripe secret key with validation
     */
    protected function askForStripeSecret(string $question): string
    {
        while (true) {
            $secret = $this->secret($question);

            if (empty($secret)) {
                $this->error('❌ Stripe secret cannot be empty.');
                continue;
            }

            if (!str_starts_with($secret, 'sk_')) {
                $this->error('❌ Stripe secret key must start with "sk_"');
                continue;
            }

            if (strlen($secret) < 20) {
                $this->error('❌ Stripe secret key appears to be invalid (too short).');
                continue;
            }

            return $secret;
        }
    }

    /**
     * Ask for payment percentage with validation
     */
    protected function askForPercentage(string $question, int $default = 50): int
    {
        while (true) {
            $percentage = $this->ask($question, $default);

            if (!is_numeric($percentage)) {
                $this->error('❌ Payment percentage must be a number.');
                continue;
            }

            $percentage = (int) $percentage;

            if ($percentage < 0 || $percentage > 100) {
                $this->error('❌ Payment percentage must be between 0 and 100.');
                continue;
            }

            return $percentage;
        }
    }

    /**
     * Validate Stripe credentials by making a test API call
     */
    protected function validateStripeCredentials(string $key, string $secret): bool
    {
        $this->line('');
        $this->comment('🔍 Validating Stripe credentials...');

        try {
            \Stripe\Stripe::setApiKey($secret);
            
            // Try to retrieve account information
            $account = \Stripe\Account::retrieve();
            
            if ($account && $account->id) {
                $this->info("✅ Stripe account validated: {$account->email}");
                return true;
            }
        } catch (\Exception $e) {
            $this->error('❌ Stripe validation failed: ' . $e->getMessage());
            return false;
        }

        return false;
    }

    /**
     * Mask sensitive strings for display
     */
    protected function maskString(string $string, int $visibleChars = 6): string
    {
        if (strlen($string) <= $visibleChars) {
            return $string;
        }

        $visible = substr($string, 0, $visibleChars);
        $hidden = str_repeat('•', strlen($string) - $visibleChars);

        return $visible . $hidden;
    }
}
