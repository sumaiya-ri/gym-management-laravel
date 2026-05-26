<?php

namespace App\Livewire\GymAdmin;

use Livewire\Component;
use App\Models\Booking;
use App\Models\User;
use Carbon\Carbon;

class LiveNotificationFeed extends Component
{
    public function render()
    {
        $gymId = auth()->user()->gym_id;

        // Eager load relationships to optimize performance and prevent N+1 queries
        $bookings = Booking::with(['user', 'timeslot.service'])
            ->where('gym_id', $gymId)
            ->orderBy('created_at', 'desc')
            ->take(15)
            ->get();

        $cancellations = Booking::with(['user', 'timeslot.service'])
            ->where('gym_id', $gymId)
            ->where('status', 'cancelled')
            ->orderBy('updated_at', 'desc')
            ->take(15)
            ->get();

        $members = User::where('gym_id', $gymId)
            ->where('role', 'member')
            ->orderBy('created_at', 'desc')
            ->take(15)
            ->get();

        $activities = [];

        // 1. Process Bookings
        foreach ($bookings as $b) {
            if ($b->user) {
                $activities[] = [
                    'id' => 'booking_created_' . $b->id,
                    'type' => 'booking_created',
                    'title' => 'New Class Booking',
                    'description' => $b->user->name . ' booked "' . ($b->timeslot->service->name ?? 'Fitness Class') . '"',
                    'timestamp' => $b->created_at,
                    'badge_class' => 'bg-purple-50 text-purple-600 border border-purple-100',
                    'icon' => '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>',
                ];

                if ($b->payment_status === 'paid') {
                    $activities[] = [
                        'id' => 'payment_success_' . $b->id,
                        'type' => 'payment_success',
                        'title' => 'Successful Payment',
                        'description' => $b->user->name . ' paid $' . number_format($b->payment_amount, 2) . ' for "' . ($b->timeslot->service->name ?? 'Fitness Class') . '"',
                        'timestamp' => $b->created_at,
                        'badge_class' => 'bg-emerald-50 text-emerald-600 border border-emerald-100',
                        'icon' => '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>',
                    ];
                }
            }
        }

        // 2. Process Cancellations
        foreach ($cancellations as $c) {
            if ($c->user) {
                $activities[] = [
                    'id' => 'booking_cancelled_' . $c->id,
                    'type' => 'booking_cancelled',
                    'title' => 'Booking Cancelled',
                    'description' => $c->user->name . ' cancelled "' . ($c->timeslot->service->name ?? 'Fitness Class') . '"',
                    'timestamp' => $c->updated_at,
                    'badge_class' => 'bg-rose-50 text-rose-600 border border-rose-100',
                    'icon' => '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>',
                ];
            }
        }

        // 3. Process New Members
        foreach ($members as $m) {
            $activities[] = [
                'id' => 'member_registered_' . $m->id,
                'type' => 'member_registered',
                'title' => 'New Member Joined',
                'description' => $m->name . ' registered with the gym',
                'timestamp' => $m->created_at,
                'badge_class' => 'bg-indigo-50 text-indigo-600 border border-indigo-100',
                'icon' => '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>',
            ];
        }

        // Sort unified activities desc by timestamp
        usort($activities, function ($a, $b) {
            return $b['timestamp']->timestamp <=> $a['timestamp']->timestamp;
        });

        // Limit to latest 10 items
        $latestActivities = array_slice($activities, 0, 10);

        return view('livewire.gym-admin.live-notification-feed', [
            'activities' => $latestActivities,
        ]);
    }
}
