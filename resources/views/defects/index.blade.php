@extends('layouts.app')

@section('title', 'Barang Bermasalah')
@section('page-title', '<i class="fas fa-exclamation-triangle mr-2"></i> Barang Bermasalah')

@section('content')
<!-- Stats Row -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-danger shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Total Defects</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-100">{{ number_format($totalDefects) }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-bug fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Tahun 2025</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-100">{{ number_format($defect2025) }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-calendar-alt fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Tahun 2026</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-100">{{ number_format($defect2026) }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-calendar-check fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Saat Ini Ditampilkan</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-100">{{ number_format($defects->total()) }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-filter fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-filter mr-2"></i>Filter & Search</h6>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('defects.index') }}" id="filterForm">
            <div class="row">
                <!-- Search -->
                <div class="col-lg-3 col-md-6 mb-3">
                    <label class="text-gray-500 text-xs font-weight-bold">Search (LotID, RewID)</label>
                    <div class="input-group">
                        <input type="text" name="search" class="form-control" placeholder="Cari..." value="{{ request('search') }}">
                        <div class="input-group-append">
                            <button class="btn btn-primary" type="submit">
                                <i class="fas fa-search fa-sm"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Year -->
                <div class="col-lg-2 col-md-4 mb-3">
                    <label class="text-gray-500 text-xs font-weight-bold">Tahun</label>
                    <select name="year" class="form-control custom-select">
                        <option value="">Semua</option>
                        @foreach($years as $y)
                            <option value="{{ $y }}" {{ request('year') == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Reason -->
                <div class="col-lg-2 col-md-4 mb-3">
                    <label class="text-gray-500 text-xs font-weight-bold">Alasan</label>
                    <select name="reason" class="form-control custom-select">
                        <option value="">Semua</option>
                        @foreach($reasons as $r)
                            <option value="{{ $r }}" {{ request('reason') == $r ? 'selected' : '' }}>{{ $r }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Paper Type -->
                <div class="col-lg-2 col-md-4 mb-3">
                    <label class="text-gray-500 text-xs font-weight-bold">Paper Type</label>
                    <select name="paper_type" class="form-control custom-select">
                        <option value="">Semua</option>
                        @foreach($paperTypes as $pt)
                            <option value="{{ $pt }}" {{ request('paper_type') == $pt ? 'selected' : '' }}>{{ $pt }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Month -->
                <div class="col-lg-2 col-md-4 mb-3">
                    <label class="text-gray-500 text-xs font-weight-bold">Bulan</label>
                    <select name="month" class="form-control custom-select">
                        <option value="">Semua</option>
                        @foreach($months as $m)
                            <option value="{{ $m }}" {{ request('month') == $m ? 'selected' : '' }}>{{ $m }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Clear -->
                <div class="col-lg-1 col-md-2 mb-3 d-flex align-items-end">
                    <button type="button" class="btn btn-outline-secondary btn-block" onclick="window.location.href='{{ route('defects.index') }}'" title="Reset">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Defects Table -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">
            <i class="fas fa-exclamation-circle mr-2"></i>Daftar Barang Bermasalah
        </h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Tahun</th>
                        <th>Lot ID</th>
                        <th>Rew ID</th>
                        <th>Paper Type</th>
                        <th>GSM</th>
                        <th>Plybond</th>
                        <th>Width</th>
                        <th>Alasan</th>
                        <th>Kategori</th>
                        <th>Tanggal Defect</th>
                        <th>Bulan</th>
                        <th>TR Type</th>
                        <th>Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($defects as $i => $def)
                    <tr>
                        <td>{{ ($defects->currentPage() - 1) * $defects->perPage() + $i + 1 }}</td>
                        <td><span class="badge badge-secondary">{{ $def->year }}</span></td>
                        <td>
                            @if($def->lot_id)
                                <a href="{{ route('items.index', ['search' => $def->lot_id]) }}" class="text-primary font-weight-bold">
                                    {{ $def->lot_id }}
                                </a>
                            @else
                                <span class="text-gray-500">-</span>
                            @endif
                        </td>
                        <td>{{ $def->rew_id ?? '-' }}</td>
                        <td>
                            @if($def->paper_type)
                                <span class="badge badge-secondary">{{ $def->paper_type }}</span>
                            @else
                                <span class="text-gray-500">-</span>
                            @endif
                        </td>
                        <td>{{ $def->gsm ?? '-' }}</td>
                        <td>{{ $def->plybond ?? '-' }}</td>
                        <td>{{ $def->width ?? '-' }}</td>
                        <td><span class="badge badge-danger">{{ $def->reason }}</span></td>
                        <td>{{ $def->category ?? '-' }}</td>
                        <td>{{ $def->defect_date ?? '-' }}</td>
                        <td>{{ $def->month ?? '-' }}</td>
                        <td>{{ $def->tr_type ?? '-' }}</td>
                        <td class="text-gray-500" style="max-width: 150px;">{{ Str::limit($def->keterangan, 50) }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="14" class="text-center text-gray-500 py-4">
                            <i class="fas fa-check-circle fa-2x mb-2 text-success"></i><br>
                            Tidak ada defect ditemukan
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="d-flex justify-content-between align-items-center mt-3">
            <small class="text-gray-500">
                Menampilkan {{ ($defects->currentPage() - 1) * $defects->perPage() + 1 }} - {{ min($defects->currentPage() * $defects->perPage(), $defects->total()) }} dari {{ number_format($defects->total()) }}
            </small>
            {{ $defects->links('vendor.pagination.custom') }}
        </div>
    </div>
</div>
@endsection
