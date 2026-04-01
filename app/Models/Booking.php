<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    public function gymClass()
    {
        return $this->belongsTo(GymClass::class, 'class_id');
    }
}
