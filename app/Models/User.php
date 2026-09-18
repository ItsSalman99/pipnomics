<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'is_premium', 'is_online', 'last_seen_at', 'last_login_at', 'last_logout_at'])]
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
            'is_premium' => 'boolean',
            'is_online' => 'boolean',
            'last_seen_at' => 'datetime',
            'last_login_at' => 'datetime',
            'last_logout_at' => 'datetime',
        ];
    }

    /**
     * The groups the user has joined.
     */
    public function groups()
    {
        return $this->belongsToMany(Group::class)->withTimestamps();
    }

    /**
     * The groups the user owns.
     */
    public function ownedGroups()
    {
        return $this->hasMany(Group::class, 'owner_id');
    }

    /**
     * The news comments posted by the user.
     */
    public function newsComments()
    {
        return $this->hasMany(NewsComment::class);
    }

    /**
     * The activity logs for the user.
     */
    public function activities()
    {
        return $this->hasMany(UserActivity::class)->latest();
    }

    /**
     * Mark the user as online and update last_seen_at.
     */
    public function setOnline(bool $isLogin = false): self
    {
        $data = [
            'is_online' => true,
            'last_seen_at' => now(),
        ];

        if ($isLogin) {
            $data['last_login_at'] = now();
        }

        $this->update($data);
        return $this;
    }

    /**
     * Mark the user as offline and update last_logout_at.
     */
    public function setOffline(): self
    {
        $this->update([
            'is_online' => false,
            'last_seen_at' => now(),
            'last_logout_at' => now(),
        ]);
        return $this;
    }
}
