<?php

namespace App\Livewire\Member;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Timeslot;
use App\Models\Booking;
use App\Models\Service;
use Carbon\Carbon;

class LiveClassSearch extends Component
{
    use WithPagination;

    public $search = '';
    public $category = '';
    public $date = '';

    protected $queryString = [
        'search' => ['except' => ''],
        'category' => ['except' => ''],
        'date' => ['except' => ''],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingCategory()
    {
        $this->resetPage();
    }

    public function updatingDate()
    {
        $this->resetPage();
    }

    public function render()
    {
        $gymId = auth()->user()->gym_id;

        // Fetch categories (services) for the filter dropdown
        $categories = Service::where('gym_id', $gymId)
            ->where('status', 'active')
            ->orderBy('name')
            ->get();

        // Build the timeslots query with eager loading
        $query = Timeslot::with(['service', 'trainer', 'bookings'])
            ->withCount('bookings')
            ->where('gym_id', $gymId)
            ->where('date', '>=', Carbon::today())
            ->where('status', 'active');

        // Apply text search on service name or trainer name
        if ($this->search) {
            $query->where(function ($q) {
                $q->whereHas('service', function ($sq) {
                    $sq->where('name', 'like', '%' . $this->search . '%');
                })->orWhereHas('trainer', function ($tq) {
                    $tq->where('name', 'like', '%' . $this->search . '%');
                });
            });
        }

        // Apply category filter
        if ($this->category) {
            $query->where('service_id', $this->category);
        }

        // Apply date filter
        if ($this->date) {
            $query->where('date', $this->date);
        }

        $classes = $query->orderBy('date')
            ->orderBy('start_time')
            ->paginate(9);

        // Fetch user's booked timeslots (excluding cancelled)
        $myBookedIds = Booking::where('user_id', auth()->id())
            ->where('status', '!=', 'cancelled')
            ->pluck('timeslot_id')
            ->toArray();

        return view('livewire.member.live-class-search', [
            'classes' => $classes,
            'categories' => $categories,
            'myBookedIds' => $myBookedIds,
        ]);
    }
}
