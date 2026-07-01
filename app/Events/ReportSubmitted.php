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
     */
    public function broadcastOn(): array
    {
        // 1. Always broadcast to the global admin channel
        $channels = [
            new Channel('admin-notifications'),
        ];

        // 2. If the report is assigned to a specific barangay, 
        // broadcast to their unique channel too!
        if ($this->report->barangay_id) {
            $channels[] = new Channel('barangay-notifications.' . $this->report->barangay_id);
        }

        return $channels;
    }

    /**
     * The broadcast event name alias.
     * Matches channel.listen('.report.submitted', ...)
     */
    public function broadcastAs(): string
    {
        return 'report.submitted';
    }

    /**
     * Get the data to broadcast. This explicitly shapes the payload 
     * to match exactly what your JavaScript properties are looking for.
     */
    public function broadcastWith(): array
    {
        return [
            'id' => $this->report->id,
            'disaster_type' => $this->report->disaster_type,
            'disaster_type_name' => ucfirst($this->report->disaster_type),
            'description' => $this->report->description,
            'user_name' => $this->report->user->name ?? 'Anonymous User',
            
            // Location Data (Removed 'N/A' default so JS can fallback to lat/long if location is null)
            'location' => $this->report->location,
            'latitude' => $this->report->latitude,
            'longitude' => $this->report->longitude,
            
            // Split Date and Time for the respective columns
            'formatted_date' => $this->report->created_at ? $this->report->created_at->format('M d, Y') : now()->format('M d, Y'),
            'formatted_time' => $this->report->created_at ? $this->report->created_at->format('h:i A') : now()->format('h:i A'),
            
            // Core Statuses
            'status' => $this->report->status ?? 'pending',
            'solved' => $this->report->solved ?? false,
            
            // Check for in-progress responses (to calculate 'actionStatus' in JS)
            'has_in_progress_responses' => $this->report->responses()->where('action_type', 'in_progress')->exists(),
            
            // Barangay Data
            'barangay_id' => $this->report->barangay_id,
            'barangay_name' => $this->report->barangay ? $this->report->barangay->name : null,
            'barangay_action_status' => $this->report->barangay_action_status ?? 'none',
            
            // Media URLs (Using the Storage facade)
            'image_url' => $this->report->image ? \Illuminate\Support\Facades\Storage::url($this->report->image) : null,
            'video_url' => $this->report->video ? \Illuminate\Support\Facades\Storage::url($this->report->video) : null,
        ];
    }
}