<?php

namespace App\Livewire\Member;

use Livewire\Component;
use App\Models\Timeslot;

class LiveSeatAvailability extends Component
{
    public $timeslotId;
    public $displayType = 'badge'; // 'badge' or 'button'
    public $price = 0.00;

    public function render()
    {
        $timeslot = Timeslot::find($this->timeslotId);
        $isFull = $timeslot ? $timeslot->capacity <= 0 : true;
        $spots = $timeslot ? $timeslot->capacity : 0;

        return view('livewire.member.live-seat-availability', [
            'timeslot' => $timeslot,
            'isFull' => $isFull,
            'spots' => $spots,
        ]);
    }
}
