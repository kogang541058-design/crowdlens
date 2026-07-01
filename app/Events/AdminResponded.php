<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast; 
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AdminResponded implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $reportId;
    public $userId;
    public $status;
    public $actionType;
    public $responseMessage;

    public function __construct($reportId, $userId, $status, $actionType, $responseMessage)
    {
        $this->reportId = $reportId;
        $this->userId = $userId;
        $this->status = $status;
        $this->actionType = $actionType;
        $this->responseMessage = $responseMessage;
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('user.' . $this->userId),
            
            new Channel('reports'), 
        ];
    }

    public function broadcastAs(): string
    {
        return 'admin.responded';
    }

    public function broadcastWith(): array
    {
        return [
            'report_id' => $this->reportId,
            'status' => $this->status,
            'action_type' => $this->actionType,
            'message' => $this->responseMessage, 
        ];
    }
}