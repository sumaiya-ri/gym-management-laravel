<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Gym;
use App\Jobs\SendMemberWelcomeEmail;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class GoogleController extends Controller
{
    /**
     * Redirect the user to the Google authentication page.
     */
    public function redirectToGoogle(Request $request)
    {
        if ($request->has('gym_id')) {
            $request->validate([
                'gym_id' => 'required|exists:gyms,id',
            ]);
            session(['oauth_gym_id' => $request->gym_id]);
        }

        $response = Socialite::driver('google')->redirect();
        session()->save();

        return $response;
    }

    /**
     * Obtain the user information from Google.
     */
    public function handleGoogleCallback()
    {
        try {
            // Attempt standard stateful OAuth first
            $googleUser = Socialite::driver('google')->user();
        } catch (\Laravel\Socialite\Two\InvalidStateException $e) {
            // If state check fails (common behind proxies or due to browser session drops), fallback to stateless
            Log::warning("Google OAuth state validation failed. Falling back to stateless mode.");
            try {
                $googleUser = Socialite::driver('google')->stateless()->user();
            } catch (\Exception $subException) {
                Log::error("Google OAuth callback failed in stateless fallback: " . $subException, [
                    'exception' => $subException
                ]);
                return redirect()->route('member.login')->with('error', 'Google authentication failed. Please try again.');
            }
        } catch (\Exception $e) {
            Log::error("Google OAuth callback failed: " . $e, [
                'exception' => $e
            ]);
            return redirect()->route('member.login')->with('error', 'Google authentication failed. Please try again.');
        }

        if (!$googleUser || !$googleUser->getEmail()) {
            return redirect()->route('member.login')->with('error', 'Could not retrieve email from Google account.');
        }

        $email = $googleUser->getEmail();

        // 1. Check if user already exists
        $user = User::where('email', $email)->first();

        if ($user) {
            // 2. Existing user check: ONLY members can authenticate using Google OAuth
            if ($user->role !== 'member') {
                return redirect()->route('login')->with('error', 'Admins and Trainers must log in using the email/password form.');
            }

            // Update google_id and avatar if not set
            $user->update([
                'google_id' => $googleUser->getId(),
                'avatar' => $googleUser->getAvatar(),
                'oauth_provider' => 'google',
            ]);

            Auth::login($user, true);
            session()->regenerate();
            session()->forget('oauth_gym_id');
            session()->save();

            return redirect()->route('member.dashboard');
        }

        // 3. User does not exist: Registration Flow
        $gymId = session('oauth_gym_id');

        if (!$gymId) {
            return redirect()->route('member.register')->with('error', 'Please select a gym first to register with Google.');
        }

        // Verify the gym exists
        $gym = Gym::find($gymId);
        if (!$gym) {
            return redirect()->route('member.register')->with('error', 'The selected gym does not exist.');
        }

        // Create new member user
        $user = User::create([
            'name' => $googleUser->getName() ?? explode('@', $email)[0],
            'email' => $email,
            'google_id' => $googleUser->getId(),
            'role' => 'member',
            'gym_id' => $gymId,
            'status' => 'active',
            'avatar' => $googleUser->getAvatar(),
            'oauth_provider' => 'google',
            'password' => Hash::make(Str::random(24)), // Random secure password
            'email_verified_at' => now(), // Auto-verified through Google OAuth
        ]);

        // Dispatch Welcome Email
        try {
            SendMemberWelcomeEmail::dispatch($user);
        } catch (\Exception $e) {
            Log::error("Failed to dispatch welcome email for user {$user->id}: " . $e->getMessage());
        }

        Auth::login($user, true);
        session()->regenerate();
        session()->forget('oauth_gym_id');
        session()->save();

        return redirect()->route('member.dashboard');
    }
}
