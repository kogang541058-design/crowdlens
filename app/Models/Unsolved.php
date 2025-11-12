<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Unsolved extends Model
{
    protected $table = 'unsolved';

    protected $fillable = [
        'report_id',
        'admin_id',
        'priority',
        'notes',
        'pending_since',
    ];

    protected $casts = [
        'pending_since' => 'datetime',
    ];

    public function report()
    {
        return $this->belongsTo(Report::class);
    }

    public function admin()
    {
        return $this->belongsTo(Admin::class);
    }
}
