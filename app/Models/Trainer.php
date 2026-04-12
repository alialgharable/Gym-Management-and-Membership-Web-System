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

    public function specialtyLabel(): string
    {
        return self::SPECIALTIES[$this->specialty] ?? 'N/A';
    }
}
