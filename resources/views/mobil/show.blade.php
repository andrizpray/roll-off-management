@extends('layouts.app')

@section('title', 'Mobil: ' . $mobilId)
@section('page-title')
<i class="fas fa-truck mr-2 text-blue-500"></i>Mobil: {{ $mobilId }}
@endsection

@push('styles')
<style>
    .status-badge { display: inline-flex; align-items: center; gap: 4px; font-size: 0.7rem; font-weight: 600; padding: 3px 8px; border-radius: 6px; white-space: nowrap; }
</style>
@endpush

@section('content')
<div class="mb-4">
    <a href="{{ route('mobil.index') }}" class="btn btn-ghost text-xs">
        <i class="fas fa-arrow-left mr-1"></i>Kembali ke Daftar Mobil
    </a>
</div>

<div class="card overflow-hidden">
    <div class="px-4 py-3 border-b border-gray-100">
        <h3 class="font-semibold text-gray-900">
            <i class="fas fa-list mr-2 text-blue-500"></i>
            Riwayat Assignment — {{ $mobilId }}
            <span class="text-gray-400 font-normal text-xs ml-2">({{ $assignments->total() }} total)</span>
        </h3>
    </div>
    <div class="overflow-x-auto">
        <table class="data-table">
            <thead>
                <tr>
                    <th>DO Number</th>
                    <th>Penerima</th>
                    <th>Tanggal</th>
                    <th>Driver</th>
                    <th>Status DO</th>
                    <th>Status Sebelum</th>
                    <th>Jam Berangkat</th>
                    <th>Jam Tiba</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($assignments as $assignment)
                    @php $do = $assignment->deliveryOrder; @endphp
                    <tr>
                        <td>
                            @if($do)
                                <a href="{{ route('delivery.show', $do->id) }}"
                                   class="font-semibold text-blue-600 hover:underline">
                                    {{ $do->do_number }}
                                </a>
                            @else
                                <span class="text-gray-400">—</span>
                            @endif
                        </td>
                        <td class="text-gray-700">
                            {{ $do ? $do->recipient_name : '-' }}
                        </td>
                        <td class="text-gray-500 text-sm">
                            {{ $assignment->assigned_date->format('d M Y') }}
                        </td>
                        <td class="text-gray-700">{{ $assignment->driver_name }}</td>
                        <td>
                            @if($do)
                                <span class="tag {{ $do->status_badge }}">{{ $do->status_label }}</span>
                            @else
                                <span class="tag tag-gray">Unknown</span>
                            @endif
                        </td>
                        <td>
                            <span class="text-gray-500 text-sm">{{ $assignment->status_before }}</span>
                        </td>
                        <td class="text-gray-500 text-sm">
                            {{ $assignment->departure_time ? substr($assignment->departure_time, 0, 5) : '-' }}
                        </td>
                        <td class="text-gray-500 text-sm">
                            {{ $assignment->arrival_time ? substr($assignment->arrival_time, 0, 5) : '-' }}
                        </td>
                        <td onclick="event.stopPropagation();">
                            @if($do && in_array($do->status, ['confirmed', 'in_transit']))
                                <form method="POST"
                                      action="{{ route('mobil.remove-do', ['mobilId' => $mobilId, 'doId' => $do->id]) }}"
                                      class="inline"
                                      onsubmit="return confirm('Lepas DO {{ $do->do_number }} dari kendaraan ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-delete text-xs px-2 py-1">
                                        <i class="fas fa-unlink"></i>
                                    </button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="text-center text-gray-400 py-8">
                            <i class="fas fa-inbox text-2xl mb-2"></i>
                            <p>Tidak ada riwayat assignment</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($assignments->hasPages())
        <div class="px-4 py-3 border-t border-gray-100 flex items-center justify-between">
            <div class="text-xs text-gray-400">
                Menampilkan {{ $assignments->firstItem() }}–{{ $assignments->lastItem() }} dari {{ $assignments->total() }}
            </div>
            {{ $assignments->withQueryString()->links('vendor.pagination.custom') }}
        </div>
    @endif
</div>
@endsection