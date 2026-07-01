@extends('layouts.admin')

@section('title', 'Admin Dashboard')

@push('styles')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.js"></script>
@endpush

@section('content')
<div class="p-4 md:p-8 space-y-8">
    
    
    @include('partials.notif_logout', ['page_name' => 'Dashboard Overview'])

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 xl:grid-cols-5 gap-6">
        <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-100 flex flex-col justify-between">
            <div class="flex justify-between items-start">
                <div class="text-slate-500 text-sm font-medium">Total Reports</div>
                <div class="p-2.5 bg-blue-50 text-blue-600 rounded-lg">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
            </div>
            <div class="text-3xl font-bold text-slate-800 mt-4">{{ $totalReports }}</div>
        </div>

        <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-100 flex flex-col justify-between">
            <div class="flex justify-between items-start">
                <div class="text-slate-500 text-sm font-medium">Pending Reports</div>
                <div class="p-2.5 bg-amber-50 text-amber-600 rounded-lg">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
            <div class="text-3xl font-bold text-slate-800 mt-4">{{ $pendingReports }}</div>
        </div>

        <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-100 flex flex-col justify-between">
            <div class="flex justify-between items-start">
                <div class="text-slate-500 text-sm font-medium">Verified Reports</div>
                <div class="p-2.5 bg-emerald-50 text-emerald-600 rounded-lg">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
            <div class="text-3xl font-bold text-slate-800 mt-4">{{ $verifiedReports }}</div>
        </div>

        <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-100 flex flex-col justify-between">
            <div class="flex justify-between items-start">
                <div class="text-slate-500 text-sm font-medium">Solved Reports</div>
                <div class="p-2.5 bg-purple-50 text-purple-600 rounded-lg">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
            </div>
            <div class="text-3xl font-bold text-slate-800 mt-4">{{ $solvedReports }}</div>
        </div>

        <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-100 flex flex-col justify-between sm:col-span-2 lg:col-span-4 xl:col-span-1">
            <div class="flex justify-between items-start">
                <div class="text-slate-500 text-sm font-medium">Registered Users</div>
                <div class="p-2.5 bg-indigo-50 text-indigo-600 rounded-lg">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                </div>
            </div>
            <div class="text-3xl font-bold text-slate-800 mt-4">{{ $totalUsers }}</div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-100">
            <h2 class="text-lg font-bold text-slate-800 mb-4">📊 Monthly Analytics</h2>
            <div class="relative w-full h-64">
                <canvas id="monthlyChart"></canvas>
            </div>
        </div>

        <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-100">
            <h2 class="text-lg font-bold text-slate-800 mb-4">📈 Report Status Analytics</h2>
            <div class="relative w-full h-64">
                <canvas id="reportChart"></canvas>
            </div>
        </div>

        <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-100">
            <h2 class="text-lg font-bold text-slate-800 mb-4">⚠️ Disaster Type Analytics</h2>
            <div class="relative w-full h-64">
                <canvas id="disasterChart"></canvas>
            </div>
        </div>

        <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-100">
            <h2 class="text-lg font-bold text-slate-800 mb-4">🏘️ Barangay Reports Analytics</h2>
            <div class="relative w-full h-64">
                <canvas id="barangayChart"></canvas>
            </div>
        </div>
    </div>
</div>
@endsection


@push('scripts')
<script>
    
    // Initialize Analytics Charts
    function initCharts() {
        // Monthly Analytics - Line Chart
        const monthlyCtx = document.getElementById('monthlyChart');
        if (monthlyCtx) {
            new Chart(monthlyCtx.getContext('2d'), {
                type: 'line',
                data: {
                    labels: {!! json_encode($monthlyLabels ?? []) !!},
                    datasets: [{
                        label: 'Reports Submitted',
                        data: {!! json_encode($monthlyData ?? []) !!},
                        borderColor: '#3b82f6',
                        backgroundColor: 'rgba(59, 130, 246, 0.1)',
                        borderWidth: 2,
                        tension: 0.4,
                        fill: true,
                        pointBackgroundColor: '#3b82f6',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2,
                        pointRadius: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: true,
                            labels: { color: '#64748b', font: { size: 12 } }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: { color: '#e2e8f0' },
                            ticks: { color: '#64748b' }
                        },
                        x: {
                            grid: { display: false },
                            ticks: { color: '#64748b' }
                        }
                    }
                }
            });
        }

        // Report Status Analytics - Pie Chart
        const reportCtx = document.getElementById('reportChart');
        if (reportCtx) {
            new Chart(reportCtx.getContext('2d'), {
                type: 'doughnut',
                data: {
                    labels: ['Pending', 'Verified', 'Solved'],
                    datasets: [{
                        data: {!! json_encode([$pendingReports, $verifiedReports, $solvedReports]) !!},
                        backgroundColor: [
                            '#f59e0b',
                            '#10b981',
                            '#8b5cf6'
                        ],
                        borderColor: '#fff',
                        borderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: true,
                            position: 'bottom',
                            labels: { color: '#64748b', font: { size: 12 }, padding: 15 }
                        }
                    }
                }
            });
        }

        // Disaster Type Analytics - Bar Chart
        const disasterCtx = document.getElementById('disasterChart');
        if (disasterCtx) {
            new Chart(disasterCtx.getContext('2d'), {
                type: 'bar',
                data: {
                    labels: {!! json_encode($disasterLabels ?? []) !!},
                    datasets: [{
                        label: 'Number of Reports',
                        data: {!! json_encode($disasterChartData ?? []) !!},
                        backgroundColor: [
                            '#3b82f6',
                            '#ef4444',
                            '#f59e0b',
                            '#8b5cf6',
                            '#10b981',
                            '#06b6d4'
                        ],
                        borderRadius: 8,
                        borderSkipped: false
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    indexAxis: 'y',
                    plugins: {
                        legend: {
                            display: true,
                            labels: { color: '#64748b', font: { size: 12 } }
                        }
                    },
                    scales: {
                        x: {
                            beginAtZero: true,
                            grid: { color: '#e2e8f0' },
                            ticks: { color: '#64748b' }
                        },
                        y: {
                            grid: { display: false },
                            ticks: { color: '#64748b' }
                        }
                    }
                }
            });
        }

        // Barangay Reports Analytics - Bar Chart
        const barangayCtx = document.getElementById('barangayChart');
        if (barangayCtx) {
            new Chart(barangayCtx.getContext('2d'), {
                type: 'bar',
                data: {
                    labels: {!! json_encode($barangayLabels ?? []) !!},
                    datasets: [{
                        label: 'Number of Reports',
                        data: {!! json_encode($barangayChartData ?? []) !!},
                        backgroundColor: [
                            '#3b82f6',
                            '#ef4444',
                            '#f59e0b',
                            '#8b5cf6',
                            '#10b981',
                            '#06b6d4',
                            '#ec4899',
                            '#14b8a6'
                        ],
                        borderRadius: 8,
                        borderSkipped: false
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: true,
                            labels: { color: '#64748b', font: { size: 12 } }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: { color: '#e2e8f0' },
                            ticks: { color: '#64748b' }
                        },
                        x: {
                            grid: { display: false },
                            ticks: { color: '#64748b' }
                        }
                    }
                }
            });
        }
    }

    // Initialize charts when page loads
    document.addEventListener('DOMContentLoaded', initCharts);
</script>
@endpush