<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MembershipPlan extends Model
{
    protected $fillable = [
        'name',
        'price',
        'duration',
        'description',
    ];
    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    public function durationLabel(): string
    {
        if ($this->duration % 30 === 0) {
            $months = (int) ($this->duration / 30);
            return $months . ' month' . ($months === 1 ? '' : 's');
        }

        return $this->duration . ' days';
    }
}
