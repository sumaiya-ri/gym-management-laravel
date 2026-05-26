<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Gym;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules;

class MemberAuthController extends Controller
{
    /**
     * Show the member registration form.
     */
    public function showRegister()
    {
        $gyms = Gym::all();
        return view('auth.member-register', compact('gyms'));
    }

    /**
     * Handle member registration.
     */
    public function register(Request $request)
    {
        $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'gym_id'   => ['required', 'exists:gyms,id'],
        ]);

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'gym_id'   => $request->gym_id,
            'role'     => 'member',
            'status'   => 'active',
        ]);

        Auth::login($user);

        return redirect()->route('member.dashboard');
    }
}
