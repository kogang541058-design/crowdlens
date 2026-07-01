<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>@yield('title')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="bg-slate-50 flex flex-col md:flex-row min-h-screen font-sans antialiased text-slate-900">

    @include('partials.sidebar')

    <main class="flex-1 w-full md:w-[calc(100%-260px)] md:ml-[260px] min-w-0">
        @yield('content')
    </main>

    @include('partials.footer')

    @stack('scripts')
</body>
</html>