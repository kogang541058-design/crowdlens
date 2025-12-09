<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AdminAuthController extends Controller
{
    /**
     * Show the admin login form.
     */
    public function showLoginForm()
    {
        if (Auth::guard('admin')->check()) {
            return redirect()->route('admin.dashboard');
        }

        return view('admin.login');
    }

    /**
     * Handle admin login.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $remember = (bool) $request->boolean('remember');

        if (Auth::guard('admin')->attempt($credentials, $remember)) {
            $request->session()->regenerate();
            return redirect()->intended(route('admin.dashboard'));
        }

        throw ValidationException::withMessages([
            'email' => __('The provided credentials are incorrect.'),
        ]);
    }

    /**
     * Log the admin out.
     */
    public function logout(Request $request)
    {
        Auth::guard('admin')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }

    /**
     * Show admin dashboard.
     */
    public function dashboard()
    {
        $totalUsers = \App\Models\User::count();
        $totalReports = \App\Models\Report::count();
        $pendingReports = \App\Models\Report::where('status', 'pending')->count();
        $verifiedReports = \App\Models\Report::where('status', 'verified')->count();
        $solvedReports = \App\Models\Report::where('status', 'verified')->count();
        $unsolvedReports = \App\Models\Report::where('status', 'pending')->count();
        $totalAdmins = \App\Models\Admin::count();
        
        return view('admin.dashboard', compact('totalUsers', 'totalReports', 'pendingReports', 'verifiedReports', 'solvedReports', 'unsolvedReports', 'totalAdmins'));
    }

    /**
     * Show map page.
     */
    public function map()
    {
        $verifiedReports = \App\Models\Report::with('user')
            ->where('status', 'verified')
            ->orderBy('created_at', 'desc')
            ->get();
        $disasterTypes = \App\Models\DisasterType::where('is_active', true)->get();
        return view('admin.map', compact('verifiedReports', 'disasterTypes'));
    }

    /**
     * Show reports page.
     */
    public function reports()
    {
        $reports = \App\Models\Report::with(['user', 'solved', 'responses'])
            ->whereIn('status', ['pending', 'verified', 'unverified'])
            ->orderBy('created_at', 'desc')
            ->get();
        $disasterTypes = \App\Models\DisasterType::where('is_active', true)->orderBy('name')->get();
        return view('admin.reports', compact('reports', 'disasterTypes'));
    }

    /**
     * Show users page.
     */
    public function users()
    {
        $users = \App\Models\User::latest()->get();
        return view('admin.users', compact('users'));
    }

    /**
     * Verify a report.
     */
    public function verifyReport(\App\Models\Report $report)
    {
        // Update report status only
        $report->update(['status' => 'verified']);
        
        return redirect()->back()->with('success', 'Report verified successfully!');
    }

    /**
     * Respond to a report and update its status.
     */
    public function respondToReport(Request $request, \App\Models\Report $report)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,verified,unverified',
            'action_type' => 'nullable|string',
            'response_message' => 'required|string',
            'notes' => 'nullable|string',
        ]);

        // Save the response to report_responses table
        $response = \App\Models\ReportResponse::create([
            'report_id' => $report->id,
            'admin_id' => auth('admin')->id(),
            'status' => $validated['status'],
            'action_type' => $validated['action_type'],
            'response_message' => $validated['response_message'],
            'notes' => $validated['notes'],
        ]);

        // Update report status
        $report->update([
            'status' => $validated['status'],
        ]);

        // Handle action type if provided
        if (!empty($validated['action_type']) && $validated['action_type'] === 'solved') {
            // Create or update solved entry
            \App\Models\Solved::updateOrCreate(
                ['report_id' => $report->id],
                [
                    'admin_id' => auth('admin')->id(),
                    'solved_at' => now(),
                ]
            );
        } elseif (!empty($validated['action_type']) && $validated['action_type'] === 'in_progress') {
            // Remove from solved if exists (report is no longer solved if marked as in progress)
            \App\Models\Solved::where('report_id', $report->id)->delete();
        }

        // Broadcast real-time notification to the user who submitted the report
        \Log::info('Broadcasting AdminResponded event for report ID: ' . $report->id . ' to user ID: ' . $report->user_id);
        broadcast(new \App\Events\AdminResponded($report->load(['user', 'responses.admin']), $response->load('admin'), $report->user_id))->toOthers();

        return redirect()->back()->with('success', 'Response submitted successfully! Status updated to ' . $validated['status'] . '.');
    }

    /**
     * Mark report as solved.
     */
    public function markSolved(\App\Models\Report $report)
    {
        // Create or update solved entry
        \App\Models\Solved::updateOrCreate(
            ['report_id' => $report->id],
            [
                'admin_id' => auth('admin')->id(),
                'solved_at' => now(),
            ]
        );
        
        return redirect()->back()->with('success', 'Report marked as solved!');
    }

    /**
     * Delete a report.
     */
    public function deleteReport(\App\Models\Report $report)
    {
        // Delete associated files if they exist
        if ($report->image) {
            \Storage::disk('public')->delete($report->image);
        }
        if ($report->video) {
            \Storage::disk('public')->delete($report->video);
        }

        // Remove from solved table
        \App\Models\Solved::where('report_id', $report->id)->delete();

        $report->delete();
        return redirect()->back()->with('success', 'Report deleted successfully!');
    }

    /**
     * Show solved reports page.
     */
    public function solved()
    {
        $solvedReports = \App\Models\Solved::with(['report.user', 'admin'])
            ->orderBy('solved_at', 'desc')
            ->get();
        $disasterTypes = \App\Models\DisasterType::where('is_active', true)->get();
        return view('admin.solved', compact('solvedReports', 'disasterTypes'));
    }

    /**
     * Get total report count for real-time notifications.
     */
    public function getReportCount()
    {
        $count = \App\Models\Report::whereIn('status', ['pending', 'verified', 'unverified'])->count();
        return response()->json(['count' => $count]);
    }

    /**
     * Get new reports since last check for real-time updates.
     */
    public function getNewReports(Request $request)
    {
        $lastId = $request->query('last_id', 0);
        
        $newReports = \App\Models\Report::with(['user', 'solved', 'responses'])
            ->where('id', '>', $lastId)
            ->whereIn('status', ['pending', 'verified', 'unverified'])
            ->orderBy('created_at', 'asc')
            ->get()
            ->map(function ($report) {
                // Determine action status
                $actionStatus = '';
                if ($report->solved) {
                    $actionStatus = 'solved';
                } elseif ($report->responses->where('action_type', 'in_progress')->count() > 0) {
                    $actionStatus = 'in_progress';
                }

                return [
                    'id' => $report->id,
                    'user_name' => $report->user ? $report->user->name : 'N/A',
                    'disaster_type' => $report->disaster_type,
                    'disaster_type_name' => $report->disaster_type,
                    'description' => $report->description,
                    'location' => $report->location,
                    'created_at' => $report->created_at->toIso8601String(),
                    'status' => $report->status,
                    'action_status' => $actionStatus,
                    'image' => $report->image ? \Storage::url($report->image) : null,
                    'video' => $report->video ? \Storage::url($report->video) : null,
                ];
            });

        return response()->json(['reports' => $newReports]);
    }

    /**
     * Check for new reports (polling fallback)
     */
    public function checkNewReports(Request $request)
    {
        $sinceId = $request->input('since', 0);
        
        $newReports = \App\Models\Report::where('id', '>', $sinceId)
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($report) {
                // Check action status
                $actionStatus = null;
                if ($report->solved) {
                    $actionStatus = 'solved';
                } elseif ($report->responses()->where('action_type', 'in_progress')->exists()) {
                    $actionStatus = 'in_progress';
                }
                
                return [
                    'id' => $report->id,
                    'disaster_type' => $report->disaster_type,
                    'disaster_type_name' => ucfirst($report->disaster_type),
                    'description' => $report->description,
                    'location' => $report->location ?? ($report->latitude . ', ' . $report->longitude),
                    'latitude' => $report->latitude,
                    'longitude' => $report->longitude,
                    'user_name' => $report->user->name,
                    'user_id' => $report->user_id,
                    'status' => $report->status,
                    'action_status' => $actionStatus,
                    'image' => $report->image ? \Storage::url($report->image) : null,
                    'video' => $report->video ? \Storage::url($report->video) : null,
                    'created_at' => $report->created_at->toISOString(),
                    'formatted_date' => $report->created_at->format('M d, Y'),
                    'formatted_time' => $report->created_at->format('h:i A'),
                ];
            });

        return response()->json(['new_reports' => $newReports]);
    }

    /**
     * Get notifications for current admin
     */
    public function getNotifications(Request $request)
    {
        $adminId = auth('admin')->id();
        $limit = $request->input('limit', 50);
        
        $notifications = \App\Models\Notification::where('admin_id', $adminId)
            ->with(['report.user'])
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get()
            ->map(function ($notification) {
                $disasterType = 'N/A';
                $userName = 'Unknown User';
                
                if ($notification->report) {
                    $disasterType = ucfirst($notification->report->disaster_type);
                    if ($notification->report->user) {
                        $userName = $notification->report->user->name;
                    }
                }
                
                return [
                    'id' => $notification->id,
                    'report_id' => $notification->report_id,
                    'disaster_type' => $disasterType,
                    'user_name' => $userName,
                    'message' => $notification->message,
                    'is_read' => $notification->is_read,
                    'time_ago' => $notification->time_ago,
                    'created_at' => $notification->created_at->toISOString(),
                ];
            });

        $unreadCount = \App\Models\Notification::where('admin_id', $adminId)
            ->where('is_read', false)
            ->count();

        return response()->json([
            'notifications' => $notifications,
            'unread_count' => $unreadCount,
        ]);
    }

    /**
     * Mark notification as read
     */
    public function markNotificationRead($id)
    {
        $adminId = auth('admin')->id();
        
        $notification = \App\Models\Notification::where('id', $id)
            ->where('admin_id', $adminId)
            ->first();

        if ($notification) {
            $notification->update(['is_read' => true]);
            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false], 404);
    }

    /**
     * Mark all notifications as read
     */
    public function markAllNotificationsRead()
    {
        $adminId = auth('admin')->id();
        
        \App\Models\Notification::where('admin_id', $adminId)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return response()->json(['success' => true]);
    }

}

