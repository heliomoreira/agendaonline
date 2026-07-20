<?php

namespace App\Http\Controllers;

use App\Services\BookingService;
use App\Models\Service;
use App\Services\StripeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class BookingPaymentController extends Controller
{
    public function __construct(
        protected StripeService $stripeService,
        protected BookingService $bookingService
    ) {
    }

    /**
     * Criar Payment Intent com suporte para MB WAY e Multibanco
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
            if (!$this->stripeService->isConfigured()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Payment processing is not configured for this tenant.',
                ], 503);
            }

            $service = Service::findOrFail($request->service_id);
            $paymentPercentage = $this->stripeService->getPaymentPercentage();
            $fullAmount = (float) $service->price;
            $amountToPay = ($fullAmount * $paymentPercentage) / 100;

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
            Log::error('Erro ao processar pagamento', [
                'tenant_id' => tenant()?->id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro ao processar pagamento: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Webhook handler para receber notificações da Stripe
     * IMPORTANTE: Multibanco é assíncrono - pagamento confirma depois
     */
    public function handleWebhook(Request $request)
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature', '');

        try {
            $event = $this->stripeService->verifyWebhook($payload, $sigHeader);

            switch ($event->type) {
                case 'payment_intent.succeeded':
                    $this->bookingService->handlePaymentSuccess($event->data->object->id);
                    break;

                case 'payment_intent.payment_failed':
                    $failureMessage = $event->data->object->last_payment_error->message ?? '';
                    $this->bookingService->handlePaymentFailed($event->data->object->id, $failureMessage);
                    break;

                case 'payment_intent.canceled':
                    $this->bookingService->handlePaymentCanceled($event->data->object->id);
                    break;

                default:
                    Log::info('Unhandled Stripe webhook event', ['event_type' => $event->type]);
            }

            return response()->json(['status' => 'success']);
        } catch (\Exception $e) {
            Log::error('Erro ao processar webhook Stripe', [
                'tenant_id' => tenant()?->id,
                'error' => $e->getMessage(),
            ]);

            if (str_contains(strtolower($e->getMessage()), 'signature') || str_contains(strtolower($e->getMessage()), 'payload')) {
                return response()->json(['error' => $e->getMessage()], 400);
            }

            return response()->json([
                'error' => 'Webhook processing failed',
            ], 500);
        }
    }
}
