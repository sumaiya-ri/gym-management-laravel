<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    protected $fillable = [
        'gym_id',
        'user_id',
        'timeslot_id',
        'booking_date',
        'status',
        'payment_status',
        'payment_amount',
        'payment_transaction_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function timeslot()
    {
        return $this->belongsTo(Timeslot::class);
    }

    public function gym()
    {
        return $this->belongsTo(Gym::class);
    }
}
