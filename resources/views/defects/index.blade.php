@extends('layouts.app')

@section('title', 'Barang Bermasalah')
@section('page-title')
<i class="fas fa-triangle-exclamation mr-2 opacity-60"></i>Barang Bermasalah
@endsection

@section('content')
<div class="animate-in space-y-4">

    <!-- Stats -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
        <div class="card stat-card red p-4">
            <div class="flex items-center justify-between mb-2">
                <span class="text-[10px] font-semibold uppercase tracking-wide" style="color: #f87171;">Total Defects</span>
                <div class="w-7 h-7 rounded-lg flex items-center justify-center" style="background: rgba(239,68,68,0.12);">
                    <i class="fas fa-bug text-xs" style="color: #f87171;"></i>
                </div>
            </div>
            <div class="text-xl lg:text-2xl font-extrabold text-white">{{ number_format($totalDefects) }}</div>
        </div>
        <div class="card stat-card purple p-4">
            <div class="flex items-center justify-between mb-2">
                <span class="text-[10px] font-semibold uppercase tracking-wide" style="color: #a78bfa;">Tahun 2025</span>
                <div class="w-7 h-7 rounded-lg flex items-center justify-center" style="background: rgba(139,92,246,0.12);">
                    <i class="fas fa-calendar-alt text-xs" style="color: #a78bfa;"></i>
                </div>
            </div>
            <div class="text-xl lg:text-2xl font-extrabold text-white">{{ number_format($defect2025) }}</div>
        </div>
        <div class="card stat-card teal p-4">
            <div class="flex items-center justify-between mb-2">
                <span class="text-[10px] font-semibold uppercase tracking-wide" style="color: #2dd4bf;">Tahun 2026</span>
                <div class="w-7 h-7 rounded-lg flex items-center justify-center" style="background: rgba(20,184,166,0.12);">
                    <i class="fas fa-calendar-check text-xs" style="color: #2dd4bf;"></i>
                </div>
            </div>
            <div class="text-xl lg:text-2xl font-extrabold text-white">{{ number_format($defect2026) }}</div>
        </div>
        <div class="card stat-card blue p-4">
            <div class="flex items-center justify-between mb-2">
                <span class="text-[10px] font-semibold uppercase tracking-wide" class="text-blue-500">Ditampilkan</span>
                <div class="w-7 h-7 rounded-lg flex items-center justify-center" style="background: rgba(59,130,246,0.12);">
                    <i class="fas fa-filter text-xs" class="text-blue-500"></i>
                </div>
            </div>
            <div class="text-xl lg:text-2xl font-extrabold text-white">{{ number_format($defects->total()) }}</div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card overflow-hidden">
        <button onclick="document.getElementById('defectFilters').classList.toggle('hidden')"
                class="w-full px-4 py-3 flex items-center justify-between text-left hover:bg-gray-50 transition">
            <span class="text-xs font-semibold uppercase tracking-wide flex items-center gap-2" class="text-gray-500">
                <i class="fas fa-filter"></i> Filter
            </span>
            <i class="fas fa-chevron-down text-xs transition-transform" class="text-gray-400"></i>
        </button>
        <div id="defectFilters" class="hidden {{ (request('search') || request('year') || request('reason') || request('paper_type') || request('month')) ? '' : 'hidden' }}">
            <div class="px-4 pb-4 pt-1">
                <form method="GET" action="{{ route('defects.index') }}" id="filterForm">
                    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3">
                        <div class="col-span-2 md:col-span-1">
                            <label class="block text-[10px] font-semibold uppercase tracking-wide mb-1.5" class="text-gray-400">Search</label>
                            <div class="flex gap-1.5">
                                <input type="text" name="search" class="input-field" placeholder="Lot ID, Rew ID..." value="{{ request('search') }}">
                                <button class="btn btn-primary flex-shrink-0" type="submit" style="padding: 8px 12px;">
                                    <i class="fas fa-search text-xs"></i>
                                </button>
                            </div>
                        </div>
                        <div>
                            <label class="block text-[10px] font-semibold uppercase tracking-wide mb-1.5" class="text-gray-400">Tahun</label>
                            <select name="year" class="select-field w-full" onchange="document.getElementById('filterForm').submit()">
                                <option value="">Semua</option>
                                @foreach($years as $y)<option value="{{ $y }}" {{ request('year') == $y ? 'selected' : '' }}>{{ $y }}</option>@endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-[10px] font-semibold uppercase tracking-wide mb-1.5" class="text-gray-400">Alasan</label>
                            <select name="reason" class="select-field w-full" onchange="document.getElementById('filterForm').submit()">
                                <option value="">Semua</option>
                                @foreach($reasons as $r)<option value="{{ $r }}" {{ request('reason') == $r ? 'selected' : '' }}>{{ $r }}</option>@endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-[10px] font-semibold uppercase tracking-wide mb-1.5" class="text-gray-400">Paper Type</label>
                            <select name="paper_type" class="select-field w-full" onchange="document.getElementById('filterForm').submit()">
                                <option value="">Semua</option>
                                @foreach($paperTypes as $pt)<option value="{{ $pt }}" {{ request('paper_type') == $pt ? 'selected' : '' }}>{{ $pt }}</option>@endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-[10px] font-semibold uppercase tracking-wide mb-1.5" class="text-gray-400">Bulan</label>
                            <div class="flex gap-1.5">
                                <select name="month" class="select-field flex-1" onchange="document.getElementById('filterForm').submit()">
                                    <option value="">Semua</option>
                                    @foreach($months as $m)<option value="{{ $m }}" {{ request('month') == $m ? 'selected' : '' }}>{{ $m }}</option>@endforeach
                                </select>
                                <a href="{{ route('defects.index') }}" class="btn btn-ghost flex-shrink-0" style="padding: 8px 10px;" title="Reset">
                                    <i class="fas fa-times text-xs"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @if(!request('search') && !request('year') && !request('reason') && !request('paper_type') && !request('month'))
        <script>document.getElementById('defectFilters').classList.add('hidden');</script>
    @else
        <script>document.getElementById('defectFilters').classList.remove('hidden');</script>
    @endif

    <!-- Result info -->
    <div class="flex items-center justify-between text-xs" class="text-gray-400">
        <span><i class="fas fa-list mr-1"></i>{{ number_format($defects->total()) }} items</span>
        <span class="hidden sm:inline">Hal. {{ $defects->currentPage() }}/{{ $defects->lastPage() }}</span>
    </div>

    <!-- DESKTOP TABLE -->
    <div class="hidden md:block glass overflow-hidden">
        <div class="overflow-x-auto">
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width: 35px;">#</th>
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
                        <td class="text-gray-300">{{ ($defects->currentPage()-1)*$defects->perPage()+$i+1 }}</td>
                        <td><span class="tag tag-gray">{{ $def->year }}</span></td>
                        <td>
                            @if($def->lot_id)
                                <a href="{{ route('items.index', ['search' => $def->lot_id]) }}" class="font-semibold no-underline hover:underline" class="text-blue-500">{{ $def->lot_id }}</a>
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
                        <td class="truncate" style="max-width: 150px;">{{ $def->keterangan ?? '-' }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10" class="text-center py-10">
                            <i class="fas fa-check-circle text-2xl mb-2 block" style="color: #22c55e;"></i>
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
        <div class="mobile-card" @if($def->lot_id) onclick="window.location='{{ route('items.index', ['search' => $def->lot_id]) }}'" style="cursor: pointer;" @endif>
            <div class="flex items-center justify-between mb-2">
                @if($def->lot_id)
                    <span class="font-semibold text-sm" class="text-blue-500">
                        <i class="fas fa-barcode mr-1 text-xs"></i>{{ $def->lot_id }}
                    </span>
                @else
                    <span class="text-xs" class="text-gray-400">No Lot ID</span>
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
                    <span class="truncate text-right" style="color: #c9d1d9; max-width: 60%;">{{ Str::limit($def->keterangan, 40) }}</span>
                </div>
            @endif
        </div>
        @empty
        <div class="text-center py-10">
            <i class="fas fa-check-circle text-2xl mb-2 block" style="color: #22c55e;"></i>
            <span class="text-gray-400">Tidak ada defect</span>
        </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="flex items-center justify-between text-xs" class="text-gray-400">
        <span class="hidden sm:inline">
            {{ ($defects->currentPage()-1)*$defects->perPage()+1 }}-{{ min($defects->currentPage()*$defects->perPage(), $defects->total()) }} dari {{ number_format($defects->total()) }}
        </span>
        <div class="ml-auto">{{ $defects->links('vendor.pagination.custom') }}</div>
    </div>

</div>
@endsection
