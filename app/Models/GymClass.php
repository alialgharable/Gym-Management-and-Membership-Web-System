<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GymClass extends Model
{

    protected $fillable = [
        'trainer_id',
        'room_id',
        'name',
        'category',
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

    public function room()
    {
        return $this->belongsTo(Room::class, 'room_id');
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class, 'class_id');
    }
}
