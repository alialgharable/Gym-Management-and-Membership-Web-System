<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GymClass extends Model
{
    protected $table = 'classes';
    public function trainer()
    {
        return $this->belongsTo(Trainer::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }
}
