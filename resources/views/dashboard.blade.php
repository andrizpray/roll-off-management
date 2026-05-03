@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title')
<i class="fas fa-chart-pie mr-2 text-blue-400"></i>Dashboard
@endsection

@section('content')
<div class="animate-in space-y-5">

    <!-- Stat Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 lg:gap-4">
        <a href="{{ route('items.index') }}" class="no-underline">
            <div class="card stat-card blue p-4">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-semibold uppercase tracking-wide text-blue-500">Total Rolls</span>
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center bg-blue-50">
                        <i class="fas fa-boxes-stacked text-sm text-blue-500"></i>
                    </div>
                </div>
                <div class="text-2xl lg:text-3xl font-extrabold text-gray-900 mb-1">{{ number_format($totalRolls) }}</div>
                <div class="text-xs text-gray-400">inventory items</div>
            </div>
        </a>
        <a href="{{ route('defects.index') }}" class="no-underline">
            <div class="card stat-card red p-4">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-semibold uppercase tracking-wide text-red-500">Defects</span>
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center bg-red-50">
                        <i class="fas fa-triangle-exclamation text-sm text-red-500"></i>
                    </div>
                </div>
                <div class="text-2xl lg:text-3xl font-extrabold text-gray-900 mb-1">{{ number_format($totalDefects) }}</div>
                <div class="text-xs text-gray-400">{{ $totalRolls > 0 ? number_format(($totalDefects/$totalRolls)*100, 2) : '0' }}% defect rate</div>
            </div>
        </a>
        <div class="card stat-card green p-4">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-semibold uppercase tracking-wide text-green-600">Terlokasi</span>
                <div class="w-8 h-8 rounded-lg flex items-center justify-center bg-green-50">
                    <i class="fas fa-map-marker-alt text-sm text-green-600"></i>
                </div>
            </div>
            <div class="text-2xl lg:text-3xl font-extrabold text-gray-900 mb-1">{{ number_format($totalRolls - $noLocationCount) }}</div>
            <div class="text-xs text-gray-400">{{ number_format($noLocationCount) }} belum terlokasi</div>
        </div>
        <div class="card stat-card teal p-4">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-semibold uppercase tracking-wide text-teal-600">Lokasi Unik</span>
                <div class="w-8 h-8 rounded-lg flex items-center justify-center bg-teal-50">
                    <i class="fas fa-sitemap text-sm text-teal-600"></i>
                </div>
            </div>
            <div class="text-2xl lg:text-3xl font-extrabold text-gray-900 mb-1">{{ count($locationRekap) }}</div>
            <div class="text-xs text-gray-400">lokasi berbeda</div>
        </div>
    </div>

    <!-- Charts Row 1 -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        <div class="card p-4">
            <h3 class="text-sm font-semibold text-gray-800 mb-4 flex items-center gap-2">
                <i class="fas fa-chart-bar text-xs text-blue-500"></i>Distribusi Paper Type
            </h3>
            <div style="position:relative;height:240px;"><canvas id="paperTypeChart"></canvas></div>
        </div>
        <div class="card p-4">
            <h3 class="text-sm font-semibold text-gray-800 mb-4 flex items-center gap-2">
                <i class="fas fa-map-marker-alt text-xs text-teal-500"></i>Top 10 Lokasi Rekap
            </h3>
            <div style="position:relative;height:240px;"><canvas id="locationChart"></canvas></div>
        </div>
    </div>

    <!-- Charts Row 2 -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        <div class="card p-4">
            <h3 class="text-sm font-semibold text-gray-800 mb-4 flex items-center gap-2">
                <i class="fas fa-ruler text-xs text-purple-500"></i>Distribusi GSM
            </h3>
            <div style="position:relative;height:240px;"><canvas id="gsmChart"></canvas></div>
        </div>
        <div class="card p-4">
            <h3 class="text-sm font-semibold text-gray-800 mb-4 flex items-center gap-2">
                <i class="fas fa-bug text-xs text-red-500"></i>Defect per Alasan
            </h3>
            <div style="position:relative;height:240px;"><canvas id="defectChart"></canvas></div>
        </div>
    </div>

    <!-- Tables -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        <div class="card overflow-hidden">
            <div class="px-4 pt-4 pb-2">
                <h3 class="text-sm font-semibold text-gray-800 flex items-center gap-2">
                    <i class="fas fa-warehouse text-xs text-blue-500"></i>Top Lokasi
                </h3>
            </div>
            <div class="overflow-x-auto">
                <table class="data-table">
                    <thead><tr><th>#</th><th>Lokasi</th><th>Jumlah</th><th>Progress</th></tr></thead>
                    <tbody>
                        @foreach($locationRekap as $i => $loc)
                        <tr onclick="window.location='{{ route('items.index', ['search' => $loc->lokasi]) }}'">
                            <td class="text-gray-400">{{ $i + 1 }}</td>
                            <td><span class="tag tag-blue">{{ $loc->lokasi }}</span></td>
                            <td class="font-semibold text-gray-800">{{ number_format($loc->count) }}</td>
                            <td style="min-width:120px;">
                                <div class="flex items-center gap-2">
                                    <div class="flex-1 h-1.5 rounded-full bg-gray-100">
                                        <div class="h-1.5 rounded-full" style="background:linear-gradient(90deg,#3b82f6,#60a5fa);width:{{ ($loc->count/$totalRolls)*100 }}%;"></div>
                                    </div>
                                    <span class="text-xs text-gray-400" style="min-width:38px;">{{ number_format(($loc->count/$totalRolls)*100, 1) }}%</span>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card overflow-hidden">
            <div class="px-4 pt-4 pb-2">
                <h3 class="text-sm font-semibold text-gray-800 flex items-center gap-2">
                    <i class="fas fa-tags text-xs text-green-500"></i>Status Barang
                </h3>
            </div>
            <div class="overflow-x-auto">
                <table class="data-table">
                    <thead><tr><th>#</th><th>Status</th><th>Jumlah</th><th>Progress</th></tr></thead>
                    <tbody>
                        @foreach($statusStats as $i => $s)
                        @php
                            $tagClass='tag-gray'; $barColor='#cbd5e1';
                            $st=strtolower($s->status_barang);
                            if($st=='good'){ $tagClass='tag-green'; $barColor='#22c55e'; }
                            elseif(in_array($st,['hold','pending'])){ $tagClass='tag-yellow'; $barColor='#eab308'; }
                            elseif(in_array($st,['reject','problem','rusak'])){ $tagClass='tag-red'; $barColor='#ef4444'; }
                        @endphp
                        <tr>
                            <td class="text-gray-400">{{ $i + 1 }}</td>
                            <td><span class="tag {{ $tagClass }}">{{ $s->status_barang }}</span></td>
                            <td class="font-semibold text-gray-800">{{ number_format($s->count) }}</td>
                            <td style="min-width:120px;">
                                <div class="flex items-center gap-2">
                                    <div class="flex-1 h-1.5 rounded-full bg-gray-100">
                                        <div class="h-1.5 rounded-full" style="background:{{ $barColor }};width:{{ ($s->count/$totalRolls)*100 }}%;"></div>
                                    </div>
                                    <span class="text-xs text-gray-400" style="min-width:38px;">{{ number_format(($s->count/$totalRolls)*100, 1) }}%</span>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const cc = window.__chartColors || { text: '#64748b', grid: '#f1f5f9', doughnutBorder: '#fff' };
Chart.defaults.color = cc.text;
Chart.defaults.font.family = 'Inter';
Chart.defaults.font.size = 11;
Chart.defaults.plugins.legend.labels.boxWidth = 12;
Chart.defaults.plugins.legend.labels.padding = 14;
Chart.defaults.plugins.legend.labels.usePointStyle = true;
Chart.defaults.plugins.legend.labels.pointStyleWidth = 8;

const palette = [
    { bg: 'rgba(59,130,246,0.7)', border: '#3b82f6' },
    { bg: 'rgba(34,197,94,0.7)', border: '#22c55e' },
    { bg: 'rgba(239,68,68,0.7)', border: '#ef4444' },
    { bg: 'rgba(234,179,8,0.7)', border: '#eab308' },
    { bg: 'rgba(139,92,246,0.7)', border: '#8b5cf6' },
    { bg: 'rgba(20,184,166,0.7)', border: '#14b8a6' },
    { bg: 'rgba(249,115,22,0.7)', border: '#f97316' },
    { bg: 'rgba(236,72,153,0.7)', border: '#ec4899' },
    { bg: 'rgba(99,102,241,0.7)', border: '#6366f1' },
    { bg: 'rgba(6,182,212,0.7)', border: '#06b6d4' },
];
const gridColor = cc.grid;

const paperTypeChart = new Chart(document.getElementById('paperTypeChart'), {
    type: 'bar',
    data: {
        labels: [{!! $paperTypeStats->map(fn($p) => '"'.addslashes($p->paper_type).'"')->join(',') !!}],
        datasets: [{ data: [{!! $paperTypeStats->pluck('count')->join(',') !!}], backgroundColor: palette.map(c => c.bg), borderColor: palette.map(c => c.border), borderWidth: 1, borderRadius: 6, borderSkipped: false }]
    },
    options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, grid: { color: gridColor }, border: { display: false }, ticks: { padding: 8 } }, x: { grid: { display: false }, border: { display: false }, ticks: { padding: 6, maxRotation: 45 } } } }
});
window.charts.push(paperTypeChart);

const locationChart = new Chart(document.getElementById('locationChart'), {
    type: 'bar',
    data: {
        labels: [{!! collect($locationRekap)->take(10)->map(fn($l) => '"'.addslashes($l->lokasi).'"')->join(',') !!}],
        datasets: [{ data: [{!! collect($locationRekap)->take(10)->pluck('count')->join(',') !!}], backgroundColor: 'rgba(20,184,166,0.6)', borderColor: '#14b8a6', borderWidth: 1, borderRadius: 6, borderSkipped: false }]
    },
    options: { indexAxis: 'y', responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { x: { beginAtZero: true, grid: { color: gridColor }, border: { display: false }, ticks: { padding: 8 } }, y: { grid: { display: false }, border: { display: false }, ticks: { padding: 6, font: { size: 10 } } } } }
});
window.charts.push(locationChart);

const gsmChart = new Chart(document.getElementById('gsmChart'), {
    type: 'bar',
    data: {
        labels: [{!! $gsmStats->map(fn($g) => '"'.$g->gsm.'"')->join(',') !!}],
        datasets: [{ data: [{!! $gsmStats->pluck('count')->join(',') !!}], backgroundColor: 'rgba(139,92,246,0.6)', borderColor: '#8b5cf6', borderWidth: 1, borderRadius: 6, borderSkipped: false }]
    },
    options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, grid: { color: gridColor }, border: { display: false }, ticks: { padding: 8 } }, x: { grid: { display: false }, border: { display: false }, ticks: { padding: 6, maxRotation: 45 } } } }
});
window.charts.push(gsmChart);

const defectChart = new Chart(document.getElementById('defectChart'), {
    type: 'doughnut',
    data: {
        labels: [{!! $defectReasonStats->map(fn($d) => '"'.addslashes($d->reason).'"')->join(',') !!}],
        datasets: [{ data: [{!! $defectReasonStats->pluck('count')->join(',') !!}], backgroundColor: palette.map(c => c.bg), borderColor: cc.doughnutBorder, borderWidth: 2 }]
    },
    options: { responsive: true, maintainAspectRatio: false, cutout: '65%', plugins: { legend: { position: 'bottom', labels: { padding: 12, font: { size: 10 } } } } }
});
window.charts.push(defectChart);
</script>
@endpush
