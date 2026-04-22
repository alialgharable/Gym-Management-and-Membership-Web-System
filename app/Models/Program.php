<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Program extends Model
{
    protected $fillable = [
        'trainer_id',
        'member_id',
        'specialty',
        'title',
        'duration_weeks',
        'goal',
        'notes',
    ];

    public function trainer()
    {
        return $this->belongsTo(Trainer::class);
    }

    public function member()
    {
        return $this->belongsTo(Member::class);
    }
}
