<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    protected $fillable = [
        'name',
        'description',
    ];

    public function gymClasses()
    {
        return $this->hasMany(GymClass::class, 'room_id');
    }
}
