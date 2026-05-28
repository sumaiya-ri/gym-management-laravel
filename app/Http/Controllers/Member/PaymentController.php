<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Timeslot;
use App\Models\Booking;
use App\Services\StripeService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Jobs\SendBookingConfirmationEmail;
use App\Jobs\SendAdminBookingNotificationEmail;

class PaymentController extends Controller
{
    protected $stripeService;

    public function __construct(StripeService $stripeService)
    {
        $this->stripeService = $stripeService;
    }

    /**
     * Show the simulated payment page.
     */
    public function showCheckout(int $timeslotId)
    {
        $gymId = auth()->user()->gym_id;

        // Retrieve timeslot and check gym isolation
        $timeslot = Timeslot::with(['service', 'trainer'])
            ->where('id', $timeslotId)
            ->where('gym_id', $gymId)
            ->firstOrFail();

        // Check if class is full
        if ($timeslot->capacity <= 0) {
            return redirect()->route('member.classes')->with('error', 'This class is already full.');
        }

        // Check if class is in the past
        if ($timeslot->date < Carbon::today()->toDateString()) {
            return redirect()->route('member.classes')->with('error', 'Cannot book past classes.');
        }

        // Prevent duplicate bookings
        $exists = Booking::where('user_id', auth()->id())
            ->where('timeslot_id', $timeslotId)
            ->where('status', '!=', 'cancelled')
            ->exists();

        if ($exists) {
            return redirect()->route('member.classes')->with('error', 'You have already booked this class.');
        }

        $price = 25.00; // Standard simulated flat booking rate
        $stripeEnabled = $this->stripeService->isEnabled();

        return view('member.payment.checkout', compact('timeslot', 'price', 'stripeEnabled'));
    }

    /**
     * Process the simulated credit card payment and secure the booking.
     */
    public function processPayment(Request $request, int $timeslotId)
    {
        $gymId = auth()->user()->gym_id;

        // Retrieve timeslot and enforce gym isolation
        $timeslot = Timeslot::where('id', $timeslotId)
            ->where('gym_id', $gymId)
            ->firstOrFail();

        // Perform security / validation checks
        if ($timeslot->capacity <= 0) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'This class is already full.'], 422);
            }
            return redirect()->route('member.classes')->with('error', 'This class is already full.');
        }

        $exists = Booking::where('user_id', auth()->id())
            ->where('timeslot_id', $timeslotId)
            ->where('status', '!=', 'cancelled')
            ->exists();

        if ($exists) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'You have already booked this class.'], 422);
            }
            return redirect()->route('member.classes')->with('error', 'You have already booked this class.');
        }

        $price = 25.00;

        if ($this->stripeService->isEnabled()) {
            try {
                $booking = Booking::create([
                    'gym_id' => auth()->user()->gym_id,
                    'user_id' => auth()->id(),
                    'timeslot_id' => $timeslot->id,
                    'booking_date' => now(),
                    'status' => 'pending',
                    'payment_status' => 'pending',
                    'payment_amount' => $price,
                ]);

                $successUrl = route('member.payment.success', $booking->id);
                $cancelUrl = route('member.payment.checkout', $timeslot->id);
                $session = $this->stripeService->createBookingSession($booking, $successUrl, $cancelUrl);

                $booking->update([
                    'stripe_session_id' => $session->id,
                ]);

                if ($request->expectsJson()) {
                    return response()->json([
                        'checkout_url' => $session->url,
                    ], 200);
                }

                return redirect()->away($session->url);
            } catch (\Exception $e) {
                Log::error("Booking Stripe checkout initiation failed: " . $e->getMessage());
                if (isset($booking)) {
                    $booking->delete();
                }
                if ($request->expectsJson()) {
                    return response()->json(['error' => 'Could not initiate Stripe payment.'], 500);
                }
                return back()->with('error', 'Could not initiate secure Stripe checkout. Please try again.');
            }
        }

        // Validate the fake payment card input details
        $request->validate([
            'cardholder_name' => 'required|string|max:255',
            'card_number' => ['required', 'string', 'regex:/^[0-9]{16}$/'],
            'expiry' => ['required', 'string', 'regex:/^(0[1-9]|1[0-2])\/[0-9]{2}$/'],
            'cvv' => ['required', 'string', 'regex:/^[0-9]{3,4}$/'],
        ], [
            'card_number.regex' => 'The card number must be exactly 16 digits.',
            'expiry.regex' => 'Expiry date must be in the format MM/YY.',
            'cvv.regex' => 'CVV must be 3 or 4 digits.',
        ]);

        $price = 25.00;

        // Simulate IPG Payment Gateway failure if user passes '999' as CVV
        if ($request->cvv === '999') {
            Log::error("Payment transaction failed: Simulated IPG failure for User ID: " . auth()->id() . " on Timeslot ID: " . $timeslotId);
            
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Payment transaction was declined by the simulated gateway.'], 402);
            }
            return redirect()->route('member.payment.failed', $timeslotId);
        }

        // Generate simulated successful Transaction ID
        $transactionId = 'TXN-' . strtoupper(Str::random(10));

        // Create booking & decrement capacity under DB transaction
        try {
            $booking = DB::transaction(function () use ($timeslot, $price, $transactionId) {
                $booking = Booking::create([
                    'gym_id' => auth()->user()->gym_id,
                    'user_id' => auth()->id(),
                    'timeslot_id' => $timeslot->id,
                    'booking_date' => now(),
                    'status' => 'confirmed',
                    'payment_status' => 'paid',
                    'payment_amount' => $price,
                    'payment_transaction_id' => $transactionId,
                ]);

                $timeslot->decrement('capacity');
                return $booking;
            });

            // Activity Logs
            Log::info("Payment successful. Transaction ID: {$transactionId}. Amount: ${price} for User: " . auth()->user()->email);
            Log::info("Booking ID: {$booking->id} created successfully for Timeslot ID: {$timeslotId}");

            // Dispatch Email Queue Jobs asynchronously with try/catch to avoid blocking the redirect
            try {
                SendBookingConfirmationEmail::dispatch($booking);
            } catch (\Exception $e) {
                Log::error("Failed to dispatch booking confirmation email for Booking ID {$booking->id}: " . $e->getMessage());
            }

            try {
                SendAdminBookingNotificationEmail::dispatch($booking);
            } catch (\Exception $e) {
                Log::error("Failed to dispatch admin booking notification email for Booking ID {$booking->id}: " . $e->getMessage());
            }

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Payment successful and class booked.',
                    'booking' => $booking->load(['timeslot.service', 'timeslot.trainer'])
                ], 201);
            }

            return redirect()->route('member.payment.success', $booking->id);

        } catch (\Exception $e) {
            Log::error("Booking transaction failed: " . $e->getMessage());

            if ($request->expectsJson()) {
                return response()->json(['error' => 'Internal server error processing booking.'], 500);
            }
            return back()->with('error', 'Booking transaction could not be processed.');
        }
    }

    /**
     * Show simulated successful payment confirmation.
     */
    public function showSuccess(Request $request, int $bookingId)
    {
        $booking = Booking::with(['timeslot.service', 'timeslot.trainer', 'gym'])
            ->where('id', $bookingId)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        if ($request->has('session_id') && $this->stripeService->isEnabled()) {
            $sessionId = $request->query('session_id');
            $session = $this->stripeService->retrieveSession($sessionId);

            if ($session && $session->payment_status === 'paid' && $booking->status !== 'confirmed') {
                $timeslot = $booking->timeslot;
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

                    $timeslot->decrement('capacity');

                    DB::commit();

                    // Send emails with non-blocking try/catch
                    try {
                        SendBookingConfirmationEmail::dispatch($booking);
                    } catch (\Exception $e) {
                        Log::error("Failed to dispatch booking confirmation email: " . $e->getMessage());
                    }

                    try {
                        SendAdminBookingNotificationEmail::dispatch($booking);
                    } catch (\Exception $e) {
                        Log::error("Failed to dispatch admin booking notification email: " . $e->getMessage());
                    }
                } catch (\Exception $e) {
                    DB::rollBack();
                    Log::error("Stripe Booking Redirect Confirmation failed: " . $e->getMessage());
                }
            }
        }

        return view('member.payment.success', compact('booking'));
    }

    /**
     * Show simulated failed payment notification.
     */
    public function showFailed(int $timeslotId)
    {
        $timeslot = Timeslot::with(['service', 'trainer'])
            ->where('id', $timeslotId)
            ->where('gym_id', auth()->user()->gym_id)
            ->firstOrFail();

        return view('member.payment.failed', compact('timeslot'));
    }
}
