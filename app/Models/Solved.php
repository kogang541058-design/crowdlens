<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Solved extends Model
{
    protected $table = 'solved';

    protected $fillable = [
        'report_id',
        'admin_id',
        'admin_notes',
        'solved_at',
    ];

    protected $casts = [
        'solved_at' => 'datetime',
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
