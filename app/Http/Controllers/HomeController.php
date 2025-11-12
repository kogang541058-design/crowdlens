<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

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
        // Get current user's reports
        $reports = \App\Models\Report::where('user_id', auth()->id())
            ->latest()
            ->get();
        
        // Get all verified reports to show on map
        $verifiedReports = \App\Models\Report::with('user')
            ->where('status', 'verified')
            ->orderBy('created_at', 'desc')
            ->get();
        
        // Get active disaster types
        $disasterTypes = \App\Models\DisasterType::where('is_active', true)
            ->orderBy('name')
            ->get();
        
        return view('dashboard', compact('reports', 'verifiedReports', 'disasterTypes'));
    }
}
