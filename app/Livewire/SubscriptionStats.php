<?php

namespace App\Livewire;

use App\Models\Gym;
use Livewire\Component;

class SubscriptionStats extends Component
{
    public $active;
    public $expired;
    public $starterCount;
    public $professionalCount;
    public $enterpriseCount;

    public function mount($active, $expired)
    {
        $this->active = $active;
        $this->expired = $expired;
        $this->loadPlanStats();
    }

    /**
     * Load counts for Starter, Professional, and Enterprise plans.
     */
    public function loadPlanStats()
    {
        $this->starterCount = Gym::where('subscription_plan', 'Starter')->count();
        $this->professionalCount = Gym::where('subscription_plan', 'Professional')->count();
        $this->enterpriseCount = Gym::where('subscription_plan', 'Enterprise')->count();
    }

    public function render()
    {
        return view('livewire.subscription-stats');
    }
}
