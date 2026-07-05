<?php

namespace App\Livewire;

use App\Services\MongoDBService;
use Livewire\Component;

class LiveRevenueCounter extends Component
{
    public $totalRevenue;

    public function mount($totalRevenue)
    {
        $this->totalRevenue = $totalRevenue;
    }

    /**
     * Refresh the revenue stats dynamically from MongoDB.
     */
    public function refreshRevenue()
    {
        $revenueAggregate = MongoDBService::collection('payment_logs')->aggregate([
            [
                '$group' => [
                    '_id' => 'total_revenue',
                    'total' => ['$sum' => '$amount']
                ]
            ]
        ]);
        
        $this->totalRevenue = $revenueAggregate[0]['total'] ?? 0.00;
        $this->dispatch('revenue-updated', totalRevenue: $this->totalRevenue);
    }

    public function render()
    {
        return view('livewire.live-revenue-counter');
    }
}
