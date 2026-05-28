<?php

namespace App\Actions\Fortify;

use App\Models\User;
use App\Mail\AdminOtpMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class RedirectIfAdminNeedsTwoFactor
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  callable  $next
     * @return mixed
     */
    public function handle(Request $request, $next)
    {
        $user = User::where('email', $request->email)->first();

        // Check if the user exists, password is correct, and role is admin or super_admin
        if ($user && 
            in_array($user->role, ['admin', 'super_admin']) && 
            Hash::check($request->password, $user->password)) {
            
            // Generate secure 6-digit OTP
            try {
                $otp = random_int(100000, 999999);
            } catch (\Exception $e) {
                $otp = rand(100000, 999999);
            }

            // Save hashed OTP, expiration, last sent timestamp, and reset attempts
            $user->forceFill([
                'otp_code' => Hash::make($otp),
                'otp_expires_at' => now()->addMinutes(5),
                'otp_last_sent_at' => now(),
                'otp_attempts' => 0,
            ])->save();

            // Store user ID and remember option in session
            session([
                '2fa_user_id' => $user->id,
                '2fa_remember' => $request->filled('remember'),
            ]);

            // Send OTP email
            try {
                Mail::to($user->email)->send(new AdminOtpMail($otp));
            } catch (\Throwable $e) {
                Log::error("Failed to send 2FA OTP email to {$user->email}: " . $e->getMessage(), [
                    'exception' => $e
                ]);
            }

            return redirect()->route('auth.otp.verify');
        }

        return $next($request);
    }
}
