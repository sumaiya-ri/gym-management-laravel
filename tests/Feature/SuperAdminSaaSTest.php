<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Gym;
use App\Services\MongoDBService;
use App\Jobs\SendSubscriptionConfirmationEmail;
use App\Jobs\SendSuperAdminSubscriptionNotificationEmail;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;

class SuperAdminSaaSTest extends TestCase
{
    use RefreshDatabase;

    protected $superAdmin;
    protected $gymAdmin;
    protected $member;
    protected $gym;

    protected function setUp(): void
    {
        parent::setUp();

        config(['services.stripe.secret' => null]);

        // Create gym
        $this->gym = Gym::create([
            'name' => 'GlowGym Central',
            'email' => 'central@glowgym.com',
            'subscription_plan' => 'Professional',
            'subscription_status' => 'inactive',
        ]);

        // Retrieve or create default super admin
        $this->superAdmin = User::where('email', 'superadmin@gymsaas.com')->first();
        if (!$this->superAdmin) {
            $this->superAdmin = User::create([
                'name' => 'Super Admin User',
                'email' => 'superadmin@gymsaas.com',
                'password' => Hash::make('password'),
                'role' => 'super_admin',
                'email_verified_at' => now(),
            ]);
        } else {
            $this->superAdmin->update(['email_verified_at' => now()]);
        }

        // Create default gym admin
        $this->gymAdmin = User::create([
            'name' => 'Gym Admin User',
            'email' => 'admin@glowgym.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'gym_id' => $this->gym->id,
            'email_verified_at' => now(),
        ]);

        // Create normal member
        $this->member = User::create([
            'name' => 'Alice Member',
            'email' => 'alice@gmail.com',
            'password' => Hash::make('password'),
            'role' => 'member',
            'gym_id' => $this->gym->id,
            'email_verified_at' => now(),
        ]);
    }

    /**
     * Test guest is redirected to login from Super Admin dashboard.
     */
    public function test_super_admin_dashboard_redirects_guest_to_login(): void
    {
        $response = $this->get('/super-admin/dashboard');
        $response->assertRedirect('/login');
    }

    /**
     * Test normal member is denied access to Super Admin dashboard.
     */
    public function test_super_admin_dashboard_denies_normal_member(): void
    {
        $response = $this->actingAs($this->member)->get('/super-admin/dashboard');
        $response->assertStatus(403);
    }

    /**
     * Test gym admin is denied access to Super Admin dashboard.
     */
    public function test_super_admin_dashboard_denies_gym_admin(): void
    {
        $response = $this->actingAs($this->gymAdmin)->get('/super-admin/dashboard');
        $response->assertStatus(403);
    }

    /**
     * Test super admin can access Super Admin dashboard.
     */
    public function test_super_admin_dashboard_allows_super_admin(): void
    {
        $response = $this->actingAs($this->superAdmin)->get('/super-admin/dashboard');
        $response->assertStatus(200);
        $response->assertViewIs('super-admin.dashboard');
    }

    /**
     * Test SaaS pricing page is accessible.
     */
    public function test_saas_pricing_page_is_accessible(): void
    {
        $response = $this->get('/saas/pricing');
        $response->assertStatus(200);
        $response->assertSee('Starter');
        $response->assertSee('Professional');
        $response->assertSee('Enterprise');
    }

    /**
     * Test custom register enterprise form.
     */
    public function test_custom_register_enterprise_form_renders(): void
    {
        $response = $this->get('/register-enterprise?plan=Professional');
        $response->assertStatus(200);
        $response->assertSee('Professional');
    }

    /**
     * Test enterprise registration redirects to checkout.
     */
    public function test_enterprise_registration_creates_records_and_redirects(): void
    {
        $response = $this->post('/register-enterprise', [
            'name' => 'New Gym Admin',
            'email' => 'newadmin@newgym.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'gym_name' => 'New Gym Venture',
            'phone' => '1234567890',
            'address' => '123 Gym Street',
            'plan' => 'Enterprise',
        ]);

        $gym = Gym::where('name', 'New Gym Venture')->first();
        $this->assertNotNull($gym);
        $this->assertEquals('inactive', $gym->subscription_status);
        $this->assertEquals('Enterprise', $gym->subscription_plan);

        $this->assertDatabaseHas('users', [
            'email' => 'newadmin@newgym.com',
            'role' => 'admin',
            'gym_id' => $gym->id,
        ]);

        $response->assertRedirect(route('saas.checkout', ['gym' => $gym->id]));
    }

    /**
     * Test simulated checkout success with CVV 123.
     */
    public function test_simulated_checkout_success_with_cvv_123(): void
    {
        Queue::fake();

        $response = $this->post(route('saas.process', $this->gym->id), [
            'cardholder_name' => 'Gym Admin User',
            'card_number' => '1111222233334444',
            'expiry' => '12/29',
            'cvv' => '123', // CVV 123 triggers success
        ]);

        $response->assertRedirect(route('saas.success', $this->gym->id));

        // Gym subscription must be active
        $this->gym->refresh();
        $this->assertEquals('active', $this->gym->subscription_status);
        $this->assertNotNull($this->gym->subscription_expires_at);
        $this->assertStringStartsWith('SUB-', $this->gym->subscription_transaction_id);

        // Gym Admin is logged in automatically
        $this->assertAuthenticatedAs($this->gymAdmin);

        // Queue jobs dispatched
        Queue::assertPushed(SendSubscriptionConfirmationEmail::class);
        Queue::assertPushed(SendSuperAdminSubscriptionNotificationEmail::class);

        // Check MongoDB mock analytics
        $subLog = MongoDBService::collection('subscription_analytics')->find([
            'gym_id' => $this->gym->id,
            'status' => 'active'
        ]);
        $this->assertCount(1, $subLog);
        $this->assertEquals('Professional', $subLog[0]['plan']);

        $revLog = MongoDBService::collection('gym_revenue_analytics')->find([
            'gym_id' => $this->gym->id
        ]);
        $this->assertCount(1, $revLog);
        $this->assertEquals(59.00, $revLog[0]['amount']);
    }

    /**
     * Test simulated checkout fails on CVV 999.
     */
    public function test_simulated_checkout_fails_on_cvv_999(): void
    {
        Queue::fake();

        $response = $this->post(route('saas.process', $this->gym->id), [
            'cardholder_name' => 'Gym Admin User',
            'card_number' => '1111222233334444',
            'expiry' => '12/29',
            'cvv' => '999', // CVV 999 triggers failure
        ]);

        $response->assertRedirect(route('saas.failed', $this->gym->id));

        // Gym subscription remains inactive
        $this->gym->refresh();
        $this->assertEquals('inactive', $this->gym->subscription_status);

        // No queue jobs dispatched
        Queue::assertNotPushed(SendSubscriptionConfirmationEmail::class);
        Queue::assertNotPushed(SendSuperAdminSubscriptionNotificationEmail::class);

        // Check MongoDB mock analytics
        $failedLog = MongoDBService::collection('subscription_analytics')->find([
            'gym_id' => $this->gym->id,
            'status' => 'failed'
        ]);
        $this->assertCount(1, $failedLog);
        $this->assertStringContainsString('declined', $failedLog[0]['error']);
    }

    /**
     * Test Sanctum API endpoints require authentications and super admin role.
     */
    public function test_sanctum_api_endpoints_restrictions(): void
    {
        // 1. Guest
        $response = $this->getJson('/api/admin/gyms');
        $response->assertStatus(401);

        // 2. Normal gym admin
        Sanctum::actingAs($this->gymAdmin, ['*']);
        $response = $this->getJson('/api/admin/gyms');
        $response->assertStatus(403);

        // 3. Super Admin
        Sanctum::actingAs($this->superAdmin, ['*']);
        $response = $this->getJson('/api/admin/gyms');
        $response->assertStatus(200);

        // Verify structure or data
        $response->assertJsonFragment(['name' => 'GlowGym Central']);
    }

    /**
     * Test Sanctum Subscriptions API.
     */
    public function test_sanctum_subscriptions_api(): void
    {
        // Set up active subscription
        $this->gym->update([
            'subscription_status' => 'active',
            'subscription_expires_at' => now()->addMonth(),
        ]);

        MongoDBService::collection('subscription_analytics')->insertOne([
            'gym_id' => $this->gym->id,
            'gym_name' => $this->gym->name,
            'plan' => 'Professional',
            'status' => 'active',
            'created_at' => now()->toDateTimeString(),
        ]);

        Sanctum::actingAs($this->superAdmin, ['*']);
        $response = $this->getJson('/api/admin/subscriptions');
        $response->assertStatus(200);

        $response->assertJsonStructure([
            'active_gyms',
            'transactions',
        ]);
    }

    /**
     * Test Sanctum Analytics API with MongoDB mock aggregations.
     */
    public function test_sanctum_analytics_api_and_mongodb_aggregations(): void
    {
        // Seed MongoDB mock collections
        MongoDBService::collection('booking_statistics')->insertMany([
            ['gym_name' => 'GlowGym Central', 'class_name' => 'Yoga 101'],
            ['gym_name' => 'GlowGym Central', 'class_name' => 'Yoga 101'],
            ['gym_name' => 'GlowGym North', 'class_name' => 'HIIT Blast'],
            ['gym_name' => 'GlowGym North', 'class_name' => 'Yoga 101'],
        ]);

        MongoDBService::collection('gym_revenue_analytics')->insertMany([
            ['amount' => 59.00, 'created_at' => '2026-05-01 10:00:00'],
            ['amount' => 29.00, 'created_at' => '2026-05-15 12:00:00'],
        ]);

        MongoDBService::collection('subscription_analytics')->insertMany([
            ['plan' => 'Starter'],
            ['plan' => 'Professional'],
            ['plan' => 'Professional'],
        ]);

        Sanctum::actingAs($this->superAdmin, ['*']);
        $response = $this->getJson('/api/admin/analytics');
        $response->assertStatus(200);

        // Verify aggregation results in response
        $data = $response->json();

        // 1. Top gyms: GlowGym Central should have 2 bookings, GlowGym North should have 2 bookings
        $this->assertNotEmpty($data['top_gyms']);
        $this->assertEquals('GlowGym Central', $data['top_gyms'][0]['_id']);
        $this->assertEquals(2, $data['top_gyms'][0]['bookings_count']);

        // 2. Popular classes: Yoga 101 should have 3 bookings
        $this->assertNotEmpty($data['popular_classes']);
        $this->assertEquals('Yoga 101', $data['popular_classes'][0]['_id']);
        $this->assertEquals(3, $data['popular_classes'][0]['bookings_count']);

        // 3. Monthly revenue: should group by month '2026-05' and sum to 88.00
        $this->assertNotEmpty($data['monthly_revenue']);
        $this->assertEquals('2026-05', $data['monthly_revenue'][0]['_id']);
        $this->assertEquals(88.00, $data['monthly_revenue'][0]['total_revenue']);
    }

    /**
     * Test Sanctum Analytics API restrictions and new extended JSON payload structure.
     */
    public function test_sanctum_analytics_api_restrictions_and_extended_structure(): void
    {
        // 1. Guest
        $response = $this->getJson('/api/admin/analytics');
        $response->assertStatus(401);

        // 2. Normal gym admin
        Sanctum::actingAs($this->gymAdmin, ['*']);
        $response = $this->getJson('/api/admin/analytics');
        $response->assertStatus(403);

        // 3. Super Admin but without correct ability/scope
        Sanctum::actingAs($this->superAdmin, ['read:other']);
        $response = $this->getJson('/api/admin/analytics');
        $response->assertStatus(403);

        // 4. Super Admin with correct ability
        Sanctum::actingAs($this->superAdmin, ['read:analytics']);
        $response = $this->getJson('/api/admin/analytics');
        $response->assertStatus(200);

        // Verify structure contains the new properties
        $response->assertJsonStructure([
            'total_gyms',
            'total_members',
            'total_trainers',
            'total_bookings',
            'total_revenue',
            'top_gym',
            'active_subs',
            'expired_subs',
            'starter_count',
            'professional_count',
            'enterprise_count',
        ]);
    }
}
