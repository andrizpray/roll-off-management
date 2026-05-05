@extends('layouts.app')

@section('title', $do->do_number)
@section('page-title')
<i class="fas fa-truck mr-2 text-blue-500"></i>{{ $do->do_number }}
@endsection

@push('styles')
<style>
    .status-badge { display: inline-flex; align-items: center; gap: 4px; font-size: 0.7rem; font-weight: 600; padding: 3px 8px; border-radius: 6px; white-space: nowrap; }
    .timeline-step { position: relative; padding-left: 32px; padding-bottom: 24px; }
    .timeline-step::before { content: ''; position: absolute; left: 9px; top: 6px; bottom: 0; width: 2px; background: #e2e8f0; }
    .timeline-step:last-child::before { display: none; }
    .timeline-step-dot { position: absolute; left: 0; top: 4px; width: 20px; height: 20px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 0.6rem; }
</style>
@endpush

@section('content')
{{-- ── Header ── --}}
<div class="flex flex-col lg:flex-row lg:items-start justify-between gap-4 mb-6">
    <div>
        <div class="flex items-center gap-3 mb-1">
            <h2 class="text-xl font-bold text-gray-900">{{ $do->do_number }}</h2>
            <span class="tag {{ $do->status_badge }}">{{ $do->status_label }}</span>
        </div>
        <p class="text-sm text-gray-500">
            Dibuat: {{ $do->created_at->format('d M Y, H:i') }}
        </p>
    </div>
    <div class="flex flex-wrap gap-2">
        @if($do->status === 'draft')
            <form method="POST" action="{{ route('delivery.confirm', $do->id) }}" class="inline">
                @csrf
                <button type="submit" class="btn btn-primary text-xs"
                        onclick="return confirm('Konfirmasi DO ini?')">
                    <i class="fas fa-check mr-1"></i>Confirm
                </button>
            </form>
        @endif

        @if($do->status === 'in_transit')
            <form method="POST" action="{{ route('delivery.delivered', $do->id) }}" class="inline">
                @csrf
                <button type="submit" class="btn btn-primary text-xs"
                        onclick="return confirm('Tandai DO ini sebagai terkirim?')">
                    <i class="fas fa-check-double mr-1"></i>Tandai Terkirim
                </button>
            </form>
        @endif

        <a href="{{ route('delivery.manifest', $do->id) }}" class="btn btn-ghost text-xs">
            <i class="fas fa-file-export mr-1"></i>Export Manifest
        </a>

        @if(in_array($do->status, ['draft', 'confirmed']))
            <a href="{{ route('delivery.edit', $do->id) }}" class="btn btn-ghost text-xs">
                <i class="fas fa-edit mr-1"></i>Edit
            </a>
        @endif

        @if($do->status === 'draft')
            <form method="POST" action="{{ route('delivery.destroy', $do->id) }}" class="inline"
                  onsubmit="return confirm('Hapus DO ini?')">
                @csrf @method('DELETE')
                <button type="submit" class="btn btn-delete text-xs">
                    <i class="fas fa-trash mr-1"></i>Hapus
                </button>
            </form>
        @endif
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
    {{-- ── Left: Items ── --}}
    <div class="lg:col-span-2 space-y-4">

        {{-- ── Items Table ── --}}
        <div class="card overflow-hidden">
            <div class="px-4 py-3 border-b border-gray-100">
                <h3 class="font-semibold text-gray-900">
                    <i class="fas fa-box mr-2 text-blue-500"></i>Item ({{ $do->items->count() }})
                </h3>
            </div>
            <div class="overflow-x-auto">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Lot ID</th>
                            <th>Paper Type</th>
                            <th>GSM</th>
                            <th>Width</th>
                            <th class="text-center">Qty Order</th>
                            <th class="text-center">Qty Actual</th>
                            <th>Weight</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($do->items as $item)
                            <tr>
                                <td class="font-medium text-gray-900">{{ $item->lot_id }}</td>
                                <td class="text-gray-700">{{ $item->paper_type ?? '-' }}</td>
                                <td class="text-gray-700">{{ $item->gsm ?? '-' }}</td>
                                <td class="text-gray-700">{{ $item->width ?? '-' }}</td>
                                <td class="text-center font-semibold">{{ $item->qty_order }}</td>
                                <td class="text-center text-gray-500">{{ $item->qty_actual ?? '-' }}</td>
                                <td class="text-gray-500">{{ $item->weight_kg ? $item->weight_kg . ' kg' : '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-gray-400 py-6">Tidak ada item</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- ── Assignment Card ── --}}
        @if($do->assignments->count() > 0)
            <div class="card p-5">
                <h3 class="font-semibold text-gray-900 mb-4">
                    <i class="fas fa-truck mr-2 text-blue-500"></i>Data Kendaraan
                </h3>
                @foreach($do->assignments as $assignment)
                    <div class="info-box mb-3">
                        <div class="grid grid-cols-2 gap-3 text-sm">
                            <div>
                                <span class="text-xs text-gray-400">Mobil ID</span>
                                <div class="font-semibold text-gray-900">{{ $assignment->mobil_id }}</div>
                            </div>
                            <div>
                                <span class="text-xs text-gray-400">Driver</span>
                                <div class="font-semibold text-gray-900">{{ $assignment->driver_name }}</div>
                            </div>
                            <div>
                                <span class="text-xs text-gray-400">Tanggal</span>
                                <div class="text-gray-700">{{ $assignment->assigned_date->format('d M Y') }}</div>
                            </div>
                            <div>
                                <span class="text-xs text-gray-400">Jam Berangkat</span>
                                <div class="text-gray-700">{{ $assignment->departure_time ? substr($assignment->departure_time, 0, 5) : '-' }}</div>
                            </div>
                            <div>
                                <span class="text-xs text-gray-400">Jam Tiba</span>
                                <div class="text-gray-700">{{ $assignment->arrival_time ? substr($assignment->arrival_time, 0, 5) : '-' }}</div>
                            </div>
                            <div>
                                <span class="text-xs text-gray-400">Status Sebelum</span>
                                <div class="text-gray-700">{{ $assignment->status_before }}</div>
                            </div>
                        </div>
                        @if($assignment->notes)
                            <div class="mt-2 text-xs text-gray-500">{{ $assignment->notes }}</div>
                        @endif
                    </div>
                @endforeach

                {{-- Assign form (only if confirmed and no active assignment) --}}
                @if($do->status === 'confirmed')
                    @php $hasActive = $do->assignments->contains(fn($a) => $a->deliveryOrder && $a->deliveryOrder->status === 'in_transit'); @endphp
                    @if(!$hasActive)
                        <div class="mt-4 pt-4 border-t border-gray-100">
                            <h4 class="font-semibold text-gray-800 mb-3 text-sm">Assign ke Kendaraan</h4>
                            <form method="POST" action="{{ route('delivery.assign', $do->id) }}">
                                @csrf
                                <div class="grid grid-cols-2 gap-3 mb-3">
                                    <div>
                                        <label class="text-xs text-gray-500 mb-1 block">Mobil ID</label>
                                        <input type="text" name="mobil_id" class="input-field @error('mobil_id') border-red-400 @enderror"
                                               required placeholder="B 1234 XX">
                                        @error('mobil_id')
                                            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div>
                                        <label class="text-xs text-gray-500 mb-1 block">Nama Driver</label>
                                        <input type="text" name="driver_name" class="input-field @error('driver_name') border-red-400 @enderror"
                                               required placeholder="Nama lengkap">
                                        @error('driver_name')
                                            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div>
                                        <label class="text-xs text-gray-500 mb-1 block">Tanggal Berangkat</label>
                                        <input type="date" name="assigned_date" class="input-field @error('assigned_date') border-red-400 @enderror"
                                               value="{{ date('Y-m-d') }}" required>
                                        @error('assigned_date')
                                            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div>
                                        <label class="text-xs text-gray-500 mb-1 block">Jam Berangkat</label>
                                        <input type="time" name="departure_time" class="input-field">
                                    </div>
                                    <div class="md:col-span-2">
                                        <label class="text-xs text-gray-500 mb-1 block">Catatan</label>
                                        <input type="text" name="notes" class="input-field" placeholder="Opsional">
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-primary text-xs">
                                    <i class="fas fa-truck mr-1"></i>Assign & Kirim
                                </button>
                            </form>
                        </div>
                    @endif
                @endif
            </div>
        @elseif($do->status === 'confirmed')
            {{-- Show assign form if no assignments yet --}}
            <div class="card p-5">
                <h3 class="font-semibold text-gray-900 mb-3">
                    <i class="fas fa-truck mr-2 text-blue-500"></i>Assign ke Kendaraan
                </h3>
                <form method="POST" action="{{ route('delivery.assign', $do->id) }}">
                    @csrf
                    <div class="grid grid-cols-2 gap-3 mb-3">
                        <div>
                            <label class="text-xs text-gray-500 mb-1 block">Mobil ID <span class="text-red-500">*</span></label>
                            <input type="text" name="mobil_id" class="input-field @error('mobil_id') border-red-400 @enderror"
                                   required placeholder="B 1234 XX">
                            @error('mobil_id')
                                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="text-xs text-gray-500 mb-1 block">Nama Driver <span class="text-red-500">*</span></label>
                            <input type="text" name="driver_name" class="input-field @error('driver_name') border-red-400 @enderror"
                                   required placeholder="Nama lengkap">
                            @error('driver_name')
                                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="text-xs text-gray-500 mb-1 block">Tanggal Berangkat <span class="text-red-500">*</span></label>
                            <input type="date" name="assigned_date" class="input-field @error('assigned_date') border-red-400 @enderror"
                                   value="{{ date('Y-m-d') }}" required>
                            @error('assigned_date')
                                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="text-xs text-gray-500 mb-1 block">Jam Berangkat</label>
                            <input type="time" name="departure_time" class="input-field">
                        </div>
                        <div class="md:col-span-2">
                            <label class="text-xs text-gray-500 mb-1 block">Catatan</label>
                            <input type="text" name="notes" class="input-field" placeholder="Opsional">
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary text-xs">
                        <i class="fas fa-truck mr-1"></i>Assign & Kirim
                    </button>
                </form>
            </div>
        @endif
    </div>

    {{-- ── Right: Info ── --}}
    <div class="space-y-4">
        <div class="card p-5">
            <h3 class="font-semibold text-gray-900 mb-3">
                <i class="fas fa-user mr-2 text-blue-500"></i>Info Penerima
            </h3>
            <div class="space-y-2 text-sm">
                <div>
                    <span class="text-xs text-gray-400">Nama</span>
                    <div class="font-medium text-gray-900">{{ $do->recipient_name }}</div>
                </div>
                @if($do->recipient_phone)
                    <div>
                        <span class="text-xs text-gray-400">Telepon</span>
                        <div class="text-gray-700">{{ $do->recipient_phone }}</div>
                    </div>
                @endif
                @if($do->recipient_address)
                    <div>
                        <span class="text-xs text-gray-400">Alamat</span>
                        <div class="text-gray-700">{{ $do->recipient_address }}</div>
                    </div>
                @endif
                @if($do->destination)
                    <div>
                        <span class="text-xs text-gray-400">Tujuan</span>
                        <div class="text-gray-700">{{ $do->destination }}</div>
                    </div>
                @endif
                @if($do->notes)
                    <div>
                        <span class="text-xs text-gray-400">Catatan</span>
                        <div class="text-gray-700">{{ $do->notes }}</div>
                    </div>
                @endif
            </div>
        </div>

        {{-- ── Status Timeline ── --}}
        <div class="card p-5">
            <h3 class="font-semibold text-gray-900 mb-4">
                <i class="fas fa-route mr-2 text-blue-500"></i>Status Timeline
            </h3>
            <div class="space-y-0">
                @foreach(['draft','confirmed','in_transit','delivered'] as $step)
                    @php
                        $isActive = $loop->index <= array_search($do->status, ['draft','confirmed','in_transit','delivered']);
                        $dotClass = $isActive ? 'bg-blue-500 text-white' : 'bg-gray-200 text-gray-400';
                        $lineClass = $isActive ? 'bg-blue-400' : 'bg-gray-200';
                    @endphp
                    <div class="timeline-step">
                        <div class="timeline-step-dot {{ $dotClass }}">
                            @if($isActive && $loop->index < array_search($do->status, ['draft','confirmed','in_transit','delivered']))
                                <i class="fas fa-check"></i>
                            @elseif($isActive)
                                <i class="fas fa-circle-notch fa-spin" style="font-size:0.5rem"></i>
                            @else
                                <i class="fas fa-circle" style="font-size:0.5rem"></i>
                            @endif
                        </div>
                        <div class="text-sm {{ $isActive ? 'text-gray-900 font-medium' : 'text-gray-400' }}">
                            {{ [
                                'draft' => 'Draft',
                                'confirmed' => 'Confirmed',
                                'in_transit' => 'Dalam Perjalanan',
                                'delivered' => 'Terkirim'
                            ][$step] }}
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection