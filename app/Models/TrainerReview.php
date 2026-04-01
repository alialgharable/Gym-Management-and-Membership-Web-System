<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TrainerReview extends Model
{

    protected $fillable = [
        'member_id',
        'trainer_id',
        'rating',
        'comment',
    ];


    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    public function trainer()
    {
        return $this->belongsTo(Trainer::class);
    }
}
