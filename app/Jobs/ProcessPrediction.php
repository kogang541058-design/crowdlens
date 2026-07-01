<?php

namespace App\Jobs;

use App\Models\Report;
use App\Models\Prediction;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Events\PredictionCompleted;

class ProcessPrediction implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $report;

    public function __construct(Report $report)
    {
        $this->report = $report;
    }

    public function handle(): void
    {
        $url = config('services.ai_model.url');
        $key = config('services.ai_model.key');

        $response = Http::withHeaders([
            'X-API-KEY' => $key,
            'Accept' => 'application/json',
        ])->post($url, [
            'date' => $this->report->created_at->format('m/d/Y'),
            'call_type' => $this->report->disaster_type,
            'remarks' => $this->report->description ?? 'No remarks provided',
            'lat' => (float) $this->report->latitude,
            'lon' => (float) $this->report->longitude,
        ]);

        if ($response->successful()) {
            $data = $response->json();

            Prediction::create([
                'report_id' => $this->report->id,
                'latitude' => $this->report->latitude,
                'longitude' => $this->report->longitude,
                'call_type' => $this->report->disaster_type,
                'remarks' => $this->report->description,
                'report_date' => $this->report->created_at,
                'report_day_of_week' => $this->report->created_at->dayOfWeek, // 0 (Sun) to 6 (Sat)
                'prediction_label' => $data['is_valid'],
                'confidence_score' => $data['confidence'],
            ]);

            \Log::info("Attempting to broadcast prediction.completed for report: " . $this->report->id);
            PredictionCompleted::dispatch(
                $this->report->id, 
                $data['is_valid'], 
                $data['confidence']
            );

        } else {
            Log::error("HF API Failed for Report #{$this->report->id}: " . $response->body());
            
            // If the server is 503 (Service Unavailable) or 504 (Gateway Timeout), 
            // it's likely HF waking up. Release it back to the queue to try again in 30s.
            if ($response->status() === 503 || $response->status() === 504 || $response->serverError()) {
                $this->release(30); 
            }
        }
    }
}