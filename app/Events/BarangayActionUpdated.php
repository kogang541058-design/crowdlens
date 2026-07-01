<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BarangayActionUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $reportId;
    public $status;

    // The data you pass here will be sent to the frontend
    public function __construct($reportId, $status)
    {
        $this->reportId = $reportId;
        $this->status = $status;
    }

    // The channel the frontend will listen to
    public function broadcastOn()
    {
        // Using a public channel called 'reports' for simplicity. 
        // If it's sensitive, use PrivateChannel('reports')
        return new Channel('reports'); 
    }

    // Optional: Give the event a clean name for JavaScript to listen for
    public function broadcastAs()
    {
        return 'action.updated';
    }
}