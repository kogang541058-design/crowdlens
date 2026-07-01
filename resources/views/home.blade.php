<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>CrowdLens</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full font-sans antialiased text-slate-900" style="background: url('{{ asset("images/Background.png") }}') center/cover no-repeat fixed;">

    <main class="min-h-screen flex flex-col lg:flex-row">
        <div class="relative flex flex-col justify-center items-start from-slate-900 to-indigo-950 p-8 md:p-12 lg:p-16 xl:p-24 lg:w-5/12 xl:w-1/2">
            
            <div class="relative max-w-xl z-10">
                <span class="text-xs md:text-sm font-bold uppercase tracking-widest bg-indigo-500/10 px-3 py-1.5 rounded-full border border-indigo-500/20">
                    DAVAO CITY REPORTS
                </span>
                <h1 class="text-4xl md:text-5xl xl:text-6xl font-black tracking-tight mt-6 mb-4">
                    CROWDLENS
                </h1>
                <p class="leading-relaxed text-sm md:text-base opacity-90">
                    Davao City Reports is the official complaint and appreciation page of The City Government of Davao.
                    It is a platform designed to address complaints, requests, and inquiries submitted via text, call, email,
                    and Facebook within Davao City.
                </p>
            </div>
            
            <div class="absolute bottom-6 left-8 md:left-12 lg:left-16 text-xs text-slate-500 hidden lg:block">
                &copy; {{ date('Y') }} The City Government of Davao. All rights reserved.
            </div>
        </div>

        <div class="flex-1 flex items-center justify-center p-6 md:p-12 xl:p-16 bg-transparent backdrop-blur-sm">
            <div class="w-full max-w-md">
                
                <div id="loginForm" class="bg-white rounded-2xl border border-slate-100 p-8 transition-all duration-300 transform">
                    <div class="flex flex-col items-center text-center mb-8">
                        <img src="{{ asset('images/img.png') }}" alt="CrowdLens Logo" class="h-14 w-auto mb-4 object-contain">
                        <h3 class="text-2xl font-black text-slate-800 tracking-tight">Welcome back</h3>
                        <p class="text-slate-500 text-sm mt-1">Sign in to continue to CrowdLens</p>
                    </div>

                    @if (($errors->has('email') || $errors->has('password')) && old('_form') === 'login')
                        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl mb-6 text-sm font-medium flex items-center gap-2">
                            <svg class="w-5 h-5 flex-shrink-0 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                            <span>
                                @if($errors->has('email'))
                                    {{ $errors->first('email') }}
                                @elseif($errors->has('password'))
                                    {{ $errors->first('password') }}
                                @endif
                            </span>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('login') }}" class="space-y-5">
                        @csrf
                        <input type="hidden" name="_form" value="login" />
                        
                        <div>
                            <label class="block mb-1.5 text-xs font-bold text-slate-700 uppercase tracking-wider" for="login_email">Email or Username</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2M12 3a4 4 0 1 0 0 8 4 4 0 0 0 0-8z"/></svg>
                                </div>
                                <input id="login_email" name="email" type="text" value="{{ old('email') }}" required
                                    class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm outline-none transition-all placeholder-slate-400" 
                                    placeholder="your@email.com or username" />
                            </div>
                        </div>

                        <div>
                            <div class="flex justify-between items-center mb-1.5">
                                <label class="text-xs font-bold text-slate-700 uppercase tracking-wider" for="login_password">Password</label>
                            </div>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                                </div>
                                <input id="login_password" name="password" type="password" required
                                    class="w-full pl-10 pr-10 py-2.5 bg-slate-50 border border-slate-200 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm outline-none transition-all placeholder-slate-400" 
                                    placeholder="••••••••" />

                                <button type="button" class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-indigo-600 transition-colors" onclick="togglePasswordVisibility('login_password', this)">
                                    <svg class="w-5 h-5 eye-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                </button>
                                
                            </div>
                            <a href="#" class="text-xs font-semibold text-indigo-600 hover:text-indigo-800 transition-colors">Forgot password?</a>
                                
                        </div>

                        <div class="flex items-center">
                            <label class="relative flex items-center cursor-pointer select-none text-sm font-medium text-slate-600">
                                <input type="checkbox" name="remember" class="sr-only peer">
                                <div class="w-4 h-4 bg-slate-100 border border-slate-300 rounded peer-checked:bg-indigo-600 peer-checked:border-indigo-600 transition-all flex items-center justify-center mr-2">
                                    <svg class="w-3 h-3 text-white hidden peer-checked:block" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                </div>
                                Remember me
                            </label>
                        </div>

                        <button type="submit" class="w-full flex items-center justify-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-3 rounded-lg font-bold text-sm transition-all shadow-md shadow-indigo-500/20 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                            <span>Log in</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14M12 5l7 7-7 7"/></svg>
                        </button>
                    </form>

                    <div class="mt-8 text-center">
                        <div class="relative flex items-center justify-center mb-6">
                            <div class="absolute inset-0 flex items-center"><div class="w-full border-t border-slate-100"></div></div>
                            <span class="relative bg-white px-3 text-xs uppercase font-bold text-slate-400 tracking-wider">or</span>
                        </div>
                        
                        <button type="button" onclick="switchAuthForm('register')" class="w-full flex items-center justify-center gap-2 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 px-4 py-3 rounded-lg font-bold text-sm transition-all border border-emerald-200">
                            <span>Create an account</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14M12 5l7 7-7 7"/></svg>
                        </button>
                    </div>
                </div>


                <div id="registerForm" class="hidden bg-white rounded-2xl shadow-xl shadow-slate-200/80 border border-slate-100 p-8 transition-all duration-300 transform">
                    <div class="mb-6">
                        <h3 class="text-2xl font-black text-slate-800 tracking-tight">Create your account</h3>
                        <p class="text-slate-500 text-sm mt-1">Get started with CrowdLens portal integration</p>
                    </div>

                    @if ($errors->any() && old('_form') === 'register')
                        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl mb-6 text-sm font-medium">
                            There are items that require your attention
                        </div>
                    @endif

                    <form method="POST" action="{{ route('register') }}" class="space-y-4">
                        @csrf
                        <input type="hidden" name="_form" value="register" />
                        
                        <div>
                            <label class="block mb-1.5 text-xs font-bold text-slate-700 uppercase tracking-wider" for="name">Full name</label>
                            <input id="name" name="name" type="text" value="{{ old('name') }}" required
                                class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm outline-none transition-all placeholder-slate-400" 
                                placeholder="John Doe" />
                            @error('name')<p class="text-red-500 text-xs mt-1.5 font-medium">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label class="block mb-1.5 text-xs font-bold text-slate-700 uppercase tracking-wider" for="email">Email address</label>
                            <input id="email" name="email" type="email" value="{{ old('email') }}" required
                                class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm outline-none transition-all placeholder-slate-400" 
                                placeholder="you@example.com" />
                            @error('email')<p class="text-red-500 text-xs mt-1.5 font-medium">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label class="block mb-1.5 text-xs font-bold text-slate-700 uppercase tracking-wider" for="password">Password</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                                </div>
                                <input id="password" name="password" type="password" required
                                    class="w-full pl-10 pr-10 py-2.5 bg-slate-50 border border-slate-200 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm outline-none transition-all placeholder-slate-400" 
                                    placeholder="••••••••" />
                                <button type="button" class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-indigo-600 transition-colors" onclick="togglePasswordVisibility('password', this)">
                                    <svg class="w-5 h-5 eye-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                </button>
                            </div>
                            @error('password')<p class="text-red-500 text-xs mt-1.5 font-medium">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label class="block mb-1.5 text-xs font-bold text-slate-700 uppercase tracking-wider" for="password_confirmation">Confirm password</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                                </div>
                                <input id="password_confirmation" name="password_confirmation" type="password" required
                                    class="w-full pl-10 pr-10 py-2.5 bg-slate-50 border border-slate-200 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm outline-none transition-all placeholder-slate-400" 
                                    placeholder="••••••••" />
                                <button type="button" class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-indigo-600 transition-colors" onclick="togglePasswordVisibility('password_confirmation', this)">
                                    <svg class="w-5 h-5 eye-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                </button>
                            </div>
                            @error('password_confirmation')<p class="text-red-500 text-xs mt-1.5 font-medium">{{ $message }}</p>@enderror
                        </div>

                        <div class="flex items-center justify-between pt-4 gap-4">
                            <button type="button" onclick="switchAuthForm('login')" class="text-sm font-bold text-slate-500 hover:text-slate-800 transition-colors flex items-center gap-1">
                                <span>&larr; Back to Login</span>
                            </button>
                            
                            <button type="submit" class="flex items-center justify-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white px-5 py-2.5 rounded-lg font-bold text-sm transition-all shadow-md shadow-emerald-500/20 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2">
                                <span>Create account</span>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14M12 5l7 7-7 7"/></svg>
                            </button>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </main>

    <script>
        /**
         * Reusable logic interface handling input visibility state modifications.
         * Cache elements natively on interaction to maximize overall performance.
         */
        function togglePasswordVisibility(targetInputId, buttonElement) {
            const input = document.getElementById(targetInputId);
            if (!input) return;
            
            const isPassword = input.type === 'password';
            input.type = isPassword ? 'text' : 'password';
            
            // Adjust visual treatment indicator class on button
            if (isPassword) {
                buttonElement.classList.add('text-indigo-600');
            } else {
                buttonElement.classList.remove('text-indigo-600');
            }
        }

        /**
         * State container shifting display flags between presentation form structures via explicit class injection hooks.
         */
        function switchAuthForm(mode) {
            const loginCard = document.getElementById('loginForm');
            const registerCard = document.getElementById('registerForm');
            
            if (mode === 'register') {
                loginCard.classList.add('hidden');
                registerCard.classList.remove('hidden');
            } else {
                registerCard.classList.add('hidden');
                loginCard.classList.remove('hidden');
            }
        }

        // Handle server fallback persistence routines
        document.addEventListener('DOMContentLoaded', function() {
            const structuralFormContext = @json(old('_form'));
            if (structuralFormContext === 'register') {
                switchAuthForm('register');
            }
        });
    </script>
</body>
</html>