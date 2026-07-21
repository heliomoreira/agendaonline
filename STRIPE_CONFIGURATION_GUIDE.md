# Stripe Multi-Tenant Configuration Guide

## Quick Start

Run the interactive tenant wizard:

```bash
php artisan stripe:configure-tenant
# or target a specific tenant
php artisan stripe:configure-tenant --tenant=tenant-id
```

The wizard will:
- ask for publishable key (`pk_...`)
- ask for secret key (`sk_...`)
- validate the Stripe API credentials
- encrypt and store the tenant secret key
- save payment percentage and allowed methods

---

## Manual Configuration

If you prefer manual setup, update the tenant `portal` record:

- `payment_stripe_key`
- `payment_stripe_secret` (encrypted)
- `payment_stripe_allow_card`
- `payment_stripe_allow_multibanco`
- `requires_payment`
- `payment_percentage`

Global fallback keys (used when tenant values are missing):

```env
STRIPE_KEY=
STRIPE_SECRET=
STRIPE_WEBHOOK_SECRET=
STRIPE_ALLOW_CARD=true
STRIPE_ALLOW_MULTIBANCO=true
```

---

## Fallback Chain

For each request, the app resolves Stripe config in this order:

1. Tenant `portal` Stripe fields
2. Global `config/services.php` (`STRIPE_*` env)

If no valid secret is found, payment intent creation fails with HTTP `503`.

---

## Payment Percentage

The upfront payment amount is computed with tenant configuration:

```text
amount_to_pay = service_price * (payment_percentage / 100)
```

Examples:

- Service €100 + percentage 30% → pays €30 now
- Service €80 + percentage 50% → pays €40 now

---

## Payment Methods

Supported methods:
- `card`
- `multibanco`

They are controlled per tenant with:
- `payment_stripe_allow_card`
- `payment_stripe_allow_multibanco`

---

## Webhook Setup

1. Configure endpoint in Stripe dashboard:
   - `https://<tenant-domain>/booking/stripe/webhook`
2. Set `STRIPE_WEBHOOK_SECRET` in environment.
3. Ensure events include at least:
   - `payment_intent.succeeded`
   - `payment_intent.payment_failed`
   - `payment_intent.canceled`

Webhook verification is handled by `StripeService::verifyWebhook()`.

---

## Testing

### Automated

```bash
php artisan test tests/Unit/Services/StripeServiceTest.php
php artisan test tests/Unit/Services/BookingServiceTest.php
php artisan test tests/Feature/BookingPaymentControllerTest.php
```

### Manual Payment Flow

1. Open `/booking`
2. Pick service/professional/date
3. Enter client details
4. Use test card: `4242 4242 4242 4242`
5. Confirm webhook receives success event

---

## Troubleshooting

### `Payment processing is not configured for this tenant`
- Configure tenant Stripe keys in portal or via CLI wizard.

### `Invalid webhook signature`
- Check `STRIPE_WEBHOOK_SECRET`.
- Ensure Stripe endpoint secret matches environment.

### Stripe API authentication errors
- Confirm secret key starts with `sk_`.
- Re-run wizard and validate credentials.

---

## Verification Checklist

- [ ] Tenant Stripe publishable key is configured
- [ ] Tenant Stripe secret is encrypted in DB
- [ ] `STRIPE_WEBHOOK_SECRET` configured
- [ ] Payment percentage correct for tenant
- [ ] Webhook events are being processed
- [ ] Booking is confirmed after successful payment
