<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Report;
use App\Events\BarangayActionUpdated;

class BarangayAuthController extends Controller
{
    /**
     * Show the barangay dashboard.
     */
    public function dashboard()
    {
        $barangay = Auth::guard('barangay')->user();
        $totalReports   = Report::where('barangay_id', $barangay->id)->count();
        $pendingReports = Report::where('barangay_id', $barangay->id)->where('status', 'pending')->count();
        $resolvedReports = Report::where('barangay_id', $barangay->id)->whereHas('solved')->count();
        return view('barangay.dashboard', compact('barangay', 'totalReports', 'pendingReports', 'resolvedReports'));
    }

    /**
     * Show the barangay reports page.
     */
    public function reports()
    {
        $barangay = Auth::guard('barangay')->user();
        $reports = Report::with(['user', 'solved', 'responses'])
            ->where('barangay_id', $barangay->id)
            ->orderBy('created_at', 'desc')
            ->get();
        return view('barangay.reports', compact('barangay', 'reports'));
    }

    /**
     * Handle barangay logout.
     */
    public function logout(Request $request)
    {
        Auth::guard('barangay')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }

    /**
     * Polling endpoint — returns reports created/updated after `since` timestamp.
     * Used as a real-time fallback when Pusher is not configured.
     */
    public function pollReports(Request $request)
    {
        $barangay = Auth::guard('barangay')->user();
        $since    = $request->query('since'); // ISO-8601 string

        // 'solved' is correctly eager-loaded here
        $query = Report::with(['user', 'solved', 'responses'])
            ->where('barangay_id', $barangay->id);

        if ($since) {
            $query->where(function ($q) use ($since) {
                $q->where('created_at', '>', $since)
                ->orWhere('updated_at', '>', $since);
            });
        }

        $reports = $query->orderBy('created_at', 'desc')->get()->map(function ($r) use ($since) {
            // Checks if the 'hasOne' Solved relationship exists
            $actionStatus = $r->solved
                ? 'solved'
                : ($r->responses->where('action_type', 'in_progress')->count() ? 'in_progress' : 'none');

            $location = $r->location
                ?: (number_format($r->latitude, 6) . ', ' . number_format($r->longitude, 6));

            return [
                'id'                => $r->id,
                'disaster_type'     => $r->disaster_type,
                'disaster_type_name'=> ucfirst($r->disaster_type),
                'description'       => $r->description,
                'location'          => $location,
                'user_name'         => $r->user->name ?? 'Unknown',
                'status'            => $r->status,
                'action_status'     => $actionStatus,
                'barangay_action'   => $r->barangay_action_status ?? 'none',
                'image'             => $r->image ? Storage::url($r->image) : '',
                'video'             => $r->video ? Storage::url($r->video) : '',
                'formatted_date'    => $r->created_at->format('M d, Y'),
                'formatted_time'    => $r->created_at->format('h:i A'),
                'is_new'            => $since && $r->created_at->toISOString() > $since,
            ];
        });

        return response()->json([
            'reports'     => $reports,
            'server_time' => now()->toISOString(),
        ]);
    }

    /**
     * Update the barangay action status on a report.
     */
    public function updateActionStatus(Request $request, Report $report)
    {
        $barangay = Auth::guard('barangay')->user();

        if ($report->barangay_id !== $barangay->id) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'barangay_action_status' => 'required|in:approved,disapproved,none',
        ]);

        $statusToSave = $validated['barangay_action_status'] === 'none' ? null : $validated['barangay_action_status'];

        $report->update([
            'barangay_action_status' => $statusToSave,
        ]);

        broadcast(new BarangayActionUpdated($report->id, $statusToSave));

        if ($request->wantsJson() || $request->isXmlHttpRequest()) {
            return response()->json([
                'success' => true,
                'message' => 'Barangay action status updated successfully.',
                'status'  => $validated['barangay_action_status'],
            ]);
        }

        return redirect()->back()->with('success', 'Barangay action status updated successfully.');
        // return redirect()->route('barangay.reports')->with('success', 'Barangay action status updated successfully.');
    }

    public function getNotifications(Request $request)
    {
        // 1. Get the currently logged-in barangay
        $barangay = Auth::guard('barangay')->user();

        // 2. Fetch recent reports assigned to THIS specific barangay
        // We use "with('user')" to prevent N+1 database query issues
        $reports = Report::with('user')
            ->where('barangay_id', $barangay->id)
            ->orderBy('created_at', 'desc')
            ->take(15) // Limit to the 15 most recent for the dropdown
            ->get();

        // 3. Format the data to match the JavaScript array expectations
        $notifications = $reports->map(function ($report) {
            return [
                'id' => $report->id, // If you have a separate notifications table, use that ID. Otherwise, use report ID.
                'report_id' => $report->id,
                'disaster_type' => $report->disaster_type,
                'user_name' => $report->user->name ?? 'Anonymous User',
                'time_ago' => $report->created_at->diffForHumans(), // e.g., "5 minutes ago"
                
                // If you don't track "read/unread" status in the database yet, default to false
                'is_read' => $report->status !== 'pending', 
            ];
        });

        // 4. Return as JSON for the fetch() call
        return response()->json([
            'notifications' => $notifications
        ]);
    }
}
