# Stripe Multi-Tenant Configuration Guide

## Overview

This guide explains how to configure Stripe payment processing for the Agenda Online multi-tenant system. Each tenant can have their own Stripe account, allowing complete isolation of payment data and credentials.

## Table of Contents

1. [Quick Start](#quick-start)
2. [Manual Configuration](#manual-configuration)
3. [Environment Setup](#environment-setup)
4. [Payment Percentage Configuration](#payment-percentage-configuration)
5. [Payment Methods](#payment-methods)
6. [Webhook Configuration](#webhook-configuration)
7. [Testing](#testing)
8. [Troubleshooting](#troubleshooting)

---

## Quick Start

### Using the Interactive CLI Command

The easiest way to configure Stripe credentials is through the interactive CLI command:

```bash
php artisan stripe:configure-tenant
```

This command will:
- ✅ Guide you through entering Stripe credentials
- ✅ Validate your keys format
- ✅ Test connection to Stripe API
- ✅ Configure payment percentage and methods
- ✅ Automatically encrypt and save credentials

**Example:**
```
╔══════════════════════════════════════════════════════╗
║      Stripe Multi-Tenant Configuration Wizard         ║
╚══════════════════════════════════════════════════════╝

📌 Configuring Stripe for: My Salon

Step 1️⃣  - Stripe Public Key (Publishable Key)
Find this at: https://dashboard.stripe.com/apikeys
Enter your Stripe public key (starts with pk_): ••••••••••••••••••••••••••••••••••••••

Step 2️⃣  - Stripe Secret Key
⚠️  Keep this secret and never share it!
Enter your Stripe secret key (starts with sk_): ••••••••••••••••••••••••••••••••••••••

Step 3️⃣  - Payment Percentage
What percentage of the service price should be paid upfront?
Enter payment percentage (0-100): [50] 50

Step 4️⃣  - Payment Methods
Allow credit/debit card payments? (yes/no) [yes]: yes
Allow Multibanco/MB WAY payments? (yes/no) [yes]: yes

✅ Stripe configuration saved successfully!
```

---

## Manual Configuration

If you prefer manual setup through the Admin Panel:

### 1. Get Your Stripe API Keys

1. Log in to [Stripe Dashboard](https://dashboard.stripe.com)
2. Navigate to **Developers** → **API Keys**
3. Copy your **Publishable Key** (starts with `pk_`)
4. Copy your **Secret Key** (starts with `sk_`)

**Important:** Always use **Test Keys** for development and **Live Keys** for production.

### 2. Admin Panel Configuration

1. Log in to your Admin Dashboard
2. Go to **Portal Settings** → **Payment**
3. Enter the following fields:

| Field | Value | Example |
|-------|-------|----------|
| **Stripe Key** | Your Publishable Key | `pk_test_51H...` |
| **Stripe Secret** | Your Secret Key | `sk_test_4e...` |
| **% Pré-pagamento** | Payment percentage (0-100) | `50` |
| **Cartão de Crédito** | Enable card payments | Yes/No |
| **Multibanco / MBWay** | Enable local methods | Yes/No |

4. Click **Save**

---

## Environment Setup

### Global Fallback Configuration

If a tenant doesn't have specific Stripe credentials configured, the system will fall back to global environment variables.

#### `.env` Example

```dotenv
# Global Stripe Configuration (Fallback)
STRIPE_KEY=pk_test_global_key
STRIPE_SECRET=sk_test_global_secret
STRIPE_WEBHOOK_SECRET=whsec_test_webhook_secret
STRIPE_ALLOW_CARD=true
STRIPE_ALLOW_MULTIBANCO=true
```

### Configuration Priority (Fallback Chain)

The system checks credentials in this order:

```
1. Portal.payment_stripe_key (Tenant-specific) ✅ PRIMARY
   ↓ (if not set)
2. config('services.stripe.key') from .env ✅ FALLBACK
   ↓ (if not set)
3. Return null + Log warning ⚠️ NO CONFIG
```

---

## Payment Percentage Configuration

### What is Payment Percentage?

The payment percentage determines what portion of the service price must be paid upfront via Stripe:

- **100%** = Full payment required before confirmation
- **50%** = 50% upfront, 50% on-site (deposit)
- **0%** = No upfront payment (booking only)

### Example Scenarios

**Scenario 1: Hair Salon (50% deposit)**
```
Service: Haircut - €50.00
Payment Percentage: 50%
Client pays: €25.00 (deposit via Stripe)
Client pays: €25.00 (balance on-site)
```

**Scenario 2: Massage Studio (100% payment)**
```
Service: Full Body Massage - €80.00
Payment Percentage: 100%
Client pays: €80.00 (full payment via Stripe)
Client pays: €0.00 (already paid)
```

**Scenario 3: Consultation (No upfront payment)**
```
Service: Initial Consultation - €0.00
Payment Percentage: 0%
Client pays: €0.00
Client pays: €0.00
Status: Booking confirmed immediately
```

---

## Payment Methods

### Available Payment Methods

The system supports multiple payment methods, which can be enabled/disabled per tenant:

#### 1. **Credit/Debit Card** (Recommended)
- Visa, Mastercard, American Express
- Immediate payment
- Status: ✅ Always available

#### 2. **Multibanco** (Portugal)
- Local transfer method
- ATM-based payment
- 30-minute payment window
- Status: ⚠️ Asynchronous (payment confirmed via webhook)

#### 3. **MB WAY** (Portugal)
- Mobile payment app
- Instant smartphone notification
- Status: ✅ Uses Multibanco infrastructure

### Enabling/Disabling Methods

**Via Admin Panel:**
1. Portal Settings → Payment
2. Check/Uncheck:
   - ☑️ Cartão de Crédito (Credit Card)
   - ☑️ Multibanco / MBWay
3. Save

**Via CLI:**
```bash
php artisan stripe:configure-tenant
# Follow prompts to enable/disable payment methods
```

---

## Webhook Configuration

### What are Webhooks?

Webhooks allow Stripe to notify your application when payment events occur (success, failure, etc.).

### Setup Steps

1. **Log in to Stripe Dashboard**
   - Navigate to **Developers** → **Webhooks**

2. **Add Endpoint**
   - Endpoint URL: `https://yourdomain.com/booking/stripe/webhook`
   - Replace `yourdomain.com` with your actual domain

3. **Select Events**
   - ☑️ `payment_intent.succeeded` - Payment completed
   - ☑️ `payment_intent.payment_failed` - Payment failed
   - ☑️ `payment_intent.canceled` - Payment canceled

4. **Copy Webhook Secret**
   - Click the endpoint to view details
   - Copy the **Signing Secret** (starts with `whsec_`)

5. **Save to Environment**
   ```dotenv
   STRIPE_WEBHOOK_SECRET=whsec_test_1234567890...
   ```

### How Webhooks Work

```
Client makes payment → Stripe processes → Webhook triggered → Booking confirmed
                                              ↓
                    POST /booking/stripe/webhook
                    Signature validation
                    Update booking status
                    Send confirmation email
```

### Testing Webhooks Locally

Use the Stripe CLI to test webhooks on your local machine:

```bash
# Install Stripe CLI
# macOS:
brew install stripe/stripe-cli/stripe

# Windows:
choco install stripe-cli

# Linux:
curl https://files.stripe.com/stripe-cli/install.sh -o install.sh && bash install.sh

# Login to your Stripe account
stripe login

# Forward webhook events to your local app
stripe listen --forward-to localhost:8000/booking/stripe/webhook

# In another terminal, trigger test events
stripe trigger payment_intent.succeeded
```

---

## Testing

### Test Credentials

Use these test credentials from Stripe for testing:

#### Test Card Numbers

| Card Type | Number | CVC | Date |
|-----------|--------|-----|------|
| **Visa** | `4242 4242 4242 4242` | Any 3 digits | Any future date |
| **Mastercard** | `5555 5555 5555 4444` | Any 3 digits | Any future date |
| **Amex** | `3782 822463 10005` | Any 4 digits | Any future date |

#### Test Results

- **Succeeds:** Any future expiry date
- **Fails:** Use expiry date in the past (e.g., 01/20)
- **Requires authentication:** Use CVC code `000`

### Test Payment Flow

1. **Start your local server**
   ```bash
   php artisan serve
   ```

2. **Access booking page**
   ```
   http://localhost:8000/booking
   ```

3. **Complete booking form**
   - Select service, professional, date/time
   - Enter client details

4. **Enter test card details**
   - Card: `4242 4242 4242 4242`
   - Any CVC: `123`
   - Any future date: `12/25`

5. **Verify payment**
   - Stripe test dashboard shows transaction
   - Email confirmation sent
   - Booking status updated to "confirmed"

---

## Troubleshooting

### Common Issues

#### 1. "Payment processing is not configured for this tenant"

**Causes:**
- Stripe credentials not set in Portal settings
- Stripe secret not encrypted properly
- Database query failed

**Solution:**
```bash
# Run configuration wizard
php artisan stripe:configure-tenant

# Or verify in database
php artisan tinker
>>> Portal::first()->payment_stripe_key
# Should return your pk_* key
```

#### 2. "Invalid Stripe credentials"

**Causes:**
- Wrong API keys (test vs. production mixed)
- Keys have extra spaces or special characters
- Account suspended or keys revoked

**Solution:**
1. Log in to Stripe Dashboard
2. Verify API keys in **Developers** → **API Keys**
3. Ensure using consistent environment (all test or all live)
4. Copy keys again, ensuring no extra spaces

#### 3. Webhook not triggering

**Causes:**
- Webhook endpoint URL incorrect
- Webhook signing secret not configured
- Server returning error responses

**Solution:**
```bash
# Check webhook secret is set
grep STRIPE_WEBHOOK_SECRET .env

# Test webhook locally
stripe listen --forward-to localhost:8000/booking/stripe/webhook

# Monitor logs
tail -f storage/logs/laravel.log | grep -i stripe

# Verify endpoint is accessible
curl https://yourdomain.com/booking/stripe/webhook
# Should return 400 (missing signature) not 404
```

#### 4. "Multibanco payment not confirming"

**Causes:**
- Webhook not configured
- Payment reference not generated correctly
- Payment window expired (30 minutes)

**Solution:**
1. Ensure webhook is configured and working
2. Check logs for webhook events:
   ```bash
   grep "payment_intent" storage/logs/laravel.log
   ```
3. Verify Stripe dashboard shows payment attempt
4. Test within 30-minute window

#### 5. Double bookings on payment failure

**Causes:**
- User clicked "Confirm" twice
- Network latency
- Browser refresh during payment

**Solution:**
```php
// Database uniqueness on payment_intent_id prevents duplicates
Schema::table('agenda', function (Blueprint $table) {
    $table->unique('payment_intent_id');
});
```

### Debug Mode

Enable detailed logging for debugging:

```bash
# Edit .env
LOG_LEVEL=debug

# Or set in code
Log::channel('stack')->debug('Stripe config:', [
    'key' => $config['key'],
    'configured' => !empty($config['secret']),
]);

# View logs
tail -f storage/logs/laravel.log
```

### Verification Checklist

- [ ] Stripe API keys configured in Portal settings
- [ ] Environment variables set in `.env`
- [ ] Webhook endpoint configured in Stripe dashboard
- [ ] Webhook secret saved to `.env`
- [ ] Payment methods enabled (Card and/or Multibanco)
- [ ] Payment percentage set (0-100)
- [ ] Database migrations run
- [ ] Tests passing: `php artisan test`
- [ ] Booking wizard loads payment step
- [ ] Test payment completes successfully
- [ ] Webhook events received and processed
- [ ] Confirmation email sent after payment

---

## Additional Resources

- [Stripe Documentation](https://stripe.com/docs)
- [Stripe API Reference](https://stripe.com/docs/api)
- [Stripe Testing Guide](https://stripe.com/docs/testing)
- [Stripe Webhooks](https://stripe.com/docs/webhooks)
- [Payment Intents API](https://stripe.com/docs/payments/payment-intents)

---

## Support

For issues or questions:

1. Check this guide's troubleshooting section
2. Review application logs: `storage/logs/laravel.log`
3. Test with Stripe CLI: `stripe trigger payment_intent.succeeded`
4. Contact Stripe Support: https://support.stripe.com
