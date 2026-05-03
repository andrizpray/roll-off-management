@extends('layouts.app')

@section('title', 'Roll Items')
@section('page-title', '<i class="fas fa-box mr-1"></i> Roll Items')

@section('content')
<!-- Quick Search -->
<div class="quick-search tw-mb-3">
    <form method="GET" action="{{ route('items.index') }}" class="tw-flex tw-items-center tw-gap-2 tw-flex-wrap">
        <div class="tw-input-group" style="max-width:420px; flex:1; display:flex;">
            <span class="tw-flex tw-items-center tw-px-3" style="background:var(--bg-card-header); border:1px solid var(--border); border-right:none; border-radius:20px 0 0 20px; color:var(--text-muted); font-size:0.8rem;"><i class="fas fa-search"></i></span>
            <input type="text" name="search" class="form-control" placeholder="Cari Lot ID, deskripsi, lokasi, SO, PIC..." value="{{ request('search') }}" style="border-radius:0 20px 20px 0; border-left:none;">
        </div>
        <button class="btn btn-primary btn-sm" type="submit" style="border-radius:20px; padding:6px 18px;">Cari</button>
        @if(request('search') || request('paper_type') || request('gsm') || request('receiving_2026'))
            <a href="{{ route('items.index') }}" class="btn btn-outline-secondary btn-sm" style="border-radius:20px; padding:6px 14px;"><i class="fas fa-times mr-1"></i>Reset</a>
        @endif
    </form>
</div>

<!-- Advanced Filters -->
<div class="card shadow tw-mb-3">
    <a class="card-header py-2 d-flex align-items-center justify-content-between text-decoration-none" data-toggle="collapse" href="#advFilters" style="cursor:pointer;">
        <h6 class="m-0 font-weight-bold text-primary" style="font-size:0.82rem;"><i class="fas fa-sliders-h mr-1"></i>Filter Lanjutan</h6>
        <i class="fas fa-chevron-down text-gray-500" style="font-size:0.7rem;"></i>
    </a>
    <div class="collapse {{ (request('paper_type') || request('gsm') || request('width') || request('receiving_2026') || request('status')) ? 'show' : '' }}" id="advFilters">
        <div class="card-body pt-2 pb-3">
            <form method="GET" action="{{ route('items.index') }}" id="filterForm">
                @if(request('search'))<input type="hidden" name="search" value="{{ request('search') }}">@endif
                <div class="row filter-row">
                    <div class="col-lg-2 col-md-4 mb-2">
                        <label>Paper Type</label>
                        <select name="paper_type" class="form-control form-control-sm custom-select" onchange="document.getElementById('filterForm').submit()">
                            <option value="">Semua</option>
                            @foreach($paperTypes as $pt)<option value="{{ $pt }}" {{ request('paper_type') == $pt ? 'selected' : '' }}>{{ $pt }}</option>@endforeach
                        </select>
                    </div>
                    <div class="col-lg-2 col-md-4 mb-2">
                        <label>GSM</label>
                        <select name="gsm" class="form-control form-control-sm custom-select" onchange="document.getElementById('filterForm').submit()">
                            <option value="">Semua</option>
                            @foreach($gsms as $g)<option value="{{ $g }}" {{ request('gsm') == $g ? 'selected' : '' }}>{{ $g }}</option>@endforeach
                        </select>
                    </div>
                    <div class="col-lg-2 col-md-4 mb-2">
                        <label>Width</label>
                        <select name="width" class="form-control form-control-sm custom-select" onchange="document.getElementById('filterForm').submit()">
                            <option value="">Semua</option>
                            @foreach($widths as $w)<option value="{{ $w }}" {{ request('width') == $w ? 'selected' : '' }}>{{ $w }}</option>@endforeach
                        </select>
                    </div>
                    <div class="col-lg-2 col-md-4 mb-2">
                        <label>Lokasi Receiving</label>
                        <select name="receiving_2026" class="form-control form-control-sm custom-select" onchange="document.getElementById('filterForm').submit()">
                            <option value="">Semua</option>
                            @foreach($locations as $loc)<option value="{{ $loc }}" {{ request('receiving_2026') == $loc ? 'selected' : '' }}>{{ $loc }}</option>@endforeach
                        </select>
                    </div>
                    <div class="col-lg-2 col-md-4 mb-2">
                        <label>Status</label>
                        <select name="status" class="form-control form-control-sm custom-select" onchange="document.getElementById('filterForm').submit()">
                            <option value="">Semua</option>
                            @foreach($statuses as $st)<option value="{{ $st }}" {{ request('status') == $st ? 'selected' : '' }}>{{ $st }}</option>@endforeach
                        </select>
                    </div>
                    <div class="col-lg-2 col-md-4 mb-2">
                        <label>Sort</label>
                        <select name="sort" class="form-control form-control-sm custom-select" onchange="document.getElementById('filterForm').submit()">
                            <option value="created_at" {{ request('sort') == 'created_at' ? 'selected' : '' }}>Terbaru</option>
                            <option value="lot_id" {{ request('sort') == 'lot_id' ? 'selected' : '' }}>Lot ID</option>
                            <option value="receiving_2026" {{ request('sort') == 'receiving_2026' ? 'selected' : '' }}>Lokasi</option>
                            <option value="end_qty" {{ request('sort') == 'end_qty' ? 'selected' : '' }}>End Qty</option>
                        </select>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Result info -->
<div class="tw-flex tw-justify-between tw-items-center tw-mb-2" style="font-size:0.78rem;">
    <span class="text-gray-500"><i class="fas fa-list mr-1"></i>{{ number_format($items->total()) }} items</span>
    <span class="text-gray-500">Hal. {{ $items->currentPage() }}/{{ $items->lastPage() }}</span>
</div>

<!-- DESKTOP TABLE -->
<div class="desktop-table">
    <div class="card shadow">
        <div class="card-body tw-p-2">
            <div class="table-responsive">
                <table class="table table-bordered table-hover mb-0">
                    <thead>
                        <tr>
                            <th style="width:30px;">#</th>
                            <th>Lot ID</th>
                            <th>Description</th>
                            <th>GSM</th>
                            <th>Width</th>
                            <th>Qty</th>
                            <th>Lokasi</th>
                            <th>SO Des</th>
                            <th>SO Mar</th>
                            <th>PIC</th>
                            <th>Status</th>
                            <th style="width:35px;"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($items as $i => $item)
                        <tr onclick="window.location='{{ route('items.show', $item->id) }}'">
                            <td class="text-gray-500">{{ ($items->currentPage()-1)*$items->perPage()+$i+1 }}</td>
                            <td><a href="{{ route('items.show', $item->id) }}" class="text-primary font-weight-bold">{{ $item->lot_id }}</a></td>
                            <td class="truncate" style="max-width:130px;" title="{{ $item->description }}">{{ $item->description ?? '-' }}</td>
                            <td>{{ $item->gsm ?? '-' }}</td>
                            <td>{{ $item->width ?? '-' }}</td>
                            <td class="font-weight-bold">{{ number_format($item->end_qty) }}</td>
                            <td>
                                @if($item->current_location)
                                    <span class="badge badge-loc" title="{{ $item->current_location_label }}">{{ Str::limit($item->current_location, 16) }}</span>
                                @else
                                    <span class="text-gray-500" style="font-size:0.7rem;">-</span>
                                @endif
                            </td>
                            <td>
                                @if($item->so_desember && $item->so_desember != '-')
                                    <span class="badge badge-so">{{ Str::limit($item->so_desember, 16) }}</span>
                                @else <span class="text-gray-500" style="font-size:0.7rem;">-</span> @endif
                            </td>
                            <td>
                                @if($item->so_maret_2026 && $item->so_maret_2026 != '-')
                                    <span class="badge badge-so">{{ Str::limit($item->so_maret_2026, 16) }}</span>
                                @else <span class="text-gray-500" style="font-size:0.7rem;">-</span> @endif
                            </td>
                            <td class="truncate" style="max-width:90px;" title="{{ $item->pic_2026 }}">
                                <small>{{ $item->pic_2026 && $item->pic_2026 != '-' ? Str::limit($item->pic_2026, 14) : '-' }}</small>
                            </td>
                            <td>
                                @php
                                    $bc='badge-na'; $st=strtolower($item->status_barang ?? '');
                                    if($st=='good') $bc='badge-good';
                                    elseif(in_array($st,['hold','pending'])) $bc='badge-hold';
                                    elseif(in_array($st,['reject','problem','rusak'])) $bc='badge-problem';
                                @endphp
                                @if($item->status_barang && $item->status_barang != '-')
                                    <span class="badge {{ $bc }}">{{ $item->status_barang }}</span>
                                @else <span class="text-gray-500" style="font-size:0.7rem;">-</span> @endif
                            </td>
                            <td><a href="{{ route('items.show', $item->id) }}" class="btn btn-sm btn-outline-primary" onclick="event.stopPropagation()"><i class="fas fa-eye" style="font-size:0.65rem;"></i></a></td>
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
            <div class="tw-flex tw-justify-between tw-items-center tw-mb-1">
                <span class="lot-id" style="margin-bottom:0;"><i class="fas fa-barcode mr-1"></i>{{ $item->lot_id }}</span>
                @if($item->current_location)
                    <span class="loc-tag"><i class="fas fa-map-pin mr-1"></i>{{ Str::limit($item->current_location, 14) }}</span>
                @endif
            </div>
            @if($item->description)
                <div class="info-row"><span class="info-label">Desc</span><span class="info-value truncate">{{ Str::limit($item->description, 35) }}</span></div>
            @endif
            <div class="info-row"><span class="info-label">Spec</span><span class="info-value">{{ $item->gsm ?? '-' }} / {{ $item->width ?? '-' }} mm</span></div>
            <div class="info-row"><span class="info-label">Qty</span><span class="info-value" style="color:var(--text-primary);">{{ number_format($item->end_qty) }}</span></div>
            <div class="loc-tags">
                @if($item->so_desember && $item->so_desember != '-')
                    <span class="loc-tag"><i class="fas fa-file mr-1"></i>Des: {{ Str::limit($item->so_desember, 12) }}</span>
                @endif
                @if($item->so_maret_2026 && $item->so_maret_2026 != '-')
                    <span class="loc-tag"><i class="fas fa-file mr-1"></i>Mar: {{ Str::limit($item->so_maret_2026, 12) }}</span>
                @endif
                @if($item->pic_2026 && $item->pic_2026 != '-')
                    <span class="loc-tag"><i class="fas fa-user mr-1"></i>{{ Str::limit($item->pic_2026, 12) }}</span>
                @endif
            </div>
        </div>
    </a>
    @empty
    <div class="text-center text-gray-500 py-4"><i class="fas fa-inbox fa-2x mb-2"></i><br>Tidak ada data</div>
    @endforelse
</div>

<!-- Pagination -->
<div class="tw-flex tw-justify-between tw-items-center tw-mt-3" style="font-size:0.78rem;">
    <span class="text-gray-500 d-none d-sm-block">
        {{ ($items->currentPage()-1)*$items->perPage()+1 }}-{{ min($items->currentPage()*$items->perPage(), $items->total()) }} / {{ number_format($items->total()) }}
    </span>
    <div class="ml-auto">{{ $items->links('vendor.pagination.custom') }}</div>
</div>
@endsection
