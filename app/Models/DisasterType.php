<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DisasterType extends Model
{
    protected $fillable = [
        'name',
        'icon',
        'color',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get the reports for this disaster type.
     */
    public function reports()
    {
        return $this->hasMany(Report::class, 'disaster_type', 'name');
    }
}
