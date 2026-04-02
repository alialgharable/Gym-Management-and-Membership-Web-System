<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Trainer extends Model
{

    protected $fillable = [
        'user_id',
        'specialty',
        'bio',
        'profile_image',
    ];


    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function gymClasses()
    {
        return $this->hasMany(GymClass::class);
    }

    public function reviews()
    {
        return $this->hasMany(TrainerReview::class);
    }
}
