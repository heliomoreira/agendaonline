<?php

namespace App\Http\Controllers;

use App\Models\Agenda;
use App\Models\Service;
use App\Services\StripeService;
use App\Services\BookingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class BookingPaymentController extends Controller
{
    protected StripeService $stripeService;
    protected BookingService $bookingService;

    public function __construct(StripeService $stripeService, BookingService $bookingService)
    {
        $this->stripeService = $stripeService;
        $this->bookingService = $bookingService;
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
            // Check if Stripe is configured
            if (!$this->stripeService->isConfigured()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Payment processing is not configured for this tenant.',
                ], 503);
            }

            $service = Service::findOrFail($request->service_id);

            // Get payment percentage from tenant Portal configuration
            $paymentPercentage = $this->stripeService->getPaymentPercentage();
            $fullAmount = $service->price;
            $amountToPay = ($fullAmount * $paymentPercentage) / 100;

            // Create payment intent using StripeService
            $paymentData = $this->stripeService->createPaymentIntent(
                $amountToPay,
                null,
                [
                    'service_id' => $request->service_id,
                    'professional_id' => $request->professional_id,
                    'day' => $request->day,
                    'start_hour' => $request->start_hour,
                    'client_name' => $request->client_name,
                    'client_phone_1' => $request->client_phone_1,
                    'client_email' => $request->client_email ?? '',
                    'full_amount' => $fullAmount,
                    'payment_percentage' => $paymentPercentage,
                    'amount_paid' => $amountToPay,
                ],
                "Sinal {$paymentPercentage}%: {$service->name}",
                $request->client_email
            );

            return response()->json([
                'success' => true,
                'client_secret' => $paymentData['client_secret'],
                'payment_intent_id' => $paymentData['payment_intent_id'],
                'amount' => $amountToPay,
                'full_amount' => $fullAmount,
                'percentage' => $paymentPercentage,
                'payment_methods' => $paymentData['payment_methods'],
            ]);

        } catch (\Exception $e) {
            Log::error('Payment Intent creation failed', [
                'tenant_id' => tenant()?->id,
                'error' => $e->getMessage(),
                'request' => $request->all(),
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
        $sig_header = $request->header('Stripe-Signature');

        try {
            // Verify webhook with tenant-specific or global secret
            $event = $this->stripeService->verifyWebhook($payload, $sig_header);

            // Extract tenant info from webhook metadata for logging
            $tenantId = $event->data?->object?->metadata?->tenant_id;
            $tenantDomain = $event->data?->object?->metadata?->tenant_domain;

            Log::info('Stripe webhook received', [
                'event_type' => $event->type,
                'tenant_id' => $tenantId,
                'tenant_domain' => $tenantDomain,
            ]);

            // Handle the event
            match ($event->type) {
                'payment_intent.succeeded' => $this->handlePaymentSuccess($event->data->object),
                'payment_intent.payment_failed' => $this->handlePaymentFailed($event->data->object),
                'payment_intent.canceled' => $this->handlePaymentCanceled($event->data->object),
                default => Log::info('Unhandled Stripe event: ' . $event->type),
            };

            return response()->json(['status' => 'success']);

        } catch (\Exception $e) {
            Log::error('Webhook processing failed', [
                'error' => $e->getMessage(),
                'tenant_id' => tenant()?->id,
            ]);

            return response()->json([
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Handler when payment succeeds (via webhook)
     * Called by webhook for Multibanco/MB WAY
     */
    protected function handlePaymentSuccess($paymentIntent)
    {
        try {
            $paymentIntentId = $paymentIntent->id;
            $metadata = $paymentIntent->metadata?->toArray() ?? [];

            Log::info('Payment succeeded', [
                'payment_intent_id' => $paymentIntentId,
                'metadata' => $metadata,
            ]);

            // Use BookingService to handle confirmation
            $this->bookingService->handlePaymentSuccess($paymentIntentId);

        } catch (\Exception $e) {
            Log::error('Payment success handler failed', [
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Handler when payment fails
     */
    protected function handlePaymentFailed($paymentIntent)
    {
        try {
            $paymentIntentId = $paymentIntent->id;
            $failureReason = $paymentIntent->last_payment_error?->message ?? 'Unknown reason';

            Log::warning('Payment failed', [
                'payment_intent_id' => $paymentIntentId,
                'reason' => $failureReason,
            ]);

            // Use BookingService to handle failure
            $this->bookingService->handlePaymentFailed($paymentIntentId, $failureReason);

        } catch (\Exception $e) {
            Log::error('Payment failure handler failed', [
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Handler when payment is canceled
     */
    protected function handlePaymentCanceled($paymentIntent)
    {
        try {
            $paymentIntentId = $paymentIntent->id;

            Log::info('Payment canceled', [
                'payment_intent_id' => $paymentIntentId,
            ]);

            // Use BookingService to handle cancellation
            $this->bookingService->handlePaymentCanceled($paymentIntentId);

        } catch (\Exception $e) {
            Log::error('Payment cancellation handler failed', [
                'error' => $e->getMessage(),
            ]);
        }
    }
}
