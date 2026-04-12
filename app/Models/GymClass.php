<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GymClass extends Model
{

    protected $fillable = [
        'trainer_id',
        'name',
        'description',
        'schedule',
        'capacity',
    ];

    protected $casts = [
        'schedule' => 'datetime',
    ];
    
    protected $table = 'classes';
    public function trainer()
    {
        return $this->belongsTo(Trainer::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class, 'class_id');
    }
}
