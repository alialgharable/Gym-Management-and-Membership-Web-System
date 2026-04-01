<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TrainerReview extends Model
{
    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    public function trainer()
    {
        return $this->belongsTo(Trainer::class);
    }
}
