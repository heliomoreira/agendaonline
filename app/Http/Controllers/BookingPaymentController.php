<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\PaymentIntent;

class BookingPaymentController extends Controller
{
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

        $service = Service::findOrFail($request->service_id);

        // Percentagem a cobrar (hardcoded por agora, depois vem de config)
        $paymentPercentage = 50; // 50% do valor total

        // Calcular valor a pagar
        $fullAmount = $service->price;
        $amountToPay = ($fullAmount * $paymentPercentage) / 100;
        $amountInCents = (int) ($amountToPay * 100); // converter para cêntimos

        Stripe::setApiKey(config('services.stripe.secret'));

        try {
            $paymentIntent = PaymentIntent::create([
                'amount' => $amountInCents,
                'currency' => 'eur',
                'payment_method_types' => [
                    'card',
                    'multibanco',
                ],
                'metadata' => [
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
                'description' => "Sinal {$paymentPercentage}%: {$service->name}",
                'receipt_email' => $request->client_email,
            ]);

            return response()->json([
                'client_secret' => $paymentIntent->client_secret,
                'payment_intent_id' => $paymentIntent->id,
                'amount' => $amountToPay,
                'full_amount' => $fullAmount,
                'percentage' => $paymentPercentage,
            ]);

        } catch (\Exception $e) {
            return response()->json([
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
        $endpoint_secret = config('services.stripe.webhook_secret');
        $payload = $request->getContent();
        $sig_header = $request->header('Stripe-Signature');

        try {
            $event = \Stripe\Webhook::constructEvent(
                $payload,
                $sig_header,
                $endpoint_secret
            );
        } catch (\UnexpectedValueException $e) {
            return response()->json(['error' => 'Invalid payload'], 400);
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            return response()->json(['error' => 'Invalid signature'], 400);
        }

        // Handle the event
        switch ($event->type) {
            case 'payment_intent.succeeded':
                $paymentIntent = $event->data->object;
                $this->handlePaymentSuccess($paymentIntent);
                break;

            case 'payment_intent.payment_failed':
                $paymentIntent = $event->data->object;
                $this->handlePaymentFailed($paymentIntent);
                break;

            case 'payment_intent.canceled':
                $paymentIntent = $event->data->object;
                $this->handlePaymentCanceled($paymentIntent);
                break;

            default:
                \Log::info('Unhandled Stripe event: ' . $event->type);
        }

        return response()->json(['status' => 'success']);
    }

    /**
     * Handler quando pagamento é bem-sucedido
     * Chamado pelo webhook para Multibanco/MB WAY
     */
    protected function handlePaymentSuccess($paymentIntent)
    {
        \Log::info('Payment succeeded', ['payment_intent' => $paymentIntent->id]);

        // Buscar ou criar marcação
        $booking = \App\Models\Booking::firstOrCreate(
            ['payment_intent_id' => $paymentIntent->id],
            [
                'service_id' => $paymentIntent->metadata->service_id,
                'professional_id' => $paymentIntent->metadata->professional_id ?? null,
                'day' => $paymentIntent->metadata->day,
                'start_hour' => $paymentIntent->metadata->start_hour,
                'client_name' => $paymentIntent->metadata->client_name,
                'client_phone_1' => $paymentIntent->metadata->client_phone_1,
                'client_email' => $paymentIntent->metadata->client_email,
                'status' => 'confirmed',
                'paid' => true,
                'payment_method' => $paymentIntent->payment_method_types[0] ?? 'card',
            ]
        );

        // Se já existia, actualizar status
        if (!$booking->wasRecentlyCreated) {
            $booking->update([
                'status' => 'confirmed',
                'paid' => true,
            ]);
        }

        // Enviar email de confirmação
        try {
            \Mail::to($booking->client_email)
                ->send(new \App\Mail\BookingConfirmed($booking));
        } catch (\Exception $e) {
            \Log::error('Failed to send confirmation email', [
                'booking_id' => $booking->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Handler quando pagamento falha
     */
    protected function handlePaymentFailed($paymentIntent)
    {
        \Log::warning('Payment failed', ['payment_intent' => $paymentIntent->id]);

        $booking = \App\Models\Booking::where('payment_intent_id', $paymentIntent->id)->first();
        if ($booking) {
            $booking->update([
                'status' => 'payment_failed',
                'paid' => false,
            ]);
        }
    }

    /**
     * Handler quando pagamento é cancelado
     */
    protected function handlePaymentCanceled($paymentIntent)
    {
        \Log::info('Payment canceled', ['payment_intent' => $paymentIntent->id]);

        $booking = \App\Models\Booking::where('payment_intent_id', $paymentIntent->id)->first();
        if ($booking) {
            $booking->update([
                'status' => 'canceled',
                'paid' => false,
            ]);
        }
    }
}
