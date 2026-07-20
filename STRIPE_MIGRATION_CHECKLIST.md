# Stripe Multi-Tenant Migration Checklist

Use this checklist to ensure your migration from a single global Stripe configuration to per-tenant configuration is complete and correct.

## Pre-Migration

### Assessment
- [ ] Audit current Stripe integration in `master` branch
- [ ] Document current payment flow
- [ ] Identify any custom payment logic
- [ ] List all places Stripe API keys are used
- [ ] Backup production database
- [ ] Create staging environment for testing

### Code Review
- [ ] Review `BookingPaymentController.php` for hardcoded values
- [ ] Check `BookingController.php` for Stripe calls
- [ ] Identify all `config('services.stripe.*')` references
- [ ] Look for `Stripe::setApiKey()` calls

## Database Migrations

### Schema Updates
- [ ] Run migration: `database/migrations/tenant/2026_02_24_014844_add_stripe_to_portal.php`
- [ ] Verify columns added to `portal` table:
  - [ ] `payment_stripe_key`
  - [ ] `payment_stripe_secret`
  - [ ] `payment_stripe_allow_card`
  - [ ] `payment_stripe_allow_multibanco`
  - [ ] `payment_percentage`

### Data Migration
```bash
# If migrating from global config:
php artisan migrate
# Manually populate portal.payment_stripe_key and portal.payment_stripe_secret
# from previous global STRIPE_* env variables
```

## Service Layer Implementation

### StripeService
- [ ] Service created: `app/Services/StripeService.php`
- [ ] Methods implemented:
  - [ ] `getConfig()` - Returns tenant-specific config
  - [ ] `isConfigured()` - Validates credentials exist
  - [ ] `getPaymentPercentage()` - Gets tenant percentage
  - [ ] `createPaymentIntent()` - Creates payment intent
  - [ ] `retrievePaymentIntent()` - Gets payment status
  - [ ] `verifyWebhook()` - Validates webhook signature
  - [ ] `getFrontendKey()` - Returns public key for JS

### BookingService
- [ ] Service enhanced: `app/Services/BookingService.php`
- [ ] Methods added:
  - [ ] `createBookingWithPayment()` - Creates booking + payment intent
  - [ ] `confirmBooking()` - Confirms paid booking
  - [ ] `handlePaymentSuccess()` - Webhook handler
  - [ ] `handlePaymentFailed()` - Failure handler
  - [ ] `handlePaymentCanceled()` - Cancellation handler

## Controller Updates

### BookingPaymentController
- [ ] Inject `StripeService`
- [ ] Update `createPaymentIntent()`:
  - [ ] Remove hardcoded 50% percentage
  - [ ] Remove direct `Stripe::setApiKey()` call
  - [ ] Use `$stripeService->getPaymentPercentage()`
  - [ ] Use `$stripeService->createPaymentIntent()`
  - [ ] Use `$stripeService->isConfigured()` for validation
- [ ] Update `handleWebhook()`:
  - [ ] Use `$stripeService->verifyWebhook()`
  - [ ] Use `$bookingService->handlePaymentSuccess()` etc.
  - [ ] Add tenant context to webhook processing

### BookingController
- [ ] Remove old `createPaymentIntent()` method (duplicate)
- [ ] Update `index()` to pass Stripe key:
  ```php
  'stripeKey' => app(StripeService::class)->getFrontendKey(),
  ```

## Frontend Configuration

### Blade View
- [ ] Update `resources/views/front/portal/booking.blade.php`
- [ ] Ensure `WizConfig` includes:
  ```javascript
  window.WizConfig = {
      stripeKey: '{{ $stripeKey ?? config('services.stripe.key', '') }}',
      paymentPercentage: {{ $paymentPercentage ?? 100 }},
      ...
  }
  ```

### JavaScript
- [ ] Update `public/assets/js/booking-wizard.js`
- [ ] Use `CFG.stripeKey` from WizConfig
- [ ] Use `CFG.paymentPercentage` for amount calculation
- [ ] Verify payment element displays correct amount

## Environment Configuration

### .env Setup
- [ ] Configure global Stripe keys as fallback:
  ```dotenv
  STRIPE_KEY=pk_test_...
  STRIPE_SECRET=sk_test_...
  STRIPE_WEBHOOK_SECRET=whsec_test_...
  ```
- [ ] Keep keys for backward compatibility
- [ ] Document that tenant keys override these

### config/services.php
- [ ] Verify Stripe config section exists
- [ ] Ensure fallback keys are referenced
- [ ] Add webhook_secret if not present

## Tenant Configuration

### Per-Tenant Setup
For each tenant that uses Stripe:
- [ ] Log in to Admin Dashboard
- [ ] Go to Portal Settings → Payment
- [ ] Enter Stripe Public Key
- [ ] Enter Stripe Secret Key
- [ ] Set Payment Percentage (0-100)
- [ ] Enable/disable payment methods
- [ ] Save and verify

### CLI Configuration
Or use the CLI command for each tenant:
```bash
php artisan stripe:configure-tenant
```
- [ ] Run command for each tenant
- [ ] Follow interactive prompts
- [ ] Verify connection to Stripe API
- [ ] Confirm configuration saved

## Webhook Configuration

### Stripe Dashboard Setup
1. Log in to Stripe Dashboard
2. Go to Developers → Webhooks
3. For each environment (test/production):
   - [ ] Add endpoint: `https://your-domain.com/booking/stripe/webhook`
   - [ ] Select events:
     - [ ] `payment_intent.succeeded`
     - [ ] `payment_intent.payment_failed`
     - [ ] `payment_intent.canceled`
   - [ ] Copy signing secret
   - [ ] Save to `.env`: `STRIPE_WEBHOOK_SECRET=whsec_...`

### Local Testing Setup
- [ ] Install Stripe CLI
- [ ] Test webhook forwarding:
  ```bash
  stripe listen --forward-to localhost:8000/booking/stripe/webhook
  ```
- [ ] Verify webhook events received

## Testing

### Unit Tests
- [ ] Create/run `tests/Unit/Services/StripeServiceTest.php`
  - [ ] Test `getConfig()` returns correct config
  - [ ] Test `isConfigured()` validation
  - [ ] Test payment percentage retrieval
  - [ ] Test fallback to global config
  - [ ] Run: `php artisan test tests/Unit/Services/StripeServiceTest.php`

- [ ] Create/run `tests/Unit/Services/BookingServiceTest.php`
  - [ ] Test booking confirmation
  - [ ] Test payment success handler
  - [ ] Test payment failure handler
  - [ ] Test duplicate prevention
  - [ ] Run: `php artisan test tests/Unit/Services/BookingServiceTest.php`

### Feature Tests
- [ ] Create/run `tests/Feature/BookingPaymentControllerTest.php`
  - [ ] Test payment intent endpoint
  - [ ] Test validation errors
  - [ ] Test Stripe not configured response
  - [ ] Test webhook handling
  - [ ] Run: `php artisan test tests/Feature/BookingPaymentControllerTest.php`

### Manual Testing
- [ ] Test booking flow end-to-end:
  - [ ] Select service, professional, date/time
  - [ ] Enter client details
  - [ ] Enter test card: `4242 4242 4242 4242`
  - [ ] Verify payment processes
  - [ ] Check booking created with status "confirmed"
  - [ ] Verify email sent

- [ ] Test Multibanco payment:
  - [ ] Complete booking form
  - [ ] Select Multibanco payment method
  - [ ] Generate reference
  - [ ] Verify payment intent in Stripe dashboard
  - [ ] Check booking status "payment_pending"
  - [ ] Simulate payment via Stripe dashboard
  - [ ] Verify webhook updates booking to "confirmed"

- [ ] Test payment failure:
  - [ ] Use expired card: `4242 4242 4242 4240`
  - [ ] Verify error displayed
  - [ ] Check booking status "payment_failed"

- [ ] Test multiple tenants:
  - [ ] Switch to different tenant domain
  - [ ] Verify different Stripe credentials used
  - [ ] Complete payment flow
  - [ ] Confirm tenant isolation

## Security Review

### Encryption
- [ ] Verify `payment_stripe_secret` is encrypted:
  ```bash
  php artisan tinker
  >>> Portal::first()->payment_stripe_secret
  # Should be encrypted, not plaintext
  ```
- [ ] Verify decryption in StripeService:
  ```php
  decrypt($portal->payment_stripe_secret ?? '')
  ```

### Logging
- [ ] Ensure secret keys NOT logged
- [ ] Verify logs mask sensitive data
- [ ] Check `storage/logs/laravel.log` has no secrets

### Validation
- [ ] Verify middleware validates configuration
- [ ] Check payment form requires authentication
- [ ] Ensure webhook signature validated
- [ ] Verify CSRF token on payment forms

## Deployment

### Pre-Deployment Checklist
- [ ] All tests passing: `php artisan test`
- [ ] Code review completed
- [ ] Database migrations tested on staging
- [ ] Performance tested (load testing)
- [ ] Security audit completed
- [ ] Documentation updated

### Deployment Steps
1. [ ] Pull latest code
2. [ ] Run migrations: `php artisan migrate`
3. [ ] Clear cache: `php artisan cache:clear`
4. [ ] Configure each tenant:
   ```bash
   php artisan stripe:configure-tenant
   ```
5. [ ] Configure webhooks in Stripe Dashboard
6. [ ] Test payment flow in production
7. [ ] Monitor logs for errors

### Post-Deployment
- [ ] Monitor error logs for 24 hours
- [ ] Test booking flow daily for 1 week
- [ ] Verify webhooks processing correctly
- [ ] Check payment success rate
- [ ] Get feedback from tenants
- [ ] Archive old Stripe configuration

## Rollback Plan

If issues occur:
- [ ] Revert to previous branch
- [ ] Restore database backup
- [ ] Notify users of maintenance
- [ ] Document issue for future reference
- [ ] Re-plan migration with fixes

## Documentation

### Files to Create/Update
- [ ] `STRIPE_CONFIGURATION_GUIDE.md` - User guide
- [ ] `STRIPE_MIGRATION_CHECKLIST.md` - This file
- [ ] Update `README.md` with Stripe setup instructions
- [ ] Create admin panel help text
- [ ] Document API changes in changelog

## Sign-Off

- [ ] Developer: _______________________ Date: _______
- [ ] QA/Tester: _______________________ Date: _______
- [ ] DevOps: _______________________ Date: _______
- [ ] Project Manager: _______________________ Date: _______

---

## Notes

Use this section to document any issues, decisions, or deviations during migration:

```
[Your notes here]
```
