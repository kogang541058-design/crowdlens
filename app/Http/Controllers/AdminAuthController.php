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
        $reports = \App\Models\Report::with(['user', 'solved', 'unsolved'])->orderBy('created_at', 'desc')->get();
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
     * Mark report as solved.
     */
    public function markSolved(\App\Models\Report $report)
    {
        // Remove from unsolved if exists
        \App\Models\Unsolved::where('report_id', $report->id)->delete();
        
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
     * Mark report as unsolved.
     */
    public function markUnsolved(\App\Models\Report $report)
    {
        // Remove from solved if exists
        \App\Models\Solved::where('report_id', $report->id)->delete();
        
        // Create or update unsolved entry
        \App\Models\Unsolved::updateOrCreate(
            ['report_id' => $report->id],
            [
                'admin_id' => auth('admin')->id(),
                'priority' => 'medium',
                'pending_since' => now(),
            ]
        );
        
        return redirect()->back()->with('success', 'Report marked as unsolved!');
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

        // Remove from solved/unsolved tables
        \App\Models\Solved::where('report_id', $report->id)->delete();
        \App\Models\Unsolved::where('report_id', $report->id)->delete();

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
     * Show unsolved reports page.
     */
    public function unsolved()
    {
        $unsolvedReports = \App\Models\Unsolved::with(['report.user', 'admin'])
            ->orderBy('pending_since', 'desc')
            ->get();
        $disasterTypes = \App\Models\DisasterType::where('is_active', true)->get();
        return view('admin.unsolved', compact('unsolvedReports', 'disasterTypes'));
    }
}
