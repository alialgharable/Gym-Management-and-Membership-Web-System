<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Trainer extends Model
{
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function classes()
    {
        return $this->hasMany(GymClass::class);
    }

    public function reviews()
    {
        return $this->hasMany(TrainerReview::class);
    }
}
