@extends('layouts.app')

@section('title', 'Roll Items')
@section('page-title')
<i class="fas fa-box mr-2 opacity-60"></i>Roll Items
@endsection

@section('content')
<div class="animate-in space-y-4">

    <!-- Search Bar -->
    <form method="GET" action="{{ route('items.index') }}" class="flex gap-2 flex-wrap">
        <div class="relative flex-1 min-w-[200px]">
            <i class="fas fa-search absolute left-3.5 top-1/2 -translate-y-1/2 text-xs" class="text-gray-400"></i>
            <input type="text" name="search" class="input-field pl-9" placeholder="Cari Lot ID, deskripsi, lokasi, SO, PIC..."
                   value="{{ request('search') }}">
        </div>
        <button class="btn btn-primary" type="submit">
            <i class="fas fa-search text-xs"></i> Cari
        </button>
        @if(request('search') || request('paper_type') || request('gsm') || request('receiving_2026') || request('status'))
            <a href="{{ route('items.index') }}" class="btn btn-ghost">
                <i class="fas fa-times text-xs"></i> Reset
            </a>
        @endif
    </form>

    <!-- Filters -->
    <div class="card overflow-hidden">
        <button onclick="document.getElementById('advFilters').classList.toggle('hidden')"
                class="w-full px-4 py-3 flex items-center justify-between text-left hover:bg-gray-50 transition">
            <span class="text-xs font-semibold uppercase tracking-wide flex items-center gap-2" class="text-gray-500">
                <i class="fas fa-sliders-h"></i> Filter Lanjutan
            </span>
            <i class="fas fa-chevron-down text-xs transition-transform" id="filterChevron" class="text-gray-400"></i>
        </button>
        <div id="advFilters" class="hidden {{ (request('paper_type') || request('gsm') || request('width') || request('receiving_2026') || request('status') || request('sort')) ? '' : 'hidden' }}">
            <div class="px-4 pb-4 pt-1">
                <form method="GET" action="{{ route('items.index') }}" id="filterForm">
                    @if(request('search'))<input type="hidden" name="search" value="{{ request('search') }}">@endif
                    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3">
                        <div>
                            <label class="block text-[10px] font-semibold uppercase tracking-wide mb-1.5" class="text-gray-400">Paper Type</label>
                            <select name="paper_type" class="select-field w-full" onchange="document.getElementById('filterForm').submit()">
                                <option value="">Semua</option>
                                @foreach($paperTypes as $pt)<option value="{{ $pt }}" {{ request('paper_type') == $pt ? 'selected' : '' }}>{{ $pt }}</option>@endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-[10px] font-semibold uppercase tracking-wide mb-1.5" class="text-gray-400">GSM</label>
                            <select name="gsm" class="select-field w-full" onchange="document.getElementById('filterForm').submit()">
                                <option value="">Semua</option>
                                @foreach($gsms as $g)<option value="{{ $g }}" {{ request('gsm') == $g ? 'selected' : '' }}>{{ $g }}</option>@endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-[10px] font-semibold uppercase tracking-wide mb-1.5" class="text-gray-400">Width</label>
                            <select name="width" class="select-field w-full" onchange="document.getElementById('filterForm').submit()">
                                <option value="">Semua</option>
                                @foreach($widths as $w)<option value="{{ $w }}" {{ request('width') == $w ? 'selected' : '' }}>{{ $w }}</option>@endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-[10px] font-semibold uppercase tracking-wide mb-1.5" class="text-gray-400">Receiving</label>
                            <select name="receiving_2026" class="select-field w-full" onchange="document.getElementById('filterForm').submit()">
                                <option value="">Semua</option>
                                @foreach($locations as $loc)<option value="{{ $loc }}" {{ request('receiving_2026') == $loc ? 'selected' : '' }}>{{ $loc }}</option>@endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-[10px] font-semibold uppercase tracking-wide mb-1.5" class="text-gray-400">Status</label>
                            <select name="status" class="select-field w-full" onchange="document.getElementById('filterForm').submit()">
                                <option value="">Semua</option>
                                @foreach($statuses as $st)<option value="{{ $st }}" {{ request('status') == $st ? 'selected' : '' }}>{{ $st }}</option>@endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-[10px] font-semibold uppercase tracking-wide mb-1.5" class="text-gray-400">Sort</label>
                            <select name="sort" class="select-field w-full" onchange="document.getElementById('filterForm').submit()">
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

    @if(!request('paper_type') && !request('gsm') && !request('width') && !request('receiving_2026') && !request('status') && !request('sort'))
        <script>document.getElementById('advFilters').classList.add('hidden');</script>
    @else
        <script>document.getElementById('advFilters').classList.remove('hidden');</script>
    @endif

    <!-- Result info -->
    <div class="flex items-center justify-between text-xs" class="text-gray-400">
        <span><i class="fas fa-list mr-1"></i>{{ number_format($items->total()) }} items</span>
        <span class="hidden sm:inline">Hal. {{ $items->currentPage() }}/{{ $items->lastPage() }}</span>
    </div>

    <!-- DESKTOP TABLE -->
    <div class="hidden md:block glass overflow-hidden">
        <div class="overflow-x-auto">
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width: 35px;">#</th>
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
                        <th style="width: 35px;"></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($items as $i => $item)
                    <tr onclick="window.location='{{ route('items.show', $item->id) }}'">
                        <td class="text-gray-300">{{ ($items->currentPage()-1)*$items->perPage()+$i+1 }}</td>
                        <td>
                            <a href="{{ route('items.show', $item->id) }}" class="font-semibold no-underline hover:underline" class="text-blue-500" onclick="event.stopPropagation();">{{ $item->lot_id }}</a>
                        </td>
                        <td class="truncate" style="max-width: 140px;" title="{{ $item->description }}">{{ $item->description ?? '-' }}</td>
                        <td>{{ $item->gsm ?? '-' }}</td>
                        <td>{{ $item->width ?? '-' }}</td>
                        <td class="font-semibold text-white">{{ number_format($item->end_qty) }}</td>
                        <td>
                            @if($item->current_location)
                                <span class="tag tag-blue" title="{{ $item->current_location_label }}">{{ Str::limit($item->current_location, 16) }}</span>
                            @else
                                <span class="text-gray-300">-</span>
                            @endif
                        </td>
                        <td>
                            @if($item->so_desember && $item->so_desember != '-')
                                <span class="tag tag-purple">{{ Str::limit($item->so_desember, 16) }}</span>
                            @else
                                <span class="text-gray-300">-</span>
                            @endif
                        </td>
                        <td>
                            @if($item->so_maret_2026 && $item->so_maret_2026 != '-')
                                <span class="tag tag-purple">{{ Str::limit($item->so_maret_2026, 16) }}</span>
                            @else
                                <span class="text-gray-300">-</span>
                            @endif
                        </td>
                        <td class="truncate" style="max-width: 90px;" title="{{ $item->pic_2026 }}">
                            <span style="font-size: 0.78rem;">{{ $item->pic_2026 && $item->pic_2026 != '-' ? Str::limit($item->pic_2026, 14) : '-' }}</span>
                        </td>
                        <td>
                            @php
                                $tagClass = 'tag-gray';
                                $st = strtolower($item->status_barang ?? '');
                                if($st == 'good') $tagClass = 'tag-green';
                                elseif(in_array($st, ['hold','pending'])) $tagClass = 'tag-yellow';
                                elseif(in_array($st, ['reject','problem','rusak'])) $tagClass = 'tag-red';
                            @endphp
                            @if($item->status_barang && $item->status_barang != '-')
                                <span class="tag {{ $tagClass }}">{{ $item->status_barang }}</span>
                            @else
                                <span class="text-gray-300">-</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('items.show', $item->id) }}" class="w-7 h-7 rounded-lg flex items-center justify-center hover:bg-gray-100 transition no-underline" onclick="event.stopPropagation();">
                                <i class="fas fa-arrow-right text-xs" class="text-gray-400"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="12" class="text-center py-10">
                            <i class="fas fa-inbox text-2xl mb-2 block" class="text-gray-300"></i>
                            <span class="text-gray-400">Tidak ada data</span>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- MOBILE CARDS -->
    <div class="md:hidden">
        @forelse($items as $item)
        <a href="{{ route('items.show', $item->id) }}" class="no-underline block">
            <div class="mobile-card">
                <div class="flex items-center justify-between mb-2">
                    <span class="font-semibold text-sm" class="text-blue-500">
                        <i class="fas fa-barcode mr-1 text-xs"></i>{{ $item->lot_id }}
                    </span>
                    @if($item->current_location)
                        <span class="tag tag-teal" style="font-size: 0.65rem;"><i class="fas fa-map-pin mr-1"></i>{{ Str::limit($item->current_location, 14) }}</span>
                    @endif
                </div>
                @if($item->description)
                    <div class="flex justify-between text-xs mb-1.5">
                        <span class="text-gray-400">Desc</span>
                        <span class="truncate text-right" style="color: #c9d1d9; max-width: 65%;">{{ Str::limit($item->description, 35) }}</span>
                    </div>
                @endif
                <div class="flex justify-between text-xs mb-1.5">
                    <span class="text-gray-400">Spec</span>
                    <span class="text-gray-600">{{ $item->gsm ?? '-' }} / {{ $item->width ?? '-' }} mm</span>
                </div>
                <div class="flex justify-between text-xs mb-2">
                    <span class="text-gray-400">Qty</span>
                    <span class="font-semibold text-white">{{ number_format($item->end_qty) }}</span>
                </div>
                <div class="flex flex-wrap gap-1.5">
                    @if($item->so_desember && $item->so_desember != '-')
                        <span class="tag tag-purple" style="font-size: 0.62rem;"><i class="fas fa-file mr-1"></i>Des: {{ Str::limit($item->so_desember, 12) }}</span>
                    @endif
                    @if($item->so_maret_2026 && $item->so_maret_2026 != '-')
                        <span class="tag tag-purple" style="font-size: 0.62rem;"><i class="fas fa-file mr-1"></i>Mar: {{ Str::limit($item->so_maret_2026, 12) }}</span>
                    @endif
                    @if($item->pic_2026 && $item->pic_2026 != '-')
                        <span class="tag tag-teal" style="font-size: 0.62rem;"><i class="fas fa-user mr-1"></i>{{ Str::limit($item->pic_2026, 12) }}</span>
                    @endif
                    @php
                        $tagClass = 'tag-gray';
                        $st = strtolower($item->status_barang ?? '');
                        if($st == 'good') $tagClass = 'tag-green';
                        elseif(in_array($st, ['hold','pending'])) $tagClass = 'tag-yellow';
                        elseif(in_array($st, ['reject','problem','rusak'])) $tagClass = 'tag-red';
                    @endphp
                    @if($item->status_barang && $item->status_barang != '-')
                        <span class="tag {{ $tagClass }}" style="font-size: 0.62rem;">{{ $item->status_barang }}</span>
                    @endif
                </div>
            </div>
        </a>
        @empty
        <div class="text-center py-10">
            <i class="fas fa-inbox text-2xl mb-2 block" class="text-gray-300"></i>
            <span class="text-gray-400">Tidak ada data</span>
        </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="flex items-center justify-between text-xs" class="text-gray-400">
        <span class="hidden sm:inline">
            {{ ($items->currentPage()-1)*$items->perPage()+1 }}-{{ min($items->currentPage()*$items->perPage(), $items->total()) }} / {{ number_format($items->total()) }}
        </span>
        <div class="ml-auto">{{ $items->links('vendor.pagination.custom') }}</div>
    </div>

</div>

<script>
// Toggle filter chevron
const filterBtn = document.querySelector('[onclick*="advFilters"]');
const chevron = document.getElementById('filterChevron');
if(filterBtn && chevron) {
    filterBtn.addEventListener('click', () => {
        chevron.style.transform = document.getElementById('advFilters').classList.contains('hidden') ? 'rotate(0deg)' : 'rotate(180deg)';
    });
}
</script>
@endsection
