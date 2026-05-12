<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ config('app.name', 'Laravel') }}</title>
        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @endif
        <style>
            :root { --accent:#2f6fed; --accent-hover:#1d5ce0; }
            * { box-sizing: border-box; }
            html, body { height: 100%; }
            body {
                margin: 0;
                font-family: 'Inter', ui-sans-serif, system-ui, -apple-system, Segoe UI, Roboto, "Helvetica Neue", Arial, "Noto Sans", "Apple Color Emoji", "Segoe UI Emoji";
                color: #1b1b18;
                background: url('{{ asset("images/Background.png") }}') center/cover no-repeat fixed;
                position: relative;
                min-height: 100%;
                overflow-x: auto;
            }
            .hero { 
                display:flex; 
                align-items:center; 
                justify-content:space-between; 
                min-height:100vh;
                padding: 3rem 4rem; 
                position: relative;
                z-index: 1;
                max-width: 1600px;
                margin: 0 auto;
                gap: 6rem;
            }
            .hero-content {
                animation: fadeInUp 0.8s ease-out;
                flex: 1;
                text-align: left;
                display: flex;
                flex-direction: column;
                justify-content: center;
            }
            .hero-sidebar {
                flex: 0 0 auto;
                display: flex;
                gap: 1.5rem;
                align-items: center;
                justify-content: center;
                position: relative;
            }
            .action-cards {
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 1.25rem;
                margin: 2.5rem 0;
                max-width: 350px;
            }
            .action-card {
                background: #fff;
                border-radius: 14px;
                padding: 1.5rem;
                text-align: center;
                display: flex;
                flex-direction: column;
                align-items: center;
                gap: .75rem;
                box-shadow: 0 4px 12px rgba(0,0,0,.08);
                transition: all 0.3s ease;
                animation: fadeInUp 0.8s ease-out backwards;
            }
            .action-card:nth-child(1) { animation-delay: 0.4s; }
            .action-card:nth-child(2) { animation-delay: 0.45s; }
            .action-card:nth-child(3) { animation-delay: 0.5s; }
            .action-card:nth-child(4) { animation-delay: 0.55s; }
            .action-card:hover {
                transform: translateY(-4px);
                box-shadow: 0 8px 24px rgba(0,0,0,.12);
            }
            .action-card-icon {
                width: 48px;
                height: 48px;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 1.5rem;
                color: #FF9500;
            }
            .action-card-icon svg {
                stroke: #FF9500;
            }
            .action-card-label {
                font-size: .85rem;
                font-weight: 600;
                color: #1a1a1a;
                line-height: 1.3;
            }
            @keyframes fadeInUp {
                from { opacity: 0; transform: translateY(30px); }
                to { opacity: 1; transform: translateY(0); }
            }
            .eyebrow { 
                letter-spacing:.2em; 
                font-size:.7rem; 
                opacity:.7; 
                margin-bottom:1rem; 
                font-weight: 700;
                text-transform: uppercase;
                animation: fadeInUp 0.8s ease-out 0.1s backwards;
                text-align: left;
            }
            h1 { 
                font-size: clamp(2.8rem, 5vw, 3.5rem); 
                line-height:1.1; 
                margin: 0 0 .5rem 0; 
                font-weight: 900;
                letter-spacing: -0.02em;
                animation: fadeInUp 0.8s ease-out 0.2s backwards;
            }
            .subtitle {
                font-size: .9rem;
                color: #6b7280;
                margin: 0 0 2rem 0;
                animation: fadeInUp 0.8s ease-out 0.25s backwards;
            }
            p { 
                margin: 0; 
                font-size: .95rem; 
                line-height: 1.6; 
                opacity:.88;
                font-weight: 400;
                animation: fadeInUp 0.8s ease-out 0.3s backwards;
            }
            .btn { 
                display:inline-flex; 
                align-items:center; 
                justify-content:center; 
                gap:.4rem; 
                padding:.85rem 1.5rem; 
                border-radius: 10px; 
                border: none; 
                cursor: pointer; 
                background: var(--accent); 
                color: #fff; 
                font-weight: 700;
                font-size: .95rem;
                width: 100%;
                box-shadow: 0 4px 16px rgba(47,111,237,.25), 0 2px 6px rgba(47,111,237,.15);
                transition: all 0.25s ease;
                position: relative;
                overflow: hidden;
                animation: fadeInUp 0.8s ease-out 0.4s backwards;
            }
            .btn::before {
                content: '';
                position: absolute;
                top: 50%;
                left: 50%;
                width: 0;
                height: 0;
                border-radius: 50%;
                background: rgba(255,255,255,.15);
                transform: translate(-50%, -50%);
                transition: width 0.4s, height 0.4s;
            }
            .btn:hover::before {
                width: 250px;
                height: 250px;
            }
            .btn:hover { 
                transform: translateY(-1px); 
                box-shadow: 0 6px 24px rgba(47,111,237,.3), 0 3px 8px rgba(47,111,237,.18);
                background: var(--accent-hover);
            }
            .btn:active { 
                transform: translateY(0); 
                box-shadow: 0 3px 12px rgba(47,111,237,.25);
            }
            .btn:focus { 
                outline: 2px solid rgba(47,111,237,.4); 
                outline-offset: 2px; 
            }
            .link { 
                color: #0b57d0; 
                text-decoration: underline; 
                text-underline-offset: 4px;
                font-weight: 600;
                transition: all 0.2s;
                text-decoration-thickness: 2px;
            }
            .link:hover {
                color: #084298;
                text-underline-offset: 6px;
            }
            .muted { 
                margin-top: 1.5rem; 
                margin-bottom: 2rem; 
                color:#333;
                font-size: 1rem;
                animation: fadeInUp 0.8s ease-out 0.5s backwards;
            }
            .form-card { 
                border-radius: 16px; 
                padding: 0; 
                box-shadow: 0 8px 32px rgba(0,0,0,.12), 0 0 1px rgba(0,0,0,.05); 
                width: 420px;
                animation: fadeInUp 0.8s ease-out 0.4s backwards;
            }
            dialog { 
                display: none !important;
            }
            .card { 
                padding: 2rem; 
                background: #fff; 
                border-radius: 16px; 
            }
            .card-logo {
                width: 56px;
                height: 56px;
                margin: 0 auto 1.25rem;
                display: block;
            }
            .card h3 { 
                margin: 0 0 .5rem 0; 
                font-size: 1.35rem;
                font-weight: 700;
                color: #1a1a1a;
                text-align: center;
            }
            .card-subtitle {
                text-align: center;
                font-size: .9rem;
                color: #6b7280;
                margin: 0 0 1.5rem 0;
            }
            .field { 
                margin-bottom: 1.1rem; 
                text-align:left; 
                position: relative;
            }
            .field svg {
                position: absolute;
                left: 1rem;
                top: 50%;
                transform: translateY(-50%);
                width: 20px;
                height: 20px;
                color: #6b7280;
                pointer-events: none;
                flex-shrink: 0;
            }
            .field .toggle-password {
                position: absolute;
                right: 1rem;
                top: 50%;
                transform: translateY(-50%);
                cursor: pointer;
                color: #6b7280;
                background: none;
                border: none;
                padding: 0;
                width: 24px;
                height: 24px;
                display: flex;
                align-items: center;
                justify-content: center;
                transition: color 0.2s;
            }
            .field .toggle-password:hover {
                color: #374151;
            }
            .field .toggle-password svg {
                width: 20px;
                height: 20px;
            }
            .label { 
                display:none;
            }
            .input { 
                width:100%; 
                padding:.85rem 1rem .85rem 2.75rem; 
                border-radius:10px; 
                border:1.5px solid #e5e7eb; 
                font-size:.95rem;
                transition: all 0.2s;
                background: #f8f9fa;
            }
            .input:focus {
                outline: none;
                border-color: var(--accent);
                background: #fff;
                box-shadow: 0 0 0 2px rgba(47,111,237,.08);
            }
            .input:hover {
                border-color: #d1d5db;
            }
            .row { 
                display:flex; 
                align-items:center; 
                justify-content:space-between; 
                gap:.75rem;
                margin-top: 1.25rem;
            }
            .close { 
                display: none;
            }
            .close:hover {
                background: #f3f4f6;
                color: #1f2937;
            }
            .error-msg {
                color:#dc2626;
                font-size:.8rem;
                margin-top: .3rem;
                font-weight: 500;
            }
            .error-banner {
                background: #fef2f2;
                border: 1px solid #fecaca;
                color: #991b1b;
                padding: .65rem .9rem;
                border-radius: 8px;
                margin-bottom: .85rem;
                font-size: .85rem;
            }
            .checkbox-label {
                display: inline-flex;
                align-items: center;
                gap: .4rem;
                font-size: .85rem;
                cursor: pointer;
                color: #6b7280;
            }
            input[type="checkbox"] {
                width: 16px;
                height: 16px;
                cursor: pointer;
            }
            .btn-register {
                background: #10b981;
                box-shadow: 0 10px 25px rgba(16,185,129,.3), 0 4px 10px rgba(16,185,129,.15);
            }
            .btn-register:hover {
                background: #059669;
                box-shadow: 0 15px 35px rgba(16,185,129,.35), 0 5px 15px rgba(16,185,129,.2);
            }
            .btn-register:focus {
                outline-color: rgba(16,185,129,.4);
            }
            @media (max-width: 1024px) {
                .hero {
                    flex-direction: column;
                    text-align: center;
                    padding: 2rem;
                    gap: 2rem;
                }
                .hero-content {
                    text-align: center;
                    max-width: 100%;
                }
                .eyebrow {
                    text-align: center;
                }
                .hero-sidebar {
                    width: 100%;
                    justify-content: center;
                    margin-top: 1rem;
                    gap: 1rem;
                }
                .form-card {
                    width: 300px;
                }
            }
        </style>
    </head>
    <body>
        <main class="hero">
            <div class="hero-content">
                <div class="eyebrow">DAVAO CITY REPORTS</div>
                <h1>CROWDLENS</h1>
                <div class="subtitle">Sign in to continue to CrowdLens</div>
                <p>
                    Davao City Reports is the official complaint and appreciation page of The City Government of Davao.
                    It is a platform designed to address complaints, requests, and inquiries submitted via text, call, email,
                    and Facebook within Davao City.
                </p>
            </div>

            <div class="hero-sidebar">
                <!-- Login Form -->
                <div class="form-card" id="loginForm">
            <div class="card">
                <img src="{{ asset('images/img.png') }}" alt="Logo" class="card-logo">
                <h3>Welcome back</h3>
                <div class="card-subtitle">Sign in to continue to CrowdLens</div>
                @if (($errors->has('email') || $errors->has('password')) && old('_form') === 'login')
                    <div class="error-banner">
                        @if($errors->has('email'))
                            {{ $errors->first('email') }}
                        @elseif($errors->has('password'))
                            {{ $errors->first('password') }}
                        @endif
                    </div>
                @endif
                <form method="POST" action="{{ route('login') }}">
                    @csrf
                    <input type="hidden" name="_form" value="login" />
                    <div class="field">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2M12 3a4 4 0 1 0 0 8 4 4 0 0 0 0-8z"/></svg>
                        <label class="label" for="login_email">Email or Username</label>
                        <input id="login_email" name="email" type="text" value="{{ old('email') }}" class="input" placeholder="your@email.com or username" />
                    </div>
                    <div class="field">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                        <label class="label" for="login_password">Password</label>
                        <input id="login_password" name="password" type="password" class="input" placeholder="••••••••" />
                        <button type="button" class="toggle-password" onclick="const input = document.getElementById('login_password'); input.type = input.type === 'password' ? 'text' : 'password'; this.classList.toggle('active');">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                        </button>
                    </div>
                    <div class="forgot-password">
                        <a href="#">Forgot password?</a>
                    </div>
                    <div class="row" style="margin-top:1.5rem; margin-bottom:0; justify-content: space-between;">
                        <label class="checkbox-label">
                            <input type="checkbox" name="remember"> Remember me
                        </label>
                    </div>
                    <button type="submit" class="btn" style="margin-top: 1.5rem;">
                        Log in
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M5 12h14M12 5l7 7-7 7"/>
                        </svg>
                    </button>
                </form>
                <div style="margin-top: 1.5rem; text-align: center;">
                    <p style="margin: 0 0 1rem 0; font-size: .85rem; color: #6b7280;">or</p>
                    <button type="button" onclick="document.getElementById('loginForm').style.display='none'; document.getElementById('registerForm').style.display='block';" class="btn" style="background: #10b981; box-shadow: 0 4px 16px rgba(16,185,129,.25), 0 2px 6px rgba(16,185,129,.15); text-decoration: none; cursor: pointer;">
                        Create an account
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M5 12h14M12 5l7 7-7 7"/>
                        </svg>
                    </button>
                </div>
            </div>
            </div>

            <!-- Register Form -->
            <div class="form-card" style="display: none;" id="registerForm">
            <div class="card">
                <h3>Create your account</h3>
                @if ($errors->any() && old('_form') === 'register')
                    <div class="error-banner">
                        There are items that require your attention
                    </div>
                @endif
                <form method="POST" action="{{ route('register') }}">
                    @csrf
                    <input type="hidden" name="_form" value="register" />
                    <div class="field">
                        <label class="label" for="name">Full name</label>
                        <input id="name" name="name" type="text" value="{{ old('name') }}" class="input" placeholder="John Doe" />
                        @error('name')<div class="error-msg">{{ $message }}</div>@enderror
                    </div>
                    <div class="field">
                        <label class="label" for="email">Email address</label>
                        <input id="email" name="email" type="email" value="{{ old('email') }}" class="input" placeholder="you@example.com" />
                        @error('email')<div class="error-msg">{{ $message }}</div>@enderror
                    </div>
                    <div class="field">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                        <label class="label" for="password">Password</label>
                        <input id="password" name="password" type="password" class="input" placeholder="••••••••" />
                        <button type="button" class="toggle-password" onclick="const input = document.getElementById('password'); input.type = input.type === 'password' ? 'text' : 'password'; this.classList.toggle('active');">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                        </button>
                        @error('password')<div class="error-msg">{{ $message }}</div>@enderror
                    </div>
                    <div class="field">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                        <label class="label" for="password_confirmation">Confirm password</label>
                        <input id="password_confirmation" name="password_confirmation" type="password" class="input" placeholder="••••••••" />
                        <button type="button" class="toggle-password" onclick="const input = document.getElementById('password_confirmation'); input.type = input.type === 'password' ? 'text' : 'password'; this.classList.toggle('active');">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                        </button>
                        @error('password_confirmation')<div class="error-msg">{{ $message }}</div>@enderror
                    </div>
                    <div class="row" style="justify-content: space-between;">
                        <button type="button" onclick="document.getElementById('registerForm').style.display='none'; document.getElementById('loginForm').style.display='block';" style="background: transparent; color: #6b7280; border: none; cursor: pointer; font-size: .85rem; font-weight: 600;">← Back to Login</button>
                        <button type="submit" class="btn" style="background: #10b981; box-shadow: 0 4px 16px rgba(16,185,129,.25), 0 2px 6px rgba(16,185,129,.15);">
                            Create account
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M5 12h14M12 5l7 7-7 7"/>
                            </svg>
                        </button>
                    </div>
                </form>
            </div>
                </div>
            </div>
        </main>

        <script>
            // Show register form if validation errors exist
            (function(){
                const which = @json(old('_form'));
                if (which === 'register') {
                    document.getElementById('loginForm').style.display = 'none';
                    document.getElementById('registerForm').style.display = 'block';
                }
            })();
        </script>
    </body>
</html>
