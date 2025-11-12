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
                background: radial-gradient(1500px 600px at 50% -10%, #ffe677 0%, #ffd24d 30%, #ffb83b 55%, #ff9f2d 75%, #ff8a26 100%);
                position: relative;
                min-height: 100%;
                overflow-x: hidden;
            }
            /* Animated background shapes */
            body::before, body::after {
                content: '';
                position: absolute;
                border-radius: 50%;
                opacity: .08;
                pointer-events: none;
                animation: float 20s ease-in-out infinite;
            }
            body::before {
                width: 400px;
                height: 400px;
                background: radial-gradient(circle, rgba(255,255,255,.5) 0%, transparent 70%);
                top: -100px;
                left: -100px;
                animation-delay: -5s;
            }
            body::after {
                width: 600px;
                height: 600px;
                background: radial-gradient(circle, rgba(255,255,255,.3) 0%, transparent 70%);
                bottom: -200px;
                right: -200px;
                animation-delay: -10s;
            }
            @keyframes float {
                0%, 100% { transform: translate(0, 0) scale(1); }
                33% { transform: translate(30px, -30px) scale(1.1); }
                66% { transform: translate(-20px, 20px) scale(0.9); }
            }
            .hero { 
                display:flex; 
                align-items:center; 
                justify-content:center; 
                min-height:100%; 
                padding: 2rem; 
                text-align:center;
                position: relative;
                z-index: 1;
            }
            .hero-content {
                animation: fadeInUp 0.8s ease-out;
            }
            @keyframes fadeInUp {
                from { opacity: 0; transform: translateY(30px); }
                to { opacity: 1; transform: translateY(0); }
            }
            .eyebrow { 
                letter-spacing:.25em; 
                font-size:.75rem; 
                opacity:.75; 
                margin-bottom:1.25rem; 
                font-weight: 600;
                text-transform: uppercase;
                animation: fadeInUp 0.8s ease-out 0.1s backwards;
            }
            h1 { 
                font-size: clamp(2.25rem, 6vw, 4rem); 
                line-height:1.1; 
                margin: 0 0 1.25rem 0; 
                font-weight: 900;
                letter-spacing: -0.02em;
                animation: fadeInUp 0.8s ease-out 0.2s backwards;
            }
            p { 
                max-width: 48rem; 
                margin: 0 auto 2.5rem auto; 
                font-size: 1.125rem; 
                line-height: 1.75; 
                opacity:.92;
                font-weight: 400;
                animation: fadeInUp 0.8s ease-out 0.3s backwards;
            }
            .btn { 
                display:inline-flex; 
                align-items:center; 
                justify-content:center; 
                gap:.5rem; 
                padding:1rem 2.5rem; 
                border-radius: 9999px; 
                border: none; 
                cursor: pointer; 
                background: var(--accent); 
                color: #fff; 
                font-weight: 700;
                font-size: 1.05rem;
                box-shadow: 0 10px 25px rgba(47,111,237,.35), 0 4px 10px rgba(47,111,237,.2);
                transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
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
                background: rgba(255,255,255,.2);
                transform: translate(-50%, -50%);
                transition: width 0.6s, height 0.6s;
            }
            .btn:hover::before {
                width: 300px;
                height: 300px;
            }
            .btn:hover { 
                transform: translateY(-2px); 
                box-shadow: 0 15px 35px rgba(47,111,237,.4), 0 5px 15px rgba(47,111,237,.25);
                background: var(--accent-hover);
            }
            .btn:active { 
                transform: translateY(0); 
                box-shadow: 0 5px 15px rgba(47,111,237,.3);
            }
            .btn:focus { 
                outline: 3px solid rgba(47,111,237,.4); 
                outline-offset: 3px; 
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
            dialog { 
                border: none; 
                border-radius: 20px; 
                padding: 0; 
                box-shadow: 0 25px 80px rgba(0,0,0,.3), 0 0 1px rgba(0,0,0,.1); 
                width: 95%; 
                max-width: 440px;
                animation: modalFadeIn 0.3s ease-out;
            }
            dialog::backdrop {
                background: rgba(0,0,0,.6);
                backdrop-filter: blur(4px);
                animation: backdropFadeIn 0.3s ease-out;
            }
            @keyframes modalFadeIn {
                from { opacity: 0; transform: scale(0.9) translateY(20px); }
                to { opacity: 1; transform: scale(1) translateY(0); }
            }
            @keyframes backdropFadeIn {
                from { opacity: 0; }
                to { opacity: 1; }
            }
            .card { 
                padding: 2rem; 
                background: #fff; 
                border-radius: 20px; 
            }
            .card h3 { 
                margin: 0 0 1.5rem 0; 
                font-size: 1.5rem;
                font-weight: 700;
                color: #1a1a1a;
            }
            .field { 
                margin-bottom: 1.25rem; 
                text-align:left; 
            }
            .label { 
                display:block; 
                font-size:.95rem; 
                margin-bottom:.5rem;
                font-weight: 600;
                color: #374151;
            }
            .input { 
                width:100%; 
                padding:.85rem 1rem; 
                border-radius:12px; 
                border:2px solid #e5e7eb; 
                font-size:1rem;
                transition: all 0.2s;
                background: #fafafa;
            }
            .input:focus {
                outline: none;
                border-color: var(--accent);
                background: #fff;
                box-shadow: 0 0 0 3px rgba(47,111,237,.1);
            }
            .input:hover {
                border-color: #d1d5db;
            }
            .row { 
                display:flex; 
                align-items:center; 
                justify-content:space-between; 
                gap:1rem;
                margin-top: 1.5rem;
            }
            .close { 
                background:none; 
                border:none; 
                font-size:1.75rem; 
                line-height:1; 
                cursor:pointer;
                color: #6b7280;
                transition: all 0.2s;
                width: 32px;
                height: 32px;
                display: flex;
                align-items: center;
                justify-content: center;
                border-radius: 8px;
            }
            .close:hover {
                background: #f3f4f6;
                color: #1f2937;
            }
            .error-msg {
                color:#dc2626;
                font-size:.9rem;
                margin-top: .4rem;
                font-weight: 500;
            }
            .error-banner {
                background: #fef2f2;
                border: 1px solid #fecaca;
                color: #991b1b;
                padding: .75rem 1rem;
                border-radius: 10px;
                margin-bottom: 1rem;
                font-size: .95rem;
            }
            .checkbox-label {
                display: inline-flex;
                align-items: center;
                gap: .5rem;
                font-size: .95rem;
                cursor: pointer;
                color: #4b5563;
            }
            input[type="checkbox"] {
                width: 18px;
                height: 18px;
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
        </style>
    </head>
    <body>
        <main class="hero">
            <div class="hero-content">
                <div class="eyebrow">DAVAO CITY REPORTS</div>
                <h1>Davao City Reports</h1>
                <p>
                    Davao City Reports is the official complaint and appreciation page of The City Government of Davao.
                    It is a platform designed to address complaints, requests, and inquiries submitted via text, call, email,
                    and Facebook within Davao City.
                </p>

                <button type="button" class="btn" onclick="document.getElementById('loginDialog').showModal()">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4M10 17l5-5-5-5M13.8 12H3"/>
                    </svg>
                    Login
                </button>
                <div class="muted">
                    <a href="#" class="link" onclick="document.getElementById('registerDialog').showModal(); return false;">Not yet registered? Click Here</a>
                </div>
            </div>
        </main>

        <!-- Login modal -->
        <dialog id="loginDialog">
            <form method="dialog" style="position:absolute; right:12px; top:12px; z-index:10;">
                <button class="close" aria-label="Close">×</button>
            </form>
            <div class="card">
                <h3>Welcome back</h3>
                @if ($errors->has('email') && old('_form') === 'login')
                    <div class="error-banner">{{ $errors->first('email') }}</div>
                @endif
                <form method="POST" action="{{ route('login') }}">
                    @csrf
                    <input type="hidden" name="_form" value="login" />
                    <div class="field">
                        <label class="label" for="login_email">Email address</label>
                        <input id="login_email" name="email" type="email" value="{{ old('email') }}" required class="input" placeholder="you@example.com" />
                    </div>
                    <div class="field">
                        <label class="label" for="login_password">Password</label>
                        <input id="login_password" name="password" type="password" required class="input" placeholder="••••••••" />
                    </div>
                    <div class="row" style="margin-top:1rem; margin-bottom:0;">
                        <label class="checkbox-label">
                            <input type="checkbox" name="remember"> Remember me
                        </label>
                        <button type="submit" class="btn">
                            Log in
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M5 12h14M12 5l7 7-7 7"/>
                            </svg>
                        </button>
                    </div>
                </form>
            </div>
        </dialog>

        <!-- Register modal -->
        <dialog id="registerDialog">
            <form method="dialog" style="position:absolute; right:12px; top:12px; z-index:10;">
                <button class="close" aria-label="Close">×</button>
            </form>
            <div class="card">
                <h3>Create your account</h3>
                @if ($errors->any() && old('_form') === 'register')
                    <div class="error-banner">Please fix the errors below</div>
                @endif
                <form method="POST" action="{{ route('register') }}">
                    @csrf
                    <input type="hidden" name="_form" value="register" />
                    <div class="field">
                        <label class="label" for="name">Full name</label>
                        <input id="name" name="name" type="text" value="{{ old('name') }}" required class="input" placeholder="John Doe" />
                        @error('name')<div class="error-msg">{{ $message }}</div>@enderror
                    </div>
                    <div class="field">
                        <label class="label" for="email">Email address</label>
                        <input id="email" name="email" type="email" value="{{ old('email') }}" required class="input" placeholder="you@example.com" />
                        @error('email')<div class="error-msg">{{ $message }}</div>@enderror
                    </div>
                    <div class="field">
                        <label class="label" for="password">Password</label>
                        <input id="password" name="password" type="password" required class="input" placeholder="••••••••" />
                        @error('password')<div class="error-msg">{{ $message }}</div>@enderror
                    </div>
                    <div class="field">
                        <label class="label" for="password_confirmation">Confirm password</label>
                        <input id="password_confirmation" name="password_confirmation" type="password" required class="input" placeholder="••••••••" />
                    </div>
                    <div class="row">
                        <span></span>
                        <button type="submit" class="btn btn-register">
                            Create account
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M5 12h14M12 5l7 7-7 7"/>
                            </svg>
                        </button>
                    </div>
                </form>
            </div>
        </dialog>

        <script>
            // Auto-open the relevant dialog if validation failed
            (function(){
                const which = @json(old('_form'));
                if (which === 'login') {
                    document.getElementById('loginDialog').showModal();
                } else if (which === 'register') {
                    document.getElementById('registerDialog').showModal();
                }
            })();
        </script>
    </body>
</html>
