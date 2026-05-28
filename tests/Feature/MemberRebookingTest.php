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
use Carbon\Carbon;

class MemberRebookingTest extends TestCase
{
    use RefreshDatabase;

    protected $gym;
    protected $member;
    protected $trainerUser;
    protected $trainer;
    protected $service;
    protected $timeslot1;
    protected $timeslot2;

    protected function setUp(): void
    {
        parent::setUp();

        $this->gym = Gym::create([
            'name' => 'Test Gym',
            'email' => 'test@gymglow.com',
            'subscription_status' => 'active',
        ]);

        $this->member = User::create([
            'name' => 'Member User',
            'email' => 'member@gymglow.com',
            'password' => bcrypt('password'),
            'role' => 'member',
            'gym_id' => $this->gym->id,
            'email_verified_at' => now(),
        ]);

        $this->trainerUser = User::create([
            'name' => 'Trainer User',
            'email' => 'trainer@gymglow.com',
            'password' => bcrypt('password'),
            'role' => 'trainer',
            'gym_id' => $this->gym->id,
            'email_verified_at' => now(),
        ]);

        $this->trainer = Trainer::create([
            'gym_id' => $this->gym->id,
            'user_id' => $this->trainerUser->id,
            'name' => 'John Trainer',
            'specialization' => 'Pilates',
            'status' => 'active',
        ]);

        $this->service = Service::create([
            'gym_id' => $this->gym->id,
            'name' => 'Pilates',
            'duration' => 60,
            'status' => 'active',
        ]);

        // Create timeslots
        $this->timeslot1 = Timeslot::create([
            'gym_id' => $this->gym->id,
            'service_id' => $this->service->id,
            'trainer_id' => $this->trainer->id,
            'date' => Carbon::tomorrow()->toDateString(),
            'start_time' => '10:00:00',
            'end_time' => '11:00:00',
            'capacity' => 10,
            'status' => 'active',
        ]);

        $this->timeslot2 = Timeslot::create([
            'gym_id' => $this->gym->id,
            'service_id' => $this->service->id,
            'trainer_id' => $this->trainer->id,
            'date' => Carbon::tomorrow()->toDateString(),
            'start_time' => '14:00:00',
            'end_time' => '15:00:00',
            'capacity' => 10,
            'status' => 'active',
        ]);
    }

    /**
     * Test a cancelled booking shows the "Book Class Again" button.
     */
    public function test_cancelled_booking_shows_book_class_again_button(): void
    {
        $this->actingAs($this->member);

        // Create a cancelled booking
        $booking = Booking::create([
            'gym_id' => $this->gym->id,
            'user_id' => $this->member->id,
            'timeslot_id' => $this->timeslot1->id,
            'booking_date' => now(),
            'status' => 'cancelled',
        ]);

        $response = $this->get(route('member.bookings'));

        $response->assertStatus(200);
        $response->assertSee('Book Class Again');
        $response->assertSee(route('member.payment.checkout', $this->timeslot1->id));
    }

    /**
     * Test cancelled bookings do not appear under upcoming sessions on dashboard.
     */
    public function test_cancelled_bookings_not_in_upcoming_sessions_on_dashboard(): void
    {
        $this->actingAs($this->member);

        // Create a cancelled booking and a confirmed booking
        Booking::create([
            'gym_id' => $this->gym->id,
            'user_id' => $this->member->id,
            'timeslot_id' => $this->timeslot1->id,
            'booking_date' => now(),
            'status' => 'cancelled',
        ]);

        Booking::create([
            'gym_id' => $this->gym->id,
            'user_id' => $this->member->id,
            'timeslot_id' => $this->timeslot2->id,
            'booking_date' => now(),
            'status' => 'confirmed',
        ]);

        $response = $this->get(route('member.dashboard'));

        $response->assertStatus(200);
        
        // Should only count/show the confirmed one
        $response->assertViewHas('myUpcoming', function ($upcoming) {
            return $upcoming->count() === 1 && $upcoming->first()->timeslot_id === $this->timeslot2->id;
        });
    }

    /**
     * Test already booked classes do not show up in the upcoming/available classes list on dashboard.
     */
    public function test_already_booked_classes_excluded_from_upcoming_classes_list(): void
    {
        $this->actingAs($this->member);

        // Book timeslot1
        Booking::create([
            'gym_id' => $this->gym->id,
            'user_id' => $this->member->id,
            'timeslot_id' => $this->timeslot1->id,
            'booking_date' => now(),
            'status' => 'confirmed',
        ]);

        $response = $this->get(route('member.dashboard'));

        $response->assertStatus(200);

        // The dashboard list (availableClasses) should only show timeslot2 since timeslot1 is booked.
        $response->assertViewHas('availableClasses', function ($classes) {
            return $classes->count() === 1 && $classes->first()->id === $this->timeslot2->id;
        });

        // Heading must be "Upcoming Classes"
        $response->assertSee('Upcoming Classes');
    }

    /**
     * Test cancelled classes display "Book Class" in Livewire search.
     */
    public function test_cancelled_classes_display_book_class_in_livewire_search(): void
    {
        $this->actingAs($this->member);

        // Create a cancelled booking
        Booking::create([
            'gym_id' => $this->gym->id,
            'user_id' => $this->member->id,
            'timeslot_id' => $this->timeslot1->id,
            'booking_date' => now(),
            'status' => 'cancelled',
        ]);

        // Livewire component test
        Livewire::test(LiveClassSearch::class)
            ->assertViewHas('myBookedIds', []) // Should be empty because the booking is cancelled
            ->assertSee('Book Class');
    }
}
