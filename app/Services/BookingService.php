<?php

namespace App\Services;

use App\Models\Agenda;
use Illuminate\Support\Facades\Log;

class BookingService
{
    public function handlePaymentSuccess(string $paymentIntentId): void
    {
        $booking = Agenda::where('payment_intent_id', $paymentIntentId)->first();

        if (!$booking) {
            Log::warning('Stripe payment_intent.succeeded for unknown booking', [
                'payment_intent_id' => $paymentIntentId,
            ]);

            return;
        }

        $booking->markAsPaid($paymentIntentId);
    }

    public function handlePaymentFailed(string $paymentIntentId, ?string $reason = null): void
    {
        $booking = Agenda::where('payment_intent_id', $paymentIntentId)->first();

        if (!$booking) {
            return;
        }

        $booking->update(['status' => Agenda::STATUS_PAYMENT_FAILED]);
    }

    public function handlePaymentCanceled(string $paymentIntentId): void
    {
        $booking = Agenda::where('payment_intent_id', $paymentIntentId)->first();

        if (!$booking) {
            return;
        }

        $booking->cancel('Pagamento cancelado', 'system');
    }
}
