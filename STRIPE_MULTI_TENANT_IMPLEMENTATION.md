# Stripe Multi-Tenant Implementation Plan

## Overview
This document provides a comprehensive implementation strategy to make Stripe payment integration fully configurable per tenant, replacing hardcoded global configuration with dynamic per-Portal settings.

## Current State Analysis

### ✅ Already Implemented
- Database schema supports per-tenant Stripe credentials in `Portal` model
- Admin UI form for configuring Stripe keys and payment methods per tenant
- Payment method support (Card, MB WAY, Multibanco)
- Webhook handler structure

### ❌ Issues to Fix
1. **BookingPaymentController** uses global `config('services.stripe.secret')` instead of tenant-specific keys
2. **JavaScript frontend** (booking-wizard.js) hardcodes stripe key from global config
3. **Payment percentage** is hardcoded at 50% instead of using `Portal.payment_percentage`
4. **Webhook handler** lacks tenant validation/context
5. **No service layer** to abstract Stripe API calls

## Implementation Steps

### Step 1: Create StripeService for Tenant-Aware Operations
**File:** `app/Services/StripeService.php`

Provides a centralized service that:
- Retrieves tenant Portal settings
- Validates Stripe credentials before API calls
- Creates PaymentIntents with tenant context
- Handles errors gracefully with fallback to global config

### Step 2: Refactor BookingPaymentController
**File:** `app/Http/Controllers/BookingPaymentController.php`

Changes:
- Inject `StripeService` 
- Use tenant Portal credentials for API calls
- Replace hardcoded payment percentage with `Portal.payment_percentage`
- Add tenant context to payment intent metadata
- Add validation for Stripe configuration before processing

### Step 3: Create Booking Service Layer
**File:** `app/Services/BookingService.php`

Extracts booking logic from controller:
- Payment intent creation
- Booking confirmation
- Payment status updates
- Webhook event processing

### Step 4: Update Booking Controller Routes to Pass Tenant Config to Frontend
**File:** `app/Http/Controllers/BookingController.php`

Changes:
- Load Portal settings in `index()` method
- Pass Stripe key and payment percentage to Blade view
- Ensure frontend gets tenant-specific configuration

### Step 5: Update Booking Wizard JavaScript
**File:** `public/assets/js/booking-wizard.js`

Changes:
- Read `stripeKey` and `paymentPercentage` from `window.WizConfig` (passed from Blade)
- Remove any hardcoded global references
- Ensure all API calls are tenant-aware

### Step 6: Add Webhook Tenant Resolution
**File:** `app/Http/Controllers/BookingPaymentController.php`

Webhook handler improvements:
- Extract tenant domain from webhook metadata or referer
- Validate webhook with correct tenant's Stripe secret
- Route event handling through BookingService

### Step 7: Add Configuration Validation Middleware
**File:** `app/Http/Middleware/ValidateStripeConfiguration.php`

Prevents payment form rendering without valid Stripe credentials:
- Check Portal has valid Stripe key and secret
- Return error if Stripe not configured for tenant
- Optional: redirect to admin portal settings

### Step 8: Update Environment Configuration
**File:** `.env.example` and `config/services.php`

Changes:
- Keep global `STRIPE_*` env vars as fallback
- Update documentation to note per-tenant override
- Add webhook secret config (can be global or per-tenant)

### Step 9: Create Fallback/Validation Helper
**File:** `app/Helpers/StripeConfigHelper.php`

Utility functions:
- `getStripeConfig()` - returns tenant-specific or global config
- `hasStripeEnabled()` - checks if tenant has valid config
- `validateStripeCredentials()` - validates key/secret format

### Step 10: Add Database Seeders/Migrations
**File:** `database/migrations/tenant/add_stripe_webhook_config.php`

Optional: Add webhook secret storage per tenant:
- `payment_stripe_webhook_secret` field
- Allows webhook signature validation per tenant

## Database Schema (Already Exists in Portal Model)

```php
$table->string('payment_stripe_key', 255)->nullable();
$table->string('payment_stripe_secret', 255)->nullable();  // Encrypted
$table->string('payment_stripe_allow_card', 255)->nullable();
$table->string('payment_stripe_allow_multibanco', 255)->nullable();
$table->tinyInteger('payment_percentage')->default(100);
```

## Flow Diagrams

### Payment Intent Creation Flow
```
BookingWizard (Frontend)
    ↓
BookingController.index() → Load Portal config
    ↓ (pass to Blade as WizConfig)
booking-wizard.js → Reads CFG.stripeKey & CFG.paymentPercentage
    ↓
POST /booking/payment-intent
    ↓
BookingPaymentController.createPaymentIntent()
    ↓
StripeService.createPaymentIntent(tenant)
    ↓ (fetch Portal settings)
Stripe API (with tenant-specific credentials)
    ↓
Return client_secret
```

### Webhook Flow
```
Stripe Webhook → POST /booking/stripe/webhook
    ↓
BookingPaymentController.handleWebhook()
    ↓
StripeService.validateWebhook(tenant)
    ↓
BookingService.handlePaymentEvent()
    ↓
Update Booking status
```

## Configuration Priority (Fallback Chain)

```
1. Portal.payment_stripe_key
   ↓ (if not set, try)
2. config('services.stripe.key') from .env
   ↓ (if not set)
3. Return null + log warning
```

## Testing Strategy

1. **Unit Tests**
   - StripeService credential validation
   - BookingService payment intent creation
   - Portal configuration loading

2. **Integration Tests**
   - Payment intent creation with tenant context
   - Webhook signature validation
   - Cross-tenant isolation (tenant A can't use tenant B's settings)

3. **E2E Tests**
   - Full booking flow with payment
   - Webhook handling and booking confirmation

## Migration Strategy

### Phase 1: Non-Breaking (Current)
- Add StripeService alongside existing controller code
- Controllers can use either old or new approach
- Ensure feature flag or gradual rollout

### Phase 2: Controller Refactor
- Update BookingPaymentController to use StripeService
- Keep global config as fallback
- Add logging for debugging

### Phase 3: Frontend Update
- Update booking-wizard.js to use per-tenant config
- Test with multiple tenants

### Phase 4: Cleanup
- Remove any remaining hardcoded global references
- Document per-tenant override in admin docs

## Security Considerations

1. **Secret Storage**
   - Encrypt `payment_stripe_secret` in database (currently bcrypt - should be reversible)
   - Consider Laravel's encryption helper: `Crypt::encryptString()`

2. **Webhook Validation**
   - Always validate webhook signature with correct tenant's secret
   - Include tenant domain/id in webhook metadata

3. **Cross-Tenant Access**
   - Ensure tenant middleware prevents accessing other tenant's payments
   - Validate payment_intent_id belongs to current tenant

4. **Rate Limiting**
   - Add rate limiting to payment endpoints
   - Prevent brute force on webhook endpoint

## Rollback Plan

If issues arise:
1. Revert to global config using environment variables
2. Disable payment feature via `Portal.requires_payment = false`
3. Keep both code paths during transition

## Files to Create/Modify

### New Files
- `app/Services/StripeService.php`
- `app/Services/BookingService.php`
- `app/Helpers/StripeConfigHelper.php`
- `app/Http/Middleware/ValidateStripeConfiguration.php`

### Modified Files
- `app/Http/Controllers/BookingPaymentController.php`
- `app/Http/Controllers/BookingController.php`
- `app/Http/Controllers/PortalController.php`
- `public/assets/js/booking-wizard.js`
- `config/services.php`
- `.env.example`

### Optional
- `database/migrations/tenant/add_stripe_webhook_secret.php`
- Tests for all new services

## Expected Outcomes

✅ Per-tenant Stripe credentials  
✅ Per-tenant payment methods (card, MB WAY)  
✅ Per-tenant payment percentages  
✅ Webhook validation per tenant  
✅ Global fallback for backward compatibility  
✅ Cleaner, more maintainable code  
✅ Better security and isolation  

---

**Next Step:** Proceed to Step 1 (StripeService creation) with detailed code implementation.
