@extends('layouts.admin')

@section('title', "Barangay Reports - $barangay->name")


@section('content')
<div class="p-4 sm:p-6 lg:p-8 w-full min-h-screen bg-slate-50">

    @include('partials.notif_logout', ['page_name' => 'Reports', 'display_name' => $barangay->name])

    <div class="bg-white p-4 sm:p-5 rounded-xl shadow-sm border border-slate-200 mb-6 flex flex-col sm:flex-row flex-wrap items-start sm:items-center gap-3 sm:gap-4">
        <label class="text-sm font-semibold text-slate-700 shrink-0">Filter by:</label>
        
        <select id="typeFilter" onchange="filterReports()" class="w-full sm:w-auto flex-1 px-3 py-2 bg-slate-50 border border-slate-300 rounded-lg text-sm shadow-sm focus:ring-blue-500 focus:border-blue-500 focus:bg-white transition-colors">
            <option value="">All Types</option>
            @foreach($reports->pluck('disaster_type')->unique()->sort() as $type)
                <option value="{{ $type }}">{{ ucfirst($type) }}</option>
            @endforeach
        </select>
        
        <select id="statusFilter" onchange="filterReports()" class="w-full sm:w-auto flex-1 px-3 py-2 bg-slate-50 border border-slate-300 rounded-lg text-sm shadow-sm focus:ring-blue-500 focus:border-blue-500 focus:bg-white transition-colors">
            <option value="">All Statuses</option>
            <option value="pending">Pending</option>
            <option value="verified">Verified</option>
            <option value="unverified">Unverified</option>
        </select>
        
        <select id="actionFilter" onchange="filterReports()" class="w-full sm:w-auto flex-1 px-3 py-2 bg-slate-50 border border-slate-300 rounded-lg text-sm shadow-sm focus:ring-blue-500 focus:border-blue-500 focus:bg-white transition-colors">
            <option value="">All Action Status</option>
            <option value="solved">Solved</option>
            <option value="in_progress">In Progress</option>
            <option value="none">No Action</option>
        </select>
        
        <select id="barangayActionFilter" onchange="filterReports()" class="w-full sm:w-auto flex-1 px-3 py-2 bg-slate-50 border border-slate-300 rounded-lg text-sm shadow-sm focus:ring-blue-500 focus:border-blue-500 focus:bg-white transition-colors">
            <option value="">All Barangay Action</option>
            <option value="approved">Approved</option>
            <option value="disapproved">Disapproved</option>
            <option value="none">No Action</option>
        </select>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-200 flex justify-between items-center bg-slate-50/50">
            <h2 class="text-lg font-bold text-slate-800">All Reports</h2>
            <span id="reportCount" class="px-3 py-1 bg-white border border-slate-200 rounded-full text-xs font-semibold text-slate-600 shadow-sm">
                {{ $reports->count() }} reports
            </span>
            <button onclick="exportTableToExcel('Reports_Export')" class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-lg shadow-sm transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                Export to Excel
            </button>
        </div>

        @if($reports->count() > 0)
        <div class="overflow-x-auto w-full">
            <table class="w-full text-left border-collapse min-w-[1000px] table-fixed">
                <thead>
                    <tr class="bg-slate-50 text-slate-500 text-xs uppercase tracking-wider border-b border-slate-200">
                        <th class="px-6 py-4 font-semibold">Type</th>
                        <th class="px-6 py-4 font-semibold">Description</th>
                        <th class="px-6 py-4 font-semibold">Reporter</th>
                        <th class="px-6 py-4 font-semibold">Location</th>
                        <th class="px-6 py-4 font-semibold">Date</th>
                        <th class="px-6 py-4 font-semibold text-center">Status</th>
                        <th class="px-6 py-4 font-semibold text-center">Action Status</th>
                        <th class="px-6 py-4 font-semibold text-center">Barangay Action</th>
                    </tr>
                </thead>
                <tbody id="reportsTableBody" class="text-sm text-slate-700 divide-y divide-slate-100">
                    @foreach($reports as $report)
                    @php
                        $actionStatus = $report->solved ? 'solved' : ($report->responses->where('action_type', 'in_progress')->count() ? 'in_progress' : 'none');
                        $reportLocation = $report->location ?: (number_format($report->latitude,6).', '.number_format($report->longitude,6));
                        $barangayAction = $report->barangay_action_status ?? 'none';
                    @endphp
                    <tr class="report-row hover:bg-slate-50 transition-colors cursor-pointer group"
                        data-type="{{ $report->disaster_type }}"
                        data-status="{{ $report->status }}"
                        data-action="{{ $actionStatus }}"
                        data-barangay-action="{{ $barangayAction }}"
                        data-report-id="{{ $report->id }}"
                        data-disaster="{{ e(ucfirst($report->disaster_type)) }}"
                        data-description="{{ e($report->description) }}"
                        data-reporter="{{ e($report->user->name) }}"
                        data-location="{{ e($reportLocation) }}"
                        data-date="{{ $report->created_at->format('M d, Y h:i A') }}"
                        data-image="{{ $report->image ? Storage::url($report->image) : '' }}"
                        data-video="{{ $report->video ? Storage::url($report->video) : '' }}"
                        onclick="openModalWrapper(this)">
                        
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-slate-100 text-slate-700 border border-slate-200">
                                {{ ucfirst($report->disaster_type) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 max-w-[260px]">
                            <div class="truncate group-hover:text-blue-600 transition-colors">{{ Str::limit($report->description, 70) }}</div>
                        </td>
                        <td class="px-6 py-4 font-medium text-slate-900">{{ $report->user->name }}</td>
                        <td class="px-6 py-4 max-w-[180px]">
                            <div class="truncate text-slate-500" title="{{ $reportLocation }}">{{ Str::limit($reportLocation, 45) }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-slate-500">{{ $report->created_at->format('M d, Y') }}</td>
                        
                        <td class="px-6 py-4 text-center whitespace-nowrap">
                            @if($report->status === 'pending')
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-yellow-50 text-yellow-700 border border-yellow-200">Pending</span>
                            @elseif($report->status === 'verified')
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">Verified</span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-rose-50 text-rose-700 border border-rose-200">Unverified</span>
                            @endif
                        </td>
                        
                        <td class="px-6 py-4 text-center whitespace-nowrap">
                            @if($report->solved)
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-blue-50 text-blue-700 border border-blue-200">Solved</span>
                            @elseif($report->responses->where('action_type','in_progress')->count())
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-indigo-50 text-indigo-700 border border-indigo-200">In Progress</span>
                            @else
                                <span class="text-slate-400 font-bold">—</span>
                            @endif
                        </td>
                        
                        <td class="px-6 py-4 text-center whitespace-nowrap barangay-action-cell" id="barangay-action-{{ $report->id }}" data-field="barangay-action">
                            @if($report->barangay_action_status === 'approved')
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">Approved</span>
                            @elseif($report->barangay_action_status === 'disapproved')
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-rose-50 text-rose-700 border border-rose-200">Disapproved</span>
                            @else
                                <span class="text-slate-400 font-bold">—</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="flex flex-col items-center justify-center p-12 text-slate-400 gap-3">
            <div class="p-4 bg-slate-50 rounded-full">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="w-10 h-10">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </div>
            <p class="text-sm font-medium">No reports assigned to {{ $barangay->name }} yet.</p>
        </div>
        @endif
    </div>
</div>



<div id="detailModal" class="fixed inset-0 z-[100] hidden items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4 sm:p-6 transition-opacity" onclick="closeModal(event)">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] flex flex-col overflow-hidden" onclick="event.stopPropagation()">
        
        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100 bg-slate-50/50 sticky top-0">
            <h3 class="text-lg font-bold text-slate-800">Report Details</h3>
            <button class="text-slate-400 hover:text-rose-500 hover:bg-rose-50 rounded-lg p-1.5 transition-colors focus:outline-none" onclick="document.getElementById('detailModal').classList.add('hidden'); document.getElementById('detailModal').classList.remove('flex');">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>
        
        <div class="p-6 overflow-y-auto custom-scrollbar">
            
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
            
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mb-6">
                <div>
                    <div class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Status</div>
                    <div id="d-status"></div>
                </div>
                <div>
                    <div class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Action Status</div>
                    <div id="d-action"></div>
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
            
            <form id="updateActionForm" method="POST" action="" class="mt-8 pt-6 border-t border-slate-200">
                @csrf
                @method('PATCH') <div class="text-sm font-bold text-slate-800 mb-3">Update Barangay Action Status</div>
                <div class="flex flex-col sm:flex-row gap-3 items-stretch sm:items-center">
                    
                    <select name="barangay_action_status" id="barangayActionSelect" required class="flex-1 px-4 py-2.5 bg-slate-50 border border-slate-300 rounded-lg text-sm shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 focus:bg-white transition-colors">
                        <option value="none">— No Action —</option>
                        <option value="approved">Approved</option>
                        <option value="disapproved">Disapproved</option>
                    </select>
                    
                    <button type="submit" class="w-full sm:w-auto px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg shadow-sm transition-colors focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                        Save Status
                    </button>
                    
                </div>
            </form>
            
        </div>
    </div>
</div>
@endsection

@push('scripts')

<!-- ── Export Scripts -->
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

    // 4. Convert table to Excel workbook
    let wb = XLSX.utils.table_to_book(cloneTable, { sheet: "Reports" });
    
    // 5. Download the file
    XLSX.writeFile(wb, filename + ".xlsx");
}
</script>

<script>

    // ── Global Badge Definitions (Tailwind) ──────────────────────────────
    const badges = {
        status: {
            pending:    '<span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-yellow-50 text-yellow-700 border border-yellow-200">Pending</span>',
            verified:   '<span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">Verified</span>',
            unverified: '<span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-rose-50 text-rose-700 border border-rose-200">Unverified</span>',
        },
        action: {
            solved:      '<span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-blue-50 text-blue-700 border border-blue-200">Solved</span>',
            in_progress: '<span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-indigo-50 text-indigo-700 border border-indigo-200">In Progress</span>',
            none:        '<span class="text-slate-400 font-bold">—</span>',
        },
        barangayAction: {
            approved:    '<span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">Approved</span>',
            disapproved: '<span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-rose-50 text-rose-700 border border-rose-200">Disapproved</span>',
            none:        '<span class="text-slate-400 font-bold">—</span>',
        }
    };

    // ── Filtering ────────────────────────────────────────────────────────
    function filterReports() {
        const type          = document.getElementById('typeFilter').value;
        const status        = document.getElementById('statusFilter').value;
        const action        = document.getElementById('actionFilter').value;
        const barangayAction = document.getElementById('barangayActionFilter').value;
        const rows          = document.querySelectorAll('.report-row');
        let visible         = 0;

        rows.forEach(row => {
            const matchType          = !type          || row.dataset.type          === type;
            const matchStatus        = !status        || row.dataset.status        === status;
            const matchAction        = !action        || row.dataset.action        === action;
            const matchBarangayAction = !barangayAction || row.dataset.barangayAction === barangayAction;
            
            if (matchType && matchStatus && matchAction && matchBarangayAction) {
                row.classList.remove('hidden'); // Tailwind hide/show
                visible++;
            } else {
                row.classList.add('hidden');
            }
        });
        document.getElementById('reportCount').textContent = visible + ' report' + (visible !== 1 ? 's' : '');
    }

    // ── Modal Handling ───────────────────────────────────────────────────
    document.addEventListener('DOMContentLoaded', () => {

        const tableBody = document.getElementById('reportsTableBody');
        const modal = document.getElementById('detailModal');
        const myBarangayId = {{ auth()->guard('barangay')->id() }};

        if (tableBody) 
        {
            tableBody.addEventListener('click', (e) => {
                // Safety check: Ignore clicks on action buttons or links inside the row
                if (e.target.closest('button') || e.target.closest('a')) {
                    return; 
                }

                // Find the clicked row
                const row = e.target.closest('.report-row');
                if (row) {
                    openDetailModal(row);
                }
            });
        }

        // Close modal when clicking on the dark backdrop outside the modal
        if (modal) {
            modal.addEventListener('click', (e) => {
                if (e.target === modal) {
                    closeDetailModal();
                }
            });
        }

        // Check if Echo is available
        if (window.Echo) {
            window.Echo.channel('reports')
                // 🟢 Listen for the Queued Response Event (for the new table)
                .listen('.admin.responded', (event) => {
                    console.log('✅ Queue processed response:', event);
                    
                    // Find the specific row using the new data-report-id attribute
                    const row = document.querySelector(`tr.report-row[data-report-id="${event.report_id}"]`);
                    if (!row) {
                        console.warn(`Row for report ${event.report_id} not found in this table.`);
                        return;
                    }

                    // 1. Update the row's dataset so the modal gets the fresh data next time it's clicked
                    row.dataset.status = event.status;
                    row.dataset.action = event.action_type || 'none';

                    // 2. Update the Status Cell (Column index 5)
                    const statusCell = row.cells[5]; 
                    if (statusCell) {
                        let statusHtml = '';
                        if (event.status === 'pending') {
                            statusHtml = `<span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-yellow-50 text-yellow-700 border border-yellow-200">Pending</span>`;
                        } else if (event.status === 'verified') {
                            statusHtml = `<span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">Verified</span>`;
                        } else { // unverified
                            statusHtml = `<span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-rose-50 text-rose-700 border border-rose-200">Unverified</span>`;
                        }
                        statusCell.innerHTML = statusHtml;
                    }

                    // 3. Update the Action Status Cell (Column index 6)
                    const actionCell = row.cells[6];
                    if (actionCell) {
                        let actionHtml = `<span class="text-slate-400 font-bold">—</span>`;
                        if (event.action_type === 'solved') {
                            actionHtml = `<span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-blue-50 text-blue-700 border border-blue-200">Solved</span>`;
                        } else if (event.action_type === 'in_progress') {
                            actionHtml = `<span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-indigo-50 text-indigo-700 border border-indigo-200">In Progress</span>`;
                        }
                        actionCell.innerHTML = actionHtml;
                    }

                    // 4. Flash the row to indicate it was updated by the queue
                    row.classList.add('bg-blue-50', 'transition-colors', 'duration-1000');
                    setTimeout(() => row.classList.remove('bg-blue-50'), 2000);
                });

            window.Echo.channel(`barangay-notifications.${myBarangayId}`)
            
                // 🟢 Listen for NEW reports being submitted
                .listen('.report.submitted', (event) => {
                    console.log('✅ New report received!', event);
                    
                    // Pass the event payload and the current barangay ID
                    if (typeof window.addReportToTable === 'function') {
                        window.addReportToTable(event, myBarangayId);
                    }
                });
        
            window.addReportToTable = function(report, currentBarangayId) {
                // 1. Double-check: Only add the row if the report belongs to this barangay
                if (String(report.barangay_id) !== String(currentBarangayId)) {
                    return; 
                }

                const tbody = document.getElementById('reportsTableBody');
                if (!tbody) return;

                // Remove empty state placeholder if it exists
                const emptyPlaceholder = tbody.querySelector('td[colspan="8"]');
                if (emptyPlaceholder) {
                    emptyPlaceholder.closest('tr').remove();
                }

                // 2. Helper Functions
                const truncate = (str, length) => str && str.length > length ? str.substring(0, length) + '...' : (str || '');
                const formatCoordinate = (coord) => coord ? parseFloat(coord).toFixed(6) : '0.000000';
                const capitalize = (str) => str ? str.charAt(0).toUpperCase() + str.slice(1) : '';

                // 3. Fallbacks and Data Preparation
                const disasterName = report.disaster_type_name || capitalize(report.disaster_type || 'unknown');
                const locationStr = report.location || `${formatCoordinate(report.latitude)}, ${formatCoordinate(report.longitude)}`;
                let actionStatus = report.solved ? 'solved' : (report.has_in_progress_responses ? 'in_progress' : 'none');
                const barangayAction = report.barangay_action_status || 'none';
                const dateCombined = (report.formatted_date && report.formatted_time) ? `${report.formatted_date} ${report.formatted_time}` : new Date().toLocaleString();

                // -- Generate Status Badge HTML --
                let statusHtml = '';
                if (report.status === 'pending' || !report.status) {
                    statusHtml = `<span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-yellow-50 text-yellow-700 border border-yellow-200">Pending</span>`;
                } else if (report.status === 'verified') {
                    statusHtml = `<span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">Verified</span>`;
                } else {
                    statusHtml = `<span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-rose-50 text-rose-700 border border-rose-200">Unverified</span>`;
                }

                // -- Generate Action Status Badge HTML --
                let actionHtml = `<span class="text-slate-400 font-bold">—</span>`;
                if (actionStatus === 'solved') {
                    actionHtml = `<span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-blue-50 text-blue-700 border border-blue-200">Solved</span>`;
                } else if (actionStatus === 'in_progress') {
                    actionHtml = `<span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-indigo-50 text-indigo-700 border border-indigo-200">In Progress</span>`;
                }

                // -- Generate Barangay Action Badge HTML --
                let bgActionHtml = `<span class="text-slate-400 font-bold">—</span>`;
                if (barangayAction === 'approved') {
                    bgActionHtml = `<span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">Approved</span>`;
                } else if (barangayAction === 'disapproved') {
                    bgActionHtml = `<span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-rose-50 text-rose-700 border border-rose-200">Disapproved</span>`;
                }

                // 4. Create the Row Elements
                const tr = document.createElement('tr');
                
                // Add classes (including a green flash effect 'bg-emerald-50' to highlight the new arrival)
                tr.className = 'report-row bg-emerald-50 hover:bg-slate-50 transition-colors duration-1000 cursor-pointer group';
                tr.setAttribute('onclick', 'openModalWrapper(this)');

                // 5. Populate Dataset Attributes (Exactly matching your Blade setup)
                tr.dataset.type = report.disaster_type;
                tr.dataset.status = report.status || 'pending';
                tr.dataset.action = actionStatus;
                tr.dataset.barangayAction = barangayAction;
                tr.dataset.reportId = report.id;
                tr.dataset.disaster = disasterName;
                tr.dataset.description = report.description || '';
                tr.dataset.reporter = report.user_name || 'Unknown User';
                tr.dataset.location = locationStr;
                tr.dataset.date = dateCombined;
                tr.dataset.image = report.image_url || '';
                tr.dataset.video = report.video_url || '';

                // 6. Build the Inner HTML
                tr.innerHTML = `
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-slate-100 text-slate-700 border border-slate-200">
                            ${disasterName}
                        </span>
                    </td>
                    <td class="px-6 py-4 max-w-[260px]">
                        <div class="truncate group-hover:text-blue-600 transition-colors">${truncate(report.description, 70)}</div>
                    </td>
                    <td class="px-6 py-4 font-medium text-slate-900">${report.user_name || 'Unknown User'}</td>
                    <td class="px-6 py-4 max-w-[180px]">
                        <div class="truncate text-slate-500" title="${locationStr}">${truncate(locationStr, 45)}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-slate-500">${report.formatted_date || new Date().toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })}</td>
                    <td class="px-6 py-4 text-center whitespace-nowrap">${statusHtml}</td>
                    <td class="px-6 py-4 text-center whitespace-nowrap">${actionHtml}</td>
                    <td class="px-6 py-4 text-center whitespace-nowrap barangay-action-cell" id="barangay-action-${report.id}" data-field="barangay-action">
                        ${bgActionHtml}
                    </td>
                `;

                // 7. Insert at the top of the table
                tbody.insertBefore(tr, tbody.firstChild);

                // 8. Fade out the green highlight after 2 seconds
                setTimeout(() => {
                    tr.classList.remove('bg-emerald-50');
                }, 2000);
            };
        
        }
    });

    function openDetailModal(row) {
        const data = row.dataset;
        
        // 1. Populate standard text fields
        document.getElementById('d-type').textContent     = data.disaster || '—';
        document.getElementById('d-date').textContent     = data.date || '—';
        document.getElementById('d-reporter').textContent = data.reporter || '—';
        document.getElementById('d-action').textContent   = data.action ? data.action.toUpperCase().replace('_', ' ') : '—';
        document.getElementById('d-desc').textContent     = data.description || '—';
        document.getElementById('d-location').textContent = data.location || '—';
        document.getElementById('d-status').textContent   = data.status ? data.status.toUpperCase() : '—';

        // 2. Pre-fill the Barangay Action Select dropdown
        const actionSelect = document.getElementById('barangayActionSelect');
        if (actionSelect) {
            actionSelect.value = data.barangayAction || 'none';
        }

        // 3. Dynamically point the Form to the correct route based on the Row's ID
        const form = document.getElementById('updateActionForm');
        if (form) {
            // Adjust the prefix here if your route group requires /barangay/reports/...
            form.action = `/barangay/reports/${data.reportId}/action`;
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
            videoEl.src = '';
            videoWrap.classList.add('hidden');
        }

        // 6. Toggle Tailwind classes to show the modal
        const modal = document.getElementById('detailModal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function closeDetailModal() {
        const modal = document.getElementById('detailModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        
        // Safety feature: Stop video playback if the user closes the modal while a video is running
        const videoEl = document.getElementById('d-video');
        if (videoEl) {
            videoEl.pause();
            videoEl.currentTime = 0;
        }
    }

    // ── Expose close function to the HTML inline-onclick attributes ──
    window.closeModal = function(e) {
        const modal = document.getElementById('detailModal');
        if (e.target === modal) {
            closeDetailModal();
        }
    };

    // ── Barangay Action Update ───────────────────────────────────────────
    function submitBarangayActionUpdate(button) {
        if (!currentReportId) {
            alert('No report selected');
            return;
        }

        const statusValue = document.getElementById('barangayActionSelect').value;
        if (statusValue === 'none') {
            alert('Please select a status');
            return;
        }

        const originalText = button.textContent;
        button.textContent = 'Saving...';
        button.disabled = true;

        fetch(`/barangay/reports/${currentReportId}/action`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-Token': document.querySelector('meta[name="csrf-token"]').content,
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({ barangay_action_status: statusValue })
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                const cell = document.getElementById(`barangay-action-${currentReportId}`);
                if (cell) cell.innerHTML = badges.barangayAction[statusValue] || badges.barangayAction.none;
                
                // Highlight row briefly using inline style (works cleanly with Tailwind)
                const row = document.querySelector(`tr[data-report-id="${currentReportId}"]`);
                if (row) {
                    row.dataset.barangayAction = statusValue; // Update dataset
                    row.style.backgroundColor = '#fef9c3'; // yellow-100
                    setTimeout(() => { row.style.backgroundColor = ''; }, 2000);
                }
                
                closeDetailModal();
                showToast('Success', 'Barangay action updated successfully!');
            } else {
                alert('❌ Error: ' + (data.message || 'Failed'));
            }
        })
        .catch(err => {
            console.error(err);
            alert('❌ Error: ' + err.message);
        })
        .finally(() => {
            button.textContent = originalText;
            button.disabled = false;
        });
    }

    // ── Notifications Configuration ───────────────────────────────────────
    let notifList = [];
    let unreadCount = 0;

    function toggleNotificationDropdown(e) {
        e.stopPropagation();
        const dd = document.getElementById('notificationDropdown');
        if (!dd) return;
        
        dd.classList.toggle('hidden');
        
        // If the dropdown was just opened
        if (!dd.classList.contains('hidden')) {
            renderNotificationList();
            unreadCount = 0;
            notifList.forEach(n => n.read = true);
            updateNotificationBadge();
            localStorage.setItem('barangay_notif_reset', Date.now());
        }
    }

    // Global click handler using closest() to find the wrapper class
    document.addEventListener('click', (e) => {
        const dd = document.getElementById('notificationDropdown');
        const isClickInsideBell = e.target.closest('.notification-bell');
        
        if (!isClickInsideBell && dd && !dd.classList.contains('hidden')) {
            dd.classList.add('hidden');
        }
    });

    function updateNotificationBadge() {
        const badge = document.getElementById('notificationBadge');
        if (!badge) return;

        badge.textContent = unreadCount;
        
        // Matches your Tailwind logic: hidden [&.show]:block
        if (unreadCount > 0) {
            badge.classList.remove('hidden');
            badge.classList.add('show');
        } else {
            badge.classList.add('hidden');
            badge.classList.remove('show');
        }
    }

    function renderNotificationList() {
        const container = document.getElementById('notificationList');
        if (!container) return;

        // Empty state matching your exact HTML design fallback
        if (!notifList.length) {
            container.innerHTML = `
                <div class="p-6 text-center flex flex-col items-center justify-center text-slate-400">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="w-8 h-8 mb-2 opacity-50">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                    <div class="text-sm">No new notifications</div>
                </div>`;
            return;
        }

        // Dynamic list item generator
        container.innerHTML = notifList.map(n => `
            <div class="flex items-start gap-3 p-4 border-b border-slate-100 hover:bg-slate-50 transition-colors ${n.read ? 'bg-white' : 'bg-blue-50/50'}">
                <div class="p-2 bg-blue-100 text-blue-600 rounded-full shrink-0">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="w-4 h-4">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
                <div class="flex-1">
                    <div class="text-sm font-semibold text-slate-800">${n.label || 'Notification'}</div>
                    <div class="text-xs text-slate-600 mt-0.5">${n.disaster_type} — ${n.user_name}</div>
                    <div class="text-[10px] font-medium text-slate-400 mt-1 uppercase tracking-wider">${n.time_ago}</div>
                </div>
            </div>
        `).join('');
    }

    // ── Toast & Audio ────────────────────────────────────────────────────
    function showToast(title, message) {
        const toast = document.createElement('div');
        // Tailwind classes for a sleek animated toast
        toast.className = 'fixed bottom-5 right-5 z-[200] flex items-start gap-3 px-5 py-4 bg-slate-900 text-white rounded-xl shadow-2xl transform translate-x-[150%] transition-transform duration-300 ease-out border border-slate-700 max-w-sm';
        toast.innerHTML = `
            <div class="mt-0.5 text-blue-400">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="w-5 h-5">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <strong class="block text-sm font-semibold">${title}</strong>
                <span class="block text-xs text-slate-300 mt-0.5 leading-relaxed">${message}</span>
            </div>`;
        
        document.body.appendChild(toast);
        
        // Trigger slide-in
        requestAnimationFrame(() => {
            toast.classList.remove('translate-x-[150%]');
            toast.classList.add('translate-x-0');
        });

        // Slide out and remove
        setTimeout(() => {
            toast.classList.remove('translate-x-0');
            toast.classList.add('translate-x-[150%]');
            setTimeout(() => toast.remove(), 300);
        }, 5000);
    }

    function playBeep() {
        try {
            const ctx = new (window.AudioContext || window.webkitAudioContext)();
            const osc = ctx.createOscillator();
            const gain = ctx.createGain();
            osc.connect(gain); gain.connect(ctx.destination);
            osc.frequency.value = 800; osc.type = 'sine';
            gain.gain.setValueAtTime(0.3, ctx.currentTime);
            gain.gain.exponentialRampToValueAtTime(0.01, ctx.currentTime + 0.5);
            osc.start(ctx.currentTime); osc.stop(ctx.currentTime + 0.5);
        } catch(e) {}
    }

    // ── Table Rendering & Data Handling ──────────────────────────────────
    function addRowToTable(report) {
        const tbody = document.getElementById('reportsTableBody');
        if (!tbody) return;

        const actionStatus = 'none';
        const barangayAction = 'none';
        const location = report.location || (report.latitude + ', ' + report.longitude);

        const tr = document.createElement('tr');
        // Match the Tailwind classes of existing rows
        tr.className = 'report-row hover:bg-slate-50 transition-colors cursor-pointer group';
        tr.dataset.type          = report.disaster_type;
        tr.dataset.status        = report.status;
        tr.dataset.action        = actionStatus;
        tr.dataset.barangayAction = barangayAction;
        tr.dataset.reportId      = report.id;
        tr.dataset.disaster      = report.disaster_type_name;
        tr.dataset.description   = report.description;
        tr.dataset.reporter      = report.user_name;
        tr.dataset.location      = location;
        tr.dataset.date          = report.formatted_date + ' ' + report.formatted_time;
        tr.dataset.image         = report.image || '';
        tr.dataset.video         = report.video || '';
        tr.setAttribute('onclick', 'openModalWrapper(this)');

        // Build HTML with exact Tailwind structure
        tr.innerHTML = `
            <td class="px-6 py-4 whitespace-nowrap">
                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-slate-100 text-slate-700 border border-slate-200">${report.disaster_type_name}</span>
            </td>
            <td class="px-6 py-4 max-w-[260px]">
                <div class="truncate group-hover:text-blue-600 transition-colors">${report.description.substring(0, 70)}${report.description.length > 70 ? '…' : ''}</div>
            </td>
            <td class="px-6 py-4 font-medium text-slate-900">${report.user_name}</td>
            <td class="px-6 py-4 max-w-[180px]">
                <div class="truncate text-slate-500">${location.substring(0, 45)}${location.length > 45 ? '…' : ''}</div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-slate-500">${report.formatted_date}</td>
            <td class="px-6 py-4 text-center whitespace-nowrap">${badges.status.pending}</td>
            <td class="px-6 py-4 text-center whitespace-nowrap">${badges.action.none}</td>
            <td class="px-6 py-4 text-center whitespace-nowrap" id="barangay-action-${report.id}">${badges.barangayAction.none}</td>
        `;

        tr.style.backgroundColor = '#dcfce3'; // green-100 for insertion flash
        tbody.insertBefore(tr, tbody.firstChild);
        setTimeout(() => { tr.style.transition = 'background 1s'; tr.style.backgroundColor = ''; }, 3000);

        const countEl = document.getElementById('reportCount');
        if (countEl) {
            const cur = parseInt(countEl.textContent) || 0;
            countEl.textContent = (cur + 1) + ' report' + (cur + 1 !== 1 ? 's' : '');
        }
    }

    function handleNewReport(report) {
        if (knownReportIds.has(report.id)) return;
        knownReportIds.add(report.id);

        notifList.unshift({
            label: '🆕 New Report',
            disaster_type: report.disaster_type_name,
            user_name: report.user_name,
            time_ago: 'Just now',
            read: false
        });
        if (notifList.length > 50) notifList.pop();
        
        unreadCount++;
        updateBadge();
        showToast('New Report Submitted', `${report.disaster_type_name} by ${report.user_name}`);
        addRowToTable(report);
        playBeep();
    }

    function handleUpdatedReport(report) {
        const row = document.querySelector(`[data-report-id="${report.id}"]`);
        if (!row) return;

        const prevStatus = row.dataset.status;
        const prevAction = row.dataset.action;

        if (prevStatus === report.status && prevAction === report.action_status) return;

        row.dataset.status = report.status;
        row.dataset.action = report.action_status;

        const tds = row.querySelectorAll('td');
        if (tds[5]) tds[5].innerHTML = badges.status[report.status] || report.status;
        if (tds[6]) tds[6].innerHTML = badges.action[report.action_status] || badges.action.none;

        notifList.unshift({
            label: '📋 Admin Responded',
            disaster_type: report.disaster_type_name,
            user_name: 'Admin',
            time_ago: 'Just now',
            read: false
        });
        if (notifList.length > 50) notifList.pop();
        
        unreadCount++;
        updateBadge();
        showToast('Status Updated', `${report.disaster_type_name} is now ${report.status}`);

        // Highlight Row
        row.style.transition = 'none';
        row.style.backgroundColor = '#fef9c3'; // yellow-100
        setTimeout(() => { row.style.transition = 'background-color 1.2s'; row.style.backgroundColor = ''; }, 2500);
        playBeep();
    }

    function updateRowStatus(event) {
        handleUpdatedReport({
            id: event.report_id,
            disaster_type_name: event.disaster_type_name,
            status: event.status,
            action_status: event.action_type === 'solved' ? 'solved'
                         : event.action_type === 'in_progress' ? 'in_progress' : 'none'
        });
    }


</script>

@endpush
