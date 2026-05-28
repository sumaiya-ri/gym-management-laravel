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
use App\Services\StripeService;
use App\Services\MongoDBService;
use Illuminate\Support\Facades\Queue;
use Mockery;
use Carbon\Carbon;

class StripePaymentTest extends TestCase
{
    use RefreshDatabase;

    protected $gym;
    protected $admin;
    protected $member;
    protected $timeslot;
    protected $trainer;
    protected $service;
    protected $trainerUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->gym = Gym::create([
            'name' => 'Sana Pilates',
            'email' => 'sana@gmail.com',
            'subscription_plan' => 'Professional',
            'subscription_status' => 'inactive',
        ]);

        $this->admin = User::create([
            'name' => 'Sana Admin',
            'email' => 'sana@gmail.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'gym_id' => $this->gym->id,
        ]);

        $this->trainerUser = User::create([
            'name' => 'John Trainer',
            'email' => 'johndoe@gymsaas.com',
            'password' => bcrypt('password'),
            'role' => 'trainer',
            'gym_id' => $this->gym->id,
        ]);

        $this->trainer = Trainer::create([
            'gym_id' => $this->gym->id,
            'user_id' => $this->trainerUser->id,
            'name' => 'John Trainer',
            'specialization' => 'Yoga Specialist',
            'hourly_rate' => 50,
            'status' => 'active',
        ]);

        $this->service = Service::create([
            'gym_id' => $this->gym->id,
            'name' => 'Power Pilates',
            'duration' => 60,
            'status' => 'active',
        ]);

        $this->timeslot = Timeslot::create([
            'gym_id' => $this->gym->id,
            'service_id' => $this->service->id,
            'trainer_id' => $this->trainer->id,
            'date' => Carbon::tomorrow()->toDateString(),
            'start_time' => '10:00:00',
            'end_time' => '11:00:00',
            'capacity' => 10,
            'status' => 'active',
        ]);

        $this->member = User::create([
            'name' => 'Alice Member',
            'email' => 'alice@gmail.com',
            'password' => bcrypt('password'),
            'role' => 'member',
            'gym_id' => $this->gym->id,
        ]);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_fallback_simulation_is_used_when_stripe_disabled(): void
    {
        // StripeService::isEnabled should return false because keys are empty in test environment by default
        $response = $this->actingAs($this->member)->get(route('member.payment.checkout', $this->timeslot->id));
        $response->assertStatus(200);
        $response->assertSee('Simulated Credit Card Payment');
    }

    public function test_stripe_ui_rendered_when_stripe_enabled(): void
    {
        $mockStripe = Mockery::mock(StripeService::class);
        $mockStripe->shouldReceive('isEnabled')->andReturn(true);
        $this->app->instance(StripeService::class, $mockStripe);

        $response = $this->actingAs($this->member)->get(route('member.payment.checkout', $this->timeslot->id));
        $response->assertStatus(200);
        $response->assertSee('Secure Online Payment');
        $response->assertSee('Proceed to Stripe Payment');
    }

    public function test_stripe_booking_checkout_redirects_to_stripe_url(): void
    {
        $mockSession = Mockery::mock(\Stripe\Checkout\Session::class);
        $mockSession->id = 'sess_test_123';
        $mockSession->url = 'https://checkout.stripe.com/pay/sess_test_123';

        $mockStripe = Mockery::mock(StripeService::class);
        $mockStripe->shouldReceive('isEnabled')->andReturn(true);
        $mockStripe->shouldReceive('createBookingSession')->once()->andReturn($mockSession);
        $this->app->instance(StripeService::class, $mockStripe);

        $response = $this->actingAs($this->member)->post(route('member.payment.process', $this->timeslot->id));
        
        $response->assertRedirect('https://checkout.stripe.com/pay/sess_test_123');

        $this->assertDatabaseHas('bookings', [
            'user_id' => $this->member->id,
            'timeslot_id' => $this->timeslot->id,
            'status' => 'pending',
            'payment_status' => 'pending',
            'stripe_session_id' => 'sess_test_123'
        ]);
    }

    public function test_stripe_webhook_activates_subscription(): void
    {
        Queue::fake();

        $payload = [
            'type' => 'checkout.session.completed',
            'data' => [
                'object' => [
                    'id' => 'sess_sub_123',
                    'payment_status' => 'paid',
                    'subscription' => 'sub_stripe_abc',
                    'payment_intent' => 'pi_stripe_xyz',
                    'amount_total' => 5900,
                    'metadata' => [
                        'type' => 'subscription',
                        'gym_id' => (string)$this->gym->id,
                        'admin_user_id' => (string)$this->admin->id
                    ]
                ]
            ]
        ];

        $response = $this->postJson(route('stripe.webhook'), $payload);
        $response->assertStatus(200);

        $this->gym->refresh();
        $this->assertEquals('active', $this->gym->subscription_status);
        $this->assertEquals('sub_stripe_abc', $this->gym->subscription_transaction_id);
        $this->assertEquals('stripe', $this->gym->payment_method);
        $this->assertEquals(59.00, $this->gym->amount_paid);

        $analytics = MongoDBService::collection('subscription_analytics')->find([
            'gym_id' => $this->gym->id
        ]);
        $this->assertCount(1, $analytics);
        $this->assertEquals('active', $analytics[0]['status']);
    }

    public function test_stripe_webhook_confirms_booking(): void
    {
        Queue::fake();

        $booking = Booking::create([
            'gym_id' => $this->gym->id,
            'user_id' => $this->member->id,
            'timeslot_id' => $this->timeslot->id,
            'booking_date' => now(),
            'status' => 'pending',
            'payment_status' => 'pending',
            'payment_amount' => 25.00,
            'stripe_session_id' => 'sess_booking_123'
        ]);

        $payload = [
            'type' => 'checkout.session.completed',
            'data' => [
                'object' => [
                    'id' => 'sess_booking_123',
                    'payment_status' => 'paid',
                    'payment_intent' => 'pi_booking_xyz',
                    'amount_total' => 2500,
                    'metadata' => [
                        'type' => 'booking',
                        'booking_id' => (string)$booking->id,
                        'user_id' => (string)$this->member->id
                    ]
                ]
            ]
        ];

        $response = $this->postJson(route('stripe.webhook'), $payload);
        $response->assertStatus(200);

        $booking->refresh();
        $this->assertEquals('confirmed', $booking->status);
        $this->assertEquals('paid', $booking->payment_status);
        $this->assertEquals('pi_booking_xyz', $booking->payment_transaction_id);
        $this->assertEquals(9, $this->timeslot->fresh()->capacity);
    }
}
