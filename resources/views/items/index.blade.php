@extends('layouts.app')

@section('title', 'Roll Items')
@section('page-title', '<i class="fas fa-box mr-1"></i> Roll Items')

@section('content')
<!-- Quick Search Bar -->
<div class="quick-search mb-3">
    <form method="GET" action="{{ route('items.index') }}" class="d-flex align-items-center gap-2 flex-wrap">
        <div class="input-group" style="max-width:400px; flex:1;">
            <div class="input-group-prepend">
                <span class="input-group-text" style="background:var(--bg-card-header);border-color:var(--border);color:var(--text-muted);"><i class="fas fa-search"></i></span>
            </div>
            <input type="text" name="search" class="form-control" placeholder="Cari Lot ID, deskripsi, SO, lokasi..." value="{{ request('search') }}" style="border-radius:0 20px 20px 0;">
        </div>
        <button class="btn btn-primary btn-sm" type="submit" style="border-radius:20px; padding: 6px 16px;">Cari</button>
        @if(request('search') || request('paper_type') || request('gsm') || request('receiving_2026'))
            <a href="{{ route('items.index') }}" class="btn btn-outline-secondary btn-sm" style="border-radius:20px; padding: 6px 16px;"><i class="fas fa-times mr-1"></i>Reset</a>
        @endif
    </form>
</div>

<!-- Advanced Filters (collapsible) -->
<div class="card shadow mb-3">
    <a class="card-header py-2 d-flex align-items-center justify-content-between text-decoration-none" data-toggle="collapse" href="#advancedFilters" style="cursor:pointer;">
        <h6 class="m-0 font-weight-bold text-primary" style="font-size:0.85rem;"><i class="fas fa-sliders-h mr-1"></i>Filter Lanjutan</h6>
        <i class="fas fa-chevron-down text-gray-500" style="font-size:0.75rem;"></i>
    </a>
    <div class="collapse {{ (request('paper_type') || request('gsm') || request('width') || request('receiving_2026') || request('so_desember') || request('status')) ? 'show' : '' }}" id="advancedFilters">
        <div class="card-body pt-2 pb-3">
            <form method="GET" action="{{ route('items.index') }}" id="filterForm">
                @if(request('search'))<input type="hidden" name="search" value="{{ request('search') }}">@endif
                <div class="row filter-row">
                    <div class="col-lg-2 col-md-4 mb-2">
                        <label>Paper Type</label>
                        <select name="paper_type" class="form-control form-control-sm custom-select" onchange="document.getElementById('filterForm').submit()">
                            <option value="">Semua</option>
                            @foreach($paperTypes as $pt)
                                <option value="{{ $pt }}" {{ request('paper_type') == $pt ? 'selected' : '' }}>{{ $pt }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-lg-2 col-md-4 mb-2">
                        <label>GSM</label>
                        <select name="gsm" class="form-control form-control-sm custom-select" onchange="document.getElementById('filterForm').submit()">
                            <option value="">Semua</option>
                            @foreach($gsms as $g)
                                <option value="{{ $g }}" {{ request('gsm') == $g ? 'selected' : '' }}>{{ $g }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-lg-2 col-md-4 mb-2">
                        <label>Width</label>
                        <select name="width" class="form-control form-control-sm custom-select" onchange="document.getElementById('filterForm').submit()">
                            <option value="">Semua</option>
                            @foreach($widths as $w)
                                <option value="{{ $w }}" {{ request('width') == $w ? 'selected' : '' }}>{{ $w }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-lg-2 col-md-4 mb-2">
                        <label>Lokasi Receiving</label>
                        <select name="receiving_2026" class="form-control form-control-sm custom-select" onchange="document.getElementById('filterForm').submit()">
                            <option value="">Semua</option>
                            @foreach($locations as $loc)
                                <option value="{{ $loc }}" {{ request('receiving_2026') == $loc ? 'selected' : '' }}>{{ $loc }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-lg-2 col-md-4 mb-2">
                        <label>Status</label>
                        <select name="status" class="form-control form-control-sm custom-select" onchange="document.getElementById('filterForm').submit()">
                            <option value="">Semua</option>
                            @foreach($statuses as $st)
                                <option value="{{ $st }}" {{ request('status') == $st ? 'selected' : '' }}>{{ $st }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-lg-2 col-md-4 mb-2">
                        <label>Sort</label>
                        <select name="sort" class="form-control form-control-sm custom-select" onchange="document.getElementById('filterForm').submit()">
                            <option value="created_at" {{ request('sort') == 'created_at' ? 'selected' : '' }}>Terbaru</option>
                            <option value="lot_id" {{ request('sort') == 'lot_id' ? 'selected' : '' }}>Lot ID</option>
                            <option value="receiving_2026" {{ request('sort') == 'receiving_2026' ? 'selected' : '' }}>Lokasi</option>
                            <option value="so_desember" {{ request('sort') == 'so_desember' ? 'selected' : '' }}>SO Des</option>
                            <option value="so_maret_2026" {{ request('sort') == 'so_maret_2026' ? 'selected' : '' }}>SO Mar</option>
                            <option value="end_qty" {{ request('sort') == 'end_qty' ? 'selected' : '' }}>End Qty</option>
                        </select>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Result info -->
<div class="d-flex justify-content-between align-items-center mb-2" style="font-size:0.8rem;">
    <span class="text-gray-500"><i class="fas fa-list mr-1"></i> {{ number_format($items->total()) }} items ditemukan</span>
    <span class="text-gray-500">Hal. {{ $items->currentPage() }}/{{ $items->lastPage() }}</span>
</div>

<!-- DESKTOP TABLE -->
<div class="desktop-table">
    <div class="card shadow">
        <div class="card-body p-2">
            <div class="table-responsive">
                <table class="table table-bordered table-hover mb-0">
                    <thead>
                        <tr>
                            <th style="width:35px;">#</th>
                            <th>Lot ID</th>
                            <th>Description</th>
                            <th>GSM</th>
                            <th>Width</th>
                            <th>End Qty</th>
                            <th>Receiving 2026</th>
                            <th>SO Desember</th>
                            <th>SO Maret 2026</th>
                            <th>PIC 2026</th>
                            <th>Status</th>
                            <th style="width:40px;"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($items as $i => $item)
                        <tr onclick="window.location='{{ route('items.show', $item->id) }}'">
                            <td class="text-gray-500">{{ ($items->currentPage()-1)*$items->perPage()+$i+1 }}</td>
                            <td><a href="{{ route('items.show', $item->id) }}" class="text-primary font-weight-bold">{{ $item->lot_id }}</a></td>
                            <td class="truncate" style="max-width:150px;" title="{{ $item->description }}">{{ $item->description ?? '-' }}</td>
                            <td>{{ $item->gsm ?? '-' }}</td>
                            <td>{{ $item->width ?? '-' }}</td>
                            <td class="font-weight-bold">{{ number_format($item->end_qty) }}</td>
                            <td>
                                @if($item->receiving_2026 && $item->receiving_2026 != '-')
                                    <span class="badge badge-loc">{{ $item->receiving_2026 }}</span>
                                @else
                                    <span class="text-gray-500" style="font-size:0.75rem;">-</span>
                                @endif
                            </td>
                            <td>
                                @if($item->so_desember && $item->so_desember != '-')
                                    <span class="badge badge-so">{{ Str::limit($item->so_desember, 18) }}</span>
                                @else
                                    <span class="text-gray-500" style="font-size:0.75rem;">-</span>
                                @endif
                            </td>
                            <td>
                                @if($item->so_maret_2026 && $item->so_maret_2026 != '-')
                                    <span class="badge badge-so">{{ Str::limit($item->so_maret_2026, 18) }}</span>
                                @else
                                    <span class="text-gray-500" style="font-size:0.75rem;">-</span>
                                @endif
                            </td>
                            <td class="truncate" style="max-width:100px;" title="{{ $item->pic_2026 }}">
                                <small>{{ $item->pic_2026 && $item->pic_2026 != '-' ? Str::limit($item->pic_2026, 16) : '-' }}</small>
                            </td>
                            <td>
                                @php
                                    $bc = 'badge-na'; $st = strtolower($item->status_barang ?? '');
                                    if($st=='good') $bc='badge-good';
                                    elseif(in_array($st,['hold','pending'])) $bc='badge-hold';
                                    elseif(in_array($st,['reject','problem','rusak'])) $bc='badge-problem';
                                @endphp
                                @if($item->status_barang && $item->status_barang != '-')
                                    <span class="badge {{ $bc }}">{{ $item->status_barang }}</span>
                                @else
                                    <span class="text-gray-500" style="font-size:0.75rem;">-</span>
                                @endif
                            </td>
                            <td><a href="{{ route('items.show', $item->id) }}" class="btn btn-sm btn-outline-primary" onclick="event.stopPropagation()"><i class="fas fa-eye" style="font-size:0.7rem;"></i></a></td>
                        </tr>
                        @empty
                        <tr><td colspan="12" class="text-center text-gray-500 py-4"><i class="fas fa-inbox fa-2x mb-2"></i><br>Tidak ada data</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- MOBILE CARDS -->
<div class="mobile-cards">
    @forelse($items as $item)
    <a href="{{ route('items.show', $item->id) }}" class="text-decoration-none">
        <div class="mobile-item-card">
            <span class="lot-id"><i class="fas fa-barcode mr-1"></i>{{ $item->lot_id }}</span>
            @if($item->description)
                <div class="info-row"><span class="info-label">Desc</span><span class="info-value truncate">{{ Str::limit($item->description, 35) }}</span></div>
            @endif
            <div class="info-row"><span class="info-label">GSM</span><span class="info-value">{{ $item->gsm ?? '-' }} / {{ $item->width ?? '-' }} mm</span></div>
            <div class="info-row"><span class="info-label">End Qty</span><span class="info-value" style="color:var(--text-primary);">{{ number_format($item->end_qty) }}</span></div>
            <div class="loc-tags">
                @if($item->receiving_2026 && $item->receiving_2026 != '-')
                    <span class="loc-tag"><i class="fas fa-map-pin mr-1"></i>{{ Str::limit($item->receiving_2026, 15) }}</span>
                @endif
                @if($item->so_desember && $item->so_desember != '-')
                    <span class="loc-tag"><i class="fas fa-file mr-1"></i>{{ Str::limit($item->so_desember, 15) }}</span>
                @endif
                @if($item->so_maret_2026 && $item->so_maret_2026 != '-')
                    <span class="loc-tag"><i class="fas fa-file mr-1"></i>{{ Str::limit($item->so_maret_2026, 15) }}</span>
                @endif
                @if($item->pic_2026 && $item->pic_2026 != '-')
                    <span class="loc-tag"><i class="fas fa-user mr-1"></i>{{ Str::limit($item->pic_2026, 15) }}</span>
                @endif
            </div>
        </div>
    </a>
    @empty
    <div class="text-center text-gray-500 py-4"><i class="fas fa-inbox fa-2x mb-2"></i><br>Tidak ada data</div>
    @endforelse
</div>

<!-- Pagination -->
<div class="d-flex justify-content-between align-items-center mt-3" style="font-size:0.8rem;">
    <span class="text-gray-500 d-none d-sm-block">
        {{ ($items->currentPage()-1)*$items->perPage()+1 }} - {{ min($items->currentPage()*$items->perPage(), $items->total()) }} dari {{ number_format($items->total()) }}
    </span>
    <div class="ml-auto">{{ $items->links('vendor.pagination.custom') }}</div>
</div>
@endsection
