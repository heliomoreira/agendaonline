<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Services\BookingService;
use App\Services\StripeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Stripe\Exception\SignatureVerificationException;

class BookingPaymentController extends Controller
{
    public function __construct(
        protected StripeService $stripeService,
        protected BookingService $bookingService
    ) {
    }

    /**
     * Create Payment Intent with support for MB WAY and Multibanco
     * Uses tenant-specific Stripe credentials from Portal model
     */
    public function createPaymentIntent(Request $request)
    {
        $request->validate([
            'service_id' => 'required|exists:services,id',
            'professional_id' => 'nullable|exists:professionals,id',
            'day' => 'required|date',
            'start_hour' => 'required',
            'client_name' => 'required|string',
            'client_phone_1' => 'required|string',
            'client_email' => 'nullable|email',
        ]);

        try {
            // Check if Stripe is configured for this tenant
            if (!$this->stripeService->isConfigured()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Payment processing is not configured for this tenant.',
                ], 503);
            }

            $service = Service::findOrFail($request->service_id);

            // Get payment percentage from tenant Portal configuration
            $paymentPercentage = $this->stripeService->getPaymentPercentage();
            $fullAmount = (float) $service->price;
            $amountToPay = ($fullAmount * $paymentPercentage) / 100;

            // Create payment intent using StripeService
            $paymentData = $this->stripeService->createPaymentIntent(
                $amountToPay,
                'eur',
                [
                    'service_id' => $request->service_id,
                    'professional_id' => $request->professional_id,
                    'day' => $request->day,
                    'start_hour' => $request->start_hour,
                    'client_name' => $request->client_name,
                    'client_phone' => $request->client_phone_1,
                    'client_email' => $request->client_email ?? '',
                    'full_amount' => $fullAmount,
                    'payment_percentage' => $paymentPercentage,
                    'amount_paid' => $amountToPay,
                ],
                "Sinal {$paymentPercentage}%: {$service->name}",
                $request->client_email ?? ''
            );

            return response()->json([
                'success' => true,
                'client_secret' => $paymentData['client_secret'],
                'payment_intent_id' => $paymentData['payment_intent_id'],
                'amount' => $amountToPay,
                'full_amount' => $fullAmount,
                'percentage' => $paymentPercentage,
                'payment_methods' => $paymentData['payment_methods'] ?? [],
            ]);
        } catch (\Exception $e) {
            Log::error('Payment Intent creation failed', [
                'tenant_id' => tenant()?->id,
                'service_id' => $request->service_id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro ao processar pagamento: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Webhook handler to receive Stripe notifications
     * IMPORTANT: Multibanco is asynchronous - payment confirms later
     */
    public function handleWebhook(Request $request)
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature', '');

        try {
            // Verify webhook with tenant-specific or global secret
            $event = $this->stripeService->verifyWebhook($payload, $sigHeader);

            $object = $event->data->object;

            Log::info('Stripe webhook received', [
                'event_type' => $event->type,
                'tenant_id' => $object->metadata->tenant_id ?? tenant()?->id,
                'tenant_domain' => $object->metadata->tenant_domain ?? null,
            ]);

            match ($event->type) {
                'payment_intent.succeeded' => $this->bookingService->handlePaymentSuccess(
                    $object->id
                ),
                'payment_intent.payment_failed' => $this->bookingService->handlePaymentFailed(
                    $object->id,
                    $object->last_payment_error->message ?? 'Unknown reason'
                ),
                'payment_intent.canceled' => $this->bookingService->handlePaymentCanceled(
                    $object->id
                ),
                default => Log::info('Unhandled Stripe webhook event', [
                    'event_type' => $event->type,
                ]),
            };

            return response()->json(['status' => 'success']);
        } catch (SignatureVerificationException | \UnexpectedValueException $e) {
            // Invalid signature or malformed payload: do NOT ask Stripe to retry
            Log::error('Stripe webhook validation failed', [
                'tenant_id' => tenant()?->id,
                'error' => $e->getMessage(),
            ]);

            return response()->json(['error' => $e->getMessage()], 400);
        } catch (\Exception $e) {
            // Transient/internal failure: return 5xx so Stripe retries
            Log::error('Stripe webhook processing failed', [
                'tenant_id' => tenant()?->id,
                'error' => $e->getMessage(),
            ]);

            return response()->json(['error' => 'Webhook processing failed'], 500);
        }
    }
}