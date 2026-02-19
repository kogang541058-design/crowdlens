<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'barangay_id',
        'barangay_action_status',
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
     * Get the barangay assigned to this report.
     */
    public function barangay()
    {
        return $this->belongsTo(\App\Models\Barangay::class);
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
