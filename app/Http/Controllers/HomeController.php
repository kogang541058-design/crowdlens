<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use App\Models\Notification;

class HomeController extends Controller
{
    /**
     * Show the home page with login and register panels.
     */
    public function index(Request $request)
    {
        // Check if user wants to switch account
        if ($request->has('switch_account')) {
            if (auth()->guard('admin')->check()) {
                auth()->guard('admin')->logout();
            }
            if (auth()->guard('barangay')->check()) {
                auth()->guard('barangay')->logout(); 
            }
            if (auth()->guard('web')->check()) {
                auth()->guard('web')->logout();
            }
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return redirect()->route('home');
        }

        // Redirect authenticated users to their respective dashboards
        if (auth()->guard('admin')->check()) {
            return redirect()->route('admin.dashboard');
        }

        if (auth()->guard('barangay')->check()) {
            return redirect()->route('barangay.dashboard'); 
        }
        
        if (auth()->guard('web')->check()) {
            return redirect()->route('dashboard');
        }

        return view('home');
    }

    /**
     * Show a simple dashboard for authenticated users.
     */
    public function dashboard()
    {
        // Get current user's reports with relationships
        $reports = \App\Models\Report::with(['solved', 'responses'])
            ->where('user_id', auth()->id())
            ->latest()
            ->get();
        
        // Get all verified reports to show on map (excluding solved ones)
        $verifiedReports = \App\Models\Report::with('user')
            ->where('status', 'verified')
            ->whereDoesntHave('solved')
            ->orderBy('created_at', 'desc')
            ->get();
        
        // Get active disaster types
        $disasterTypes = \App\Models\DisasterType::where('is_active', true)
            ->orderBy('name')
            ->get();

        // Get all barangays for the report form
        $barangays = \App\Models\Barangay::orderBy('name')->get();
        
        return view('dashboard', compact('reports', 'verifiedReports', 'disasterTypes', 'barangays'));
    }

    /**
     * Check for new admin responses on user's reports.
     */
    public function checkResponses(Request $request)
    {
        $userId = auth()->id();
        $lastCheckTime = $request->session()->get('last_response_check_time', now()->subMinutes(5));
        
        // Get user's report IDs
        $userReports = \App\Models\Report::where('user_id', $userId)->pluck('id');
        
        // Get total response count
        $responseCount = \App\Models\ReportResponse::whereIn('report_id', $userReports)->count();
        
        // Check for new responses since last check
        $newResponses = \App\Models\ReportResponse::with(['report', 'admin'])
            ->whereIn('report_id', $userReports)
            ->where('created_at', '>', $lastCheckTime)
            ->orderBy('created_at', 'desc')
            ->get();
        
        $hasNewResponse = $newResponses->count() > 0;
        
        // Update session with current time
        if ($hasNewResponse) {
            $request->session()->put('last_response_check_time', now());
        }
        
        $latestResponse = null;
        if ($hasNewResponse && $newResponses->first()) {
            $response = $newResponses->first();
            $latestResponse = [
                'disaster_type' => $response->report->disaster_type ?? 'Report',
                'response_message' => $response->response_message,
                'action_type' => $response->action_type,
                'responded_at' => $response->created_at->format('M d, Y h:i A'),
                'admin_name' => $response->admin->name ?? 'Admin'
            ];
        }
        
        return response()->json([
            'response_count' => $responseCount,
            'has_new_response' => $hasNewResponse,
            'latest_response' => $latestResponse,
            'new_count' => $newResponses->count()
        ]);
    }

    /**
     * Show the settings page.
     */
    public function settings()
    {
        return view('settings');
    }

    /**
     * Update user profile (name and email).
     */
    public function updateProfile(Request $request)
    {
        $user = auth()->user();
        
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,' . $user->id],
        ]);
        
        $user->update($validated);
        
        return back()->with('success', 'Profile updated successfully!');
    }

    /**
     * Update user password.
     */
    public function updatePassword(Request $request)
    {
        $user = auth()->user();
        
        $validated = $request->validate([
            'current_password' => ['required'],
            'new_password' => [
                'required',
                'confirmed',
                'min:8',
                'regex:/[!@#$%^&*(),.?":{}|<>]/'
            ],
        ], [
            'current_password.required' => 'Please provide your current password.',
            'new_password.required' => 'Please provide a new password.',
            'new_password.min' => 'Password must be at least 8 characters long.',
            'new_password.confirmed' => 'Password confirmation does not match.',
            'new_password.regex' => 'Password must contain at least one special character (!@#$%^&*(),.?":{}|<>).'
        ]);
        
        // Check if current password is correct
        if (!Hash::check($validated['current_password'], $user->password)) {
            throw ValidationException::withMessages([
                'current_password' => ['The current password is incorrect.'],
            ]);
        }
        
        $user->update([
            'password' => $validated['new_password']
        ]);
        
        return back()->with('success', 'Password changed successfully!');
    }

    public function fetch()
    {
        // Fetch the 10 most recent notifications for the logged-in user
        // $notifications = Notification::where('user_id', auth()->id())
        //     ->latest()
        //     ->take(10)
        //     ->get();
        
        // $unreadCount = Notification::where('user_id', auth()->id())
        //     ->where('is_read', false)
        //     ->count();

        // return response()->json([
        //     'notifications' => $notifications,
        //     'unread_count' => $unreadCount
        // ]);
    }

    public function markAllRead()
    {
        // Notification::where('user_id', auth()->id())
        //     ->where('is_read', false)
        //     ->update(['is_read' => true]);

        // return response()->json(['success' => true]);
    }
}
