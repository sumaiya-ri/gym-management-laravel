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
use Livewire\Livewire;
use App\Livewire\Member\LiveClassSearch;
use App\Livewire\Member\LiveSeatAvailability;
use App\Livewire\GymAdmin\LiveNotificationFeed;
use Carbon\Carbon;

class LivewireAdvancedFeaturesTest extends TestCase
{
    use RefreshDatabase;

    protected $gym;
    protected $gymAdmin;
    protected $member;
    protected $trainerUser;
    protected $trainer;
    protected $service1;
    protected $service2;
    protected $timeslot1;
    protected $timeslot2;

    protected function setUp(): void
    {
        parent::setUp();

        // Create gym
        $this->gym = Gym::create([
            'name' => 'GlowGym North',
            'email' => 'north@glowgym.com',
            'subscription_status' => 'active',
        ]);

        // Create gym admin
        $this->gymAdmin = User::create([
            'name' => 'Gym Admin User',
            'email' => 'admin@glowgym.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'gym_id' => $this->gym->id,
            'email_verified_at' => now(),
        ]);

        // Create member
        $this->member = User::create([
            'name' => 'Member User',
            'email' => 'member@glowgym.com',
            'password' => bcrypt('password'),
            'role' => 'member',
            'gym_id' => $this->gym->id,
            'email_verified_at' => now(),
        ]);

        // Create trainer
        $this->trainerUser = User::create([
            'name' => 'Trainer User',
            'email' => 'trainer@glowgym.com',
            'password' => bcrypt('password'),
            'role' => 'trainer',
            'gym_id' => $this->gym->id,
            'email_verified_at' => now(),
        ]);
        $this->trainer = Trainer::create([
            'gym_id' => $this->gym->id,
            'user_id' => $this->trainerUser->id,
            'name' => 'John Trainer',
            'specialization' => 'Yoga',
            'status' => 'active',
        ]);

        // Create services
        $this->service1 = Service::create([
            'gym_id' => $this->gym->id,
            'name' => 'Vinyasa Yoga',
            'duration' => 60,
            'status' => 'active',
        ]);
        $this->service2 = Service::create([
            'gym_id' => $this->gym->id,
            'name' => 'Pilates Core',
            'duration' => 45,
            'status' => 'active',
        ]);

        // Create timeslots
        $this->timeslot1 = Timeslot::create([
            'gym_id' => $this->gym->id,
            'service_id' => $this->service1->id,
            'trainer_id' => $this->trainer->id,
            'date' => Carbon::tomorrow()->toDateString(),
            'start_time' => '09:00:00',
            'end_time' => '10:00:00',
            'capacity' => 15,
            'status' => 'active',
        ]);
        $this->timeslot2 = Timeslot::create([
            'gym_id' => $this->gym->id,
            'service_id' => $this->service2->id,
            'trainer_id' => $this->trainer->id,
            'date' => Carbon::tomorrow()->toDateString(),
            'start_time' => '11:00:00',
            'end_time' => '11:45:00',
            'capacity' => 10,
            'status' => 'active',
        ]);
    }

    /**
     * Test LiveClassSearch component renders correctly.
     */
    public function test_live_class_search_renders_correctly(): void
    {
        $this->actingAs($this->member);

        Livewire::test(LiveClassSearch::class)
            ->assertStatus(200)
            ->assertSee('Vinyasa Yoga')
            ->assertSee('Pilates Core');
    }

    /**
     * Test LiveClassSearch filters classes properly by search query.
     */
    public function test_live_class_search_filters_by_query(): void
    {
        $this->actingAs($this->member);

        // Search for Yoga
        Livewire::test(LiveClassSearch::class)
            ->set('search', 'Yoga')
            ->assertViewHas('classes', function ($classes) {
                return $classes->contains(fn($c) => $c->service->name === 'Vinyasa Yoga')
                    && !$classes->contains(fn($c) => $c->service->name === 'Pilates Core');
            });

        // Search for Pilates
        Livewire::test(LiveClassSearch::class)
            ->set('search', 'Pilates')
            ->assertViewHas('classes', function ($classes) {
                return $classes->contains(fn($c) => $c->service->name === 'Pilates Core')
                    && !$classes->contains(fn($c) => $c->service->name === 'Vinyasa Yoga');
            });
    }

    /**
     * Test LiveClassSearch filters classes by category.
     */
    public function test_live_class_search_filters_by_category(): void
    {
        $this->actingAs($this->member);

        Livewire::test(LiveClassSearch::class)
            ->set('category', $this->service2->id)
            ->assertViewHas('classes', function ($classes) {
                return $classes->contains(fn($c) => $c->service->name === 'Pilates Core')
                    && !$classes->contains(fn($c) => $c->service->name === 'Vinyasa Yoga');
            });
    }

    /**
     * Test LiveClassSearch filters classes by date.
     */
    public function test_live_class_search_filters_by_date(): void
    {
        $this->actingAs($this->member);

        $tomorrow = Carbon::tomorrow()->toDateString();
        $dayAfter = Carbon::tomorrow()->addDay()->toDateString();

        Livewire::test(LiveClassSearch::class)
            ->set('date', $tomorrow)
            ->assertViewHas('classes', function ($classes) {
                return $classes->contains(fn($c) => $c->service->name === 'Vinyasa Yoga')
                    && $classes->contains(fn($c) => $c->service->name === 'Pilates Core');
            });

        Livewire::test(LiveClassSearch::class)
            ->set('date', $dayAfter)
            ->assertViewHas('classes', function ($classes) {
                return $classes->isEmpty();
            });
    }

    /**
     * Test LiveSeatAvailability badge count updates correctly.
     */
    public function test_live_seat_availability_badge_updates(): void
    {
        Livewire::test(LiveSeatAvailability::class, [
            'timeslotId' => $this->timeslot1->id,
            'displayType' => 'badge',
        ])
            ->assertSee('15 spots left');

        // Decrease capacity
        $this->timeslot1->update(['capacity' => 5]);

        Livewire::test(LiveSeatAvailability::class, [
            'timeslotId' => $this->timeslot1->id,
            'displayType' => 'badge',
        ])
            ->assertSee('5 spots left');

        // Out of capacity
        $this->timeslot1->update(['capacity' => 0]);

        Livewire::test(LiveSeatAvailability::class, [
            'timeslotId' => $this->timeslot1->id,
            'displayType' => 'badge',
        ])
            ->assertSee('Fully Booked');
    }

    /**
     * Test LiveSeatAvailability button disables when fully booked.
     */
    public function test_live_seat_availability_button_disables_when_full(): void
    {
        // 1. With capacity available
        Livewire::test(LiveSeatAvailability::class, [
            'timeslotId' => $this->timeslot1->id,
            'displayType' => 'button',
        ])
            ->assertDontSee('Fully Booked')
            ->assertDontSee('disabled');

        // 2. Set capacity to 0
        $this->timeslot1->update(['capacity' => 0]);

        Livewire::test(LiveSeatAvailability::class, [
            'timeslotId' => $this->timeslot1->id,
            'displayType' => 'button',
        ])
            ->assertSee('Fully Booked')
            ->assertSee('disabled');
    }

    /**
     * Test LiveNotificationFeed component loads without errors.
     */
    public function test_live_notification_feed_loads_and_shows_activities(): void
    {
        $this->actingAs($this->gymAdmin);

        // 1. Create a booking to trigger activity
        $booking = Booking::create([
            'gym_id' => $this->gym->id,
            'user_id' => $this->member->id,
            'timeslot_id' => $this->timeslot1->id,
            'booking_date' => now(),
            'status' => 'confirmed',
            'payment_status' => 'paid',
            'payment_amount' => 25.00,
            'payment_transaction_id' => 'TXN-NOTIF-TEST',
        ]);

        Livewire::test(LiveNotificationFeed::class)
            ->assertStatus(200)
            ->assertSee('New Class Booking')
            ->assertSee('Successful Payment')
            ->assertSee($this->member->name)
            ->assertSee('booked')
            ->assertSee('paid');
    }
}
