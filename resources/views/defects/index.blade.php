@extends('layouts.app')

@section('title', 'Barang Bermasalah')
@section('page-title')
<i class="fas fa-triangle-exclamation mr-2 text-red-400"></i>Barang Bermasalah
@endsection

@section('content')
<div class="animate-in space-y-5">

    <!-- Stat Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
        <div class="card stat-card red p-4">
            <div class="flex items-center justify-between mb-2">
                <span class="text-[10px] font-semibold uppercase tracking-wide text-red-500">Total Defects</span>
                <div class="w-7 h-7 rounded-lg flex items-center justify-center bg-red-50">
                    <i class="fas fa-bug text-xs text-red-500"></i>
                </div>
            </div>
            <div class="text-xl lg:text-2xl font-extrabold text-gray-900">{{ number_format($totalDefects) }}</div>
            <div class="text-xs text-gray-400 mt-1">{{ $defectRate }}% defect rate</div>
        </div>
        <div class="card stat-card purple p-4">
            <div class="flex items-center justify-between mb-2">
                <span class="text-[10px] font-semibold uppercase tracking-wide text-purple-500">Tahun 2025</span>
                <div class="w-7 h-7 rounded-lg flex items-center justify-center bg-purple-50">
                    <i class="fas fa-calendar-alt text-xs text-purple-500"></i>
                </div>
            </div>
            <div class="text-xl lg:text-2xl font-extrabold text-gray-900">{{ number_format($defect2025) }}</div>
            <div class="text-xs text-gray-400 mt-1">{{ $totalDefects > 0 ? number_format(($defect2025/$totalDefects)*100,1) : '0' }}%</div>
        </div>
        <div class="card stat-card teal p-4">
            <div class="flex items-center justify-between mb-2">
                <span class="text-[10px] font-semibold uppercase tracking-wide text-teal-600">Tahun 2026</span>
                <div class="w-7 h-7 rounded-lg flex items-center justify-center bg-teal-50">
                    <i class="fas fa-calendar-check text-xs text-teal-600"></i>
                </div>
            </div>
            <div class="text-xl lg:text-2xl font-extrabold text-gray-900">{{ number_format($defect2026) }}</div>
            <div class="text-xs text-gray-400 mt-1">{{ $totalDefects > 0 ? number_format(($defect2026/$totalDefects)*100,1) : '0' }}%</div>
        </div>
        <div class="card stat-card blue p-4">
            <div class="flex items-center justify-between mb-2">
                <span class="text-[10px] font-semibold uppercase tracking-wide text-blue-500">Ditampilkan</span>
                <div class="w-7 h-7 rounded-lg flex items-center justify-center bg-blue-50">
                    <i class="fas fa-filter text-xs text-blue-500"></i>
                </div>
            </div>
            <div class="text-xl lg:text-2xl font-extrabold text-gray-900">{{ number_format($defects->total()) }}</div>
            <div class="text-xs text-gray-400 mt-1">filtered</div>
        </div>
    </div>

    <!-- Analytics Charts -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        <!-- 5.2 Trend per bulan -->
        <div class="card p-4 lg:col-span-2">
            <h3 class="text-sm font-semibold text-gray-800 mb-4 flex items-center gap-2">
                <i class="fas fa-chart-line text-xs text-blue-500"></i>Trend Defect per Bulan
            </h3>
            <div style="position:relative;height:250px;"><canvas id="trendChart"></canvas></div>
        </div>
        <!-- 5.3 Top Reasons -->
        <div class="card overflow-hidden">
            <div class="px-4 pt-4 pb-2">
                <h3 class="text-sm font-semibold text-gray-800 flex items-center gap-2">
                    <i class="fas fa-ranking-star text-xs text-amber-500"></i>Top Alasan
                </h3>
            </div>
            <div class="px-4 pb-4">
                @foreach($topReasons as $i => $r)
                <div class="flex items-center gap-3 mb-2.5">
                    <span class="text-[10px] font-bold text-gray-400 w-4 text-right">{{ $i + 1 }}</span>
                    <div class="flex-1">
                        <div class="flex justify-between text-xs mb-1">
                            <span class="font-medium text-gray-700 truncate" style="max-width:120px;">{{ $r->reason }}</span>
                            <span class="text-gray-500">{{ $r->count }} <span class="text-gray-400">({{ $r->percentage }}%)</span></span>
                        </div>
                        <div class="h-1.5 rounded-full bg-gray-100">
                            <div class="h-1.5 rounded-full" style="background:linear-gradient(90deg,#f59e0b,#fbbf24);width:{{ $r->percentage }}%;"></div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- 5.1 Defect Rate per Paper Type -->
    <div class="card p-4">
        <h3 class="text-sm font-semibold text-gray-800 mb-4 flex items-center gap-2">
            <i class="fas fa-chart-bar text-xs text-purple-500"></i>Defect Rate per Paper Type
        </h3>
        <div style="position:relative;height:240px;"><canvas id="paperDefectChart"></canvas></div>
    </div>

    <!-- Filters -->
    <div class="card overflow-hidden">
        <button onclick="document.getElementById('defectFilters').classList.toggle('hidden')"
                class="w-full px-4 py-3 flex items-center justify-between text-left hover:bg-gray-50 transition">
            <span class="text-xs font-semibold uppercase tracking-wide text-gray-500 flex items-center gap-2">
                <i class="fas fa-filter"></i> Filter
            </span>
            <i class="fas fa-chevron-down text-xs text-gray-400 transition-transform"></i>
        </button>
        <div id="defectFilters" class="hidden">
            <div class="px-4 pb-4 pt-1">
                <form method="GET" action="{{ route('defects.index') }}" id="filterForm">
                    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3">
                        <div class="col-span-2 md:col-span-1">
                            <label class="block text-[10px] font-semibold uppercase tracking-wide mb-1.5 text-gray-400">Search</label>
                            <div class="flex gap-1.5">
                                <input type="text" name="search" class="input-field" placeholder="Lot ID, Rew ID..." value="{{ request('search') }}">
                                <button class="btn btn-primary flex-shrink-0" type="submit" style="padding:8px 12px;"><i class="fas fa-search text-xs"></i></button>
                            </div>
                        </div>
                        <div>
                            <label class="block text-[10px] font-semibold uppercase tracking-wide mb-1.5 text-gray-400">Tahun</label>
                            <select name="year" class="select-field w-full" onchange="document.getElementById('filterForm').submit()">
                                <option value="">Semua</option>
                                @foreach($years as $y)<option value="{{ $y }}" {{ request('year') == $y ? 'selected' : '' }}>{{ $y }}</option>@endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-[10px] font-semibold uppercase tracking-wide mb-1.5 text-gray-400">Alasan</label>
                            <select name="reason" class="select-field w-full" onchange="document.getElementById('filterForm').submit()">
                                <option value="">Semua</option>
                                @foreach($reasons as $r)<option value="{{ $r }}" {{ request('reason') == $r ? 'selected' : '' }}>{{ $r }}</option>@endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-[10px] font-semibold uppercase tracking-wide mb-1.5 text-gray-400">Paper Type</label>
                            <select name="paper_type" class="select-field w-full" onchange="document.getElementById('filterForm').submit()">
                                <option value="">Semua</option>
                                @foreach($paperTypes as $pt)<option value="{{ $pt }}" {{ request('paper_type') == $pt ? 'selected' : '' }}>{{ $pt }}</option>@endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-[10px] font-semibold uppercase tracking-wide mb-1.5 text-gray-400">Bulan</label>
                            <div class="flex gap-1.5">
                                <select name="month" class="select-field flex-1" onchange="document.getElementById('filterForm').submit()">
                                    <option value="">Semua</option>
                                    @foreach($months as $m)<option value="{{ $m }}" {{ request('month') == $m ? 'selected' : '' }}>{{ $m }}</option>@endforeach
                                </select>
                                <a href="{{ route('defects.index') }}" class="btn btn-ghost flex-shrink-0" style="padding:8px 10px;" title="Reset"><i class="fas fa-times text-xs"></i></a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @if(request('search') || request('year') || request('reason') || request('paper_type') || request('month'))
        <script>document.getElementById('defectFilters').classList.remove('hidden');</script>
    @endif

    <!-- Result info -->
    <div class="flex items-center justify-between text-xs text-gray-400">
        <span><i class="fas fa-list mr-1"></i>{{ number_format($defects->total()) }} items</span>
        <span class="hidden sm:inline">Hal. {{ $defects->currentPage() }}/{{ $defects->lastPage() }}</span>
    </div>

    <!-- DESKTOP TABLE -->
    <div class="hidden md:block card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width:35px;">#</th>
                        <th>Tahun</th>
                        <th>Lot ID</th>
                        <th>Rew ID</th>
                        <th>Paper</th>
                        <th>GSM</th>
                        <th>Width</th>
                        <th>Alasan</th>
                        <th>Tanggal</th>
                        <th>Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($defects as $i => $def)
                    <tr>
                        <td class="text-gray-400">{{ ($defects->currentPage()-1)*$defects->perPage()+$i+1 }}</td>
                        <td><span class="tag tag-gray">{{ $def->year }}</span></td>
                        <td>
                            @if($def->lot_id)
                                <a href="{{ route('items.index', ['search' => $def->lot_id]) }}" class="font-semibold text-blue-500 no-underline hover:underline">{{ $def->lot_id }}</a>
                            @else
                                <span class="text-gray-300">-</span>
                            @endif
                        </td>
                        <td>{{ $def->rew_id ?? '-' }}</td>
                        <td>{{ $def->paper_type ?? '-' }}</td>
                        <td>{{ $def->gsm ?? '-' }}</td>
                        <td>{{ $def->width ?? '-' }}</td>
                        <td><span class="tag tag-red">{{ $def->reason }}</span></td>
                        <td>{{ $def->defect_date ?? '-' }}</td>
                        <td class="truncate" style="max-width:150px;">{{ $def->keterangan ?? '-' }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10" class="text-center py-10">
                            <i class="fas fa-check-circle text-2xl mb-2 block text-green-400"></i>
                            <span class="text-gray-400">Tidak ada defect</span>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- MOBILE CARDS -->
    <div class="md:hidden">
        @forelse($defects as $def)
        <div class="mobile-card" @if($def->lot_id) onclick="window.location='{{ route('items.index', ['search' => $def->lot_id]) }}'" style="cursor:pointer;" @endif>
            <div class="flex items-center justify-between mb-2">
                @if($def->lot_id)
                    <span class="font-semibold text-sm text-blue-500"><i class="fas fa-barcode mr-1 text-xs"></i>{{ $def->lot_id }}</span>
                @else
                    <span class="text-xs text-gray-400">No Lot ID</span>
                @endif
                <span class="tag tag-gray">{{ $def->year }}</span>
            </div>
            <div class="flex justify-between text-xs mb-1.5">
                <span class="text-gray-400">Alasan</span>
                <span class="tag tag-red">{{ $def->reason }}</span>
            </div>
            <div class="flex justify-between text-xs mb-1.5">
                <span class="text-gray-400">Paper</span>
                <span class="text-gray-600">{{ $def->paper_type ?? '-' }} / {{ $def->gsm ?? '-' }} GSM</span>
            </div>
            <div class="flex justify-between text-xs">
                <span class="text-gray-400">Tanggal</span>
                <span class="text-gray-600">{{ $def->defect_date ?? '-' }}</span>
            </div>
            @if($def->keterangan)
            <div class="flex justify-between text-xs mt-1.5">
                <span class="text-gray-400">Ket</span>
                <span class="truncate text-right text-gray-600" style="max-width:60%;">{{ Str::limit($def->keterangan, 40) }}</span>
            </div>
            @endif
        </div>
        @empty
        <div class="text-center py-10">
            <i class="fas fa-check-circle text-2xl mb-2 block text-green-400"></i>
            <span class="text-gray-400">Tidak ada defect</span>
        </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="flex items-center justify-between text-xs text-gray-400">
        <span class="hidden sm:inline">
            {{ ($defects->currentPage()-1)*$defects->perPage()+1 }}-{{ min($defects->currentPage()*$defects->perPage(), $defects->total()) }} dari {{ number_format($defects->total()) }}
        </span>
        <div class="ml-auto">{{ $defects->links('vendor.pagination.custom') }}</div>
    </div>
</div>
@endsection

@push('scripts')
<script>
Chart.defaults.color = '#64748b';
Chart.defaults.font.family = 'Inter';
Chart.defaults.font.size = 11;
const gridColor = '#f1f5f9';

// 5.2 Trend per bulan
const trendLabels = [{!! $defectTrend->map(fn($d) => '"'.substr($d->month,0,3).' ".$d->year."\'')->join(',') !!}];
const trendData = [{!! $defectTrend->pluck('count')->join(',') !!}];

new Chart(document.getElementById('trendChart'), {
    type: 'line',
    data: {
        labels: trendLabels,
        datasets: [{
            label: 'Defects',
            data: trendData,
            borderColor: '#3b82f6',
            backgroundColor: 'rgba(59,130,246,0.08)',
            borderWidth: 2.5,
            fill: true,
            tension: 0.4,
            pointBackgroundColor: '#3b82f6',
            pointBorderColor: '#fff',
            pointBorderWidth: 2,
            pointRadius: 5,
            pointHoverRadius: 7,
        }]
    },
    options: {
        responsive: true, maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: {
            y: { beginAtZero: true, grid: { color: gridColor }, border: { display: false }, ticks: { padding: 8 } },
            x: { grid: { display: false }, border: { display: false }, ticks: { padding: 6 } }
        }
    }
});

// 5.1 Defect rate per paper type
const paperLabels = [{!! $defectByPaper->map(fn($d) => '"'.addslashes($d->paper_type).'"')->join(',') !!}];
const paperDefectCounts = [{!! $defectByPaper->pluck('defect_count')->join(',') !!}];
const paperRollCounts = [{!! $defectByPaper->map(fn($d) => $rollByPaper[$d->paper_type]->roll_count ?? 0)->join(',') !!}];

new Chart(document.getElementById('paperDefectChart'), {
    type: 'bar',
    data: {
        labels: paperLabels,
        datasets: [
            {
                label: 'Defect',
                data: paperDefectCounts,
                backgroundColor: 'rgba(239,68,68,0.6)',
                borderColor: '#ef4444',
                borderWidth: 1,
                borderRadius: 6,
                borderSkipped: false,
            },
            {
                label: 'Total Rolls',
                data: paperRollCounts,
                backgroundColor: 'rgba(59,130,246,0.2)',
                borderColor: '#93c5fd',
                borderWidth: 1,
                borderRadius: 6,
                borderSkipped: false,
            }
        ]
    },
    options: {
        responsive: true, maintainAspectRatio: false,
        plugins: { legend: { position: 'top', labels: { padding: 14, usePointStyle: true, pointStyleWidth: 8, boxWidth: 12 } } },
        scales: {
            y: { beginAtZero: true, grid: { color: gridColor }, border: { display: false }, ticks: { padding: 8 } },
            x: { grid: { display: false }, border: { display: false }, ticks: { padding: 6 } }
        }
    }
});
</script>
@endpush
