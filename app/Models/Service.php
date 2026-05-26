<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    protected $fillable = [
        'gym_id',
        'name',
        'description',
        'duration',
        'status'
    ];

    public function gym()
    {
        return $this->belongsTo(Gym::class);
    }

    public function timeslots()
    {
        return $this->hasMany(Timeslot::class);
    }
}