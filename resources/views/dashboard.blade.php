@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title')
<i class="fas fa-chart-pie mr-2 opacity-60"></i>Dashboard
@endsection

@section('content')
<div class="animate-in space-y-5">

    <!-- Stat Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 lg:gap-4">
        <!-- Total Rolls -->
        <a href="{{ route('items.index') }}" class="no-underline">
            <div class="glass stat-card blue p-4">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-semibold uppercase tracking-wide" style="color: #60a5fa;">Total Rolls</span>
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center" style="background: rgba(59,130,246,0.12);">
                        <i class="fas fa-boxes-stacked text-sm" style="color: #60a5fa;"></i>
                    </div>
                </div>
                <div class="text-2xl lg:text-3xl font-extrabold text-white mb-1">{{ number_format($totalRolls) }}</div>
                <div class="text-xs" style="color: #484f58;">inventory items</div>
            </div>
        </a>

        <!-- Defects -->
        <a href="{{ route('defects.index') }}" class="no-underline">
            <div class="glass stat-card red p-4">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-semibold uppercase tracking-wide" style="color: #f87171;">Defects</span>
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center" style="background: rgba(239,68,68,0.12);">
                        <i class="fas fa-triangle-exclamation text-sm" style="color: #f87171;"></i>
                    </div>
                </div>
                <div class="text-2xl lg:text-3xl font-extrabold text-white mb-1">{{ number_format($totalDefects) }}</div>
                <div class="text-xs" style="color: #484f58;">{{ $totalRolls > 0 ? number_format(($totalDefects/$totalRolls)*100, 2) : '0' }}% defect rate</div>
            </div>
        </a>

        <!-- Terlokasi -->
        <div class="glass stat-card green p-4">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-semibold uppercase tracking-wide" style="color: #4ade80;">Terlokasi</span>
                <div class="w-8 h-8 rounded-lg flex items-center justify-center" style="background: rgba(34,197,94,0.12);">
                    <i class="fas fa-map-marker-alt text-sm" style="color: #4ade80;"></i>
                </div>
            </div>
            <div class="text-2xl lg:text-3xl font-extrabold text-white mb-1">{{ number_format($totalRolls - $noLocationCount) }}</div>
            <div class="text-xs" style="color: #484f58;">{{ number_format($noLocationCount) }} belum terlokasi</div>
        </div>

        <!-- Lokasi Unik -->
        <div class="glass stat-card teal p-4">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-semibold uppercase tracking-wide" style="color: #2dd4bf;">Lokasi Unik</span>
                <div class="w-8 h-8 rounded-lg flex items-center justify-center" style="background: rgba(20,184,166,0.12);">
                    <i class="fas fa-sitemap text-sm" style="color: #2dd4bf;"></i>
                </div>
            </div>
            <div class="text-2xl lg:text-3xl font-extrabold text-white mb-1">{{ count($locationRekap) }}</div>
            <div class="text-xs" style="color: #484f58;">lokasi berbeda</div>
        </div>
    </div>

    <!-- Charts Row 1 -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        <!-- Paper Type -->
        <div class="glass p-4">
            <h3 class="text-sm font-semibold text-white mb-4 flex items-center gap-2">
                <i class="fas fa-chart-bar text-xs" style="color: #60a5fa;"></i>
                Distribusi Paper Type
            </h3>
            <div style="position: relative; height: 240px;">
                <canvas id="paperTypeChart"></canvas>
            </div>
        </div>
        <!-- Top Lokasi -->
        <div class="glass p-4">
            <h3 class="text-sm font-semibold text-white mb-4 flex items-center gap-2">
                <i class="fas fa-map-marker-alt text-xs" style="color: #2dd4bf;"></i>
                Top 10 Lokasi Rekap
            </h3>
            <div style="position: relative; height: 240px;">
                <canvas id="locationChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Charts Row 2 -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        <!-- GSM Distribution -->
        <div class="glass p-4">
            <h3 class="text-sm font-semibold text-white mb-4 flex items-center gap-2">
                <i class="fas fa-ruler text-xs" style="color: #a78bfa;"></i>
                Distribusi GSM
            </h3>
            <div style="position: relative; height: 240px;">
                <canvas id="gsmChart"></canvas>
            </div>
        </div>
        <!-- Defect Reasons -->
        <div class="glass p-4">
            <h3 class="text-sm font-semibold text-white mb-4 flex items-center gap-2">
                <i class="fas fa-bug text-xs" style="color: #f87171;"></i>
                Defect per Alasan
            </h3>
            <div style="position: relative; height: 240px;">
                <canvas id="defectChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Tables -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        <!-- Top Lokasi Table -->
        <div class="glass overflow-hidden">
            <div class="px-4 pt-4 pb-2">
                <h3 class="text-sm font-semibold text-white flex items-center gap-2">
                    <i class="fas fa-warehouse text-xs" style="color: #60a5fa;"></i>
                    Top Lokasi
                </h3>
            </div>
            <div class="overflow-x-auto">
                <table class="data-table">
                    <thead>
                        <tr><th>#</th><th>Lokasi</th><th>Jumlah</th><th>Progress</th></tr>
                    </thead>
                    <tbody>
                        @foreach($locationRekap as $i => $loc)
                        <tr onclick="window.location='{{ route('items.index', ['search' => $loc->lokasi]) }}'">
                            <td style="color: #484f58;">{{ $i + 1 }}</td>
                            <td><span class="tag tag-blue">{{ $loc->lokasi }}</span></td>
                            <td class="font-semibold text-white">{{ number_format($loc->count) }}</td>
                            <td style="min-width: 120px;">
                                <div class="flex items-center gap-2">
                                    <div class="flex-1 h-1.5 rounded-full" style="background: rgba(255,255,255,0.06);">
                                        <div class="h-1.5 rounded-full" style="background: linear-gradient(90deg, #3b82f6, #60a5fa); width: {{ ($loc->count/$totalRolls)*100 }}%;"></div>
                                    </div>
                                    <span class="text-xs" style="color: #484f58; min-width: 38px;">{{ number_format(($loc->count/$totalRolls)*100, 1) }}%</span>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Status Table -->
        <div class="glass overflow-hidden">
            <div class="px-4 pt-4 pb-2">
                <h3 class="text-sm font-semibold text-white flex items-center gap-2">
                    <i class="fas fa-tags text-xs" style="color: #4ade80;"></i>
                    Status Barang
                </h3>
            </div>
            <div class="overflow-x-auto">
                <table class="data-table">
                    <thead>
                        <tr><th>#</th><th>Status</th><th>Jumlah</th><th>Progress</th></tr>
                    </thead>
                    <tbody>
                        @foreach($statusStats as $i => $s)
                        @php
                            $tagClass = 'tag-gray';
                            $barColor = '#484f58';
                            $st = strtolower($s->status_barang);
                            if($st == 'good') { $tagClass = 'tag-green'; $barColor = '#22c55e'; }
                            elseif(in_array($st, ['hold','pending'])) { $tagClass = 'tag-yellow'; $barColor = '#eab308'; }
                            elseif(in_array($st, ['reject','problem','rusak'])) { $tagClass = 'tag-red'; $barColor = '#ef4444'; }
                        @endphp
                        <tr>
                            <td style="color: #484f58;">{{ $i + 1 }}</td>
                            <td><span class="tag {{ $tagClass }}">{{ $s->status_barang }}</span></td>
                            <td class="font-semibold text-white">{{ number_format($s->count) }}</td>
                            <td style="min-width: 120px;">
                                <div class="flex items-center gap-2">
                                    <div class="flex-1 h-1.5 rounded-full" style="background: rgba(255,255,255,0.06);">
                                        <div class="h-1.5 rounded-full" style="background: {{ $barColor }}; width: {{ ($s->count/$totalRolls)*100 }}%;"></div>
                                    </div>
                                    <span class="text-xs" style="color: #484f58; min-width: 38px;">{{ number_format(($s->count/$totalRolls)*100, 1) }}%</span>
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
// Chart.js global defaults for dark theme
Chart.defaults.color = '#8b949e';
Chart.defaults.font.family = 'Inter';
Chart.defaults.font.size = 11;
Chart.defaults.plugins.legend.labels.boxWidth = 12;
Chart.defaults.plugins.legend.labels.padding = 14;
Chart.defaults.plugins.legend.labels.usePointStyle = true;
Chart.defaults.plugins.legend.labels.pointStyleWidth = 8;

const chartColors = {
    blue: { bg: 'rgba(59,130,246,0.6)', border: '#3b82f6' },
    green: { bg: 'rgba(34,197,94,0.6)', border: '#22c55e' },
    red: { bg: 'rgba(239,68,68,0.6)', border: '#ef4444' },
    yellow: { bg: 'rgba(234,179,8,0.6)', border: '#eab308' },
    purple: { bg: 'rgba(139,92,246,0.6)', border: '#8b5cf6' },
    teal: { bg: 'rgba(20,184,166,0.6)', border: '#14b8a6' },
    orange: { bg: 'rgba(249,115,22,0.6)', border: '#f97316' },
    pink: { bg: 'rgba(236,72,153,0.6)', border: '#ec4899' },
    indigo: { bg: 'rgba(99,102,241,0.6)', border: '#6366f1' },
    cyan: { bg: 'rgba(6,182,212,0.6)', border: '#06b6d4' },
};
const palette = Object.values(chartColors);

const gridColor = 'rgba(255,255,255,0.04)';

// Paper Type Chart
new Chart(document.getElementById('paperTypeChart'), {
    type: 'bar',
    data: {
        labels: [{!! $paperTypeStats->map(fn($p) => '"'.addslashes($p->paper_type).'"')->join(',') !!}],
        datasets: [{
            data: [{!! $paperTypeStats->pluck('count')->join(',') !!}],
            backgroundColor: palette.map(c => c.bg),
            borderColor: palette.map(c => c.border),
            borderWidth: 1,
            borderRadius: 6,
            borderSkipped: false,
        }]
    },
    options: {
        responsive: true, maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: {
            y: { beginAtZero: true, grid: { color: gridColor }, border: { display: false }, ticks: { padding: 8 } },
            x: { grid: { display: false }, border: { display: false }, ticks: { padding: 6, maxRotation: 45 } }
        }
    }
});

// Location Chart
new Chart(document.getElementById('locationChart'), {
    type: 'bar',
    data: {
        labels: [{!! collect($locationRekap)->take(10)->map(fn($l) => '"'.addslashes($l->lokasi).'"')->join(',') !!}],
        datasets: [{
            data: [{!! collect($locationRekap)->take(10)->pluck('count')->join(',') !!}],
            backgroundColor: 'rgba(20,184,166,0.5)',
            borderColor: '#14b8a6',
            borderWidth: 1,
            borderRadius: 6,
            borderSkipped: false,
        }]
    },
    options: {
        indexAxis: 'y',
        responsive: true, maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: {
            x: { beginAtZero: true, grid: { color: gridColor }, border: { display: false }, ticks: { padding: 8 } },
            y: { grid: { display: false }, border: { display: false }, ticks: { padding: 6, font: { size: 10 } } }
        }
    }
});

// GSM Chart
new Chart(document.getElementById('gsmChart'), {
    type: 'bar',
    data: {
        labels: [{!! $gsmStats->map(fn($g) => '"'.$g->gsm.'"')->join(',') !!}],
        datasets: [{
            data: [{!! $gsmStats->pluck('count')->join(',') !!}],
            backgroundColor: 'rgba(139,92,246,0.5)',
            borderColor: '#8b5cf6',
            borderWidth: 1,
            borderRadius: 6,
            borderSkipped: false,
        }]
    },
    options: {
        responsive: true, maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: {
            y: { beginAtZero: true, grid: { color: gridColor }, border: { display: false }, ticks: { padding: 8 } },
            x: { grid: { display: false }, border: { display: false }, ticks: { padding: 6, maxRotation: 45 } }
        }
    }
});

// Defect Chart (Doughnut)
new Chart(document.getElementById('defectChart'), {
    type: 'doughnut',
    data: {
        labels: [{!! $defectReasonStats->map(fn($d) => '"'.addslashes($d->reason).'"')->join(',') !!}],
        datasets: [{
            data: [{!! $defectReasonStats->pluck('count')->join(',') !!}],
            backgroundColor: palette.map(c => c.bg),
            borderColor: 'rgba(13,17,23,0.8)',
            borderWidth: 2,
        }]
    },
    options: {
        responsive: true, maintainAspectRatio: false,
        cutout: '65%',
        plugins: {
            legend: { position: 'bottom', labels: { padding: 12, font: { size: 10 } } }
        }
    }
});
</script>
@endpush
