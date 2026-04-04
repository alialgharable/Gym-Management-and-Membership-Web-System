<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'role'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function admin()
    {
        return $this->hasOne(Admin::class);
    }

    public function member()
    {
        return $this->hasOne(Member::class);
    }

    public function trainer()
    {
        return $this->hasOne(Trainer::class);
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin' || $this->admin()->exists();
    }

    public function isTrainer(): bool
    {
        return $this->role === 'trainer' || $this->trainer()->exists();
    }

    public function isMember(): bool
    {
        return $this->role === 'member' || $this->member()->exists();
    }

    public function primaryRole(): string
    {
        if ($this->isAdmin()) {
            return 'admin';
        }

        if ($this->isTrainer()) {
            return 'trainer';
        }

        return 'member';
    }
}
