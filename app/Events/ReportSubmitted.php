<?php

namespace App\Events;

use App\Models\Report;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ReportSubmitted implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $report;

    /**
     * Create a new event instance.
     */
    public function __construct(Report $report)
    {
        $this->report = $report;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('admin-notifications'),
        ];
    }

    /**
     * The event's broadcast name.
     *
     * @return string
     */
    public function broadcastAs(): string
    {
        return 'report.submitted';
    }

    /**
     * Get the data to broadcast.
     *
     * @return array
     */
    public function broadcastWith(): array
    {
        return [
            'id' => $this->report->id,
            'disaster_type' => $this->report->disaster_type,
            'disaster_type_name' => ucfirst($this->report->disaster_type),
            'description' => $this->report->description,
            'location' => $this->report->location ?? $this->report->latitude . ', ' . $this->report->longitude,
            'latitude' => $this->report->latitude,
            'longitude' => $this->report->longitude,
            'image' => $this->report->image ? \Storage::url($this->report->image) : null,
            'video' => $this->report->video ? \Storage::url($this->report->video) : null,
            'user_name' => $this->report->user->name,
            'user_id' => $this->report->user_id,
            'status' => $this->report->status,
            'action_status' => $this->report->solved ? 'solved' : ($this->report->responses()->where('action_type', 'in_progress')->exists() ? 'in_progress' : null),
            'created_at' => $this->report->created_at->toISOString(),
            'formatted_date' => $this->report->created_at->format('M d, Y'),
            'formatted_time' => $this->report->created_at->format('h:i A'),
        ];
    }
}
