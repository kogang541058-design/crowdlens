@extends('layouts.admin')

@section('title', 'Barangay - Admin Dashboard')

@section('content')

<!-- <div class="p-4 md:p-8 space-y-8">
    
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between bg-white p-6 rounded-xl shadow-sm border border-slate-100">
        <h1 class="text-2xl font-bold text-slate-800">Barangay Management</h1>
        
        <div class="flex items-center gap-4 self-end sm:self-auto">
            <span class="text-sm font-medium text-slate-600">Admin: {{ auth('admin')->user()->name }}</span>
            <form action="{{ route('admin.logout') }}" method="POST" class="inline">
                @csrf
                <button type="submit" class="bg-red-500 hover:bg-red-600 text-white text-sm font-medium px-4 py-2 rounded-lg transition-colors shadow-sm shadow-red-100">
                    Logout
                </button>
            </form>
        </div>
    </div> -->


    
<div class="p-4 md:p-6 lg:p-8 w-full max-w-7xl mx-auto">
    
    @include('partials.notif_logout', ['page_name' => 'Barangay Management'])

    <div class="bg-white rounded-xl shadow-sm border border-slate-100 overflow-hidden">
        
        <div class="p-6 border-b border-slate-100 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-white">
            <h2 class="text-lg font-bold text-slate-800">Barangays in Davao City</h2>
            <button onclick="openAddModal()" class="w-full sm:w-auto bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2.5 rounded-lg flex items-center justify-center gap-2 transition-colors shadow-sm shadow-blue-200">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="w-5 h-5">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Add Barangay
            </button>
        </div>

        @if(count($barangays) > 0)
        <div class="overflow-x-auto w-full">
            <table class="w-full text-left border-collapse min-w-[600px]">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-100 text-slate-500 text-xs uppercase tracking-wider">
                        <th class="p-4 font-semibold">Barangay Name</th>
                        <th class="p-4 font-semibold">Username</th>
                        <th class="p-4 font-semibold text-center w-32">Actions</th>
                    </tr>
                </thead>
                <tbody class="text-sm text-slate-700 divide-y divide-slate-100">
                    @foreach($barangays as $barangay)
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="p-4 font-medium text-slate-800">{{ $barangay->name }}</td>
                        <td class="p-4 text-slate-500">{{ $barangay->username }}</td>
                        <td class="p-4 text-center">
                            
                            <div class="relative inline-block text-left">
                                <button onclick="toggleManageMenu({{ $barangay->id }})" class="text-slate-500 hover:text-slate-800 bg-white border border-slate-200 hover:bg-slate-50 px-3 py-1.5 rounded-md inline-flex items-center gap-1.5 transition-colors text-xs font-medium">
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="w-4 h-4">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"/>
                                    </svg>
                                    Manage
                                </button>
                                
                                <div id="manageMenu{{ $barangay->id }}" class="hidden absolute right-0 mt-2 w-32 bg-white rounded-lg shadow-lg border border-slate-100 z-20 overflow-hidden">
                                    <button class="w-full px-4 py-2 text-sm text-slate-700 hover:bg-slate-50 flex items-center gap-2 transition-colors" onclick="editBarangay({{ $barangay->id }}, '{{ addslashes($barangay->name) }}', '{{ addslashes($barangay->username) }}'); toggleManageMenu({{ $barangay->id }})">
                                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="w-4 h-4 text-blue-500">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                        Edit
                                    </button>
                                    <button class="w-full px-4 py-2 text-sm text-red-600 hover:bg-red-50 flex items-center gap-2 transition-colors" onclick="deleteBarangay({{ $barangay->id }}, '{{ addslashes($barangay->name) }}'); toggleManageMenu({{ $barangay->id }})">
                                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="w-4 h-4">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                        Delete
                                    </button>
                                </div>
                            </div>
                            
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="flex flex-col items-center justify-center p-12 text-slate-400 bg-slate-50/50">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="w-12 h-12 mb-3 text-slate-300">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
            </svg>
            <p class="text-sm font-medium">No barangays added yet</p>
        </div>
        @endif
    </div>
</div>

<div id="barangayModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 backdrop-blur-sm hidden transition-opacity">
    
    <div class="bg-white w-full max-w-md mx-4 rounded-xl shadow-xl border border-slate-100 overflow-hidden transform transition-all">
        <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center bg-slate-50">
            <h3 id="modalTitle" class="text-lg font-bold text-slate-800">Add Barangay</h3>
            <button onclick="closeModal()" class="text-slate-400 hover:text-slate-600 transition-colors focus:outline-none">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        
        <div class="p-6">
            <form id="barangayForm" class="space-y-4">
                <input type="hidden" id="barangayId" value="">
                
                <div>
                    <label for="barangayName" class="block text-sm font-medium text-slate-700 mb-1">Barangay Name <span class="text-red-500">*</span></label>
                    <input type="text" id="barangayName" name="name" required class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all text-sm">
                    <div class="text-red-500 text-xs mt-1 empty:hidden" id="nameError"></div>
                </div>

                <div>
                    <label for="username" class="block text-sm font-medium text-slate-700 mb-1">Username <span class="text-red-500">*</span></label>
                    <input type="text" id="username" name="username" required class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all text-sm">
                    <div class="text-red-500 text-xs mt-1 empty:hidden" id="usernameError"></div>
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-slate-700 mb-1"><span id="passwordLabel">Password <span class="text-red-500">*</span></span></label>
                    <input type="password" id="password" name="password" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all text-sm">
                    <div class="text-red-500 text-xs mt-1 empty:hidden" id="passwordError"></div>
                </div>

                <div class="pt-2">
                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-2.5 rounded-lg transition-colors shadow-sm shadow-blue-200">
                        Save Barangay
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function toggleManageMenu(id) {
        const menu = document.getElementById('manageMenu' + id);
        // Hide all other dropdowns
        document.querySelectorAll('[id^="manageMenu"]').forEach(m => {
            if (m !== menu) m.classList.add('hidden');
        });
        // Toggle the clicked one
        if (menu) menu.classList.toggle('hidden');
    }

    // Close menus when clicking outside
    document.addEventListener('click', function(event) {
        // Check if click is outside both the button and the dropdown menu
        if (!event.target.closest('button[onclick^="toggleManageMenu"]') && !event.target.closest('[id^="manageMenu"]')) {
            document.querySelectorAll('[id^="manageMenu"]').forEach(m => {
                m.classList.add('hidden');
            });
        }
    });

    function openAddModal() {
        document.getElementById('modalTitle').textContent = 'Add Barangay';
        document.getElementById('barangayId').value = '';
        document.getElementById('barangayForm').reset();
        // Use innerHTML to keep the Tailwind red asterisk styling
        document.getElementById('passwordLabel').innerHTML = 'Password <span class="text-red-500">*</span>';
        document.getElementById('password').required = true;
        clearErrors();
        // Remove hidden to show modal
        document.getElementById('barangayModal').classList.remove('hidden');
    }

    function editBarangay(id, name, username) {
        document.getElementById('modalTitle').textContent = 'Edit Barangay';
        document.getElementById('barangayId').value = id;
        document.getElementById('barangayName').value = name;
        document.getElementById('username').value = username;
        document.getElementById('password').value = '';
        // Add Tailwind styled hint for edit mode
        document.getElementById('passwordLabel').innerHTML = 'Password <span class="text-xs text-slate-400 font-normal ml-1">(leave blank to keep current)</span>';
        document.getElementById('password').required = false;
        clearErrors();
        // Remove hidden to show modal
        document.getElementById('barangayModal').classList.remove('hidden');
    }

    function closeModal() {
        // Add hidden to hide modal
        document.getElementById('barangayModal').classList.add('hidden');
    }

    function clearErrors() {
        document.querySelectorAll('.error-message').forEach(e => e.textContent = '');
    }

    document.getElementById('barangayForm').addEventListener('submit', function(e) {
        e.preventDefault();
        clearErrors();

        const barangayId = document.getElementById('barangayId').value;
        const formData = {
            name: document.getElementById('barangayName').value,
            username: document.getElementById('username').value,
        };

        // Only include password if it's filled
        const password = document.getElementById('password').value;
        if (password) {
            formData.password = password;
        }

        const url = barangayId 
            ? `/admin/barangay/${barangayId}` 
            : '/admin/barangay';
        const method = barangayId ? 'PUT' : 'POST';

        fetch(url, {
            method: method,
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify(formData)
        })
        .then(response => response.json())
        .then(data => {
            if (data.errors) {
                for (let field in data.errors) {
                    const errorElement = document.getElementById(field + 'Error');
                    if (errorElement) {
                        errorElement.textContent = data.errors[field][0];
                    }
                }
            } else if (data.success) {
                alert(data.message);
                window.location.reload();
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred. Please try again.');
        });
    });

    function deleteBarangay(id, name) {
        if (!confirm(`Are you sure you want to delete ${name}?`)) {
            return;
        }

        fetch(`/admin/barangay/${id}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                window.location.reload();
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred. Please try again.');
        });
    }

    // Close modal when clicking outside
    document.getElementById('barangayModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeModal();
        }
    });
</script>
@endpush