<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Gym;
use App\Models\Trainer;
use App\Models\Service;
use App\Models\Timeslot;
use App\Models\Booking;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class SanctumAdvancedSecurityTest extends TestCase
{
    use RefreshDatabase;

    protected $gymA;
    protected $gymB;
    
    protected $adminA;
    protected $adminB;
    
    protected $trainerA;
    protected $memberA;
    protected $memberB;

    protected $serviceA;
    protected $timeslotA;

    protected function setUp(): void
    {
        parent::setUp();

        // 1. Create Gyms
        $this->gymA = Gym::create([
            'name' => 'Alpha Gym',
            'email' => 'alpha@gym.com',
            'subscription_status' => 'active',
        ]);

        $this->gymB = Gym::create([
            'name' => 'Beta Gym',
            'email' => 'beta@gym.com',
            'subscription_status' => 'active',
        ]);

        // 2. Create Users for Gym A
        $this->adminA = User::create([
            'name' => 'Admin Alpha',
            'email' => 'admin.alpha@gym.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'gym_id' => $this->gymA->id,
            'email_verified_at' => now(),
        ]);

        $this->trainerA = User::create([
            'name' => 'Trainer Alpha',
            'email' => 'trainer.alpha@gym.com',
            'password' => Hash::make('password'),
            'role' => 'trainer',
            'gym_id' => $this->gymA->id,
            'email_verified_at' => now(),
        ]);

        $this->memberA = User::create([
            'name' => 'Member Alpha',
            'email' => 'member.alpha@gym.com',
            'password' => Hash::make('password'),
            'role' => 'member',
            'gym_id' => $this->gymA->id,
            'email_verified_at' => now(),
        ]);

        // 3. Create Users for Gym B
        $this->adminB = User::create([
            'name' => 'Admin Beta',
            'email' => 'admin.beta@gym.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'gym_id' => $this->gymB->id,
            'email_verified_at' => now(),
        ]);

        $this->memberB = User::create([
            'name' => 'Member Beta',
            'email' => 'member.beta@gym.com',
            'password' => Hash::make('password'),
            'role' => 'member',
            'gym_id' => $this->gymB->id,
            'email_verified_at' => now(),
        ]);

        // 4. Create Services & Timeslots
        $this->serviceA = Service::create([
            'gym_id' => $this->gymA->id,
            'name' => 'Alpha Cardio',
            'description' => 'High intensity cardio',
            'duration' => 60,
            'price' => 15.00,
        ]);

        $trainerProfileA = Trainer::create([
            'gym_id' => $this->gymA->id,
            'user_id' => $this->trainerA->id,
            'name' => 'Trainer Alpha Profile',
            'specialization' => 'Cardio',
            'hourly_rate' => 30.00,
            'status' => 'active',
        ]);

        $this->timeslotA = Timeslot::create([
            'gym_id' => $this->gymA->id,
            'service_id' => $this->serviceA->id,
            'trainer_id' => $trainerProfileA->id,
            'date' => now()->addDays(2)->toDateString(),
            'start_time' => '10:00:00',
            'end_time' => '11:00:00',
            'capacity' => 10,
            'status' => 'active',
        ]);
    }

    /**
     * Test API login token generation.
     */
    public function test_api_login_generates_token_with_correct_abilities_and_expiration(): void
    {
        $response = $this->postJson('/api/login', [
            'email' => 'member.alpha@gym.com',
            'password' => 'password',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'token', 'token_type', 'abilities', 'expires_at', 'user'
            ]);

        $data = $response->json();
        $this->assertContains('create:bookings', $data['abilities']);
        $this->assertContains('view:own-bookings', $data['abilities']);
        $this->assertNotContains('manage:gyms', $data['abilities']);
        
        // Expiration check: should be ~30 days in future
        $expiresAt = new \DateTime($data['expires_at']);
        $difference = $expiresAt->getTimestamp() - time();
        $this->assertGreaterThan(29 * 24 * 60 * 60, $difference);
        $this->assertLessThan(31 * 24 * 60 * 60, $difference);
    }

    /**
     * Test API login with invalid credentials.
     */
    public function test_api_login_fails_with_invalid_credentials(): void
    {
        $response = $this->postJson('/api/login', [
            'email' => 'member.alpha@gym.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertStatus(401)
            ->assertJson([
                'message' => 'Invalid credentials.'
            ]);
    }

    /**
     * Test middleware blocks route access if missing ability.
     */
    public function test_abilities_middleware_blocks_access_with_403_json(): void
    {
        // Member logs in to get member token (abilities: view:own-bookings, create:bookings)
        $token = $this->memberA->createToken('test-token', $this->memberA->getAbilitiesByRole())->plainTextToken;

        // Try to access gym bookings endpoint (which requires manage:bookings)
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/gym/bookings');

        $response->assertStatus(403)
            ->assertJson([
                'message' => 'Invalid abilities.'
            ]);
    }

    /**
     * Test token revocation endpoints.
     */
    public function test_tokens_can_be_revoked_successfully(): void
    {
        // 1. Revoke Current Token
        $tokenResult = $this->memberA->createToken('test-token', $this->memberA->getAbilitiesByRole());
        $token = $tokenResult->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->deleteJson('/api/tokens/current');

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Current token revoked successfully.'
            ]);

        // Force forgetting guard state so retry request re-authenticates
        $this->app['auth']->forgetGuards();

        // Attempting to reuse should fail with 401
        $retryResponse = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/member/bookings');

        $retryResponse->assertStatus(401);

        // 2. Revoke Specific Token
        $this->app['auth']->forgetGuards();
        $token1 = $this->memberA->createToken('token-1', $this->memberA->getAbilitiesByRole());
        $token2 = $this->memberA->createToken('token-2', $this->memberA->getAbilitiesByRole());

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token1->plainTextToken,
        ])->deleteJson('/api/tokens/' . $token2->accessToken->id);

        $response->assertStatus(200);

        // Verify token2 is deleted
        $this->assertNull($this->memberA->tokens()->find($token2->accessToken->id));
        $this->assertNotNull($this->memberA->tokens()->find($token1->accessToken->id));

        // 3. Revoke All Tokens
        $this->app['auth']->forgetGuards();
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token1->plainTextToken,
        ])->deleteJson('/api/tokens/revoke-all');

        $response->assertStatus(200);
        $this->assertEquals(0, $this->memberA->tokens()->count());
    }

    /**
     * Test expired tokens are rejected.
     */
    public function test_expired_tokens_are_rejected_with_401(): void
    {
        $tokenResult = $this->memberA->createToken('expired-token', $this->memberA->getAbilitiesByRole());
        $token = $tokenResult->plainTextToken;

        // Manually expire the token in database
        DB::table('personal_access_tokens')
            ->where('id', $tokenResult->accessToken->id)
            ->update(['expires_at' => now()->subHour()]);

        // Clear auth guard cache
        $this->app['auth']->forgetGuards();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/member/bookings');

        $response->assertStatus(401)
            ->assertJson([
                'message' => 'Unauthenticated.'
            ]);
    }

    /**
     * Test multi-tenant gym isolation constraints.
     */
    public function test_gym_isolation_is_enforced_across_apis(): void
    {
        // Create booking for Gym A
        Booking::create([
            'gym_id' => $this->gymA->id,
            'user_id' => $this->memberA->id,
            'timeslot_id' => $this->timeslotA->id,
            'booking_date' => now()->toDateString(),
            'status' => 'confirmed',
            'payment_status' => 'paid',
            'payment_amount' => 15.00,
        ]);

        // Generate tokens
        $tokenAdminA = $this->adminA->createToken('admin-a', $this->adminA->getAbilitiesByRole())->plainTextToken;
        $tokenAdminB = $this->adminB->createToken('admin-b', $this->adminB->getAbilitiesByRole())->plainTextToken;

        // 1. Gym Admin A gets bookings (only gets Alpha Gym bookings)
        $this->app['auth']->forgetGuards();
        $responseA = $this->withHeaders([
            'Authorization' => 'Bearer ' . $tokenAdminA,
        ])->getJson('/api/gym/bookings');

        $responseA->assertStatus(200);
        $this->assertCount(1, $responseA->json());

        // 2. Gym Admin B gets bookings (gets 0 since Beta Gym has no bookings)
        $this->app['auth']->forgetGuards();
        $responseB = $this->withHeaders([
            'Authorization' => 'Bearer ' . $tokenAdminB,
        ])->getJson('/api/gym/bookings');

        $responseB->assertStatus(200);
        $this->assertCount(0, $responseB->json());

        // 3. Member B tries to book a class in Gym A (cross-tenant booking access check)
        $tokenMemberB = $this->memberB->createToken('member-b', $this->memberB->getAbilitiesByRole())->plainTextToken;

        $this->app['auth']->forgetGuards();
        $responseBook = $this->withHeaders([
            'Authorization' => 'Bearer ' . $tokenMemberB,
        ])->postJson('/api/member/bookings', [
            'timeslot_id' => $this->timeslotA->id,
        ]);

        // Should be forbidden since the timeslot is at Gym A and member belongs to Gym B
        $responseBook->assertStatus(403)
            ->assertJson([
                'message' => 'Timeslot does not belong to your gym.'
            ]);
    }

    /**
     * Test API Tokens view is renderable for Gym Admins.
     */
    public function test_api_tokens_page_is_renderable_for_admin(): void
    {
        $response = $this->actingAs($this->adminA)->get('/user/api-tokens');
        $response->assertStatus(200);
    }

    /**
     * Test API Tokens view is forbidden for Members.
     */
    public function test_api_tokens_page_is_forbidden_for_member(): void
    {
        $response = $this->actingAs($this->memberA)->get('/user/api-tokens');
        $response->assertStatus(403);
    }

    /**
     * Test API Tokens view is forbidden for Trainers.
     */
    public function test_api_tokens_page_is_forbidden_for_trainer(): void
    {
        $response = $this->actingAs($this->trainerA)->get('/user/api-tokens');
        $response->assertStatus(403);
    }

    /**
     * Test one-time token visibility, custom glowgym_ prefix, and secure masked storage.
     */
    public function test_one_time_token_visibility_and_masked_database_storage(): void
    {
        // 1. Generate token
        $tokenResult = $this->adminA->createToken('test-secure-token', ['manage:bookings']);
        
        // Plain text token must be returned once
        $plainToken = $tokenResult->plainTextToken;
        $this->assertStringStartsWith('glowgym_', $plainToken);

        // Fetch from database
        $dbToken = DB::table('personal_access_tokens')
            ->where('id', $tokenResult->accessToken->id)
            ->first();

        // 2. Database must not contain plain-text token in raw format
        $this->assertNotEquals($plainToken, $dbToken->token);
        // The token stored must be SHA-256 hash
        $this->assertEquals(hash('sha256', $plainToken), $dbToken->token);

        // 3. Database contains correct masked token matching 'glowgym_XXXX****XXXX' format
        $this->assertNotNull($dbToken->masked_token);
        $this->assertStringStartsWith('glowgym_', $dbToken->masked_token);
        $this->assertStringContainsString('****', $dbToken->masked_token);

        // 4. Test Livewire component state
        \Livewire\Livewire::actingAs($this->adminA)
            ->test(\App\Livewire\Profile\ApiTokenManager::class)
            ->set('name', 'Livewire Secure Token')
            ->set('selectedAbilities', ['manage:bookings' => true])
            ->call('createToken')
            ->assertSet('name', '')
            ->assertSet('selectedAbilities', [])
            ->assertSee('glowgym_') // Verify displayToken is rendered in the modal view
            ->call('closeTokenModal')
            ->assertSet('displayToken', null);
    }
}
