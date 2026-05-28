<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Gym;
use App\Jobs\SendMemberWelcomeEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\User as SocialiteUser;
use Tests\TestCase;

class GoogleOAuthTest extends TestCase
{
    use RefreshDatabase;

    protected $gym;

    protected function setUp(): void
    {
        parent::setUp();

        // Standard setup: Disable stripe so payment flows don't crash
        config(['services.stripe.secret' => null]);

        $this->gym = Gym::create([
            'name' => 'OAuth Test Gym',
            'email' => 'oauth.gym@gmail.com',
            'subscription_status' => 'active',
        ]);
    }

    /**
     * Test GET /auth/google/redirect redirects to Google OAuth consent page.
     */
    public function test_member_login_redirects_to_google(): void
    {
        $mockProvider = \Mockery::mock(\Laravel\Socialite\Two\GoogleProvider::class);
        $mockProvider->shouldReceive('redirect')->once()->andReturn(redirect('https://accounts.google.com/o/oauth2/auth'));
        Socialite::shouldReceive('driver')->with('google')->once()->andReturn($mockProvider);

        $response = $this->get(route('auth.google.redirect'));
        $response->assertRedirect('https://accounts.google.com/o/oauth2/auth');
    }

    /**
     * Test POST /auth/google/redirect stores gym_id in session and redirects.
     */
    public function test_member_registration_redirects_to_google_with_gym_selection(): void
    {
        $mockProvider = \Mockery::mock(\Laravel\Socialite\Two\GoogleProvider::class);
        $mockProvider->shouldReceive('redirect')->once()->andReturn(redirect('https://accounts.google.com/o/oauth2/auth'));
        Socialite::shouldReceive('driver')->with('google')->once()->andReturn($mockProvider);

        $response = $this->post(route('auth.google.redirect.post'), [
            'gym_id' => $this->gym->id,
        ]);

        $response->assertRedirect('https://accounts.google.com/o/oauth2/auth');
        $this->assertEquals($this->gym->id, session('oauth_gym_id'));
    }

    /**
     * Test Callback registers a new member, sets roles, and logs in when user does not exist.
     */
    public function test_google_callback_registers_new_member_and_sends_welcome_email(): void
    {
        Queue::fake();

        $mockUser = \Mockery::mock(SocialiteUser::class);
        $mockUser->shouldReceive('getId')->andReturn('google-unique-id-789');
        $mockUser->shouldReceive('getName')->andReturn('Alice Member');
        $mockUser->shouldReceive('getEmail')->andReturn('alice.oauth@gmail.com');
        $mockUser->shouldReceive('getAvatar')->andReturn('https://lh3.googleusercontent.com/avatar');

        $mockProvider = \Mockery::mock(\Laravel\Socialite\Two\GoogleProvider::class);
        $mockProvider->shouldReceive('user')->once()->andReturn($mockUser);
        Socialite::shouldReceive('driver')->with('google')->once()->andReturn($mockProvider);

        // Put selected gym in session (simulating step 1)
        session(['oauth_gym_id' => $this->gym->id]);

        $response = $this->get(route('auth.google.callback'));

        // Asserts
        $response->assertRedirect(route('member.dashboard'));
        $this->assertAuthenticated();

        $user = User::where('email', 'alice.oauth@gmail.com')->first();
        $this->assertNotNull($user);
        $this->assertEquals('member', $user->role);
        $this->assertEquals($this->gym->id, $user->gym_id);
        $this->assertEquals('google-unique-id-789', $user->google_id);
        $this->assertEquals('https://lh3.googleusercontent.com/avatar', $user->avatar);
        $this->assertEquals('google', $user->oauth_provider);

        // Verify welcome email is dispatched
        Queue::assertPushed(SendMemberWelcomeEmail::class);
    }

    /**
     * Test Callback logs in existing member without dispatching welcome email.
     */
    public function test_google_callback_logins_existing_member_without_duplicate_email(): void
    {
        Queue::fake();

        $existingMember = User::create([
            'name' => 'Existing Member',
            'email' => 'existing.member@gmail.com',
            'password' => bcrypt('password'),
            'role' => 'member',
            'gym_id' => $this->gym->id,
            'status' => 'active',
        ]);

        $mockUser = \Mockery::mock(SocialiteUser::class);
        $mockUser->shouldReceive('getId')->andReturn('google-unique-id-456');
        $mockUser->shouldReceive('getName')->andReturn('Existing Member');
        $mockUser->shouldReceive('getEmail')->andReturn('existing.member@gmail.com');
        $mockUser->shouldReceive('getAvatar')->andReturn('https://lh3.googleusercontent.com/avatar');

        $mockProvider = \Mockery::mock(\Laravel\Socialite\Two\GoogleProvider::class);
        $mockProvider->shouldReceive('user')->once()->andReturn($mockUser);
        Socialite::shouldReceive('driver')->with('google')->once()->andReturn($mockProvider);

        $response = $this->get(route('auth.google.callback'));

        $response->assertRedirect(route('member.dashboard'));
        $this->assertAuthenticatedAs($existingMember);

        // Check columns were updated
        $existingMember->refresh();
        $this->assertEquals('google-unique-id-456', $existingMember->google_id);
        $this->assertEquals('google', $existingMember->oauth_provider);

        // Verify no welcome email is dispatched for existing members
        Queue::assertNotPushed(SendMemberWelcomeEmail::class);
    }

    /**
     * Test Callback blocks non-member roles (e.g. admins, trainers).
     */
    public function test_google_callback_blocks_non_member_roles(): void
    {
        $existingAdmin = User::create([
            'name' => 'Gym Admin',
            'email' => 'gym.admin@gmail.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'gym_id' => $this->gym->id,
            'status' => 'active',
        ]);

        $mockUser = \Mockery::mock(SocialiteUser::class);
        $mockUser->shouldReceive('getId')->andReturn('google-unique-id-111');
        $mockUser->shouldReceive('getName')->andReturn('Gym Admin');
        $mockUser->shouldReceive('getEmail')->andReturn('gym.admin@gmail.com');
        $mockUser->shouldReceive('getAvatar')->andReturn('https://lh3.googleusercontent.com/avatar');

        $mockProvider = \Mockery::mock(\Laravel\Socialite\Two\GoogleProvider::class);
        $mockProvider->shouldReceive('user')->once()->andReturn($mockUser);
        Socialite::shouldReceive('driver')->with('google')->once()->andReturn($mockProvider);

        $response = $this->get(route('auth.google.callback'));

        // Asserts
        $response->assertRedirect(route('login'));
        $this->assertGuest();
        $response->assertSessionHas('error', 'Admins and Trainers must log in using the email/password form.');
    }

    /**
     * Test standard password login form blocks members from authenticating.
     */
    public function test_standard_login_blocks_members_from_logging_in(): void
    {
        $member = User::create([
            'name' => 'Member Blocked',
            'email' => 'blocked.member@gmail.com',
            'password' => bcrypt('password'),
            'role' => 'member',
            'gym_id' => $this->gym->id,
            'status' => 'active',
        ]);

        $response = $this->post('/login', [
            'email' => 'blocked.member@gmail.com',
            'password' => 'password',
        ]);

        $response->assertSessionHasErrors([
            'email' => 'Members must log in using Google OAuth.'
        ]);
        $this->assertGuest();
    }

    /**
     * Test callback falls back to stateless mode if state validation throws InvalidStateException.
     */
    public function test_google_callback_falls_back_to_stateless_on_invalid_state(): void
    {
        Queue::fake();

        $mockUser = \Mockery::mock(SocialiteUser::class);
        $mockUser->shouldReceive('getId')->andReturn('google-unique-id-stateless');
        $mockUser->shouldReceive('getName')->andReturn('Stateless User');
        $mockUser->shouldReceive('getEmail')->andReturn('stateless.member@gmail.com');
        $mockUser->shouldReceive('getAvatar')->andReturn('https://lh3.googleusercontent.com/avatar');

        $mockProvider = \Mockery::mock(\Laravel\Socialite\Two\GoogleProvider::class);
        // First stateful call throws InvalidStateException
        $mockProvider->shouldReceive('user')->once()->andThrow(new \Laravel\Socialite\Two\InvalidStateException());
        // Then it should switch to stateless mode and try again
        $mockProvider->shouldReceive('stateless')->once()->andReturnSelf();
        $mockProvider->shouldReceive('user')->once()->andReturn($mockUser);

        Socialite::shouldReceive('driver')->with('google')->twice()->andReturn($mockProvider);

        session(['oauth_gym_id' => $this->gym->id]);

        $response = $this->get(route('auth.google.callback'));

        $response->assertRedirect(route('member.dashboard'));
        $this->assertAuthenticated();

        $user = User::where('email', 'stateless.member@gmail.com')->first();
        $this->assertNotNull($user);
        $this->assertEquals('member', $user->role);
        $this->assertEquals('google-unique-id-stateless', $user->google_id);
    }
}

