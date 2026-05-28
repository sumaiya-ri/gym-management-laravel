<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\Gym;
use App\Models\Booking;
use App\Models\User;
use App\Services\StripeService;
use App\Services\MongoDBService;
use Stripe\Webhook;
use Stripe\Exception\SignatureVerificationException;
use Carbon\Carbon;
use Illuminate\Support\Str;

class StripeWebhookController extends Controller
{
    protected $stripeService;

    public function __construct(StripeService $stripeService)
    {
        $this->stripeService = $stripeService;
    }

    /**
     * Handle incoming Stripe webhook requests.
     */
    public function handleWebhook(Request $request)
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $webhookSecret = config('services.stripe.webhook_secret');

        Log::info("Stripe Webhook received.");

        $event = null;

        try {
            if ($webhookSecret && $sigHeader) {
                $event = Webhook::constructEvent($payload, $sigHeader, $webhookSecret);
            } else {
                // Signature verification bypassed in local testing or when webhook secret is missing
                $event = json_decode($payload);
            }
        } catch (\UnexpectedValueException $e) {
            Log::error("Stripe Webhook: invalid payload.");
            return response()->json(['error' => 'Invalid payload'], 400);
        } catch (SignatureVerificationException $e) {
            Log::error("Stripe Webhook: signature verification failed. " . $e->getMessage());
            return response()->json(['error' => 'Invalid signature'], 400);
        } catch (\Exception $e) {
            Log::error("Stripe Webhook signature check failed: " . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 400);
        }

        $eventType = isset($event->type) ? $event->type : null;
        $eventData = isset($event->data->object) ? $event->data->object : null;

        if (!$eventType || !$eventData) {
            return response()->json(['error' => 'Event type or data missing'], 400);
        }

        Log::info("Stripe Webhook event type: {$eventType}");

        switch ($eventType) {
            case 'checkout.session.completed':
                return $this->handleCheckoutSessionCompleted($eventData);

            case 'payment_intent.succeeded':
                Log::info("Stripe Webhook payment_intent.succeeded received for ID: " . ($eventData->id ?? 'unknown'));
                break;

            case 'payment_intent.payment_failed':
                return $this->handlePaymentIntentFailed($eventData);
        }

        return response()->json(['status' => 'success']);
    }

    /**
     * Handle checkout.session.completed Stripe event.
     */
    protected function handleCheckoutSessionCompleted($session)
    {
        $metadata = $session->metadata ?? null;
        $type = $metadata->type ?? null;

        Log::info("Stripe Webhook: handling checkout.session.completed. Type: {$type}");

        if ($type === 'subscription') {
            return $this->activateSubscription($session);
        } elseif ($type === 'booking') {
            return $this->confirmBooking($session);
        }

        Log::warning("Stripe Webhook: unknown metadata type '{$type}' for session: " . ($session->id ?? 'unknown'));
        return response()->json(['status' => 'ignored']);
    }

    /**
     * Activate SaaS gym subscription from Checkout Session.
     */
    protected function activateSubscription($session)
    {
        $gymId = $session->metadata->gym_id ?? null;
        $gym = Gym::find($gymId);

        if (!$gym) {
            Log::error("Stripe Webhook activateSubscription: Gym not found for ID: {$gymId}");
            return response()->json(['error' => 'Gym not found'], 404);
        }

        if ($gym->subscription_status === 'active') {
            Log::info("Stripe Webhook activateSubscription: Gym {$gym->id} is already active.");
            return response()->json(['status' => 'already_active']);
        }

        $plan = $gym->subscription_plan ?? 'Starter';
        $plans = [
            'Starter' => 29.00,
            'Professional' => 59.00,
            'Enterprise' => 99.00,
        ];
        $price = $plans[$plan] ?? 29.00;
        $transactionId = $session->subscription ?? ('SUB-' . strtoupper(Str::random(10)));

        DB::beginTransaction();
        try {
            $gym->update([
                'subscription_status' => 'active',
                'subscription_expires_at' => Carbon::now()->addMonth(),
                'subscription_transaction_id' => $transactionId,
                'stripe_session_id' => $session->id,
                'payment_method' => 'stripe',
                'transaction_reference' => $session->payment_intent ?? $session->id,
                'amount_paid' => $session->amount_total ? ($session->amount_total / 100) : $price,
                'payment_at' => now(),
            ]);

            // MongoDB analytics logging
            MongoDBService::collection('subscription_analytics')->insertOne([
                'gym_id' => $gym->id,
                'gym_name' => $gym->name,
                'plan' => $plan,
                'price' => $price,
                'status' => 'active',
                'transaction_id' => $transactionId,
                'created_at' => now()->toDateTimeString(),
            ]);

            MongoDBService::collection('gym_revenue_analytics')->insertOne([
                'gym_id' => $gym->id,
                'gym_name' => $gym->name,
                'amount' => $price,
                'transaction_id' => $transactionId,
                'type' => 'subscription',
                'created_at' => now()->toDateTimeString(),
            ]);

            MongoDBService::collection('platform_growth_metrics')->insertOne([
                'gyms_count' => Gym::count(),
                'members_count' => User::where('role', 'member')->count(),
                'trainers_count' => User::where('role', 'trainer')->count(),
                'bookings_count' => Booking::count(),
                'created_at' => now()->toDateTimeString(),
            ]);

            DB::commit();

            // Send subscription emails with non-blocking try/catch
            $adminUser = User::where('gym_id', $gym->id)->where('role', 'admin')->first();
            try {
                if ($adminUser) {
                    \App\Jobs\SendSubscriptionConfirmationEmail::dispatch($gym, $adminUser);
                }
            } catch (\Exception $e) {
                Log::error("Stripe Webhook: failed to dispatch subscription confirmation email: " . $e->getMessage());
            }

            try {
                \App\Jobs\SendSuperAdminSubscriptionNotificationEmail::dispatch($gym);
            } catch (\Exception $e) {
                Log::error("Stripe Webhook: failed to dispatch super admin subscription notification: " . $e->getMessage());
            }

            Log::info("Stripe Webhook activateSubscription: Gym {$gym->id} activated successfully.");
            return response()->json(['status' => 'activated']);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Stripe Webhook activateSubscription error: " . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Confirm member class booking from Checkout Session.
     */
    protected function confirmBooking($session)
    {
        $bookingId = $session->metadata->booking_id ?? null;
        $booking = Booking::with('timeslot')->find($bookingId);

        if (!$booking) {
            Log::error("Stripe Webhook confirmBooking: Booking not found for ID: {$bookingId}");
            return response()->json(['error' => 'Booking not found'], 404);
        }

        if ($booking->payment_status === 'paid' && $booking->status === 'confirmed') {
            Log::info("Stripe Webhook confirmBooking: Booking {$booking->id} is already confirmed and paid.");
            return response()->json(['status' => 'already_confirmed']);
        }

        $timeslot = $booking->timeslot;
        if (!$timeslot) {
            Log::error("Stripe Webhook confirmBooking: Timeslot not found for Booking ID: {$bookingId}");
            return response()->json(['error' => 'Timeslot not found'], 404);
        }

        $transactionId = $session->payment_intent ?? ('TXN-' . strtoupper(Str::random(10)));
        $amountPaid = $session->amount_total ? ($session->amount_total / 100) : ($booking->payment_amount ?? 25.00);

        DB::beginTransaction();
        try {
            $booking->update([
                'status' => 'confirmed',
                'payment_status' => 'paid',
                'payment_transaction_id' => $transactionId,
                'stripe_session_id' => $session->id,
                'payment_method' => 'stripe',
                'transaction_reference' => $transactionId,
                'amount_paid' => $amountPaid,
                'payment_at' => now(),
            ]);

            // Decrement capacity
            $timeslot->decrement('capacity');

            DB::commit();

            // Send booking emails with non-blocking try/catch
            try {
                \App\Jobs\SendBookingConfirmationEmail::dispatch($booking);
            } catch (\Exception $e) {
                Log::error("Stripe Webhook: failed to dispatch booking confirmation email: " . $e->getMessage());
            }

            try {
                \App\Jobs\SendAdminBookingNotificationEmail::dispatch($booking);
            } catch (\Exception $e) {
                Log::error("Stripe Webhook: failed to dispatch admin booking notification: " . $e->getMessage());
            }

            Log::info("Stripe Webhook confirmBooking: Booking {$booking->id} confirmed successfully.");
            return response()->json(['status' => 'confirmed']);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Stripe Webhook confirmBooking error: " . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Handle payment intent failures.
     */
    protected function handlePaymentIntentFailed($paymentIntent)
    {
        Log::error("Stripe Webhook: payment intent failed for ID: " . ($paymentIntent->id ?? 'unknown'));
        return response()->json(['status' => 'logged_failure']);
    }
}
