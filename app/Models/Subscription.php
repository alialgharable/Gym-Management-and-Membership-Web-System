<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    public function plan()
    {
        return $this->belongsTo(MembershipPlan::class);
    }
}
