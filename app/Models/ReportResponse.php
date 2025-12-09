<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReportResponse extends Model
{
    protected $fillable = [
        'report_id',
        'admin_id',
        'status',
        'action_type',
        'response_message',
        'notes',
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
