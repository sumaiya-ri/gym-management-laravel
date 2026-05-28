<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Mail\AdminOtpMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class OtpVerificationController extends Controller
{
    /**
     * Show the OTP verification form.
     */
    public function showVerify()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        if (!session()->has('2fa_user_id')) {
            return redirect()->route('login')->with('error', 'Please log in first.');
        }

        return view('auth.otp-verify');
    }

    /**
     * Handle the OTP verification submission.
     */
    public function verify(Request $request)
    {
        if (!session()->has('2fa_user_id')) {
            return redirect()->route('login')->with('error', 'Please log in first.');
        }

        $request->validate([
            'otp' => 'required|digits:6',
        ]);

        $user = User::find(session('2fa_user_id'));

        if (!$user) {
            session()->forget(['2fa_user_id', '2fa_remember']);
            return redirect()->route('login')->with('error', 'User not found. Please log in again.');
        }

        // Check if OTP is expired
        if (!$user->otp_expires_at || now()->gt($user->otp_expires_at)) {
            return back()->withErrors(['otp' => 'The verification code has expired. Please request a new one.']);
        }

        // Check retry limits (max 3 attempts)
        if ($user->otp_attempts >= 3) {
            // Invalidate OTP and clear session
            $user->forceFill([
                'otp_code' => null,
                'otp_expires_at' => null,
                'otp_attempts' => 0,
            ])->save();

            session()->forget(['2fa_user_id', '2fa_remember']);

            return redirect()->route('login')->with('error', 'Too many incorrect attempts. Please log in again.');
        }

        // Verify the OTP code
        if (Hash::check($request->otp, $user->otp_code)) {
            // Clear OTP fields
            $user->forceFill([
                'otp_code' => null,
                'otp_expires_at' => null,
                'otp_attempts' => 0,
            ])->save();

            // Store remember parameter
            $remember = session('2fa_remember', false);
            session()->forget(['2fa_user_id', '2fa_remember']);

            // Login fully and regenerate session (session fixation protection)
            Auth::login($user, $remember);
            session()->regenerate();
            session()->save();

            return redirect()->route('dashboard');
        }

        // Incorrect OTP: increment attempts
        $user->increment('otp_attempts');
        $remaining = 3 - $user->otp_attempts;

        if ($remaining <= 0) {
            // Immediately invalidate and redirect to login
            $user->forceFill([
                'otp_code' => null,
                'otp_expires_at' => null,
                'otp_attempts' => 0,
            ])->save();

            session()->forget(['2fa_user_id', '2fa_remember']);

            return redirect()->route('login')->with('error', 'Too many incorrect attempts. Please log in again.');
        }

        return back()->withErrors(['otp' => "Invalid verification code. You have {$remaining} attempts remaining."]);
    }

    /**
     * Resend the OTP code.
     */
    public function resend(Request $request)
    {
        if (!session()->has('2fa_user_id')) {
            return redirect()->route('login')->with('error', 'Please log in first.');
        }

        $user = User::find(session('2fa_user_id'));

        if (!$user) {
            session()->forget(['2fa_user_id', '2fa_remember']);
            return redirect()->route('login')->with('error', 'User not found. Please log in again.');
        }

        // Throttle check: allow resend only every 60 seconds
        if ($user->otp_last_sent_at && now()->lt($user->otp_last_sent_at->addSeconds(60))) {
            $secondsRemaining = (int) ceil(now()->diffInSeconds($user->otp_last_sent_at->addSeconds(60), true));
            return back()->withErrors(['resend' => "Please wait {$secondsRemaining} seconds before requesting another code."]);
        }

        // Generate a new secure OTP
        try {
            $otp = random_int(100000, 999999);
        } catch (\Exception $e) {
            $otp = rand(100000, 999999);
        }

        // Update database record
        $user->forceFill([
            'otp_code' => Hash::make($otp),
            'otp_expires_at' => now()->addMinutes(5),
            'otp_last_sent_at' => now(),
            'otp_attempts' => 0,
        ])->save();

        // Send OTP email
        try {
            Mail::to($user->email)->send(new AdminOtpMail($otp));
            return back()->with('success', 'A new verification code has been sent to your email.');
        } catch (\Throwable $e) {
            Log::error("Failed to resend 2FA OTP email to {$user->email}: " . $e->getMessage(), [
                'exception' => $e
            ]);
            return back()->withErrors(['resend' => 'Failed to send the code. Please try again.']);
        }
    }
}
