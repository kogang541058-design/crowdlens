<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'admin_id',
        'report_id',
        'type',
        'message',
        'is_read',
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the admin that owns the notification
     */
    public function admin()
    {
        return $this->belongsTo(Admin::class);
    }

    /**
     * Get the report associated with the notification
     */
    public function report()
    {
        return $this->belongsTo(Report::class);
    }

    /**
     * Get time ago format
     */
    public function getTimeAgoAttribute()
    {
        $diff = $this->created_at->diffForHumans();
        return $diff;
    }
}
