<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Gym extends Model
{
    protected $fillable = [
        'name',
        'email',
        'address',
        'phone',
        'subscription_plan',
        'subscription_status',
        'subscription_expires_at',
        'subscription_transaction_id',
        'stripe_session_id',
        'payment_method',
        'transaction_reference',
        'amount_paid',
        'payment_at',
    ];

    public function services()
    {
        return $this->hasMany(Service::class);
    }

    public function trainers()
    {
        return $this->hasMany(Trainer::class);
    }

    public function timeslots()
    {
        return $this->hasMany(Timeslot::class);
    }
}
