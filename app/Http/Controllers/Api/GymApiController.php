<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Trainer;
use App\Models\Timeslot;
use Illuminate\Http\Request;

class GymApiController extends Controller
{
    /**
     * List bookings for the Gym Admin's gym.
     */
    public function getGymBookings(Request $request)
    {
        $gymId = $request->user()->gym_id;

        if (!$gymId) {
            return response()->json(['message' => 'User does not belong to any gym.'], 400);
        }

        $bookings = Booking::where('gym_id', $gymId)
            ->with(['user:id,name,email', 'timeslot.service'])
            ->latest()
            ->get();

        return response()->json($bookings, 200);
    }

    /**
     * List trainers for the Gym Admin's gym.
     */
    public function getGymTrainers(Request $request)
    {
        $gymId = $request->user()->gym_id;

        if (!$gymId) {
            return response()->json(['message' => 'User does not belong to any gym.'], 400);
        }

        $trainers = Trainer::where('gym_id', $gymId)->get();

        return response()->json($trainers, 200);
    }

    /**
     * List workouts (timeslots) for the Trainer's gym.
     */
    public function getTrainerWorkouts(Request $request)
    {
        $gymId = $request->user()->gym_id;

        if (!$gymId) {
            return response()->json(['message' => 'User does not belong to any gym.'], 400);
        }

        $workouts = Timeslot::where('gym_id', $gymId)
            ->with(['service', 'trainer'])
            ->latest()
            ->get();

        return response()->json($workouts, 200);
    }

    /**
     * List bookings for the authenticated member.
     */
    public function getMemberBookings(Request $request)
    {
        $bookings = Booking::where('user_id', $request->user()->id)
            ->with(['timeslot.service', 'timeslot.trainer', 'gym'])
            ->latest()
            ->get();

        return response()->json($bookings, 200);
    }

    /**
     * Create a booking for the authenticated member.
     */
    public function createMemberBooking(Request $request)
    {
        $request->validate([
            'timeslot_id' => 'required|integer|exists:timeslots,id',
        ]);

        $gymId = $request->user()->gym_id;

        // Verify the timeslot belongs to the member's gym to enforce isolation
        $timeslot = Timeslot::where('id', $request->timeslot_id)
            ->where('gym_id', $gymId)
            ->first();

        if (!$timeslot) {
            return response()->json([
                'message' => 'Timeslot does not belong to your gym.'
            ], 403);
        }

        if ($timeslot->capacity <= 0) {
            return response()->json([
                'message' => 'This class is fully booked.'
            ], 422);
        }

        // Create the booking
        $booking = Booking::create([
            'gym_id' => $gymId,
            'user_id' => $request->user()->id,
            'timeslot_id' => $timeslot->id,
            'booking_date' => now()->toDateString(),
            'status' => 'confirmed',
            'payment_status' => 'unpaid',
            'payment_amount' => 0.00,
        ]);

        // Decrement timeslot capacity
        $timeslot->decrement('capacity');

        return response()->json([
            'message' => 'Booking created successfully.',
            'booking' => $booking->load(['timeslot.service', 'timeslot.trainer'])
        ], 201);
    }
}
