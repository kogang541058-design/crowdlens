<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;

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
        
        // Get monthly data for charts (last 12 months)
        $monthlyLabels = [];
        $monthlyData = [];
        for ($i = 11; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $monthlyLabels[] = $date->format('M');
            $count = \App\Models\Report::whereMonth('created_at', $date->month)
                ->whereYear('created_at', $date->year)
                ->count();
            $monthlyData[] = $count;
        }
        
        // Get disaster type data
        $disasterCounts = \App\Models\Report::selectRaw('disaster_type, COUNT(*) as count')
            ->groupBy('disaster_type')
            ->orderByDesc('count')
            ->limit(6)
            ->get();
        
        $disasterLabels = $disasterCounts->pluck('disaster_type')->toArray();
        $disasterChartData = $disasterCounts->pluck('count')->toArray();
        
        return view('admin.dashboard', compact(
            'totalUsers',
            'totalReports',
            'pendingReports',
            'verifiedReports',
            'solvedReports',
            'unsolvedReports',
            'totalAdmins',
            'monthlyLabels',
            'monthlyData',
            'disasterLabels',
            'disasterChartData'
        ));
    }

    /**
     * Show map page.
     */
    public function map()
    {
        $verifiedReports = \App\Models\Report::with('user')
            ->where('status', 'verified')
            ->whereDoesntHave('solved')
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
        // $reports = \App\Models\Report::with(['user', 'solved', 'responses', 'barangay'])
        //     ->whereIn('status', ['pending', 'verified', 'unverified'])
        //     ->whereDoesntHave('solved')
        //     ->orderBy('created_at', 'desc')
        //     ->get();

        $reports = \App\Models\Report::with(['user', 'solved', 'responses', 'barangay', 'prediction'])
            ->whereIn('status', ['pending', 'verified', 'unverified'])
            ->whereDoesntHave('solved')
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
     * Store a new user.
     */
    public function storeUser(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
                'regex:/[!@#$%^&*(),.?":{}|<>]/'
            ],
        ], [
            'name.required' => 'Please provide the user\'s full name.',
            'email.required' => 'Please provide an email address.',
            'email.email' => 'Please provide a valid email address.',
            'email.unique' => 'This email address is already registered.',
            'password.required' => 'Please provide a password.',
            'password.min' => 'Password must be at least 8 characters long.',
            'password.confirmed' => 'Password confirmation does not match.',
            'password.regex' => 'Password must contain at least one special character (!@#$%^&*(),.?":{}|<>).'
        ]);

        if ($validator->fails()) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }
            return redirect()->back()->withErrors($validator)->withInput();
        }

        \App\Models\User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'message' => 'User created successfully!']);
        }

        return redirect()->back()->with('success', 'User created successfully!');
    }

    /**
     * Update an existing user.
     */
    public function updateUser(Request $request, \App\Models\User $user)
    {
        $validator = \Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => [
                'nullable',
                'string',
                'min:8',
                'confirmed',
                'regex:/[!@#$%^&*(),.?":{}|<>]/'
            ],
        ], [
            'password.regex' => 'The password must contain at least one special character (!@#$%^&*(),.?":{}|<>).'
        ]);

        if ($validator->fails()) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
        ]);

        if ($request->filled('password')) {
            $user->update(['password' => bcrypt($request->password)]);
        }

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'message' => 'User updated successfully!']);
        }

        return redirect()->back()->with('success', 'User updated successfully!');
    }

    /**
     * Block or unblock a user.
     */
    public function blockUser(Request $request, \App\Models\User $user)
    {
        $action = $request->input('action', 'block');
        
        if ($action === 'unblock') {
            // Deactivate current active block
            $activeBlock = $user->activeBlock;
            if ($activeBlock) {
                $activeBlock->update(['is_active' => false]);
            }
            return redirect()->back()->with('success', 'User unblocked successfully!');
        } else {
            $request->validate([
                'block_reason' => 'required|string',
                'block_duration' => 'required'
            ]);
            
            $blockedUntil = null;
            if ($request->block_duration !== 'permanent') {
                $blockedUntil = now()->addDays((int)$request->block_duration);
            }
            
            // Deactivate any existing active blocks
            $user->blocks()->where('is_active', true)->update(['is_active' => false]);
            
            // Create new block record
            \App\Models\UserBlock::create([
                'user_id' => $user->id,
                'block_reason' => $request->block_reason,
                'blocked_until' => $blockedUntil,
                'is_active' => true
            ]);
            
            return redirect()->back()->with('success', 'User blocked successfully!');
        }
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

        // If status is unverified, delete the report and its associated files
        if ($validated['status'] === 'unverified') {
            // Delete media files from storage
            if ($report->image) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($report->image);
            }
            if ($report->video) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($report->video);
            }
            // Delete related records then the report itself
            \App\Models\Notification::where('report_id', $report->id)->delete();
            \App\Models\Solved::where('report_id', $report->id)->delete();
            \App\Models\ReportResponse::where('report_id', $report->id)->delete();
            $report->delete();

            return redirect()->back()->with('success', 'Report marked as unverified and has been deleted.');
        }

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
        $count = \App\Models\Report::whereIn('status', ['pending', 'verified', 'unverified'])
            ->whereDoesntHave('solved')
            ->count();
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
            ->whereDoesntHave('solved')
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
     * Polling endpoint for barangay action status changes.
     * Returns reports whose barangay_action_status was updated after `since` timestamp.
     */
    public function pollBarangayUpdates(Request $request)
    {
        $since = $request->query('since');

        $query = \App\Models\Report::with(['user', 'barangay']);

        if ($since) {
            $query->where('updated_at', '>', $since)
                  ->whereNotNull('barangay_action_status');
        } else {
            // Return nothing on first call — just get server time
            return response()->json(['reports' => [], 'server_time' => now()->toISOString()]);
        }

        $reports = $query->orderBy('updated_at', 'desc')->get()->map(function ($r) {
            return [
                'id'                  => $r->id,
                'barangay_action'     => $r->barangay_action_status ?? 'none',
                'barangay_name'       => $r->barangay ? $r->barangay->name : null,
                'disaster_type_name'  => ucfirst($r->disaster_type),
                'user_name'           => $r->user->name ?? 'N/A',
            ];
        });

        return response()->json(['reports' => $reports, 'server_time' => now()->toISOString()]);
    }

    /**
     * Check for new reports (polling fallback)
     */
    public function checkNewReports(Request $request)
    {
        $sinceId = $request->input('since', 0);
        
        $newReports = \App\Models\Report::where('id', '>', $sinceId)
            ->whereDoesntHave('solved')
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

    /**
     * Show barangay page.
     */
    public function barangay()
    {
        $barangays = \App\Models\Barangay::latest()->get();
        return view('admin.barangay', compact('barangays'));
    }

    /**
     * Store a new barangay.
     */
    public function storeBarangay(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:barangays,username',
            'password' => 'required|string|min:8',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        \App\Models\Barangay::create([
            'name' => $request->name,
            'username' => $request->username,
            'password' => bcrypt($request->password),
        ]);

        return response()->json(['success' => true, 'message' => 'Barangay added successfully!']);
    }

    /**
     * Update a barangay.
     */
    public function updateBarangay(Request $request, $id)
    {
        $barangay = \App\Models\Barangay::findOrFail($id);

        $validator = \Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:barangays,username,' . $id,
            'password' => 'nullable|string|min:8',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $updateData = [
            'name' => $request->name,
            'username' => $request->username,
        ];

        if ($request->filled('password')) {
            $updateData['password'] = bcrypt($request->password);
        }

        $barangay->update($updateData);

        return response()->json(['success' => true, 'message' => 'Barangay updated successfully!']);
    }

    /**
     * Delete a barangay.
     */
    public function deleteBarangay($id)
    {
        $barangay = \App\Models\Barangay::findOrFail($id);
        $barangay->delete();

        return response()->json(['success' => true, 'message' => 'Barangay deleted successfully!']);
    }

}

