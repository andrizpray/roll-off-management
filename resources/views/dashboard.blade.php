@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', '<i class="fas fa-tachometer-alt mr-2"></i> Dashboard')

@section('content')
<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-100">Dashboard Overview</h1>
</div>

<!-- Stats Row -->
<div class="row">
    <!-- Total Rolls -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Roll Items</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-100">{{ number_format($totalRolls) }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-boxes-stacked fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Total Defects -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-danger shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Total Defects</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-100">{{ number_format($totalDefects) }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Defect Rate -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Defect Rate</div>
                        <div class="row no-gutters align-items-center">
                            <div class="col-auto">
                                <div class="h5 mb-0 mr-3 font-weight-bold text-gray-100">
                                    {{ $totalRolls > 0 ? number_format(($totalDefects / $totalRolls) * 100, 2) : '0.00' }}%
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-percentage fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Locations -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Total Lokasi</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-100">{{ number_format($locationStats->count()) }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-warehouse fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Charts Row -->
<div class="row">
    <!-- Paper Type Distribution (Bar Chart) -->
    <div class="col-xl-6 col-lg-7">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-chart-bar mr-2"></i>Distribusi Paper Type</h6>
            </div>
            <div class="card-body">
                <div class="chart-area">
                    <canvas id="paperTypeChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Location Distribution (Doughnut) -->
    <div class="col-xl-6 col-lg-5">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-chart-pie mr-2"></i>Distribusi Lokasi</h6>
            </div>
            <div class="card-body">
                <div class="chart-pie pt-4 pb-2">
                    <canvas id="locationChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Second Charts Row -->
<div class="row">
    <!-- GSM Distribution -->
    <div class="col-xl-6 col-lg-7">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-chart-bar mr-2"></i>Distribusi GSM</h6>
            </div>
            <div class="card-body">
                <div class="chart-area">
                    <canvas id="gsmChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Defect by Reason -->
    <div class="col-xl-6 col-lg-5">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-chart-pie mr-2"></i>Defect per Alasan</h6>
            </div>
            <div class="card-body">
                <div class="chart-pie pt-4 pb-2">
                    <canvas id="defectChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Top Locations Table -->
<div class="row">
    <div class="col-xl-6 col-lg-6 mb-4">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-warehouse mr-2"></i>Top Lokasi</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Lokasi</th>
                                <th>Jumlah Roll</th>
                                <th>%</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($locationStats->take(10) as $i => $loc)
                            <tr>
                                <td>{{ $i + 1 }}</td>
                                <td>{{ $loc->location_id }}</td>
                                <td class="font-weight-bold">{{ number_format($loc->count) }}</td>
                                <td>
                                    <div class="progress" style="height: 20px;">
                                        <div class="progress-bar bg-primary" role="progressbar" style="width: {{ ($loc->count / $totalRolls) * 100 }}%"></div>
                                    </div>
                                    <small class="text-gray-500">{{ number_format(($loc->count / $totalRolls) * 100, 1) }}%</small>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Status Breakdown -->
    <div class="col-xl-6 col-lg-6 mb-4">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-tags mr-2"></i>Status Barang</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Status</th>
                                <th>Jumlah</th>
                                <th>%</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($statusStats as $i => $s)
                            <tr>
                                <td>{{ $i + 1 }}</td>
                                <td>
                                    @php
                                        $badgeClass = 'badge-na';
                                        if(strtolower($s->status_barang) == 'good') $badgeClass = 'badge-good';
                                        elseif(in_array(strtolower($s->status_barang), ['hold', 'pending'])) $badgeClass = 'badge-hold';
                                        elseif(in_array(strtolower($s->status_barang), ['reject', 'problem', 'rusak'])) $badgeClass = 'badge-problem';
                                    @endphp
                                    <span class="badge {{ $badgeClass }}">{{ $s->status_barang }}</span>
                                </td>
                                <td class="font-weight-bold">{{ number_format($s->count) }}</td>
                                <td>
                                    <div class="progress" style="height: 20px;">
                                        <div class="progress-bar bg-success" role="progressbar" style="width: {{ ($s->count / $totalRolls) * 100 }}%"></div>
                                    </div>
                                    <small class="text-gray-500">{{ number_format(($s->count / $totalRolls) * 100, 1) }}%</small>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
// Colors
const colors = ['#58a6ff', '#238636', '#d29922', '#f85149', '#39d2c0', '#bc8cff', '#ff7b72', '#79c0ff', '#56d364', '#e3b341'];

// Paper Type Bar Chart
const paperCtx = document.getElementById('paperTypeChart').getContext('2d');
new Chart(paperCtx, {
    type: 'bar',
    data: {
        labels: [{!! $paperTypeStats->map(fn($p) => '"' . addslashes($p->paper_type) . '"')->join(',') !!}],
        datasets: [{
            label: 'Jumlah',
            data: [{!! $paperTypeStats->pluck('count')->join(',') !!}],
            backgroundColor: colors,
            borderColor: colors,
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        scales: {
            y: { ticks: { color: '#8b949e' }, grid: { color: '#21262d' } },
            x: { ticks: { color: '#8b949e' }, grid: { color: '#21262d' } }
        },
        plugins: {
            legend: { display: false }
        }
    }
});

// Location Doughnut Chart
const locCtx = document.getElementById('locationChart').getContext('2d');
const locLabels = [{!! $locationStats->take(8)->map(fn($l) => '"' . addslashes($l->location_id) . '"')->join(',') !!}];
const locData = [{!! $locationStats->take(8)->pluck('count')->join(',') !!}];
new Chart(locCtx, {
    type: 'doughnut',
    data: {
        labels: locLabels,
        datasets: [{ data: locData, backgroundColor: colors }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
            legend: { position: 'bottom', labels: { color: '#c9d1d9', padding: 15, font: { size: 11 } } }
        }
    }
});

// GSM Bar Chart
const gsmCtx = document.getElementById('gsmChart').getContext('2d');
new Chart(gsmCtx, {
    type: 'bar',
    data: {
        labels: [{!! $gsmStats->map(fn($g) => '"' . $g->gsm . '"')->join(',') !!}],
        datasets: [{
            label: 'Jumlah',
            data: [{!! $gsmStats->pluck('count')->join(',') !!}],
            backgroundColor: '#39d2c0',
            borderColor: '#39d2c0',
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        scales: {
            y: { ticks: { color: '#8b949e' }, grid: { color: '#21262d' } },
            x: { ticks: { color: '#8b949e' }, grid: { color: '#21262d' } }
        },
        plugins: { legend: { display: false } }
    }
});

// Defect Reason Doughnut
const defCtx = document.getElementById('defectChart').getContext('2d');
const defLabels = [{!! $defectReasonStats->map(fn($d) => '"' . addslashes($d->reason) . '"')->join(',') !!}];
const defData = [{!! $defectReasonStats->pluck('count')->join(',') !!}];
new Chart(defCtx, {
    type: 'doughnut',
    data: {
        labels: defLabels,
        datasets: [{ data: defData, backgroundColor: colors }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
            legend: { position: 'bottom', labels: { color: '#c9d1d9', padding: 15, font: { size: 11 } } }
        }
    }
});
</script>
@endpush
