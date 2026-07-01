<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    

    /**
     * Display account settings page for Admin and Barangay.
     */
    public function staffSettings()
    {
        $barangay = Auth::guard('barangay')->check() ? Auth::guard('barangay')->user() : null;
        
        return view('staff-settings', compact('barangay'));
    }

    /**
     * Update the user's profile information.
     */
    public function updateProfile(Request $request)
    {
        // 1. Handle Admin Update
        if (Auth::guard('admin')->check()) {
            $admin = Auth::guard('admin')->user();
            
            $validated = $request->validate([
                'name' => ['required', 'string', 'max:255'],
                // Validate against the 'admins' table
                'email' => ['required', 'string', 'email', 'max:255', Rule::unique('admins')->ignore($admin->id)],
            ]);

            $admin->update([
                'name' => $validated['name'],
                'email' => $validated['email'],
            ]);

            return back()->with('success', 'Admin profile updated successfully.');
        }

        // 2. Handle Barangay Update
        if (Auth::guard('barangay')->check()) {
            $barangay = Auth::guard('barangay')->user();
            
            $validated = $request->validate([
                'name' => ['required', 'string', 'max:255'],
                // Validate against the 'barangays' table, specifically the 'username' column
                'email' => ['required', 'string', 'max:255', Rule::unique('barangays', 'username')->ignore($barangay->id)],
            ], [
                // Custom error message so it doesn't confuse the user
                'email.unique' => 'This username/email is already taken by another Barangay.'
            ]);

            $barangay->update([
                'name' => $validated['name'],
                'username' => $validated['email'], // Map the form's 'email' input to the DB's 'username' column
            ]);

            return back()->with('success', 'Barangay profile updated successfully.');
        }

        if (Auth::check()) {
            $user = Auth::user(); // Uses the default 'web' guard
            
            $validated = $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            ]);

            $user->update([
                'name' => $validated['name'],
                'email' => $validated['email'],
            ]);

            return back()->with('success', 'User profile updated successfully.');
        }

        abort(403, 'Unauthorized action.');
    }

    /**
     * Update the user's password.
     */
    public function updatePassword(Request $request)
    {
        // 1. Basic validation for the new password
        $request->validate([
            'current_password' => ['required', 'string'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        // 2. Identify which user is logged in
        $user = null;
        if (Auth::guard('admin')->check()) {
            $user = Auth::guard('admin')->user();
        } elseif (Auth::guard('barangay')->check()) {
            $user = Auth::guard('barangay')->user();
        } elseif (Auth::check()) {
            $user = Auth::user(); 
        } else {
            abort(403, 'Unauthorized action.');
        }

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'The provided password does not match your current password.']);
        }

        // 4. Update the password
        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return back()->with('success', 'Password updated successfully.');
    }
}
