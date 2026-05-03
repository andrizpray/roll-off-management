@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', '<i class="fas fa-tachometer-alt mr-1"></i> Dashboard')

@section('content')
<!-- Stats Row -->
<div class="row">
    <div class="col-xl-3 col-md-6 mb-3">
        <a href="{{ route('items.index') }}" class="text-decoration-none">
            <div class="card border-left-primary shadow stat-card" style="cursor:pointer; transition: transform 0.15s;" onmouseover="this.style.transform='translateY(-2px)'" onmouseout="this.style.transform='none'">
                <div class="card-body py-2">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Roll Items</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-100">{{ number_format($totalRolls) }}</div>
                        </div>
                        <div class="col-auto"><i class="fas fa-boxes-stacked fa-2x text-gray-500"></i></div>
                    </div>
                </div>
            </div>
        </a>
    </div>
    <div class="col-xl-3 col-md-6 mb-3">
        <a href="{{ route('defects.index') }}" class="text-decoration-none">
            <div class="card border-left-danger shadow stat-card" style="cursor:pointer; transition: transform 0.15s;" onmouseover="this.style.transform='translateY(-2px)'" onmouseout="this.style.transform='none'">
                <div class="card-body py-2">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Total Defects</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-100">{{ number_format($totalDefects) }}</div>
                        </div>
                        <div class="col-auto"><i class="fas fa-exclamation-triangle fa-2x text-gray-500"></i></div>
                    </div>
                </div>
            </div>
        </a>
    </div>
    <div class="col-xl-3 col-md-6 mb-3">
        <div class="card border-left-success shadow stat-card">
            <div class="card-body py-2">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">SO Tertracking</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-100">{{ number_format($soDesemberCount + $soMaretCount) }}</div>
                        <div class="text-xs text-gray-500">Des: {{ number_format($soDesemberCount) }} | Mar: {{ number_format($soMaretCount) }}</div>
                    </div>
                    <div class="col-auto"><i class="fas fa-file-invoice fa-2x text-gray-500"></i></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-3">
        <div class="card border-left-info shadow stat-card">
            <div class="card-body py-2">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Receiving 2026</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-100">{{ number_format($receivingCount) }}</div>
                        <div class="text-xs text-gray-500">{{ number_format($picCount) }} dengan PIC</div>
                    </div>
                    <div class="col-auto"><i class="fas fa-warehouse fa-2x text-gray-500"></i></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Charts Row -->
<div class="row mb-3">
    <div class="col-xl-6 mb-3">
        <div class="card shadow">
            <div class="card-header py-2">
                <h6 class="m-0 font-weight-bold text-primary" style="font-size: 0.85rem;"><i class="fas fa-chart-bar mr-1"></i>Distribusi Paper Type</h6>
            </div>
            <div class="card-body">
                <div class="chart-container"><canvas id="paperTypeChart"></canvas></div>
            </div>
        </div>
    </div>
    <div class="col-xl-6 mb-3">
        <div class="card shadow">
            <div class="card-header py-2">
                <h6 class="m-0 font-weight-bold text-primary" style="font-size: 0.85rem;"><i class="fas fa-warehouse mr-1"></i>Top Lokasi Receiving</h6>
            </div>
            <div class="card-body">
                <div class="chart-container"><canvas id="locationChart"></canvas></div>
            </div>
        </div>
    </div>
</div>

<div class="row mb-3">
    <div class="col-xl-6 mb-3">
        <div class="card shadow">
            <div class="card-header py-2">
                <h6 class="m-0 font-weight-bold text-primary" style="font-size: 0.85rem;"><i class="fas fa-ruler mr-1"></i>Distribusi GSM</h6>
            </div>
            <div class="card-body">
                <div class="chart-container"><canvas id="gsmChart"></canvas></div>
            </div>
        </div>
    </div>
    <div class="col-xl-6 mb-3">
        <div class="card shadow">
            <div class="card-header py-2">
                <h6 class="m-0 font-weight-bold text-primary" style="font-size: 0.85rem;"><i class="fas fa-bug mr-1"></i>Defect per Alasan</h6>
            </div>
            <div class="card-body">
                <div class="chart-container"><canvas id="defectChart"></canvas></div>
            </div>
        </div>
    </div>
</div>

<!-- Tables Row -->
<div class="row">
    <div class="col-xl-6 mb-3">
        <div class="card shadow">
            <div class="card-header py-2">
                <h6 class="m-0 font-weight-bold text-primary" style="font-size: 0.85rem;"><i class="fas fa-map-marker-alt mr-1"></i>Top Lokasi Receiving 2026</h6>
            </div>
            <div class="card-body p-2">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover mb-0" style="font-size: 0.8rem;">
                        <thead><tr><th>#</th><th>Lokasi</th><th>Jumlah</th><th>%</th></tr></thead>
                        <tbody>
                            @foreach($locationStats as $i => $loc)
                            <tr onclick="window.location='{{ route('items.index', ['receiving_2026' => $loc->receiving_2026]) }}'">
                                <td>{{ $i + 1 }}</td>
                                <td class="font-weight-bold text-primary">{{ $loc->receiving_2026 }}</td>
                                <td>{{ number_format($loc->count) }}</td>
                                <td style="min-width:80px;">
                                    <div class="progress mb-1" style="height:14px;">
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
    <div class="col-xl-6 mb-3">
        <div class="card shadow">
            <div class="card-header py-2">
                <h6 class="m-0 font-weight-bold text-primary" style="font-size: 0.85rem;"><i class="fas fa-tags mr-1"></i>Status Barang</h6>
            </div>
            <div class="card-body p-2">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover mb-0" style="font-size: 0.8rem;">
                        <thead><tr><th>#</th><th>Status</th><th>Jumlah</th><th>%</th></tr></thead>
                        <tbody>
                            @foreach($statusStats as $i => $s)
                            <tr>
                                <td>{{ $i + 1 }}</td>
                                <td>
                                    @php
                                        $badgeClass = 'badge-na';
                                        $st = strtolower($s->status_barang);
                                        if($st == 'good') $badgeClass = 'badge-good';
                                        elseif(in_array($st, ['hold','pending'])) $badgeClass = 'badge-hold';
                                        elseif(in_array($st, ['reject','problem','rusak'])) $badgeClass = 'badge-problem';
                                    @endphp
                                    <span class="badge {{ $badgeClass }}">{{ $s->status_barang }}</span>
                                </td>
                                <td class="font-weight-bold">{{ number_format($s->count) }}</td>
                                <td style="min-width:80px;">
                                    <div class="progress mb-1" style="height:14px;">
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
const colors = ['#58a6ff','#238636','#d29922','#f85149','#39d2c0','#bc8cff','#ff7b72','#79c0ff','#56d364','#e3b341'];
const chartOpts = {
    responsive: true, maintainAspectRatio: true,
    plugins: { legend: { display: false } },
    scales: {
        y: { ticks: { color: '#8b949e', font: { size: 11 } }, grid: { color: '#21262d' } },
        x: { ticks: { color: '#8b949e', font: { size: 10 } }, grid: { display: false } }
    }
};
const pieOpts = {
    responsive: true, maintainAspectRatio: true,
    plugins: { legend: { position: 'bottom', labels: { color: '#c9d1d9', padding: 12, font: { size: 10 } } } }
};

new Chart(document.getElementById('paperTypeChart').getContext('2d'), {
    type: 'bar', data: {
        labels: [{!! $paperTypeStats->map(fn($p) => '"'.addslashes($p->paper_type).'"')->join(',') !!}],
        datasets: [{ data: [{!! $paperTypeStats->pluck('count')->join(',') !!}], backgroundColor: colors, borderWidth: 0, borderRadius: 3 }]
    }, options: chartOpts
});

new Chart(document.getElementById('locationChart').getContext('2d'), {
    type: 'bar', data: {
        labels: [{!! $locationStats->take(10)->map(fn($l) => '"'.addslashes($l->receiving_2026).'"')->join(',') !!}],
        datasets: [{ data: [{!! $locationStats->take(10)->pluck('count')->join(',') !!}], backgroundColor: '#39d2c0', borderWidth: 0, borderRadius: 3 }]
    }, options: chartOpts
});

new Chart(document.getElementById('gsmChart').getContext('2d'), {
    type: 'bar', data: {
        labels: [{!! $gsmStats->map(fn($g) => '"'.$g->gsm.'"')->join(',') !!}],
        datasets: [{ data: [{!! $gsmStats->pluck('count')->join(',') !!}], backgroundColor: '#bc8cff', borderWidth: 0, borderRadius: 3 }]
    }, options: chartOpts
});

new Chart(document.getElementById('defectChart').getContext('2d'), {
    type: 'doughnut', data: {
        labels: [{!! $defectReasonStats->map(fn($d) => '"'.addslashes($d->reason).'"')->join(',') !!}],
        datasets: [{ data: [{!! $defectReasonStats->pluck('count')->join(',') !!}], backgroundColor: colors }]
    }, options: pieOpts
});
</script>
@endpush
