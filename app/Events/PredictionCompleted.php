<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PredictionCompleted implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $reportId;
    public $label;
    public $confidence;

    public function __construct($reportId, $label, $confidence)
    {
        $this->reportId = $reportId;
        $this->label = $label;
        $this->confidence = $confidence;
    }

    public function broadcastOn(): array
    {
        // Reusing your existing admin channel
        return [
            new Channel('reports'),
        ];
    }

    public function broadcastAs(): string
    {
        return 'prediction.completed';
    }

    public function broadcastWith(): array
    {
        return [
            'report_id' => $this->reportId,
            'is_valid' => $this->label == 1,
            'confidence_formatted' => number_format($this->confidence * 100, 1) . '%',
        ];
    }
}