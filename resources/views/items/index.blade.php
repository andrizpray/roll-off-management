@extends('layouts.app')

@section('title', 'Roll Items')
@section('page-title', '<i class="fas fa-box mr-2"></i> Roll Items')

@section('content')
<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-100">Inventory Roll Items</h1>
    <span class="badge badge-primary p-2">{{ number_format($items->total()) }} items</span>
</div>

<!-- Filters -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-filter mr-2"></i>Filter & Search</h6>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('items.index') }}" id="filterForm">
            <div class="row">
                <!-- Search -->
                <div class="col-lg-3 col-md-6 mb-3">
                    <label class="text-gray-500 text-xs font-weight-bold">Search (LotID, ItemID, Desc)</label>
                    <div class="input-group">
                        <input type="text" name="search" class="form-control" placeholder="Cari..." value="{{ request('search') }}">
                        <div class="input-group-append">
                            <button class="btn btn-primary" type="submit">
                                <i class="fas fa-search fa-sm"></i>
                            </button>
                        </div>
                    </div>
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

                <!-- GSM -->
                <div class="col-lg-2 col-md-4 mb-3">
                    <label class="text-gray-500 text-xs font-weight-bold">GSM</label>
                    <select name="gsm" class="form-control custom-select">
                        <option value="">Semua</option>
                        @foreach($gsms as $g)
                            <option value="{{ $g }}" {{ request('gsm') == $g ? 'selected' : '' }}>{{ $g }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Width -->
                <div class="col-lg-2 col-md-4 mb-3">
                    <label class="text-gray-500 text-xs font-weight-bold">Width</label>
                    <select name="width" class="form-control custom-select">
                        <option value="">Semua</option>
                        @foreach($widths as $w)
                            <option value="{{ $w }}" {{ request('width') == $w ? 'selected' : '' }}>{{ $w }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Location -->
                <div class="col-lg-2 col-md-4 mb-3">
                    <label class="text-gray-500 text-xs font-weight-bold">Lokasi</label>
                    <select name="location" class="form-control custom-select">
                        <option value="">Semua</option>
                        @foreach($locations as $loc)
                            <option value="{{ $loc }}" {{ request('location') == $loc ? 'selected' : '' }}>{{ $loc }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Status -->
                <div class="col-lg-2 col-md-4 mb-3">
                    <label class="text-gray-500 text-xs font-weight-bold">Status</label>
                    <select name="status" class="form-control custom-select">
                        <option value="">Semua</option>
                        @foreach($statuses as $st)
                            <option value="{{ $st }}" {{ request('status') == $st ? 'selected' : '' }}>{{ $st }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Sort + Clear -->
            <div class="row mt-2">
                <div class="col-lg-4 col-md-6 mb-2">
                    <label class="text-gray-500 text-xs font-weight-bold">Sort By</label>
                    <select name="sort" class="form-control custom-select" onchange="document.getElementById('filterForm').submit()">
                        <option value="created_at" {{ request('sort') == 'created_at' ? 'selected' : '' }}>Tanggal Input</option>
                        <option value="lot_id" {{ request('sort') == 'lot_id' ? 'selected' : '' }}>Lot ID</option>
                        <option value="paper_type" {{ request('sort') == 'paper_type' ? 'selected' : '' }}>Paper Type</option>
                        <option value="gsm" {{ request('sort') == 'gsm' ? 'selected' : '' }}>GSM</option>
                        <option value="width" {{ request('sort') == 'width' ? 'selected' : '' }}>Width</option>
                        <option value="location_id" {{ request('sort') == 'location_id' ? 'selected' : '' }}>Lokasi</option>
                        <option value="end_qty" {{ request('sort') == 'end_qty' ? 'selected' : '' }}>End Qty</option>
                    </select>
                </div>
                <div class="col-lg-2 col-md-3 mb-2 d-flex align-items-end">
                    <button type="button" class="btn btn-outline-secondary btn-block" onclick="window.location.href='{{ route('items.index') }}'">
                        <i class="fas fa-times mr-1"></i> Reset
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Results Table -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">
            <i class="fas fa-table mr-2"></i>Hasil ({{ number_format($items->total()) }} items)
            <small class="text-gray-500 ml-2">Halaman {{ $items->currentPage() }} dari {{ $items->lastPage() }}</small>
        </h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Lot ID</th>
                        <th>Item ID</th>
                        <th>Description</th>
                        <th>Paper Type</th>
                        <th>GSM</th>
                        <th>Plybond</th>
                        <th>Width</th>
                        <th>End Qty</th>
                        <th>Lokasi</th>
                        <th>Status</th>
                        <th>Tanggal</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($items as $i => $item)
                    <tr>
                        <td>{{ ($items->currentPage() - 1) * $items->perPage() + $i + 1 }}</td>
                        <td>
                            <a href="{{ route('items.show', $item->id) }}" class="text-primary font-weight-bold">
                                {{ $item->lot_id }}
                            </a>
                        </td>
                        <td>{{ $item->item_id }}</td>
                        <td>
                            <small class="text-gray-500" title="{{ $item->description }}">
                                {{ Str::limit($item->description, 40) }}
                            </small>
                        </td>
                        <td>
                            @if($item->paper_type)
                                <span class="badge badge-secondary">{{ $item->paper_type }}</span>
                            @else
                                <span class="text-gray-500">-</span>
                            @endif
                        </td>
                        <td>{{ $item->gsm ?? '-' }}</td>
                        <td>{{ $item->plybond ?? '-' }}</td>
                        <td>{{ $item->width ?? '-' }}</td>
                        <td class="font-weight-bold">{{ number_format($item->end_qty) }}</td>
                        <td>
                            <small class="text-gray-400">{{ $item->location_id ?? '-' }}</small>
                        </td>
                        <td>
                            @php
                                $badgeClass = 'badge-na';
                                $status = strtolower($item->status_barang ?? '');
                                if($status == 'good') $badgeClass = 'badge-good';
                                elseif(in_array($status, ['hold', 'pending'])) $badgeClass = 'badge-hold';
                                elseif(in_array($status, ['reject', 'problem', 'rusak'])) $badgeClass = 'badge-problem';
                            @endphp
                            @if($item->status_barang && $item->status_barang != '-')
                                <span class="badge {{ $badgeClass }}">{{ $item->status_barang }}</span>
                            @else
                                <span class="text-gray-500">-</span>
                            @endif
                        </td>
                        <td><small class="text-gray-500">{{ $item->tr_date ? Carbon\Carbon::parse($item->tr_date)->format('d M Y') : '-' }}</small></td>
                        <td>
                            <a href="{{ route('items.show', $item->id) }}" class="btn btn-sm btn-outline-primary" title="Detail">
                                <i class="fas fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="13" class="text-center text-gray-500 py-4">
                            <i class="fas fa-inbox fa-2x mb-2"></i><br>
                            Tidak ada data ditemukan
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="d-flex justify-content-between align-items-center mt-3">
            <small class="text-gray-500">
                Menampilkan {{ ($items->currentPage() - 1) * $items->perPage() + 1 }} - {{ min($items->currentPage() * $items->perPage(), $items->total()) }} dari {{ number_format($items->total()) }}
            </small>
            {{ $items->links('vendor.pagination.custom') }}
        </div>
    </div>
</div>
@endsection
