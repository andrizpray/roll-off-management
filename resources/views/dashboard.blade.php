@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', '<i class="fas fa-tachometer-alt mr-1"></i> Dashboard')

@section('content')
<!-- Stats Cards -->
<div class="tw-grid tw-grid-cols-2 md:tw-grid-cols-4 tw-gap-3 tw-mb-5">
    <!-- Total Rolls -->
    <a href="{{ route('items.index') }}" class="tw-no-underline">
        <div class="tw-rounded-xl tw-p-4 tw-transition-all tw-duration-200 hover:tw-scale-[1.02] hover:tw-shadow-lg"
             style="background: linear-gradient(135deg, rgba(88,166,255,0.15) 0%, rgba(88,166,255,0.05) 100%); border: 1px solid rgba(88,166,255,0.2);">
            <div class="tw-flex tw-items-center tw-justify-between tw-mb-2">
                <span class="tw-text-xs tw-font-bold tw-uppercase" style="color: var(--accent-blue);">Total Rolls</span>
                <div class="tw-w-8 tw-h-8 tw-rounded-lg tw-flex tw-items-center tw-justify-center" style="background: rgba(88,166,255,0.15);">
                    <i class="fas fa-boxes-stacked" style="color: var(--accent-blue); font-size: 0.8rem;"></i>
                </div>
            </div>
            <div class="tw-text-2xl tw-font-extrabold" style="color: var(--text-primary);">{{ number_format($totalRolls) }}</div>
            <div class="tw-text-xs tw-mt-1" style="color: var(--text-dim);">inventory items</div>
        </div>
    </a>

    <!-- Total Defects -->
    <a href="{{ route('defects.index') }}" class="tw-no-underline">
        <div class="tw-rounded-xl tw-p-4 tw-transition-all tw-duration-200 hover:tw-scale-[1.02] hover:tw-shadow-lg"
             style="background: linear-gradient(135deg, rgba(248,81,73,0.15) 0%, rgba(248,81,73,0.05) 100%); border: 1px solid rgba(248,81,73,0.2);">
            <div class="tw-flex tw-items-center tw-justify-between tw-mb-2">
                <span class="tw-text-xs tw-font-bold tw-uppercase" style="color: var(--accent-red);">Defects</span>
                <div class="tw-w-8 tw-h-8 tw-rounded-lg tw-flex tw-items-center tw-justify-center" style="background: rgba(248,81,73,0.15);">
                    <i class="fas fa-exclamation-triangle" style="color: var(--accent-red); font-size: 0.8rem;"></i>
                </div>
            </div>
            <div class="tw-text-2xl tw-font-extrabold" style="color: var(--text-primary);">{{ number_format($totalDefects) }}</div>
            <div class="tw-text-xs tw-mt-1" style="color: var(--text-dim);">
                {{ $totalRolls > 0 ? number_format(($totalDefects/$totalRolls)*100, 2) : '0' }}% defect rate
            </div>
        </div>
    </a>

    <!-- With Location -->
    <div class="tw-rounded-xl tw-p-4 tw-transition-all tw-duration-200 hover:tw-shadow-lg"
         style="background: linear-gradient(135deg, rgba(35,134,54,0.15) 0%, rgba(35,134,54,0.05) 100%); border: 1px solid rgba(35,134,54,0.2);">
        <div class="tw-flex tw-items-center tw-justify-between tw-mb-2">
            <span class="tw-text-xs tw-font-bold tw-uppercase" style="color: var(--accent-green);">Dilokasi</span>
            <div class="tw-w-8 tw-h-8 tw-rounded-lg tw-flex tw-items-center tw-justify-center" style="background: rgba(35,134,54,0.15);">
                <i class="fas fa-map-marker-alt" style="color: var(--accent-green); font-size: 0.8rem;"></i>
            </div>
        </div>
        <div class="tw-text-2xl tw-font-extrabold" style="color: var(--text-primary);">{{ number_format($totalRolls - $noLocationCount) }}</div>
        <div class="tw-text-xs tw-mt-1" style="color: var(--text-dim);">{{ number_format($noLocationCount) }} belum terlokasi</div>
    </div>

    <!-- Lokasi Unik -->
    <div class="tw-rounded-xl tw-p-4 tw-transition-all tw-duration-200 hover:tw-shadow-lg"
         style="background: linear-gradient(135deg, rgba(57,210,192,0.15) 0%, rgba(57,210,192,0.05) 100%); border: 1px solid rgba(57,210,192,0.2);">
        <div class="tw-flex tw-items-center tw-justify-between tw-mb-2">
            <span class="tw-text-xs tw-font-bold tw-uppercase" style="color: var(--accent-teal);">Lokasi Unik</span>
            <div class="tw-w-8 tw-h-8 tw-rounded-lg tw-flex tw-items-center tw-justify-center" style="background: rgba(57,210,192,0.15);">
                <i class="fas fa-sitemap" style="color: var(--accent-teal); font-size: 0.8rem;"></i>
            </div>
        </div>
        <div class="tw-text-2xl tw-font-extrabold" style="color: var(--text-primary);">{{ count($locationRekap) }}</div>
        <div class="tw-text-xs tw-mt-1" style="color: var(--text-dim);">lokasi berbeda</div>
    </div>
</div>

<!-- Charts -->
<div class="tw-grid tw-grid-cols-1 md:tw-grid-cols-2 tw-gap-4 tw-mb-5">
    <div class="card">
        <div class="card-header tw-py-2 tw-px-4">
            <h6 class="m-0 tw-font-bold" style="color: var(--accent-blue); font-size: 0.82rem;"><i class="fas fa-chart-bar mr-1"></i>Distribusi Paper Type</h6>
        </div>
        <div class="card-body"><div class="chart-container"><canvas id="paperTypeChart"></canvas></div></div>
    </div>
    <div class="card">
        <div class="card-header tw-py-2 tw-px-4">
            <h6 class="m-0 tw-font-bold" style="color: var(--accent-blue); font-size: 0.82rem;"><i class="fas fa-map-marker-alt mr-1"></i>Top Lokasi Rekap</h6>
        </div>
        <div class="card-body"><div class="chart-container"><canvas id="locationChart"></canvas></div></div>
    </div>
    <div class="card">
        <div class="card-header tw-py-2 tw-px-4">
            <h6 class="m-0 tw-font-bold" style="color: var(--accent-blue); font-size: 0.82rem;"><i class="fas fa-ruler mr-1"></i>Distribusi GSM</h6>
        </div>
        <div class="card-body"><div class="chart-container"><canvas id="gsmChart"></canvas></div></div>
    </div>
    <div class="card">
        <div class="card-header tw-py-2 tw-px-4">
            <h6 class="m-0 tw-font-bold" style="color: var(--accent-blue); font-size: 0.82rem;"><i class="fas fa-bug mr-1"></i>Defect per Alasan</h6>
        </div>
        <div class="card-body"><div class="chart-container"><canvas id="defectChart"></canvas></div></div>
    </div>
</div>

<!-- Tables -->
<div class="tw-grid tw-grid-cols-1 md:tw-grid-cols-2 tw-gap-4 tw-mb-4">
    <!-- Top Lokasi -->
    <div class="card">
        <div class="card-header tw-py-2 tw-px-4">
            <h6 class="m-0 tw-font-bold" style="color: var(--accent-blue); font-size: 0.82rem;"><i class="fas fa-warehouse mr-1"></i>Top Lokasi</h6>
        </div>
        <div class="card-body tw-p-2">
            <div class="table-responsive">
                <table class="table table-bordered table-hover mb-0" style="font-size: 0.78rem;">
                    <thead><tr><th>#</th><th>Lokasi</th><th>Jumlah</th><th style="min-width:90px;">%</th></tr></thead>
                    <tbody>
                        @foreach($locationRekap as $i => $loc)
                        <tr onclick="window.location='{{ route('items.index', ['search' => $loc->lokasi]) }}'">
                            <td class="text-gray-500">{{ $i + 1 }}</td>
                            <td class="font-weight-bold" style="color: var(--accent-blue);">{{ $loc->lokasi }}</td>
                            <td class="font-weight-bold" style="color: var(--text-primary);">{{ number_format($loc->count) }}</td>
                            <td>
                                <div class="tw-flex tw-items-center tw-gap-2">
                                    <div class="tw-flex-1 tw-h-2 tw-rounded-full" style="background: var(--border);">
                                        <div class="tw-h-2 tw-rounded-full" style="background: var(--accent-blue); width: {{ ($loc->count/$totalRolls)*100 }}%;"></div>
                                    </div>
                                    <span class="text-gray-500" style="font-size: 0.7rem; min-width: 36px;">{{ number_format(($loc->count/$totalRolls)*100, 1) }}%</span>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Status -->
    <div class="card">
        <div class="card-header tw-py-2 tw-px-4">
            <h6 class="m-0 tw-font-bold" style="color: var(--accent-blue); font-size: 0.82rem;"><i class="fas fa-tags mr-1"></i>Status Barang</h6>
        </div>
        <div class="card-body tw-p-2">
            <div class="table-responsive">
                <table class="table table-bordered table-hover mb-0" style="font-size: 0.78rem;">
                    <thead><tr><th>#</th><th>Status</th><th>Jumlah</th><th style="min-width:90px;">%</th></tr></thead>
                    <tbody>
                        @foreach($statusStats as $i => $s)
                        <tr>
                            <td class="text-gray-500">{{ $i + 1 }}</td>
                            <td>
                                @php
                                    $bc = 'badge-na'; $st = strtolower($s->status_barang);
                                    if($st == 'good') $bc = 'badge-good';
                                    elseif(in_array($st, ['hold','pending'])) $bc = 'badge-hold';
                                    elseif(in_array($st, ['reject','problem','rusak'])) $bc = 'badge-problem';
                                @endphp
                                <span class="badge {{ $bc }}">{{ $s->status_barang }}</span>
                            </td>
                            <td class="font-weight-bold" style="color: var(--text-primary);">{{ number_format($s->count) }}</td>
                            <td>
                                <div class="tw-flex tw-items-center tw-gap-2">
                                    <div class="tw-flex-1 tw-h-2 tw-rounded-full" style="background: var(--border);">
                                        <div class="tw-h-2 tw-rounded-full" style="background: var(--accent-green); width: {{ ($s->count/$totalRolls)*100 }}%;"></div>
                                    </div>
                                    <span class="text-gray-500" style="font-size: 0.7rem; min-width: 36px;">{{ number_format(($s->count/$totalRolls)*100, 1) }}%</span>
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
const colors = ['#58a6ff','#238636','#d29922','#f85149','#39d2c0','#bc8cff','#ff7b72','#79c0ff','#56d364','#e3b341'];
const barOpts = {
    responsive: true, maintainAspectRatio: true,
    plugins: { legend: { display: false } },
    scales: {
        y: { ticks: { color: '#8b949e', font: { size: 10 } }, grid: { color: '#21262d' } },
        x: { ticks: { color: '#8b949e', font: { size: 9 }, maxRotation: 45 }, grid: { display: false } }
    }
};
const pieOpts = {
    responsive: true, maintainAspectRatio: true,
    plugins: { legend: { position: 'bottom', labels: { color: '#c9d1d9', padding: 10, font: { size: 9 }, boxWidth: 12 } } }
};

new Chart(document.getElementById('paperTypeChart').getContext('2d'), {
    type: 'bar', data: {
        labels: [{!! $paperTypeStats->map(fn($p) => '"'.addslashes($p->paper_type).'"')->join(',') !!}],
        datasets: [{ data: [{!! $paperTypeStats->pluck('count')->join(',') !!}], backgroundColor: colors, borderWidth: 0, borderRadius: 4 }]
    }, options: barOpts
});

new Chart(document.getElementById('locationChart').getContext('2d'), {
    type: 'bar', data: {
        labels: [{!! collect($locationRekap)->take(10)->map(fn($l) => '"'.addslashes($l->lokasi).'"')->join(',') !!}],
        datasets: [{ data: [{!! collect($locationRekap)->take(10)->pluck('count')->join(',') !!}], backgroundColor: '#39d2c0', borderWidth: 0, borderRadius: 4 }]
    }, options: barOpts
});

new Chart(document.getElementById('gsmChart').getContext('2d'), {
    type: 'bar', data: {
        labels: [{!! $gsmStats->map(fn($g) => '"'.$g->gsm.'"')->join(',') !!}],
        datasets: [{ data: [{!! $gsmStats->pluck('count')->join(',') !!}], backgroundColor: '#bc8cff', borderWidth: 0, borderRadius: 4 }]
    }, options: barOpts
});

new Chart(document.getElementById('defectChart').getContext('2d'), {
    type: 'doughnut', data: {
        labels: [{!! $defectReasonStats->map(fn($d) => '"'.addslashes($d->reason).'"')->join(',') !!}],
        datasets: [{ data: [{!! $defectReasonStats->pluck('count')->join(',') !!}], backgroundColor: colors }]
    }, options: pieOpts
});
</script>
@endpush
