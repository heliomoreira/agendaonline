# Stripe Multi-Tenant Migration Checklist

## 1) Pre-Migration

- [ ] Confirm tenant `portal` table has Stripe columns
- [ ] Confirm `StripeService` and `BookingService` exist
- [ ] Confirm booking routes are available in `routes/tenant.php`

## 2) Database

- [ ] Run tenant migrations
- [ ] Validate columns:
  - `payment_stripe_key`
  - `payment_stripe_secret`
  - `payment_stripe_allow_card`
  - `payment_stripe_allow_multibanco`
  - `payment_percentage`
  - `requires_payment`

## 3) Service Layer

- [ ] `StripeService::getConfig()` reads tenant + fallback global config
- [ ] `StripeService::createPaymentIntent()` uses tenant config
- [ ] `StripeService::verifyWebhook()` validates Stripe signature
- [ ] `BookingService` handles success/failure/canceled payment events

## 4) Controllers

- [ ] `BookingPaymentController` injects `StripeService`
- [ ] `BookingPaymentController` injects `BookingService`
- [ ] No hardcoded payment percentage in controller
- [ ] Returns 503 when Stripe is not configured
- [ ] Webhook delegates to `BookingService` handlers

## 5) Frontend & Booking Page

- [ ] `BookingController@index` passes `stripeKey`
- [ ] `WizConfig` receives `paymentPercentage` and `stripeKey`

## 6) Environment

- [ ] Set `STRIPE_KEY`
- [ ] Set `STRIPE_SECRET`
- [ ] Set `STRIPE_WEBHOOK_SECRET`
- [ ] (Optional) set method flags in env for fallback

## 7) Tenant-by-Tenant Configuration

For each tenant:

- [ ] Run `php artisan stripe:configure-tenant --tenant=<tenant-id>`
- [ ] Validate key formats (`pk_`, `sk_`)
- [ ] Validate Stripe API connectivity
- [ ] Save tenant payment percentage

## 8) Testing

- [ ] `php artisan test tests/Unit/Services/StripeServiceTest.php`
- [ ] `php artisan test tests/Unit/Services/BookingServiceTest.php`
- [ ] `php artisan test tests/Feature/BookingPaymentControllerTest.php`
- [ ] Manual booking flow with Stripe test card

## 9) Security Review

- [ ] No plaintext Stripe secrets stored
- [ ] Webhook signature enforced
- [ ] No secrets committed in source files

## 10) Deployment

- [ ] Deploy code changes
- [ ] Apply tenant migrations
- [ ] Configure environment secrets
- [ ] Validate webhook endpoint from Stripe dashboard

## 11) Rollback Plan

- [ ] Disable payment requirement per tenant if needed
- [ ] Revert controller changes if payment API is unavailable
- [ ] Restore previous release if critical payment flow breaks

## 12) Sign-off

- [ ] Engineering sign-off
- [ ] QA sign-off
- [ ] Product/Operations sign-off
