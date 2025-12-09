<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'disaster_type',
        'description',
        'latitude',
        'longitude',
        'location',
        'image',
        'video',
        'status',
    ];

    /**
     * Get the user that owns the report.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the solved record for this report.
     */
    public function solved()
    {
        return $this->hasOne(Solved::class);
    }

    /**
     * Get all responses for this report.
     */
    public function responses()
    {
        return $this->hasMany(ReportResponse::class);
    }
}
