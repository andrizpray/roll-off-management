@extends('layouts.app')

@section('title', 'Delivery Orders')
@section('page-title')
<i class="fas fa-truck mr-2 text-blue-500"></i>Delivery Orders
@endsection

@push('styles')
<style>
    .status-badge { display: inline-flex; align-items: center; gap: 4px; font-size: 0.7rem; font-weight: 600; padding: 3px 8px; border-radius: 6px; white-space: nowrap; }
</style>
@endpush

@section('content')
{{-- ── Stat Cards ── --}}
<div class="grid grid-cols-2 lg:grid-cols-5 gap-3 mb-6">
    <div class="card stat-card p-4">
        <div class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-1">Total DO</div>
        <div class="text-2xl font-bold text-gray-900">{{ number_format($stats['total']) }}</div>
    </div>
    <div class="card stat-card p-4">
        <div class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-1">Draft</div>
        <div class="text-2xl font-bold text-gray-900">{{ number_format($stats['draft']) }}</div>
    </div>
    <div class="card stat-card p-4">
        <div class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-1">Confirmed</div>
        <div class="text-2xl font-bold text-gray-900">{{ number_format($stats['confirmed']) }}</div>
    </div>
    <div class="card stat-card p-4">
        <div class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-1">Dalam Perjalanan</div>
        <div class="text-2xl font-bold text-yellow-600">{{ number_format($stats['in_transit']) }}</div>
    </div>
    <div class="card stat-card p-4">
        <div class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-1">Terkirim</div>
        <div class="text-2xl font-bold text-green-600">{{ number_format($stats['delivered']) }}</div>
    </div>
</div>

{{-- ── Toolbar ── --}}
<div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 mb-4">
    <form method="GET" class="flex flex-wrap gap-2 w-full">
        <div class="relative flex-1 min-w-[200px]">
            <input type="text" name="search" value="{{ request('search') }}"
                   class="input-field pl-9"
                   placeholder="Cari DO number atau penerima...">
            <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
        </div>
        <select name="status" class="select-field">
            <option value="">Semua Status</option>
            @foreach(['draft','confirmed','in_transit','delivered','cancelled'] as $s)
                <option value="{{ $s }}" {{ request('status') == $s ? 'selected' : '' }}>
                    {{ ucfirst($s) }}
                </option>
            @endforeach
        </select>
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-filter text-xs"></i> Filter
        </button>
        @if(request('search') || request('status'))
            <a href="{{ route('delivery.index') }}" class="btn btn-ghost text-xs">
                <i class="fas fa-times"></i> Reset
            </a>
        @endif
    </form>
    <a href="{{ route('delivery.create') }}" class="btn btn-primary whitespace-nowrap">
        <i class="fas fa-plus text-xs"></i> DO Baru
    </a>
</div>

{{-- ── Table ── --}}
<div class="card overflow-hidden">
    <div class="overflow-x-auto">
        <table class="data-table">
            <thead>
                <tr>
                    <th>DO Number</th>
                    <th>Penerima</th>
                    <th>Tujuan</th>
                    <th>Items</th>
                    <th>Status</th>
                    <th>Tanggal</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($dos as $do)
                    <tr onclick="window.location='{{ route('delivery.show', $do->id) }}'" style="cursor:pointer;">
                        <td>
                            <span class="font-semibold text-gray-900">{{ $do->do_number }}</span>
                        </td>
                        <td class="text-gray-700">{{ $do->recipient_name }}</td>
                        <td class="text-gray-500 text-sm">{{ $do->destination ?? '-' }}</td>
                        <td class="text-center">
                            <span class="tag tag-blue">{{ $do->items_count }} item</span>
                        </td>
                        <td>
                            <span class="tag {{ $do->status_badge }}">
                                {{ $do->status_label }}
                            </span>
                        </td>
                        <td class="text-gray-500 text-sm">{{ $do->created_at->format('d M Y') }}</td>
                        <td class="text-center" onclick="event.stopPropagation();">
                            <a href="{{ route('delivery.show', $do->id) }}"
                               class="btn btn-ghost text-xs px-2 py-1">
                                <i class="fas fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center text-gray-400 py-8">
                            <i class="fas fa-inbox text-2xl mb-2"></i>
                            <p>Belum ada Delivery Order</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($dos->hasPages())
        <div class="px-4 py-3 border-t border-gray-100 flex items-center justify-between">
            <div class="text-xs text-gray-400">
                Menampilkan {{ $dos->firstItem() }}–{{ $dos->lastItem() }} dari {{ $dos->total() }}
            </div>
            {{ $dos->withQueryString()->links('vendor.pagination.custom') }}
        </div>
    @endif
</div>
@endsection