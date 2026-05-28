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
        'payment_transaction_id',
        'stripe_session_id',
        'payment_method',
        'transaction_reference',
        'amount_paid',
        'payment_at',
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
