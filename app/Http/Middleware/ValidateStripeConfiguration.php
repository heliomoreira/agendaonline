<?php

namespace App\Http\Middleware;

use App\Helpers\StripeConfigHelper;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ValidateStripeConfiguration
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Only validate if payment is required for this tenant
        $portal = \App\Models\Portal::first();

        if ($portal && $portal->requires_payment && !$portal->enable_booking) {
            return response()->json([
                'success' => false,
                'message' => 'Booking is not enabled for this portal.',
            ], 403);
        }

        if ($portal && $portal->requires_payment) {
            if (!StripeConfigHelper::isConfigured()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Payment processing is not configured for this portal. Please contact the administrator.',
                ], 503);
            }

            $validation = StripeConfigHelper::validate();
            if (!$validation['valid']) {
                \Log::error('Stripe configuration invalid', $validation);
                return response()->json([
                    'success' => false,
                    'message' => 'Payment processing configuration is invalid. Please contact the administrator.',
                ], 503);
            }
        }

        return $next($request);
    }
}
