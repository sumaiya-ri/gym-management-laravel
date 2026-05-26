<?php

namespace App\Livewire;

use Livewire\Component;

class SuperAdminDashboardStats extends Component
{
    public $totalGyms;
    public $totalMembers;
    public $totalTrainers;
    public $totalBookings;

    public function mount($totalGyms, $totalMembers, $totalTrainers, $totalBookings)
    {
        $this->totalGyms = $totalGyms;
        $this->totalMembers = $totalMembers;
        $this->totalTrainers = $totalTrainers;
        $this->totalBookings = $totalBookings;
    }

    public function render()
    {
        return view('livewire.super-admin-dashboard-stats');
    }
}
