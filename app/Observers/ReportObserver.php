<?php

namespace App\Observers;

use App\Models\Report;
use App\Jobs\ProcessPrediction;

class ReportObserver
{
    /**
     * Handle the Report "created" event.
     */
    public function created(Report $report): void
    {
        // Only sends with proper parameters
        if ($report->latitude && $report->longitude && $report->description && $report->disaster_type) {
            ProcessPrediction::dispatch($report);
        }
    }
}