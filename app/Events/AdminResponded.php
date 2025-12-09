<?php

namespace App\Events;

use App\Models\Report;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AdminResponded implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $report;
    public $response;
    public $userId;

    /**
     * Create a new event instance.
     */
    public function __construct(Report $report, $response, $userId)
    {
        $this->report = $report;
        $this->response = $response;
        $this->userId = $userId;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        // Broadcast to specific user's private channel
        return [
            new PrivateChannel('user.' . $this->userId),
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'admin.responded';
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'report_id' => $this->report->id,
            'disaster_type' => $this->report->disaster_type,
            'description' => $this->report->description,
            'location' => $this->report->location,
            'response_message' => $this->response->response_message,
            'action_type' => $this->response->action_type,
            'responded_at' => $this->response->created_at->format('M d, Y h:i A'),
            'admin_name' => $this->response->admin->name ?? 'Admin',
        ];
    }
}
