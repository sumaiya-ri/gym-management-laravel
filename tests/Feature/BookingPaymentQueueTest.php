<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Gym;
use App\Models\Trainer;
use App\Models\Service;
use App\Models\Timeslot;
use App\Models\Booking;
use App\Jobs\SendBookingConfirmationEmail;
use App\Jobs\SendAdminBookingNotificationEmail;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class BookingPaymentQueueTest extends TestCase
{
    use RefreshDatabase;

    protected $gym;
    protected $admin;
    protected $trainer;
    protected $service;
    protected $timeslot;
    protected $member;

    protected function setUp(): void
    {
        parent::setUp();

        $this->gym = Gym::create([
            'name' => 'Sana Pilates',
            'email' => 'sana@gmail.com',
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

    /**
     * Test checkout payment succeeds and dispatches jobs.
     */
    public function test_booking_payment_checkout_succeeds_and_dispatches_jobs(): void
    {
        Queue::fake();

        $response = $this->actingAs($this->member)->post(route('member.payment.process', $this->timeslot->id), [
            'cardholder_name' => 'Alice Member',
            'card_number' => '4111222233334444',
            'expiry' => '12/28',
            'cvv' => '123',
        ]);

        // Success redirect
        $response->assertRedirect();
        
        // Assert booking exists in database
        $this->assertDatabaseHas('bookings', [
            'user_id' => $this->member->id,
            'timeslot_id' => $this->timeslot->id,
            'status' => 'confirmed',
            'payment_status' => 'paid',
        ]);

        // Assert timeslot capacity is decremented
        $this->assertEquals(9, $this->timeslot->fresh()->capacity);

        // Assert jobs are dispatched
        Queue::assertPushed(SendBookingConfirmationEmail::class);
        Queue::assertPushed(SendAdminBookingNotificationEmail::class);
    }

    /**
     * Test checkout payment failing on CVV 999.
     */
    public function test_booking_payment_checkout_fails_on_declined_cvv(): void
    {
        Queue::fake();

        $response = $this->actingAs($this->member)->post(route('member.payment.process', $this->timeslot->id), [
            'cardholder_name' => 'Alice Member',
            'card_number' => '4111222233334444',
            'expiry' => '12/28',
            'cvv' => '999', // Declines payment
        ]);

        $response->assertRedirect(route('member.payment.failed', $this->timeslot->id));

        // Assert booking does NOT exist in database
        $this->assertDatabaseMissing('bookings', [
            'user_id' => $this->member->id,
            'timeslot_id' => $this->timeslot->id,
        ]);

        // Assert capacity is NOT decremented
        $this->assertEquals(10, $this->timeslot->fresh()->capacity);

        // Assert no jobs were dispatched
        Queue::assertNotPushed(SendBookingConfirmationEmail::class);
        Queue::assertNotPushed(SendAdminBookingNotificationEmail::class);
    }

    /**
     * Test SendBookingConfirmationEmail job failure method.
     */
    public function test_booking_confirmation_email_job_failure_logging(): void
    {
        $booking = Booking::create([
            'gym_id' => $this->gym->id,
            'user_id' => $this->member->id,
            'timeslot_id' => $this->timeslot->id,
            'booking_date' => now(),
            'status' => 'confirmed',
            'payment_status' => 'paid',
            'payment_amount' => 25.00,
            'payment_transaction_id' => 'TXN-TEST',
        ]);

        Log::shouldReceive('error')
            ->once()
            ->withArgs(function ($message) use ($booking) {
                return str_contains($message, 'Booking confirmation email job failed')
                    && str_contains($message, (string)$booking->id)
                    && str_contains($message, 'alice@gmail.com');
            });

        $job = new SendBookingConfirmationEmail($booking);
        $job->failed(new \Exception('SMTP connection timed out'));
    }

    /**
     * Test SendAdminBookingNotificationEmail job failure method.
     */
    public function test_admin_booking_notification_email_job_failure_logging(): void
    {
        $booking = Booking::create([
            'gym_id' => $this->gym->id,
            'user_id' => $this->member->id,
            'timeslot_id' => $this->timeslot->id,
            'booking_date' => now(),
            'status' => 'confirmed',
            'payment_status' => 'paid',
            'payment_amount' => 25.00,
            'payment_transaction_id' => 'TXN-TEST',
        ]);

        Log::shouldReceive('error')
            ->once()
            ->withArgs(function ($message) use ($booking) {
                return str_contains($message, 'Admin booking notification email job failed')
                    && str_contains($message, (string)$booking->id)
                    && str_contains($message, 'sana@gmail.com');
            });

        $job = new SendAdminBookingNotificationEmail($booking);
        $job->failed(new \Exception('Network unreachable'));
    }

    /**
     * Test that direct booking route (POST member.book) redirects to payment checkout.
     */
    public function test_direct_booking_route_redirects_to_checkout(): void
    {
        $response = $this->actingAs($this->member)->post(route('member.book', $this->timeslot->id));

        $response->assertRedirect(route('member.payment.checkout', $this->timeslot->id));
    }

    /**
     * Test that booking cancellation restores capacity.
     */
    public function test_booking_cancellation_restores_capacity(): void
    {
        $booking = Booking::create([
            'gym_id' => $this->gym->id,
            'user_id' => $this->member->id,
            'timeslot_id' => $this->timeslot->id,
            'booking_date' => now(),
            'status' => 'confirmed',
            'payment_status' => 'paid',
            'payment_amount' => 25.00,
            'payment_transaction_id' => 'TXN-TEST',
        ]);

        // Decrement capacity initially (as done during checkout process)
        $this->timeslot->decrement('capacity');
        $this->assertEquals(9, $this->timeslot->fresh()->capacity);

        // Cancel booking
        $response = $this->actingAs($this->member)->post(route('member.cancel', $booking->id));

        $response->assertRedirect();
        
        // Assert status updated to cancelled
        $this->assertEquals('cancelled', $booking->fresh()->status);

        // Assert capacity is restored back to 10
        $this->assertEquals(10, $this->timeslot->fresh()->capacity);
    }
}
