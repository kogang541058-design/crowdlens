<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Prediction extends Model
{
    use HasFactory;

    protected $fillable = [
        'report_id',
        'latitude',
        'longitude',
        'call_type',         // Added: The category (Flood, Landslide, etc.)
        'remarks',           // Added: Critical for TF-IDF retraining
        'report_date',       // Added: Store full date to extract Y/M/D/DoW easily
        'report_day_of_week',// Added: Feature used by your RF model
        'prediction_label',  // 1 (Valid) or 0 (Invalid) from AI
        'actual_label',      // Added: The "Ground Truth" after human review
        'confidence_score',
        'is_flagged_incorrect'
    ];

    protected $casts = [
        'report_date' => 'date',
        'is_flagged_incorrect' => 'boolean',
        'latitude' => 'double',
        'longitude' => 'double',
        'confidence_score' => 'float',
    ];

    public function report(): BelongsTo
    {
        return $this->belongsTo(Report::class);
    }

    /**
     * Scope for retraining: Get data where we have a confirmed actual label.
     */
    public function scopeReadyForRetraining($query)
    {
        return $query->whereNotNull('actual_label');
    }
}