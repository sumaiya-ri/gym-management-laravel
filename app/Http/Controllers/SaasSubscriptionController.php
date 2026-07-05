<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Gym;
use App\Services\MongoDBService;
use App\Services\StripeService;
use App\Jobs\SendSubscriptionConfirmationEmail;
use App\Jobs\SendSuperAdminSubscriptionNotificationEmail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class SaasSubscriptionController extends Controller
{
    public static $plans = [
        'Starter' => 29.00,
        'Professional' => 59.00,
        'Enterprise' => 99.00,
    ];

    protected $stripeService;

    public function __construct(StripeService $stripeService)
    {
        $this->stripeService = $stripeService;
    }

    /**
     * Show SaaS pricing and subscription plans.
     */
    public function pricing()
    {
        return view('saas.pricing', [
            'plans' => self::$plans
        ]);
    }

    /**
     * Show custom Enterprise registration form carrying the plan.
     */
    public function showRegister(Request $request)
    {
        $plan = $request->query('plan', 'Starter');
        if (!array_key_exists($plan, self::$plans)) {
            $plan = 'Starter';
        }

        return view('auth.register-enterprise', compact('plan'));
    }

    /**
     * Process custom Enterprise registration.
     */
    public function registerEnterprise(Request $request)
    {
        $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', 'min:8'],
            'gym_name' => ['required', 'string', 'max:255'],
            'phone'    => ['nullable', 'string'],
            'address'  => ['nullable', 'string'],
            'plan'     => ['required', 'string'],
        ]);

        $plan = $request->plan;
        if (!array_key_exists($plan, self::$plans)) {
            $plan = 'Starter';
        }

        // Create Gym as inactive until payment is received
        $gym = Gym::create([
            'name' => $request->gym_name,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
            'subscription_plan' => $plan,
            'subscription_status' => 'inactive',
        ]);

        // Create Gym Admin user
        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'gym_id'   => $gym->id,
            'role'     => 'admin',
        ]);

        // Redirect to SaaS checkout
        return redirect()->route('saas.checkout', ['gym' => $gym->id]);
    }

    /**
     * Show simulated SaaS payment page.
     */
    public function showCheckout(int $gymId)
    {
        $gym = Gym::findOrFail($gymId);
        $plan = $gym->subscription_plan ?? 'Starter';
        $price = self::$plans[$plan];
        $stripeEnabled = $this->stripeService->isEnabled();

        return view('saas.checkout', compact('gym', 'plan', 'price', 'stripeEnabled'));
    }

    /**
     * Process simulated subscription credit card payment.
     */
    public function processCheckout(Request $request, int $gymId)
    {
        $gym = Gym::findOrFail($gymId);
        $plan = $gym->subscription_plan ?? 'Starter';
        $price = self::$plans[$plan];

        if ($this->stripeService->isEnabled()) {
            $adminUser = User::where('gym_id', $gym->id)->where('role', 'admin')->first();
            try {
                $successUrl = route('saas.success', $gym->id);
                $cancelUrl = route('saas.checkout', $gym->id);
                $session = $this->stripeService->createSubscriptionSession($gym, $adminUser, $successUrl, $cancelUrl);

                $gym->update([
                    'stripe_session_id' => $session->id,
                    'subscription_status' => 'pending',
                ]);

                return redirect()->away($session->url);
            } catch (\Exception $e) {
                Log::error("SaaS Stripe Checkout redirection failed: " . $e->getMessage());
                return back()->with('error', 'Could not initiate secure Stripe checkout. Please try again.');
            }
        }

        $request->validate([
            'cardholder_name' => 'required|string|max:255',
            'card_number' => ['required', 'string', 'regex:/^[0-9]{16}$/'],
            'expiry' => ['required', 'string', 'regex:/^(0[1-9]|1[0-2])\/[0-9]{2}$/'],
            'cvv' => ['required', 'string', 'regex:/^[0-9]{3,4}$/'],
        ]);

        // Simulated IPG Payment Gateway failure if CVV is '999'
        if ($request->cvv === '999') {
            Log::error("SaaS Subscription Payment Failed: Simulated decline for Gym ID: {$gym->id} on Plan: {$plan}");

            // Store failed transaction analytics in mock MongoDB
            MongoDBService::collection('gym_analytics')->insertOne([
                'gym_id' => $gym->id,
                'gym_name' => $gym->name,
                'plan' => $plan,
                'price' => $price,
                'status' => 'failed',
                'error' => 'Payment transaction was declined by the simulated gateway.',
                'created_at' => now()->toDateTimeString(),
            ]);

            return redirect()->route('saas.failed', $gym->id);
        }

        // Generate subscription transaction ID
        $transactionId = 'SUB-' . strtoupper(Str::random(10));

        // Update Gym subscription fields to active
        $gym->update([
            'subscription_status' => 'active',
            'subscription_expires_at' => Carbon::now()->addMonth(),
            'subscription_transaction_id' => $transactionId,
        ]);

        // Write Successful transactions to mock MongoDB
        MongoDBService::collection('gym_analytics')->insertOne([
            'gym_id' => $gym->id,
            'gym_name' => $gym->name,
            'plan' => $plan,
            'price' => $price,
            'status' => 'active',
            'transaction_id' => $transactionId,
            'created_at' => now()->toDateTimeString(),
        ]);

        MongoDBService::collection('payment_logs')->insertOne([
            'gym_id' => $gym->id,
            'gym_name' => $gym->name,
            'amount' => $price,
            'transaction_id' => $transactionId,
            'type' => 'subscription',
            'created_at' => now()->toDateTimeString(),
        ]);

        // Update platform growth metrics in MongoDB
        MongoDBService::collection('gym_analytics')->insertOne([
            'gyms_count' => Gym::count(),
            'members_count' => User::where('role', 'member')->count(),
            'trainers_count' => User::where('role', 'trainer')->count(),
            'bookings_count' => \App\Models\Booking::count(),
            'created_at' => now()->toDateTimeString(),
        ]);

        // Retrieve the Gym Admin user to send emails and log in
        $adminUser = User::where('gym_id', $gym->id)->where('role', 'admin')->first();

        // Dispatch Queue Jobs
        if ($adminUser) {
            SendSubscriptionConfirmationEmail::dispatch($gym, $adminUser);
        }
        SendSuperAdminSubscriptionNotificationEmail::dispatch($gym);

        Log::info("SaaS Subscription Payment Successful: Transaction ID: {$transactionId}. Gym ID: {$gym->id} subscribed to {$plan}.");

        // Log the admin user in automatically
        if ($adminUser) {
            Auth::login($adminUser);
        }

        return redirect()->route('saas.success', $gym->id);
    }

    /**
     * Show subscription successful confirmation page.
     */
    public function showSuccess(Request $request, int $gymId)
    {
        $gym = Gym::findOrFail($gymId);

        if ($request->has('session_id') && $this->stripeService->isEnabled()) {
            $sessionId = $request->query('session_id');
            $session = $this->stripeService->retrieveSession($sessionId);

            if ($session && $session->payment_status === 'paid' && $gym->subscription_status !== 'active') {
                $plan = $gym->subscription_plan ?? 'Starter';
                $price = self::$plans[$plan] ?? 29.00;
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

                    // MongoDB logs
                    MongoDBService::collection('gym_analytics')->insertOne([
                        'gym_id' => $gym->id,
                        'gym_name' => $gym->name,
                        'plan' => $plan,
                        'price' => $price,
                        'status' => 'active',
                        'transaction_id' => $transactionId,
                        'created_at' => now()->toDateTimeString(),
                    ]);

                    MongoDBService::collection('payment_logs')->insertOne([
                        'gym_id' => $gym->id,
                        'gym_name' => $gym->name,
                        'amount' => $price,
                        'transaction_id' => $transactionId,
                        'type' => 'subscription',
                        'created_at' => now()->toDateTimeString(),
                    ]);

                    MongoDBService::collection('gym_analytics')->insertOne([
                        'gyms_count' => Gym::count(),
                        'members_count' => User::where('role', 'member')->count(),
                        'trainers_count' => User::where('role', 'trainer')->count(),
                        'bookings_count' => \App\Models\Booking::count(),
                        'created_at' => now()->toDateTimeString(),
                    ]);

                    DB::commit();

                    // Send emails with non-blocking try/catch
                    $adminUser = User::where('gym_id', $gym->id)->where('role', 'admin')->first();
                    try {
                        if ($adminUser) {
                            \App\Jobs\SendSubscriptionConfirmationEmail::dispatch($gym, $adminUser);
                        }
                    } catch (\Exception $e) {
                        Log::error("Failed to dispatch subscription confirmation email: " . $e->getMessage());
                    }

                    try {
                        \App\Jobs\SendSuperAdminSubscriptionNotificationEmail::dispatch($gym);
                    } catch (\Exception $e) {
                        Log::error("Failed to dispatch super admin subscription notification email: " . $e->getMessage());
                    }
                } catch (\Exception $e) {
                    DB::rollBack();
                    Log::error("Stripe SaaS Redirect Activation failed: " . $e->getMessage());
                }
            }
        }

        // Auto-login admin if not authenticated
        $adminUser = User::where('gym_id', $gym->id)->where('role', 'admin')->first();
        if ($adminUser && !Auth::check()) {
            Auth::login($adminUser);
        }

        return view('saas.success', compact('gym'));
    }

    /**
     * Show subscription failed notification page.
     */
    public function showFailed(int $gymId)
    {
        $gym = Gym::findOrFail($gymId);
        return view('saas.failed', compact('gym'));
    }
}
