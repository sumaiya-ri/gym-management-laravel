<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;
use App\Models\Gym;
use App\Models\Trainer;

class User extends Authenticatable
{
    use HasApiTokens;

    /** @use HasFactory<UserFactory> */
    use HasFactory;

    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',

        'gym_id',
        'role',
        'google_id',
        'avatar',
        'oauth_provider',
    ];


    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'profile_photo_url',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /** The gym this user belongs to. */
    public function gym()
    {
        return $this->belongsTo(Gym::class);
    }

    /** The trainer profile record linked to this user (if role = trainer). */
    public function trainer()
    {
        return $this->hasOne(Trainer::class);
    }

    /**
     * Get the API token abilities available for the user's role.
     */
    public function getAbilitiesByRole(): array
    {
        return match ($this->role) {
            'super_admin' => [
                'read:analytics',
                'manage:gyms',
                'manage:subscriptions',
                'manage:platform',
            ],
            'admin' => [
                'manage:bookings',
                'manage:trainers',
                'manage:services',
                'manage:members',
            ],
            'trainer' => [
                'manage:workouts',
                'view:classes',
            ],
            'member' => [
                'create:bookings',
                'view:own-bookings',
            ],
            default => [],
        };
    }

    /**
     * Create a new personal access token with customized structure and masked copy storage.
     */
    public function createToken(string $name, array $abilities = ['*'], \DateTimeInterface $expiresAt = null)
    {
        $plainTextToken = \Illuminate\Support\Str::random(40);
        $formattedToken = 'glowgym_' . $plainTextToken;

        // Mask the token as glowgym_8f3a****2kd9
        $prefix = substr($plainTextToken, 0, 4);
        $suffix = substr($plainTextToken, -4);
        $maskedToken = 'glowgym_' . $prefix . '****' . $suffix;

        $token = $this->tokens()->create([
            'name' => $name,
            'token' => hash('sha256', $formattedToken),
            'abilities' => $abilities,
            'expires_at' => $expiresAt,
        ]);

        $token->masked_token = $maskedToken;
        $token->save();

        return new \Laravel\Sanctum\NewAccessToken($token, $formattedToken);
    }
}
