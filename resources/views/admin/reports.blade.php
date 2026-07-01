@extends('layouts.admin')

@section('title', 'Reports - Admin Dashboard')


@section('content')
    
<div class="p-4 md:p-6 lg:p-8 w-full max-w-7xl mx-auto">
    @include('partials.notif_logout', ['page_name' => 'Reports Management'])

    @if(session('success'))
    <div class="bg-emerald-50 text-emerald-800 p-4 rounded-xl border-l-4 border-emerald-500 shadow-sm flex items-center gap-3">
        <svg class="w-5 h-5 text-emerald-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
        </svg>
        <span class="text-sm font-medium">{{ session('success') }}</span>
    </div>
    @endif

    <div class="bg-white rounded-xl shadow-sm border border-slate-100 overflow-hidden">
        
        <div class="p-6 border-b border-slate-100 bg-slate-50/50 space-y-4">
            <div class="flex items-center justify-between gap-4">
                <h2 class="text-lg font-bold text-slate-800">Recent Reports Grid</h2>
                <button onclick="exportTableToExcel('Reports_Export')" class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-lg shadow-sm transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                    Export to Excel
                </button>

            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
                
                <div class="flex flex-col gap-1.5">
                    <label class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Disaster Type</label>
                    <select class="w-full bg-white border border-slate-200 text-slate-700 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-slate-200" id="adminDisasterFilter" onchange="filterAdminReports()">
                        <option value="">All Types</option>
                        @foreach($disasterTypes as $type)
                            <option value="{{ $type->name }}">{{ $type->icon }} {{ $type->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="flex flex-col gap-1.5">
                    <label class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Verification Status</label>
                    <select class="w-full bg-white border border-slate-200 text-slate-700 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-slate-200" id="statusFilter" onchange="filterAdminReports()">
                        <option value="">All Statuses</option>
                        <option value="pending">Pending</option>
                        <option value="verified">Verified</option>
                        <option value="unverified">Unverified</option>
                    </select>
                </div>

                <div class="flex flex-col gap-1.5">
                    <label class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Action Progress</label>
                    <select class="w-full bg-white border border-slate-200 text-slate-700 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-slate-200" id="actionStatusFilter" onchange="filterAdminReports()">
                        <option value="">All Progress</option>
                        <option value="solved">Solved</option>
                        <option value="in_progress">In Progress</option>
                    </select>
                </div>

                <div class="flex flex-col gap-1.5">
                    <label class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Barangay Zone</label>
                    <select class="w-full bg-white border border-slate-200 text-slate-700 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-slate-200" id="barangayFilter" onchange="filterAdminReports()">
                        <option value="">All Barangays</option>
                        @foreach(\App\Models\Barangay::orderBy('name')->get() as $b)
                            <option value="{{ $b->id }}">{{ $b->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="flex flex-col gap-1.5">
                    <label class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Barangay Validation</label>
                    <select class="w-full bg-white border border-slate-200 text-slate-700 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-slate-200" id="barangayActionFilter" onchange="filterAdminReports()">
                        <option value="">All Actions</option>
                        <option value="approved">Approved</option>
                        <option value="disapproved">Disapproved</option>
                        <option value="none">No Action</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="w-full overflow-x-auto">
            <table class="w-full text-left border-collapse text-sm text-slate-600">
                <thead>
                    <tr class="bg-slate-50/70 border-b border-slate-100 text-xs font-bold uppercase tracking-wider text-slate-500 whitespace-nowrap">
                        <th class="px-6 py-4">Type of Disaster</th>
                        <th class="px-6 py-4 min-w-[240px]">Description</th>
                        <th class="px-6 py-4">Date</th>
                        <th class="px-6 py-4">Time</th>
                        <th class="px-6 py-4">User</th>
                        <th class="px-6 py-4 min-w-[180px]">Location</th>
                        <th class="px-6 py-4">Prediction</th>
                        <th class="px-6 py-4">Confidence</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4">Action Status</th>
                        <th class="px-6 py-4">Barangay</th>
                        <th class="px-6 py-4">Barangay Action</th>
                        <th class="hidden">Image</th>
                        <th class="hidden">Video</th>
                        <th class="hidden">Action</th>
                    </tr>
                </thead>
                <tbody id="reportsTableBody" class="divide-y divide-slate-100">
                    @if($reports->count() > 0)
                        @foreach($reports as $report)
                        <tr class="report-row hover:bg-slate-50/80 transition-colors cursor-pointer" 
                            data-id="{{ $report->id }}"
                            data-type="{{ $report->disaster_type }}" 
                            data-status="{{ $report->status }}" 
                            data-action-status="{{ $report->solved ? 'solved' : ($report->responses()->where('action_type', 'in_progress')->exists() ? 'in_progress' : '') }}"
                            data-barangay-id="{{ $report->barangay_id ?? '' }}"
                            data-barangay-action="{{ $report->barangay_action_status ?? 'none' }}"
                            data-description="{{ $report->description }}"
                            data-user="{{ $report->user->name }}"
                            data-location="{{ $report->location ?: number_format($report->latitude, 6) . ', ' . number_format($report->longitude, 6) }}"
                            data-date="{{ $report->created_at->format('M d, Y') }}"
                            data-time="{{ $report->created_at->format('h:i A') }}"
                            data-image="{{ $report->image ? Storage::url($report->image) : '' }}"
                            data-video="{{ $report->video ? Storage::url($report->video) : '' }}">
                            
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="bg-blue-50 text-blue-700 px-2.5 py-1 rounded-full text-xs font-semibold border border-blue-100">
                                    {{ ucfirst($report->disaster_type) }}
                                </span>
                            </td>
                            
                            <td class="px-6 py-4 max-w-xs truncate font-medium text-slate-700">
                                {{ Str::limit($report->description, 100) }}
                            </td>
                            
                            <td class="px-6 py-4 whitespace-nowrap text-slate-500">{{ $report->created_at->format('M d, Y') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-slate-500">{{ $report->created_at->format('h:i A') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap font-medium text-slate-700">{{ $report->user->name }}</td>
                            
                            <td class="px-6 py-4 text-slate-500">
                                @if($report->location)
                                    {{ Str::limit($report->location, 50) }}
                                @else
                                    <span class="font-mono text-xs">{{ number_format($report->latitude, 6) }}, {{ number_format($report->longitude, 6) }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div id="prediction-container-{{ $report->id }}" class="flex flex-col gap-1.5 items-start">
                                    <span class="text-xs font-semibold">
                                        {{ $report->prediction ? ($report->prediction->prediction_label == 1 ? '✅ Valid' : '❌ Invalid') : '⏳ Pending...' }}
                                    </span>
                                    <button 
                                        onclick="runAiPrediction(event, {{ $report->id }})" 
                                        id="ai-btn-{{ $report->id }}"
                                        class="bg-indigo-600 hover:bg-indigo-700 text-white text-[11px] font-medium px-2 py-1 rounded transition-colors shadow-sm"
                                    >
                                        {{ $report->prediction ? 'Re-run AI' : 'Run AI' }}
                                    </button>
                                </div>
                            </td>

                            <td id="confidence-cell-{{ $report->id }}" class="px-6 py-4 whitespace-nowrap font-semibold text-slate-700">
                                {{ $report->prediction ? number_format($report->prediction->confidence_score * 100, 1) . '%' : '-' }}
                            </td>
                            
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($report->status === 'pending')
                                    <span class="bg-amber-50 text-amber-700 px-3 py-1.5 rounded-lg text-xs font-bold border border-amber-100 inline-block">
                                        Pending
                                    </span>
                                @elseif($report->status === 'verified')
                                    <span class="bg-emerald-50 text-emerald-700 px-3 py-1.5 rounded-lg text-xs font-bold border border-emerald-100 inline-block">
                                        Verified
                                    </span>
                                @else
                                    <span class="bg-red-50 text-red-700 px-3 py-1.5 rounded-lg text-xs font-bold border border-red-100 inline-block">
                                        Unverified
                                    </span>
                                @endif
                            </td>
                            
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($report->solved)
                                    <span class="bg-emerald-50 text-emerald-700 px-3 py-1.5 rounded-lg text-xs font-bold border border-emerald-100 inline-block">
                                        Solved
                                    </span>
                                @elseif($report->responses()->where('action_type', 'in_progress')->exists())
                                    <span class="bg-blue-50 text-blue-700 px-3 py-1.5 rounded-lg text-xs font-bold border border-blue-100 inline-block">
                                        In Progress
                                    </span>
                                @else
                                    <span class="text-slate-400 font-medium">-</span>
                                @endif
                            </td>
                            
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($report->barangay)
                                    <span class="bg-purple-50 text-purple-700 px-2.5 py-1 rounded-lg text-xs font-semibold border border-purple-100 inline-block">
                                        {{ $report->barangay->name }}
                                    </span>
                                @else
                                    <span class="text-slate-400 font-medium">-</span>
                                @endif
                            </td>
                            
                            <td class="px-6 py-4 whitespace-nowrap" id="barangay-action-{{ $report->id }}">
                                @if($report->barangay_action_status === 'approved')
                                    <span class="bg-emerald-50 text-emerald-700 px-2.5 py-1 rounded-lg text-xs font-semibold border border-emerald-100 inline-block">
                                        Approved
                                    </span>
                                @elseif($report->barangay_action_status === 'disapproved')
                                    <span class="bg-red-50 text-red-700 px-2.5 py-1 rounded-lg text-xs font-semibold border border-red-100 inline-block">
                                        Disapproved
                                    </span>
                                @else
                                    <span class="text-slate-400 font-medium">-</span>
                                @endif
                            </td>
                            
                            <td class="hidden">
                                @if($report->image)
                                    <a href="javascript:void(0)" class="view-link view-media" data-url="{{ Storage::url($report->image) }}" data-type="image" onclick="event.stopPropagation()">View</a>
                                @else
                                    <span>N/A</span>
                                @endif
                            </td>
                            <td class="hidden">
                                @if($report->video)
                                    <a href="javascript:void(0)" class="view-link view-media" data-url="{{ Storage::url($report->video) }}" data-type="video" onclick="event.stopPropagation()">View</a>
                                @else
                                    <span>N/A</span>
                                @endif
                            </td>
                            <td class="hidden">
                                <button class="respond-btn" data-id="{{ $report->id }}" data-type="{{ $report->disaster_type }}" data-description="{{ $report->description }}" data-location="{{ $report->location }}" data-status="{{ $report->status }}" onclick="event.stopPropagation()">
                                    📝 Respond
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="12" class="px-6 py-12">
                                <div class="flex flex-col items-center justify-center gap-3 text-slate-400">
                                    <svg class="w-12 h-12 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    <p class="text-sm font-medium text-slate-500">No reports submitted yet</p>
                                </div>
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>


<!-- Respond Modal -->
<div id="respondModal" class="hidden fixed inset-0 z-[500] flex items-center justify-center bg-slate-900/50 backdrop-blur-sm p-4 sm:p-6 transition-opacity" onclick="closeRespondModal()">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-xl max-h-[90vh] flex flex-col relative overflow-hidden" onclick="event.stopPropagation()">
        
        <button onclick="closeRespondModal()" class="absolute top-4 right-4 sm:top-5 sm:right-5 text-slate-400 hover:text-rose-500 hover:bg-rose-50 rounded-full p-2 transition-colors z-10 focus:outline-none">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
        
        <div class="p-6 sm:p-8 overflow-y-auto custom-scrollbar w-full">
            <h2 class="text-xl font-bold text-slate-800 mb-6">Respond to Report</h2>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mb-6">
                <div>
                    <div class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Type of Disaster</div>
                    <div id="d-type" class="text-sm font-semibold text-slate-800"></div>
                </div>
                <div>
                    <div class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Date & Time</div>
                    <div id="d-date" class="text-sm font-medium text-slate-700"></div>
                </div>
            </div>
            
            <div class="mb-6">
                <div class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Description</div>
                <div id="d-desc" class="text-sm text-slate-700 bg-slate-50 p-4 rounded-lg border border-slate-100 leading-relaxed"></div>
            </div>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mb-6">
                <div>
                    <div class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Reporter</div>
                    <div id="d-reporter" class="text-sm font-semibold text-slate-800"></div>
                </div>
                <div>
                    <div class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Location</div>
                    <div id="d-location" class="text-sm font-medium text-slate-700"></div>
                </div>
            </div>
            
            <div id="d-image-wrap" class="mb-6 hidden">
                <div class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Attached Image</div>
                <img id="d-image" src="" alt="Report image" class="w-full max-h-80 object-contain rounded-xl bg-slate-100 border border-slate-200" 
                    onerror="this.classList.add('hidden'); document.getElementById('d-image-fallback').classList.remove('hidden');"/>
                <div id="d-image-fallback" class="hidden p-8 text-center bg-slate-50 rounded-xl border border-slate-100">
                    <p class="text-slate-400 text-sm">Image not available</p>
                </div>
            </div>

            <div id="d-video-wrap" class="mb-6 hidden">
                <div class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Attached Video</div>
                <video id="d-video" controls class="w-full max-h-80 rounded-xl bg-black shadow-md" 
                    onerror="this.classList.add('hidden'); document.getElementById('d-video-fallback').classList.remove('hidden');">
                    Your browser does not support the video tag.
                </video>
                <div id="d-video-fallback" class="hidden p-8 text-center bg-slate-50 rounded-xl border border-slate-100">
                    <p class="text-slate-400 text-sm">Video not available</p>
                </div>
            </div>

            <form id="respondForm" method="POST" action="" class="flex flex-col gap-5">
                @csrf
                <div>
                    <label class="block mb-1.5 text-sm font-semibold text-slate-700">Status <span class="text-red-500">*</span></label>
                    <select id="statusSelect" name="status" required class="w-full bg-white border border-slate-300 text-slate-700 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-shadow">
                        <option value="">Select status</option>
                        <option value="pending">Pending</option>
                        <option value="verified">Verified</option>
                        <option value="unverified">Unverified</option>
                    </select>
                </div>

                <div>
                    <label class="block mb-1.5 text-sm font-semibold text-slate-700">Action Type</label>
                    <select id="actionTypeSelect" name="action_type" class="w-full bg-white border border-slate-300 text-slate-700 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-shadow">
                        <option value="">Select action type</option>
                        <option value="solved">Mark as Solved</option>
                        <option value="in_progress">In Progress</option>
                    </select>
                </div>

                <div>
                    <label class="block mb-1.5 text-sm font-semibold text-slate-700">Response Message <span class="text-red-500">*</span></label>
                    <textarea name="response_message" required rows="4" placeholder="Enter your response to this report..." class="w-full bg-white border border-slate-300 text-slate-700 rounded-lg px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-shadow resize-y"></textarea>
                </div>

                <div>
                    <label class="block mb-1.5 text-sm font-semibold text-slate-700">Notes (optional)</label>
                    <textarea name="notes" rows="3" placeholder="Additional notes..." class="w-full bg-white border border-slate-300 text-slate-700 rounded-lg px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-shadow resize-y"></textarea>
                </div>

                <button type="submit" class="mt-2 w-full bg-emerald-500 hover:bg-emerald-600 text-white font-semibold py-3 px-4 rounded-lg transition-colors shadow-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2">
                    Submit Response
                </button>
            </form>
        </div>
        
    </div>
</div>
@endsection

@push('scripts')

<!-- // ── Export Scripts -->
<script src="https://cdn.sheetjs.com/xlsx-latest/package/dist/xlsx.full.min.js"></script>
<script>
    function exportTableToExcel(filename = 'Export') {
        // 1. Get the table element
        let table = document.querySelector("table");
        
        // 2. Create a clone of the table so we don't modify the actual UI
        let cloneTable = table.cloneNode(true);
        
        // 3. Remove rows that are hidden by your JS filters
        let rows = cloneTable.querySelectorAll('tr');
        rows.forEach(row => {
            if (row.style.display === 'none' || row.classList.contains('hidden')) {
                row.remove();
            }
        });

        // 4. Remove hidden columns/cells (This removes your hidden image, video, and action cells)
        let cells = cloneTable.querySelectorAll('th, td');
        cells.forEach(cell => {
            if (cell.style.display === 'none' || cell.classList.contains('hidden')) {
                cell.remove();
            }
        });

        // 5. Convert table to Excel workbook
        // Adding { raw: true } forces Excel to treat everything as plain text, fixing the =#NUM! error
        let wb = XLSX.utils.table_to_book(cloneTable, { 
            sheet: "Reports",
            raw: true 
        });
        
        // 6. Download the file
        XLSX.writeFile(wb, filename + ".xlsx");
    }
</script>





<script>

    // ── Modal Handling ───────────────────────────────────────────────────
    function openRespondModal(row) {
        const data = row.dataset;
        
        // 1. Populate the Quick Summary Text
        // FIXED: Your TR says data-type, not data-disaster
        document.getElementById('d-type').textContent     = data.type ? data.type.toUpperCase() : '—';
        
        // FIXED: You have separate data-date and data-time in the TR now, so combine them
        document.getElementById('d-date').textContent     = (data.date || '') + ' ' + (data.time || '');
        
        // FIXED: Your TR says data-user, not data-reporter
        document.getElementById('d-reporter').textContent = data.user || '—';
        
        document.getElementById('d-desc').textContent     = data.description || '—';
        document.getElementById('d-location').textContent = data.location || '—';

        // 2. Pre-select dropdowns based on the row's current state
        const statusSelect = document.getElementById('statusSelect');
        if (statusSelect) {
            statusSelect.value = data.status || '';
        }

        const actionTypeSelect = document.getElementById('actionTypeSelect');
        if (actionTypeSelect) {
            actionTypeSelect.value = data.actionStatus || '';
        }

        // 3. Dynamically set the form's action URL to route to the correct report ID
        const form = document.getElementById('respondForm');
        if (form) {
            form.action = `/admin/reports/${data.id}/respond`; 
        }

        // 4. Image Handling
        const imgWrap     = document.getElementById('d-image-wrap');
        const img         = document.getElementById('d-image');
        const imgFallback = document.getElementById('d-image-fallback');

        if (data.image && data.image.trim()) {
            img.src = data.image;
            img.classList.remove('hidden');
            if (imgFallback) imgFallback.classList.add('hidden');
            imgWrap.classList.remove('hidden');
        } else {
            img.removeAttribute('src');
            imgWrap.classList.add('hidden');
        }

        // 5. Video Handling
        const videoWrap     = document.getElementById('d-video-wrap');
        const videoEl       = document.getElementById('d-video');
        const videoFallback = document.getElementById('d-video-fallback');
        
        if (data.video && data.video.trim()) {
            videoEl.src = data.video;
            videoEl.classList.remove('hidden');
            if (videoFallback) videoFallback.classList.add('hidden');
            videoWrap.classList.remove('hidden');
        } else {
            videoEl.removeAttribute('src');
            videoWrap.classList.add('hidden');
        }

        // 6. Toggle Tailwind classes to reveal the modal
        const modal = document.getElementById('respondModal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    // 5. Close function
    window.closeRespondModal = function() {
        const modal = document.getElementById('respondModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        
        // Clear the response text areas automatically so they don't leak into the next click
        const form = document.getElementById('respondForm');
        if (form) form.reset();
    };



    // Filter reports by disaster type, status, and action status
    function filterAdminReports() 
    {
        const disasterFilter      = document.getElementById('adminDisasterFilter').value;
        const statusFilter        = document.getElementById('statusFilter').value;
        const actionStatusFilter  = document.getElementById('actionStatusFilter').value;
        const barangayFilter      = document.getElementById('barangayFilter').value;
        const barangayActionFilter = document.getElementById('barangayActionFilter').value;
        const rows = document.querySelectorAll('.report-row');
        
        rows.forEach(row => {
            const disasterType   = row.getAttribute('data-disaster-type');
            const status         = row.getAttribute('data-status');
            const actionStatus   = row.getAttribute('data-action-status');
            const barangayId     = row.getAttribute('data-barangay-id');
            const barangayAction = row.getAttribute('data-barangay-action') || 'none';
            
            const matchesDisaster       = disasterFilter      === '' || disasterType   === disasterFilter;
            const matchesStatus         = statusFilter         === '' || status         === statusFilter;
            const matchesAction         = actionStatusFilter   === '' || actionStatus   === actionStatusFilter;
            const matchesBarangay       = barangayFilter       === '' || barangayId     === barangayFilter;
            const matchesBarangayAction = barangayActionFilter === '' || barangayAction === barangayActionFilter;
            
            if (matchesDisaster && matchesStatus && matchesAction && matchesBarangay && matchesBarangayAction) {
                row.classList.remove('hidden');
            } else {
                row.classList.add('hidden');
            }
        });
    }




    document.addEventListener('DOMContentLoaded', () => {

        const tableBody = document.getElementById('reportsTableBody');
        
        if (tableBody) {
            tableBody.addEventListener('click', (e) => {
                // Safety check: Ignore clicks on the "Run AI" button or any links inside the row
                if (e.target.closest('button') || e.target.closest('a')) {
                    return; 
                }

                // Find the clicked row
                const row = e.target.closest('.report-row');
                if (row) {
                    openRespondModal(row);
                }
            });
        }


        // Check if Echo is available
        if (window.Echo) {
            
            // Listen to the 'reports' channel we defined in the Event
            window.Echo.channel('reports')
                .listen('.action.updated', (event) => {
                    
                    // 1. Find the specific table cell using the ID we just added
                    const actionCell = document.getElementById(`barangay-action-${event.reportId}`);
                    
                    if (actionCell) {
                        // 2. Change the text and HTML styling based on the new status
                        if (event.status === 'approved') {
                            actionCell.innerHTML = `<span class="bg-emerald-50 text-emerald-700 px-2.5 py-1 rounded-lg text-xs font-semibold border border-emerald-100 inline-block">Approved</span>`;
                        } else if (event.status === 'disapproved') {
                            actionCell.innerHTML = `<span class="bg-red-50 text-red-700 px-2.5 py-1 rounded-lg text-xs font-semibold border border-red-100 inline-block">Disapproved</span>`;
                        } else {
                            actionCell.innerHTML = `<span class="text-slate-400 font-medium">-</span>`;
                        }

                        const row = actionCell.closest('tr');
                        if (row) {
                            row.dataset.barangayAction = event.status || 'none';
                            
                            row.classList.add('bg-yellow-50', 'transition-colors', 'duration-500');
                            
                            setTimeout(() => {
                                row.classList.remove('bg-yellow-50');
                            }, 2000); 
                        }
                    }
                })
                .listen('.prediction.completed', (event) => {
                    console.log('🤖 AI Prediction Finished:', event);
                    
                    // 1. Target the container using the ID
                    const container = document.getElementById(`prediction-container-${event.report_id}`);
                    
                    if (container) {
                        console.log('Found container, updating...'); // Debugging step
                        const labelText = event.is_valid ? '✅ Valid' : '❌ Invalid';
                        
                        container.innerHTML = `
                            <span class="text-xs font-semibold">${labelText}</span>
                            <button 
                                onclick="runAiPrediction(event, ${event.report_id})" 
                                id="ai-btn-${event.report_id}"
                                class="bg-indigo-600 hover:bg-indigo-700 text-white text-[11px] font-medium px-2 py-1 rounded transition-colors shadow-sm"
                            >
                                Re-run AI
                            </button>
                        `;
                    } else {
                        console.warn(`Could not find element: prediction-container-${event.report_id}`);
                    }

                    // 2. Update Confidence
                    const scoreCell = document.getElementById(`confidence-cell-${event.report_id}`);
                    if (scoreCell) {
                        scoreCell.innerText = event.confidence_formatted;
                        scoreCell.classList.add('bg-emerald-50', 'transition-colors', 'duration-500');
                        setTimeout(() => scoreCell.classList.remove('bg-emerald-50'), 2000);
                    }
                });


            
            window.addReportToTable = function(report) {
                const tbody = document.getElementById('reportsTableBody');
                if (!tbody) return;

                // 1. Remove the "No reports submitted yet" placeholder if it exists
                const emptyPlaceholder = tbody.querySelector('td[colspan="12"]');
                if (emptyPlaceholder) {
                    emptyPlaceholder.closest('tr').remove();
                }

                // 2. Helper functions for formatting (replicating Laravel's Str::limit and number_format)
                const truncate = (str, length) => str && str.length > length ? str.substring(0, length) + '...' : (str || '');
                const formatCoordinate = (coord) => coord ? parseFloat(coord).toFixed(6) : '0.000000';
                const capitalize = (str) => str ? str.charAt(0).toUpperCase() + str.slice(1) : '';

                // 3. Set default values to prevent undefined errors
                const disasterType = report.disaster_type || 'unknown';
                const description = report.description || '';
                const userName = report.user_name || (report.user ? report.user.name : 'Unknown User');
                const locationStr = report.location ? truncate(report.location, 50) : 
                                `<span class="font-mono text-xs">${formatCoordinate(report.latitude)}, ${formatCoordinate(report.longitude)}</span>`;
                
                // Action status logic (replicating your Blade logic)
                let actionStatus = '';
                if (report.solved) actionStatus = 'solved';
                else if (report.has_in_progress_responses) actionStatus = 'in_progress';

                // 4. Create the new row
                const tr = document.createElement('tr');
                
                // Add standard classes plus a highlight color that will fade out
                tr.className = 'report-row bg-emerald-50 hover:bg-slate-50/80 transition-colors duration-1000 cursor-pointer'; 
                
                // Replicate all your Blade data-* attributes
                tr.dataset.id = report.id;
                tr.dataset.type = disasterType;
                tr.dataset.status = report.status || 'pending';
                tr.dataset.actionStatus = actionStatus;
                tr.dataset.barangayId = report.barangay_id || '';
                tr.dataset.barangayAction = report.barangay_action_status || 'none';
                tr.dataset.description = description;
                tr.dataset.user = userName;
                tr.dataset.location = report.location || `${formatCoordinate(report.latitude)}, ${formatCoordinate(report.longitude)}`;
                tr.dataset.date = report.formatted_date || new Date().toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
                tr.dataset.time = report.formatted_time || new Date().toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' });
                tr.dataset.image = report.image_url || '';
                tr.dataset.video = report.video_url || '';

                // 5. Construct the inner HTML
                tr.innerHTML = `
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="bg-blue-50 text-blue-700 px-2.5 py-1 rounded-full text-xs font-semibold border border-blue-100">
                            ${capitalize(disasterType)}
                        </span>
                    </td>
                    
                    <td class="px-6 py-4 max-w-xs truncate font-medium text-slate-700">
                        ${truncate(description, 100)}
                    </td>
                    
                    <td class="px-6 py-4 whitespace-nowrap text-slate-500">${tr.dataset.date}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-slate-500">${tr.dataset.time}</td>
                    <td class="px-6 py-4 whitespace-nowrap font-medium text-slate-700">${userName}</td>
                    
                    <td class="px-6 py-4 text-slate-500">
                        ${locationStr}
                    </td>
                    
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div id="prediction-container-${report.id}" class="flex flex-col gap-1.5 items-start">
                            <span class="text-xs font-semibold text-slate-500">⏳ Pending...</span>
                            <button onclick="runAiPrediction(event, ${report.id})" id="ai-btn-${report.id}" class="bg-indigo-600 hover:bg-indigo-700 text-white text-[11px] font-medium px-2 py-1 rounded transition-colors shadow-sm">
                                Run AI
                            </button>
                        </div>
                    </td>
                    
                    <td id="confidence-cell-${report.id}" class="px-6 py-4 whitespace-nowrap font-semibold text-slate-700">-</td>

                    
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="bg-amber-50 text-amber-700 px-3 py-1.5 rounded-lg text-xs font-bold border border-amber-100 inline-block">
                            Pending
                        </span>
                    </td>
                    
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="text-slate-400 font-medium">-</span>
                    </td>
                    
                    <td class="px-6 py-4 whitespace-nowrap">
                        ${report.barangay_name ? 
                            `<span class="bg-purple-50 text-purple-700 px-2.5 py-1 rounded-lg text-xs font-semibold border border-purple-100 inline-block">${report.barangay_name}</span>` : 
                            `<span class="text-slate-400 font-medium">-</span>`
                        }
                    </td>
                    
                    <td class="px-6 py-4 whitespace-nowrap" id="barangay-action-${report.id}">
                        <span class="text-slate-400 font-medium">-</span>
                    </td>
                    
                    <td class="hidden">
                        ${report.image_url ? `<a href="javascript:void(0)" class="view-link view-media" data-url="${report.image_url}" data-type="image" onclick="event.stopPropagation()">View</a>` : `<span>N/A</span>`}
                    </td>
                    <td class="hidden">
                        ${report.video_url ? `<a href="javascript:void(0)" class="view-link view-media" data-url="${report.video_url}" data-type="video" onclick="event.stopPropagation()">View</a>` : `<span>N/A</span>`}
                    </td>
                    <td class="hidden">
                        <button class="respond-btn" data-id="${report.id}" data-type="${disasterType}" data-description="${description}" data-location="${report.location || ''}" data-status="pending" onclick="event.stopPropagation()">
                            📝 Respond
                        </button>
                    </td>
                `;

                // 6. Insert the new row at the top of the table
                tbody.insertBefore(tr, tbody.firstChild);

                // 7. Remove the green highlight flash after 2 seconds
                setTimeout(() => {
                    tr.classList.remove('bg-emerald-50');
                }, 2000);
            };

            window.runAiPrediction = async function(event, reportId) {
                // 1. Prevent row click event if the button is inside a clickable row
                event.stopPropagation();

                const btn = document.getElementById(`ai-btn-${reportId}`);
                const originalText = btn.innerText;

                // 2. Visual feedback
                btn.disabled = true;
                btn.innerText = 'Processing...';
                btn.classList.add('opacity-50', 'cursor-not-allowed');

                try {
                    // 3. Trigger the Laravel route
                    const response = await fetch(`/reports/${reportId}/run-ai`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Content-Type': 'application/json'
                        }
                    });

                    if (response.ok) {
                        btn.innerText = 'Queued!';
                        console.log(`✅ AI process started for report ${reportId}`);
                        
                        // Optionally: Change the UI to show it's pending
                        // e.g., update the text in the table cell
                    } else {
                        throw new Error('Failed to start AI process');
                    }
                } catch (error) {
                    console.error('Error:', error);
                    btn.innerText = 'Error';
                    setTimeout(() => {
                        btn.innerText = originalText;
                        btn.disabled = false;
                        btn.classList.remove('opacity-50', 'cursor-not-allowed');
                    }, 3000);
                }
            };
        }
    });
</script>



@endpush