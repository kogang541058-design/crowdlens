<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Handle a login request.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'string'],
            'password' => ['required'],
        ]);

        // Check if too many login attempts
        $key = $this->throttleKey($request);
        
        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);
            $attempts = RateLimiter::attempts($key);
            
            throw ValidationException::withMessages([
                'email' => __('Too many login attempts (:attempts/5). Please try again in :seconds seconds.', [
                    'attempts' => $attempts,
                    'seconds' => $seconds
                ]),
            ]);
        }

        $remember = (bool) $request->boolean('remember');

        // Try admin login first
        if (Auth::guard('admin')->attempt($credentials, $remember)) {
            RateLimiter::clear($key);
            $request->session()->regenerate();
            return redirect()->route('admin.dashboard');
        }

        // Try barangay login with username field (using email input as username)
        if (Auth::guard('barangay')->attempt(['username' => $request->email, 'password' => $request->password], $remember)) {
            RateLimiter::clear($key);
            $request->session()->regenerate();
            return redirect()->route('barangay.dashboard');
        }

        // Then try regular user login
        // Check if user exists and is blocked
        $user = User::where('email', $request->email)->first();
        
        // If user doesn't exist with this email
        if (!$user && !Auth::guard('admin')->check()) {
            throw ValidationException::withMessages([
                'email' => __('No account found with this email address.'),
            ]);
        }
        
        if ($user && $user->isBlocked()) {
            $activeBlock = $user->activeBlock;
            
            // User is still blocked
            $message = 'Your account has been blocked.';
            if ($activeBlock->block_reason) {
                $message .= ' Reason: ' . $activeBlock->block_reason . '.';
            }
            if ($activeBlock->blocked_until) {
                $message .= ' Until: ' . $activeBlock->blocked_until->format('M d, Y h:i A') . '.';
            }
            $message .= ' Please contact the administrator.';
            
            throw ValidationException::withMessages([
                'email' => __($message),
            ]);
        }

        if (Auth::attempt($credentials, $remember)) {
            RateLimiter::clear($key);
            $request->session()->regenerate();
            return redirect()->intended(route('dashboard'));
        }

        // If we reach here, password is wrong
        // Increment the rate limiter
        RateLimiter::hit($key, 60);
        
        $attempts = RateLimiter::attempts($key);
        $remainingAttempts = max(0, 5 - $attempts);
        
        throw ValidationException::withMessages([
            'password' => __('The password you entered is incorrect. :remaining attempts remaining.', [
                'remaining' => $remainingAttempts
            ]),
        ]);
    }

    /**
     * Get the rate limiting throttle key for the request.
     */
    protected function throttleKey(Request $request): string
    {
        return Str::transliterate(Str::lower($request->input('email')).'|'.$request->ip());
    }

    /**
     * Handle a registration request.
     */
    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => [
                'required', 
                'confirmed', 
                'min:8',
                'regex:/[!@#$%^&*(),.?":{}|<>]/'
            ],
        ], [
            'name.required' => 'Please provide your full name.',
            'email.required' => 'Please provide your email address.',
            'email.email' => 'Please provide a valid email address.',
            'email.unique' => 'This email address is already registered.',
            'password.required' => 'Please provide a password.',
            'password.min' => 'Password must be at least 8 characters long.',
            'password.confirmed' => 'Password confirmation does not match.',
            'password.regex' => 'Password must contain at least one special character (!@#$%^&*(),.?":{}|<>).'
        ]);

        // Password will be hashed automatically via User model cast
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'],
        ]);

        Auth::login($user);

        $request->session()->regenerate();

        return redirect()->route('dashboard');
    }

    /**
     * Log the user out.
     */
    public function logout(Request $request)
    {
        // Check which guard is authenticated and logout accordingly
        if (Auth::guard('admin')->check()) {
            Auth::guard('admin')->logout();
        } else {
            Auth::guard('web')->logout();
        }
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }
}
