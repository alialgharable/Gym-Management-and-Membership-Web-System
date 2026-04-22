<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Member extends Model
{

    protected $fillable = [
        'user_id',
        'trainer_id',
    ];

    public function trainer()
    {
        return $this->belongsTo(Trainer::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function subscription()
    {
        return $this->hasOne(Subscription::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function reviews()
    {
        return $this->hasMany(TrainerReview::class);
    }

    public function programs()
    {
        return $this->hasMany(Program::class);
    }

    public function premiumCoachRequests()
    {
        return $this->hasMany(PremiumCoachRequest::class);
    }
}
