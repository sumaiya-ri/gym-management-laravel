<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Trainer extends Model
{
    protected $fillable = [
        'gym_id',
        'user_id',
        'name',
        'specialization',
        'hourly_rate',
        'status'
    ];

    public function gym()
    {
        return $this->belongsTo(Gym::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function timeslots()
    {
        return $this->hasMany(Timeslot::class);
    }
}