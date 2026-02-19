<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

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

    /**
     * Get the active block for the user.
     */
    public function activeBlock()
    {
        return $this->hasOne(UserBlock::class)->where('is_active', true)->latest();
    }

    /**
     * Get all blocks for the user.
     */
    public function blocks()
    {
        return $this->hasMany(UserBlock::class);
    }

    /**
     * Check if user is currently blocked.
     */
    public function isBlocked()
    {
        $activeBlock = $this->activeBlock;
        
        if (!$activeBlock) {
            return false;
        }

        // Check if block has expired
        if ($activeBlock->blocked_until && now()->greaterThan($activeBlock->blocked_until)) {
            $activeBlock->update(['is_active' => false]);
            return false;
        }

        return true;
    }

    /**
     * Get the active block reason.
     */
    public function getBlockReasonAttribute()
    {
        return $this->activeBlock?->block_reason;
    }

    /**
     * Get the blocked until date.
     */
    public function getBlockedUntilAttribute()
    {
        return $this->activeBlock?->blocked_until;
    }
}
