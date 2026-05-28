<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\ServiceController;
use App\Http\Controllers\Admin\TrainerController;
use App\Http\Controllers\Admin\TimeslotController;
use App\Http\Controllers\Admin\GymController;
use App\Http\Controllers\Trainer\TrainerDashboardController;
use App\Http\Controllers\Member\MemberDashboardController;
use App\Http\Controllers\Member\BookingController;
use App\Http\Controllers\Member\PaymentController;
use App\Http\Controllers\MemberAuthController;
use App\Http\Controllers\SaasSubscriptionController;
use App\Http\Controllers\SuperAdmin\SuperAdminDashboardController;

Route::get('/', function () {
    return view('welcome');
});

// Stripe Webhook
Route::post('/stripe/webhook', [\App\Http\Controllers\StripeWebhookController::class, 'handleWebhook'])->name('stripe.webhook');

// Unified Registration Selection
Route::get('/register', function () {
    return view('auth.register-select');
})->name('register');

// SaaS Pricing and Custom Enterprise Registration
Route::get('/saas/pricing', [SaasSubscriptionController::class, 'pricing'])->name('saas.pricing');
Route::get('/register-enterprise', [SaasSubscriptionController::class, 'showRegister'])->name('register.enterprise');
Route::post('/register-enterprise', [SaasSubscriptionController::class, 'registerEnterprise'])->name('register.enterprise.post');

// SaaS Payment Checkout
Route::get('/saas/checkout/{gym}', [SaasSubscriptionController::class, 'showCheckout'])->name('saas.checkout');
Route::post('/saas/checkout/{gym}', [SaasSubscriptionController::class, 'processCheckout'])->name('saas.process');
Route::get('/saas/success/{gym}', [SaasSubscriptionController::class, 'showSuccess'])->name('saas.success');
Route::get('/saas/failed/{gym}', [SaasSubscriptionController::class, 'showFailed'])->name('saas.failed');

// Member Registration Routes (Public)
Route::get('/member/register', [MemberAuthController::class, 'showRegister'])->name('member.register');
Route::post('/member/register', [MemberAuthController::class, 'register']);
Route::get('/member/login', [MemberAuthController::class, 'showLogin'])->name('member.login');

Route::get('/auth/google/redirect', [\App\Http\Controllers\Auth\GoogleController::class, 'redirectToGoogle'])->name('auth.google.redirect');
Route::post('/auth/google/redirect', [\App\Http\Controllers\Auth\GoogleController::class, 'redirectToGoogle'])->name('auth.google.redirect.post');
Route::get('/auth/google/callback', [\App\Http\Controllers\Auth\GoogleController::class, 'handleGoogleCallback'])->name('auth.google.callback');

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/user/api-tokens', function () {
        if (!in_array(auth()->user()->role, ['super_admin', 'admin'])) {
            abort(403, 'Unauthorized action.');
        }
        return view('profile.api-tokens');
    })->name('profile.api-tokens');

    Route::get('/dashboard', function () {
        if (auth()->user()->role === 'super_admin') {
            return redirect()->route('super-admin.dashboard');
        }
        if (auth()->user()->role === 'admin') {
            $gym = auth()->user()->gym;
            if ($gym && $gym->subscription_status !== 'active') {
                return redirect()->route('saas.checkout', ['gym' => $gym->id]);
            }
            return redirect()->route('admin.dashboard');
        }
        if (auth()->user()->role === 'trainer') {
            return redirect()->route('trainer.dashboard');
        }
        if (auth()->user()->role === 'member') {
            return redirect()->route('member.dashboard');
        }
        return view('dashboard');
    })->name('dashboard');

    // Super Admin Routes
    Route::middleware(['super_admin'])->prefix('super-admin')->name('super-admin.')->group(function () {
        Route::get('/dashboard', [SuperAdminDashboardController::class, 'index'])->name('dashboard');
    });

    // Admin Routes
    Route::middleware(['admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

        Route::resource('services', ServiceController::class);
        Route::resource('trainers', TrainerController::class);
        Route::resource('timeslots', TimeslotController::class);

        Route::get('/gym', [GymController::class, 'show'])->name('gym.show');
        Route::get('/gym/edit', [GymController::class, 'edit'])->name('gym.edit');
        Route::put('/gym', [GymController::class, 'update'])->name('gym.update');
    });

    // Trainer Routes
    Route::middleware(['trainer'])->prefix('trainer')->name('trainer.')->group(function () {
        Route::get('/dashboard', [TrainerDashboardController::class, 'index'])->name('dashboard');
        Route::get('/classes',   [TrainerDashboardController::class, 'classes'])->name('classes');
        Route::get('/classes/{timeslot}/members', [TrainerDashboardController::class, 'members'])->name('members');
    });

    // Member Routes
    Route::middleware(['member'])->prefix('member')->name('member.')->group(function () {
        Route::get('/dashboard', [MemberDashboardController::class, 'index'])->name('dashboard');
        Route::get('/classes',   [MemberDashboardController::class, 'classes'])->name('classes');
        Route::get('/bookings',  [BookingController::class, 'index'])->name('bookings');
        Route::post('/book/{timeslot}', [BookingController::class, 'store'])->name('book');
        Route::post('/bookings/{booking}/cancel', [BookingController::class, 'cancel'])->name('cancel');

        // Booking Payment flow
        Route::get('/payment/checkout/{timeslot}', [PaymentController::class, 'showCheckout'])->name('payment.checkout');
        Route::post('/payment/checkout/{timeslot}', [PaymentController::class, 'processPayment'])->name('payment.process');
        Route::get('/payment/success/{booking}', [PaymentController::class, 'showSuccess'])->name('payment.success');
        Route::get('/payment/failed/{timeslot}', [PaymentController::class, 'showFailed'])->name('payment.failed');
    });
});
