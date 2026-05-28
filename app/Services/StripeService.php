<?php

namespace App\Services;

use Stripe\Stripe;
use Stripe\Checkout\Session as StripeSession;
use App\Models\Gym;
use App\Models\Booking;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class StripeService
{
    public function __construct()
    {
        $secret = config('services.stripe.secret');
        if (!empty($secret)) {
            Stripe::setApiKey($secret);
        }
    }

    /**
     * Check if Stripe integration is enabled (i.e. secret key configured).
     */
    public function isEnabled(): bool
    {
        $secret = config('services.stripe.secret');
        return !empty($secret);
    }

    /**
     * Create Stripe Checkout Session for SaaS subscription.
     */
    public function createSubscriptionSession(Gym $gym, User $adminUser, string $successUrl, string $cancelUrl): ?StripeSession
    {
        if (!$this->isEnabled()) {
            return null;
        }

        $plans = [
            'Starter' => 29.00,
            'Professional' => 59.00,
            'Enterprise' => 99.00,
        ];
        $plan = $gym->subscription_plan ?? 'Starter';
        $price = $plans[$plan] ?? 29.00;

        try {
            return StripeSession::create([
                'payment_method_types' => ['card'],
                'line_items' => [[
                    'price_data' => [
                        'currency' => 'usd',
                        'product_data' => [
                            'name' => "GlowGym {$plan} Subscription Plan",
                            'description' => "Monthly access for {$gym->name}",
                        ],
                        'unit_amount' => (int)($price * 100),
                        'recurring' => [
                            'interval' => 'month',
                        ],
                    ],
                    'quantity' => 1,
                ]],
                'mode' => 'subscription',
                'success_url' => $successUrl . '?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => $cancelUrl,
                'client_reference_id' => (string)$gym->id,
                'metadata' => [
                    'gym_id' => $gym->id,
                    'type' => 'subscription',
                    'admin_user_id' => $adminUser->id,
                ],
            ]);
        } catch (\Exception $e) {
            Log::error("Stripe createSubscriptionSession error: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Create Stripe Checkout Session for Member booking.
     */
    public function createBookingSession(Booking $booking, string $successUrl, string $cancelUrl): ?StripeSession
    {
        if (!$this->isEnabled()) {
            return null;
        }

        $timeslot = $booking->timeslot;
        if (!$timeslot) {
            return null;
        }

        $price = $booking->payment_amount ?? 25.00;

        try {
            return StripeSession::create([
                'payment_method_types' => ['card'],
                'line_items' => [[
                    'price_data' => [
                        'currency' => 'usd',
                        'product_data' => [
                            'name' => "Class Booking: " . ($timeslot->service->name ?? 'Class'),
                            'description' => "Trainer: " . ($timeslot->trainer->name ?? 'Instructor') . " | Schedule: {$timeslot->date} " . $timeslot->start_time,
                        ],
                        'unit_amount' => (int)($price * 100),
                    ],
                    'quantity' => 1,
                ]],
                'mode' => 'payment',
                'success_url' => $successUrl . '?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => $cancelUrl,
                'client_reference_id' => (string)$booking->id,
                'metadata' => [
                    'booking_id' => $booking->id,
                    'type' => 'booking',
                    'user_id' => $booking->user_id,
                ],
            ]);
        } catch (\Exception $e) {
            Log::error("Stripe createBookingSession error: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Retrieve a Stripe Checkout Session.
     */
    public function retrieveSession(string $sessionId): ?StripeSession
    {
        if (!$this->isEnabled()) {
            return null;
        }

        try {
            return StripeSession::retrieve($sessionId);
        } catch (\Exception $e) {
            Log::error("Stripe retrieveSession error: " . $e->getMessage());
            return null;
        }
    }
}
