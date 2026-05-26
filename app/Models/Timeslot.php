<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Timeslot extends Model
{
    protected $fillable = [
        'gym_id',
        'service_id',
        'trainer_id',
        'date',
        'start_time',
        'end_time',
        'capacity',
        'status'
    ];

    public function gym()
    {
        return $this->belongsTo(Gym::class);
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function trainer()
    {
        return $this->belongsTo(Trainer::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

}