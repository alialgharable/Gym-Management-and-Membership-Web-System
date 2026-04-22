<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Trainer extends Model
{
    public const SPECIALTIES = [
        'combat' => 'Combat Sports',
        'yoga_pilates' => 'Yoga & Pilates',
        'group_training' => 'Group Training',
        'fitness_machines' => 'Fitness Machines',
    ];

    protected $fillable = [
        'user_id',
        'specialty',
        'bio',
        'profile_image',
        'salary',
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

    public function members()
    {
        return $this->hasMany(Member::class);
    }

    public function programs()
    {
        return $this->hasMany(Program::class);
    }

    public function premiumCoachRequests()
    {
        return $this->hasMany(PremiumCoachRequest::class);
    }

    public function specialtyLabel(): string
    {
        $key = $this->specialty ?? null;

        if ($key && isset(self::SPECIALTIES[$key])) {
            return self::SPECIALTIES[$key];
        }

        if ($key) {
            return \Illuminate\Support\Str::headline(str_replace('_', ' ', $key));
        }

        return 'N/A';
    }
}
