@extends('layouts.admin')

<!-- @section('title', 'Reports - $barangay->name') -->
@section('title', 'Account Settings - Admin Dashboard')

@section('content')
    
<div class="p-4 md:p-6 lg:p-8 w-full max-w-7xl mx-auto">
    
    @include('partials.notif_logout', ['page_name' => 'Account Settings', 'display_name' => $barangay->name])

        
    @if(session('success'))
    <div class="mb-6 flex items-center gap-3 p-4 bg-emerald-50 text-emerald-800 rounded-lg border border-emerald-200 shadow-sm">
        <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        <span class="font-medium text-sm">{{ session('success') }}</span>
    </div>
    @endif
    @if ($errors->any())
        <div class="mb-6 p-4 rounded-lg bg-rose-50 border border-rose-200 text-rose-700 text-sm">
            <ul class="list-disc list-inside font-medium">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 sm:p-8 flex flex-col h-full">
            <div class="mb-6">
                <h2 class="text-xl font-bold text-slate-800">General Information</h2>
                <p class="text-sm text-slate-500 mt-1">Update your basic account details.</p>
            </div>
            
            <form action="{{ route('profile.update') }}" method="POST" class="flex-1 flex flex-col">
                @csrf
                @method('PATCH')
                <div class="space-y-5 flex-1">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Full Name</label>
                        <input type="text" name="name" value="{{ old('name', Auth::guard('admin')->user()->name ?? Auth::guard('barangay')->user()->name ?? '') }}" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Email Address</label>
                        <input type="email" name="email" value="{{ old('email', Auth::guard('admin')->user()->email ?? Auth::guard('barangay')->user()->username ?? '') }}" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                    </div>
                </div>
                <button type="submit" class="mt-8 w-full px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg shadow-sm transition-colors focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 text-sm">
                    Save General Info
                </button>
            </form>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 sm:p-8 flex flex-col h-full">
            <div class="mb-6">
                <h2 class="text-xl font-bold text-slate-800">Security</h2>
                <p class="text-sm text-slate-500 mt-1">Update your password to secure your account.</p>
            </div>
            
            <form action="{{ route('password.update') }}" method="POST" class="flex-1 flex flex-col">
                @csrf
                @method('PUT')
                <div class="space-y-5 flex-1">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Current Password</label>
                        <input type="password" name="current_password" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-slate-500 focus:border-slate-500 transition-colors">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">New Password</label>
                        <input type="password" name="password" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-slate-500 focus:border-slate-500 transition-colors">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Confirm Password</label>
                        <input type="password" name="password_confirmation" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-slate-500 focus:border-slate-500 transition-colors">
                    </div>
                </div>
                <button type="submit" class="mt-8 w-full px-5 py-2.5 bg-slate-800 hover:bg-slate-900 text-white font-semibold rounded-lg shadow-sm transition-colors focus:ring-2 focus:ring-slate-800 focus:ring-offset-2 text-sm">
                    Update Password
                </button>
            </form>
        </div>

    </div>
</div>















@endsection