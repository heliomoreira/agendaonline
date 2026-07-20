<?php

namespace App\Services;

use App\Models\Agenda;
use App\Models\Service;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\BookingConfirmed;

class BookingService
{
    protected StripeService $stripeService;

    public function __construct(StripeService $stripeService)
    {
        $this->stripeService = $stripeService;
    }

    /**
     * Create a booking with payment intent
     */
    public function createBookingWithPayment(
        int $serviceId,
        ?int $professionalId,
        string $day,
        string $startHour,
        string $clientName,
        string $clientPhone,
        ?string $clientEmail = null
    ): array {
        $service = Service::findOrFail($serviceId);

        // Calculate amount to pay based on tenant percentage
        $paymentPercentage = $this->stripeService->getPaymentPercentage();
        $fullAmount = $service->price;
        $amountToPay = ($fullAmount * $paymentPercentage) / 100;

        // Create payment intent
        $paymentData = $this->stripeService->createPaymentIntent(
            $amountToPay,
            'eur',
            [
                'service_id' => $serviceId,
                'professional_id' => $professionalId,
                'day' => $day,
                'start_hour' => $startHour,
                'client_name' => $clientName,
                'client_phone' => $clientPhone,
                'client_email' => $clientEmail ?? '',
                'full_amount' => $fullAmount,
                'payment_percentage' => $paymentPercentage,
                'amount_paid' => $amountToPay,
            ],
            "Sinal {$paymentPercentage}%: {$service->name}",
            $clientEmail
        );

        return [
            'client_secret' => $paymentData['client_secret'],
            'payment_intent_id' => $paymentData['payment_intent_id'],
            'amount' => $amountToPay,
            'full_amount' => $fullAmount,
            'percentage' => $paymentPercentage,
            'payment_methods' => $paymentData['payment_methods'],
        ];
    }

    /**
     * Confirm a booking after payment
     */
    public function confirmBooking(
        int $serviceId,
        ?int $professionalId,
        string $day,
        string $startHour,
        string $clientName,
        string $clientPhone,
        ?string $clientEmail,
        string $paymentIntentId,
        string $paymentMethod = 'card'
    ): Agenda {
        try {
            // Create or update booking
            $booking = Agenda::updateOrCreate(
                ['payment_intent_id' => $paymentIntentId],
                [
                    'service_id' => $serviceId,
                    'professional_id' => $professionalId,
                    'day' => $day,
                    'start_hour' => $startHour,
                    'client_name' => $clientName,
                    'client_phone_1' => $clientPhone,
                    'client_email' => $clientEmail,
                    'status' => Agenda::STATUS_CONFIRMED,
                    'paid' => true,
                    'payment_method' => $paymentMethod,
                    'confirmed_at' => now(),
                ]
            );

            Log::info('Booking confirmed', [
                'booking_id' => $booking->id,
                'payment_intent_id' => $paymentIntentId,
            ]);

            // Send confirmation email
            try {
                if ($clientEmail) {
                    Mail::to($clientEmail)->send(new BookingConfirmed($booking));
                }
            } catch (\Exception $e) {
                Log::error('Failed to send confirmation email', [
                    'booking_id' => $booking->id,
                    'error' => $e->getMessage(),
                ]);
            }

            return $booking;
        } catch (\Exception $e) {
            Log::error('Booking confirmation failed', [
                'payment_intent_id' => $paymentIntentId,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Handle payment success (via webhook)
     */
    public function handlePaymentSuccess(string $paymentIntentId): Agenda
    {
        try {
            $paymentIntent = $this->stripeService->retrievePaymentIntent($paymentIntentId);

            if (!$paymentIntent) {
                throw new \Exception('Payment intent not found: ' . $paymentIntentId);
            }

            $metadata = $paymentIntent->metadata->toArray();

            return $this->confirmBooking(
                (int) $metadata['service_id'],
                $metadata['professional_id'] ? (int) $metadata['professional_id'] : null,
                $metadata['day'],
                $metadata['start_hour'],
                $metadata['client_name'],
                $metadata['client_phone'],
                $metadata['client_email'] ?? null,
                $paymentIntentId,
                $paymentIntent->payment_method_types[0] ?? 'card'
            );
        } catch (\Exception $e) {
            Log::error('Payment success handler failed', [
                'payment_intent_id' => $paymentIntentId,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Handle payment failure
     */
    public function handlePaymentFailed(string $paymentIntentId, string $reason = ''): void
    {
        try {
            $booking = Agenda::where('payment_intent_id', $paymentIntentId)->first();

            if ($booking) {
                $booking->update([
                    'status' => Agenda::STATUS_PAYMENT_FAILED,
                    'paid' => false,
                ]);

                Log::warning('Booking payment failed', [
                    'booking_id' => $booking->id,
                    'payment_intent_id' => $paymentIntentId,
                    'reason' => $reason,
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Payment failure handler failed', [
                'payment_intent_id' => $paymentIntentId,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Handle payment cancellation
     */
    public function handlePaymentCanceled(string $paymentIntentId): void
    {
        try {
            $booking = Agenda::where('payment_intent_id', $paymentIntentId)->first();

            if ($booking) {
                $booking->update([
                    'status' => Agenda::STATUS_CANCELLED,
                    'paid' => false,
                ]);

                Log::info('Booking payment canceled', [
                    'booking_id' => $booking->id,
                    'payment_intent_id' => $paymentIntentId,
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Payment cancellation handler failed', [
                'payment_intent_id' => $paymentIntentId,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
