<?php

namespace App\Http\Controllers;

use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ReportController extends Controller
{
    /**
     * Store a newly created report.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'disaster_type' => 'required|string',
            'description' => 'required|string|max:1000',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'location' => 'nullable|string',
            'image' => 'nullable|image|max:10240', // 10MB max
            'video' => 'nullable|mimetypes:video/*|max:102400', // 100MB max
        ]);

        $validated['user_id'] = Auth::id();

        // Handle file uploads
        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('reports/images', 'public');
        }

        if ($request->hasFile('video')) {
            $validated['video'] = $request->file('video')->store('reports/videos', 'public');
        }

        $report = Report::create($validated);

        // Create unsolved entry for the new report
        \App\Models\Unsolved::create([
            'report_id' => $report->id,
            'priority' => 'medium',
            'pending_since' => now(),
        ]);

        return redirect()->back()->with('success', 'Report submitted successfully!');
    }

    /**
     * Get user's reports
     */
    public function index()
    {
        $reports = Report::where('user_id', Auth::id())
            ->latest()
            ->get();

        return response()->json($reports);
    }
}
