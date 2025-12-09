<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Admin;
use App\Models\Report;
use App\Models\Notification;

class NotificationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all admins
        $admins = Admin::all();
        
        if ($admins->isEmpty()) {
            $this->command->warn('No admins found. Please create admin accounts first.');
            return;
        }

        // Get latest 20 reports
        $reports = Report::with('user')->latest()->take(20)->get();
        
        if ($reports->isEmpty()) {
            $this->command->warn('No reports found.');
            return;
        }

        $count = 0;
        
        // Create notifications for each report for each admin
        foreach ($reports as $report) {
            foreach ($admins as $admin) {
                Notification::create([
                    'admin_id' => $admin->id,
                    'report_id' => $report->id,
                    'type' => 'new_report',
                    'message' => "New {$report->disaster_type} report submitted by {$report->user->name}",
                    'is_read' => false,
                ]);
                $count++;
            }
        }

        $this->command->info("Created {$count} notifications for {$reports->count()} reports and {$admins->count()} admin(s).");
    }
}
