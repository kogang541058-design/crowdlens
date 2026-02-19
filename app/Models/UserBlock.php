<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserBlock extends Model
{
    protected $fillable = [
        'user_id',
        'block_reason',
        'blocked_until',
        'is_active',
    ];

    protected $casts = [
        'blocked_until' => 'datetime',
        'is_active' => 'boolean',
    ];

    /**
     * Get the user that owns the block.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
