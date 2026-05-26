<?php

namespace App\Actions\Fortify;

use App\Models\User;
use App\Models\Gym;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use Laravel\Jetstream\Jetstream;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    /**
     * Validate and create a newly registered user.
     *
     * @param  array<string, string>  $input
     */
    public function create(array $input): User
    {
        // ✅ VALIDATION (add gym fields here)
        Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => $this->passwordRules(),

            // 🔥 NEW FIELDS
            'gym_name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string'],
            'address' => ['nullable', 'string'],

            'terms' => Jetstream::hasTermsAndPrivacyPolicyFeature() ? ['accepted', 'required'] : '',
        ])->validate();

        // ✅ Step 1: Create gym using user input
        $gym = Gym::create([
            'name' => $input['gym_name'],
            'email' => $input['email'],
            'phone' => $input['phone'] ?? null,
            'address' => $input['address'] ?? null,
        ]);

        // ✅ Step 2: Create user linked to gym
        return User::create([
            'name' => $input['name'],
            'email' => $input['email'],
            'password' => Hash::make($input['password']),
            'gym_id' => $gym->id,
            'role' => 'admin',
        ]);
    }
}