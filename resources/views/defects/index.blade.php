@extends('layouts.app')

@section('title', 'Barang Bermasalah')
@section('page-title', '<i class="fas fa-exclamation-triangle mr-1"></i> Barang Bermasalah')

@section('content')
<!-- Stats Row -->
<div class="row mb-3">
    <div class="col-xl-3 col-md-6 mb-3">
        <div class="card border-left-danger shadow stat-card">
            <div class="card-body py-2">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Total Defects</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-100">{{ number_format($totalDefects) }}</div>
                    </div>
                    <div class="col-auto"><i class="fas fa-bug fa-2x text-gray-500"></i></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-3">
        <div class="card border-left-warning shadow stat-card">
            <div class="card-body py-2">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Tahun 2025</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-100">{{ number_format($defect2025) }}</div>
                    </div>
                    <div class="col-auto"><i class="fas fa-calendar-alt fa-2x text-gray-500"></i></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-3">
        <div class="card border-left-info shadow stat-card">
            <div class="card-body py-2">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Tahun 2026</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-100">{{ number_format($defect2026) }}</div>
                    </div>
                    <div class="col-auto"><i class="fas fa-calendar-check fa-2x text-gray-500"></i></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-3">
        <div class="card border-left-primary shadow stat-card">
            <div class="card-body py-2">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Ditampilkan</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-100">{{ number_format($defects->total()) }}</div>
                    </div>
                    <div class="col-auto"><i class="fas fa-filter fa-2x text-gray-500"></i></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="card shadow mb-3">
    <a class="card-header py-2 d-flex align-items-center justify-content-between text-decoration-none" data-toggle="collapse" href="#defectFilters" style="cursor:pointer;">
        <h6 class="m-0 font-weight-bold text-primary" style="font-size:0.85rem;"><i class="fas fa-filter mr-1"></i>Filter</h6>
        <i class="fas fa-chevron-down text-gray-500" style="font-size:0.75rem;"></i>
    </a>
    <div class="collapse {{ (request('search') || request('year') || request('reason') || request('paper_type')) ? 'show' : '' }}" id="defectFilters">
        <div class="card-body pt-2 pb-3">
            <form method="GET" action="{{ route('defects.index') }}" id="filterForm">
                <div class="row filter-row">
                    <div class="col-lg-3 col-md-6 mb-2">
                        <label>Search</label>
                        <div class="input-group">
                            <input type="text" name="search" class="form-control form-control-sm" placeholder="Lot ID, Rew ID..." value="{{ request('search') }}">
                            <div class="input-group-append"><button class="btn btn-primary btn-sm" type="submit"><i class="fas fa-search"></i></button></div>
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-4 mb-2">
                        <label>Tahun</label>
                        <select name="year" class="form-control form-control-sm custom-select" onchange="document.getElementById('filterForm').submit()">
                            <option value="">Semua</option>
                            @foreach($years as $y)<option value="{{ $y }}" {{ request('year') == $y ? 'selected' : '' }}>{{ $y }}</option>@endforeach
                        </select>
                    </div>
                    <div class="col-lg-2 col-md-4 mb-2">
                        <label>Alasan</label>
                        <select name="reason" class="form-control form-control-sm custom-select" onchange="document.getElementById('filterForm').submit()">
                            <option value="">Semua</option>
                            @foreach($reasons as $r)<option value="{{ $r }}" {{ request('reason') == $r ? 'selected' : '' }}>{{ $r }}</option>@endforeach
                        </select>
                    </div>
                    <div class="col-lg-2 col-md-4 mb-2">
                        <label>Paper Type</label>
                        <select name="paper_type" class="form-control form-control-sm custom-select" onchange="document.getElementById('filterForm').submit()">
                            <option value="">Semua</option>
                            @foreach($paperTypes as $pt)<option value="{{ $pt }}" {{ request('paper_type') == $pt ? 'selected' : '' }}>{{ $pt }}</option>@endforeach
                        </select>
                    </div>
                    <div class="col-lg-2 col-md-4 mb-2">
                        <label>Bulan</label>
                        <select name="month" class="form-control form-control-sm custom-select" onchange="document.getElementById('filterForm').submit()">
                            <option value="">Semua</option>
                            @foreach($months as $m)<option value="{{ $m }}" {{ request('month') == $m ? 'selected' : '' }}>{{ $m }}</option>@endforeach
                        </select>
                    </div>
                    <div class="col-lg-1 col-md-2 mb-2 d-flex align-items-end">
                        <a href="{{ route('defects.index') }}" class="btn btn-outline-secondary btn-sm btn-block"><i class="fas fa-times"></i></a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Result info -->
<div class="d-flex justify-content-between align-items-center mb-2" style="font-size:0.8rem;">
    <span class="text-gray-500"><i class="fas fa-list mr-1"></i> {{ number_format($defects->total()) }} items</span>
    <span class="text-gray-500">Hal. {{ $defects->currentPage() }}/{{ $defects->lastPage() }}</span>
</div>

<!-- DESKTOP TABLE -->
<div class="desktop-table">
    <div class="card shadow">
        <div class="card-body p-2">
            <div class="table-responsive">
                <table class="table table-bordered table-hover mb-0" style="font-size:0.8rem;">
                    <thead>
                        <tr>
                            <th>#</th>
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
                            <td class="text-gray-500">{{ ($defects->currentPage()-1)*$defects->perPage()+$i+1 }}</td>
                            <td><span class="badge badge-secondary">{{ $def->year }}</span></td>
                            <td>
                                @if($def->lot_id)
                                    <a href="{{ route('items.index', ['search' => $def->lot_id]) }}" class="text-primary font-weight-bold">{{ $def->lot_id }}</a>
                                @else <span class="text-gray-500">-</span> @endif
                            </td>
                            <td>{{ $def->rew_id ?? '-' }}</td>
                            <td>{{ $def->paper_type ?? '-' }}</td>
                            <td>{{ $def->gsm ?? '-' }}</td>
                            <td>{{ $def->width ?? '-' }}</td>
                            <td><span class="badge badge-danger">{{ $def->reason }}</span></td>
                            <td>{{ $def->defect_date ?? '-' }}</td>
                            <td class="truncate" style="max-width:150px;">{{ $def->keterangan ?? '-' }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="10" class="text-center text-gray-500 py-4"><i class="fas fa-check-circle fa-2x mb-2" style="color:var(--accent-green);"></i><br>Tidak ada defect</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- MOBILE CARDS -->
<div class="mobile-cards">
    @forelse($defects as $def)
    <div class="mobile-item-card" @if($def->lot_id) onclick="window.location='{{ route('items.index', ['search' => $def->lot_id]) }}'" style="cursor:pointer;" @endif>
        <div class="d-flex justify-content-between align-items-center mb-1">
            @if($def->lot_id)
                <span class="lot-id" style="margin-bottom:0;"><i class="fas fa-barcode mr-1"></i>{{ $def->lot_id }}</span>
            @else
                <span class="text-gray-500">No Lot ID</span>
            @endif
            <span class="badge badge-secondary">{{ $def->year }}</span>
        </div>
        <div class="info-row"><span class="info-label">Alasan</span><span class="info-value"><span class="badge badge-danger">{{ $def->reason }}</span></span></div>
        <div class="info-row"><span class="info-label">Paper</span><span class="info-value">{{ $def->paper_type ?? '-' }} / {{ $def->gsm ?? '-' }} GSM</span></div>
        <div class="info-row"><span class="info-label">Tanggal</span><span class="info-value">{{ $def->defect_date ?? '-' }}</span></div>
        @if($def->keterangan)<div class="info-row"><span class="info-label">Ket</span><span class="info-value truncate" style="max-width:60%;">{{ Str::limit($def->keterangan, 40) }}</span></div>@endif
    </div>
    @empty
    <div class="text-center text-gray-500 py-4"><i class="fas fa-check-circle fa-2x mb-2" style="color:var(--accent-green);"></i><br>Tidak ada defect</div>
    @endforelse
</div>

<!-- Pagination -->
<div class="d-flex justify-content-between align-items-center mt-3" style="font-size:0.8rem;">
    <span class="text-gray-500 d-none d-sm-block">
        {{ ($defects->currentPage()-1)*$defects->perPage()+1 }} - {{ min($defects->currentPage()*$defects->perPage(), $defects->total()) }} dari {{ number_format($defects->total()) }}
    </span>
    <div class="ml-auto">{{ $defects->links('vendor.pagination.custom') }}</div>
</div>
@endsection
