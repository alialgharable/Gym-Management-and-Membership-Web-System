<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TrainerApplication extends Model
{

    protected $fillable = [
        'user_id',
        'reviewed_by',
        'cv_file',
        'experience',
        'certifications',
        'status',
    ];


    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function admin()
    {
        return $this->belongsTo(Admin::class);
    }
}
