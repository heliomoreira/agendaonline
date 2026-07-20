<?php

namespace App\Http\Controllers;

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
                'eur',
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
                'client_secret' => $paymentData['client_secret'],
                'payment_intent_id' => $paymentData['payment_intent_id'],
                'amount' => $amountToPay,
                'full_amount' => $fullAmount,
                'percentage' => $paymentPercentage,
            ]);

        } catch (\Exception $e) {
            Log::error('Payment intent creation failed', [
                'error' => $e->getMessage(),
                'service_id' => $request->service_id,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro ao processar pagamento: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Webhook handler para receber notificações da Stripe
     * IMPORTANTE: Multibanco é assíncrono - pagamento confirma depois
     */
    public function handleWebhook(Request $request)
    {
        try {
            // Verify webhook with tenant-specific secret
            $event = $this->stripeService->verifyWebhook(
                $request->getContent(),
                $request->header('Stripe-Signature')
            );
        } catch (\Exception $e) {
            Log::error('Webhook verification failed', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Invalid webhook'], 400);
        }

        // Handle the event
        try {
            switch ($event->type) {
                case 'payment_intent.succeeded':
                    $paymentIntent = $event->data->object;
                    $this->bookingService->handlePaymentSuccess($paymentIntent->id);
                    break;

                case 'payment_intent.payment_failed':
                    $paymentIntent = $event->data->object;
                    $this->bookingService->handlePaymentFailed($paymentIntent->id, $paymentIntent->last_payment_error?->message);
                    break;

                case 'payment_intent.canceled':
                    $paymentIntent = $event->data->object;
                    $this->bookingService->handlePaymentCanceled($paymentIntent->id);
                    break;

                default:
                    Log::info('Unhandled Stripe event: ' . $event->type);
            }
        } catch (\Exception $e) {
            Log::error('Webhook handler error', [
                'event_type' => $event->type,
                'error' => $e->getMessage(),
            ]);
        }

        return response()->json(['status' => 'success']);
    }
}
