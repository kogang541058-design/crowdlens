<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Admin Login | {{ config('app.name', 'Laravel') }}</title>
        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @endif
        <style>
            * { box-sizing: border-box; margin: 0; padding: 0; }
            body {
                font-family: 'Inter', ui-sans-serif, system-ui, -apple-system;
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                min-height: 100vh;
                display: flex;
                align-items: center;
                justify-content: center;
                padding: 1.5rem;
            }
            .login-card {
                background: white;
                border-radius: 20px;
                box-shadow: 0 20px 60px rgba(0,0,0,.3);
                width: 100%;
                max-width: 420px;
                padding: 2.5rem;
                animation: slideUp 0.5s ease-out;
            }
            @keyframes slideUp {
                from { opacity: 0; transform: translateY(30px); }
                to { opacity: 1; transform: translateY(0); }
            }
            .logo {
                text-align: center;
                margin-bottom: 2rem;
            }
            .logo h1 {
                font-size: 1.75rem;
                font-weight: 800;
                color: #667eea;
                margin-bottom: .5rem;
            }
            .logo p {
                color: #6b7280;
                font-size: .95rem;
            }
            .field {
                margin-bottom: 1.25rem;
            }
            .label {
                display: block;
                font-size: .95rem;
                font-weight: 600;
                color: #374151;
                margin-bottom: .5rem;
            }
            .input {
                width: 100%;
                padding: .875rem 1rem;
                border: 2px solid #e5e7eb;
                border-radius: 12px;
                font-size: 1rem;
                transition: all 0.2s;
                background: #fafafa;
            }
            .input:focus {
                outline: none;
                border-color: #667eea;
                background: white;
                box-shadow: 0 0 0 3px rgba(102,126,234,.1);
            }
            .error {
                background: #fef2f2;
                border: 1px solid #fecaca;
                color: #991b1b;
                padding: .75rem 1rem;
                border-radius: 10px;
                margin-bottom: 1rem;
                font-size: .95rem;
            }
            .btn {
                width: 100%;
                padding: 1rem;
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                color: white;
                border: none;
                border-radius: 12px;
                font-size: 1.05rem;
                font-weight: 700;
                cursor: pointer;
                transition: all 0.3s;
                box-shadow: 0 10px 25px rgba(102,126,234,.3);
            }
            .btn:hover {
                transform: translateY(-2px);
                box-shadow: 0 15px 35px rgba(102,126,234,.4);
            }
            .btn:active {
                transform: translateY(0);
            }
            .checkbox-row {
                display: flex;
                align-items: center;
                gap: .5rem;
                margin-bottom: 1.5rem;
            }
            .checkbox-row input {
                width: 18px;
                height: 18px;
                cursor: pointer;
            }
            .checkbox-row label {
                font-size: .95rem;
                color: #4b5563;
                cursor: pointer;
            }
        </style>
    </head>
    <body>
        <div class="login-card">
            <div class="logo">
                <h1>Admin Portal</h1>
                <p>Davao City Reports</p>
            </div>

            @if ($errors->has('email'))
                <div class="error">{{ $errors->first('email') }}</div>
            @endif

            <form method="POST" action="{{ route('admin.login') }}">
                @csrf
                <div class="field">
                    <label class="label" for="email">Email Address</label>
                    <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus class="input" placeholder="admin@davaocity.gov.ph" />
                </div>

                <div class="field">
                    <label class="label" for="password">Password</label>
                    <input id="password" name="password" type="password" required class="input" placeholder="••••••••" />
                </div>

                <div class="checkbox-row">
                    <input type="checkbox" name="remember" id="remember">
                    <label for="remember">Remember me</label>
                </div>

                <button type="submit" class="btn">Sign In</button>
            </form>
        </div>
    </body>
</html>
