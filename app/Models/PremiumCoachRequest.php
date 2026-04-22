<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PremiumCoachRequest extends Model
{
    protected $fillable = [
        'member_id',
        'trainer_id',
        'subscription_id',
        'status',
        'member_note',
        'trainer_note',
        'reviewed_at',
    ];

    protected $casts = [
        'reviewed_at' => 'datetime',
    ];

    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    public function trainer()
    {
        return $this->belongsTo(Trainer::class);
    }

    public function subscription()
    {
        return $this->belongsTo(Subscription::class);
    }
}
