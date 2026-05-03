@extends('layouts.app')

@section('title', 'Detail ' . $item->lot_id)
@section('page-title')
<i class="fas fa-box mr-2 opacity-60"></i>Detail Roll
@endsection

@section('content')
<div class="animate-in space-y-4">

    <!-- Back -->
    <a href="{{ URL::previous() }}" class="inline-flex items-center gap-2 text-xs font-medium no-underline transition hover:opacity-80" class="text-gray-500">
        <i class="fas fa-arrow-left"></i> Kembali
    </a>

    <!-- Header -->
    <div class="card p-5">
        <div class="flex flex-wrap items-start justify-between gap-3 mb-4">
            <div>
                <h2 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                    <i class="fas fa-barcode text-sm text-blue-500"></i>
                    {{ $item->lot_id }}
                    @if($item->item_id) <span class="text-sm font-normal text-gray-400">Item: {{ $item->item_id }}</span> @endif
                </h2>
                @if($item->description)
                    <p class="text-sm mt-1 text-gray-500">{{ $item->description }}</p>
                @endif
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('items.edit', $item->id) }}" class="btn btn-primary flex items-center gap-1.5" style="padding: 5px 12px; font-size: 0.75rem;">
                    <i class="fas fa-edit"></i> Edit
                </a>
                <button onclick="window.print()" class="btn btn-ghost flex items-center gap-1.5" style="padding: 5px 12px; font-size: 0.75rem;">
                    <i class="fas fa-print"></i> Print
                </button>
                @if($item->current_location)
                    <span class="tag tag-blue" style="padding: 5px 10px; font-size: 0.8rem;">
                        <i class="fas fa-map-pin"></i> {{ $item->current_location }}
                    </span>
                @endif
                @php
                    $tagClass = 'tag-gray';
                    $st = strtolower($item->status_barang ?? '');
                    if($st == 'good') $tagClass = 'tag-green';
                    elseif(in_array($st, ['hold','pending'])) $tagClass = 'tag-yellow';
                    elseif(in_array($st, ['reject','problem','rusak'])) $tagClass = 'tag-red';
                @endphp
                @if($item->status_barang && $item->status_barang != '-')
                    <span class="tag {{ $tagClass }}" style="padding: 5px 10px; font-size: 0.8rem;">{{ $item->status_barang }}</span>
                @endif
            </div>
        </div>

        <!-- Info Grid -->
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3">
            <div class="info-box">
                <div class="text-[10px] font-semibold uppercase tracking-wide mb-1" class="text-gray-400"><i class="fas fa-weight-hanging mr-1"></i>End Qty</div>
                <div class="text-lg font-bold" style="color: #4ade80;">{{ number_format($item->end_qty) }}</div>
            </div>
            <div class="info-box">
                <div class="text-[10px] font-semibold uppercase tracking-wide mb-1" class="text-gray-400"><i class="fas fa-scroll mr-1"></i>Paper Type</div>
                <div class="text-sm font-semibold text-gray-900">{{ $item->paper_type ?? '-' }}</div>
            </div>
            <div class="info-box">
                <div class="text-[10px] font-semibold uppercase tracking-wide mb-1" class="text-gray-400"><i class="fas fa-ruler mr-1"></i>GSM</div>
                <div class="text-sm font-semibold text-gray-900">{{ $item->gsm ?? '-' }}</div>
            </div>
            <div class="info-box">
                <div class="text-[10px] font-semibold uppercase tracking-wide mb-1" class="text-gray-400"><i class="fas fa-layer-group mr-1"></i>Plybond</div>
                <div class="text-sm font-semibold text-gray-900">{{ $item->plybond ?? '-' }}</div>
            </div>
            <div class="info-box">
                <div class="text-[10px] font-semibold uppercase tracking-wide mb-1" class="text-gray-400"><i class="fas fa-arrows-left-right mr-1"></i>Width</div>
                <div class="text-sm font-semibold text-gray-900">{{ $item->width ? $item->width . ' MM' : '-' }}</div>
            </div>
            <div class="info-box">
                <div class="text-[10px] font-semibold uppercase tracking-wide mb-1" class="text-gray-400"><i class="fas fa-sync mr-1"></i>Rew ID</div>
                <div class="text-sm font-semibold text-gray-900">{{ $item->rew_id ?? '-' }}</div>
            </div>
            <div class="info-box">
                <div class="text-[10px] font-semibold uppercase tracking-wide mb-1" class="text-gray-400"><i class="fas fa-circle mr-1"></i>Diameter</div>
                <div class="text-sm font-semibold text-gray-900">{{ $item->diameter ?? '-' }}</div>
            </div>
            <div class="info-box">
                <div class="text-[10px] font-semibold uppercase tracking-wide mb-1" class="text-gray-400"><i class="fas fa-arrows-up-down mr-1"></i>Thickness</div>
                <div class="text-sm font-semibold text-gray-900">{{ $item->thickness ?? '-' }}</div>
            </div>
            <div class="info-box">
                <div class="text-[10px] font-semibold uppercase tracking-wide mb-1" class="text-gray-400"><i class="fas fa-star mr-1"></i>Grade</div>
                <div class="text-sm font-semibold text-gray-900">{{ $item->grade ?? '-' }}</div>
            </div>
            <div class="info-box">
                <div class="text-[10px] font-semibold uppercase tracking-wide mb-1" class="text-gray-400"><i class="fas fa-calendar mr-1"></i>Tanggal</div>
                <div class="text-sm font-semibold text-gray-900">{{ $item->tr_date ? \Carbon\Carbon::parse($item->tr_date)->format('d M Y') : '-' }}</div>
            </div>
        </div>

        @if($item->comments)
            <div class="mt-4 p-3 rounded-xl text-sm bg-gray-50 border border-gray-200">
                <span class="text-gray-400"><i class="fas fa-sticky-note mr-1"></i>Comments:</span>
                <span class="text-gray-700">{{ $item->comments }}</span>
            </div>
        @endif
    </div>

    <!-- Location Timeline -->
    <div class="card p-5">
        <h3 class="text-sm font-semibold text-gray-900 mb-4 flex items-center gap-2">
            <i class="fas fa-route text-xs" class="text-blue-500"></i>
            Tracking Lokasi & SO
        </h3>
        <div class="timeline">
            @if($item->so_september && $item->so_september != '-')
            <div class="timeline-item">
                <div class="text-[10px] font-semibold uppercase tracking-wide" class="text-gray-400">SO September 2025</div>
                <div class="text-sm font-semibold text-gray-900 mt-0.5"><i class="fas fa-file-invoice mr-1.5" style="color: #a78bfa;"></i>{{ $item->so_september }}</div>
            </div>
            @endif
            @if($item->so_desember && $item->so_desember != '-')
            <div class="timeline-item filled">
                <div class="text-[10px] font-semibold uppercase tracking-wide" class="text-gray-400">SO Desember 2025</div>
                <div class="text-sm font-semibold text-gray-900 mt-0.5"><i class="fas fa-file-invoice mr-1.5" style="color: #a78bfa;"></i>{{ $item->so_desember }}</div>
                @if($item->pic_2025 && $item->pic_2025 != '-')
                    <div class="text-xs mt-1" class="text-gray-500"><i class="fas fa-user mr-1"></i>PIC: {{ $item->pic_2025 }}</div>
                @endif
            </div>
            @endif
            @if($item->lokasi_receiving && $item->lokasi_receiving != '-')
            <div class="timeline-item">
                <div class="text-[10px] font-semibold uppercase tracking-wide" class="text-gray-400">Lokasi Receiving</div>
                <div class="text-sm font-semibold text-gray-900 mt-0.5"><i class="fas fa-map-marker-alt mr-1.5" class="text-blue-500"></i>{{ $item->lokasi_receiving }}</div>
            </div>
            @endif
            @if($item->receiving_2026 && $item->receiving_2026 != '-')
            <div class="timeline-item filled">
                <div class="text-[10px] font-semibold uppercase tracking-wide" class="text-gray-400">Receiving 2026</div>
                <div class="text-sm font-semibold text-gray-900 mt-0.5"><i class="fas fa-warehouse mr-1.5" class="text-blue-500"></i>{{ $item->receiving_2026 }}</div>
            </div>
            @endif
            @if($item->pic_2026 && $item->pic_2026 != '-')
            <div class="timeline-item">
                <div class="text-[10px] font-semibold uppercase tracking-wide" class="text-gray-400">PIC 2026</div>
                <div class="text-sm font-semibold text-gray-900 mt-0.5"><i class="fas fa-user mr-1.5" style="color: #2dd4bf;"></i>{{ $item->pic_2026 }}</div>
            </div>
            @endif
            @if($item->rcv_cnv_2026 && $item->rcv_cnv_2026 != '-')
            <div class="timeline-item">
                <div class="text-[10px] font-semibold uppercase tracking-wide" class="text-gray-400">RCV/CNV 2026</div>
                <div class="text-sm font-semibold text-gray-900 mt-0.5"><i class="fas fa-right-left mr-1.5" style="color: #facc15;"></i>{{ $item->rcv_cnv_2026 }}</div>
            </div>
            @endif
            @if($item->so_maret_2026 && $item->so_maret_2026 != '-')
            <div class="timeline-item filled">
                <div class="text-[10px] font-semibold uppercase tracking-wide" class="text-gray-400">SO Maret 2026</div>
                <div class="text-sm font-semibold text-gray-900 mt-0.5"><i class="fas fa-file-invoice mr-1.5" style="color: #a78bfa;"></i>{{ $item->so_maret_2026 }}</div>
            </div>
            @endif
            @if(!$item->current_location)
                <div class="text-center py-8">
                    <i class="fas fa-route text-xl block mb-2" class="text-gray-400"></i>
                    <span class="text-xs" class="text-gray-400">Belum ada data tracking</span>
                </div>
            @endif
        </div>
    </div>

    <!-- Related Defects -->
    @if($defects->count() > 0)
    <div class="card overflow-hidden" style="border-left: 3px solid #ef4444;">
        <div class="px-5 pt-4 pb-2">
            <h3 class="text-sm font-semibold flex items-center gap-2" style="color: #f87171;">
                <i class="fas fa-triangle-exclamation text-xs"></i>
                Defect Terkait ({{ $defects->count() }})
            </h3>
        </div>

        <!-- Desktop -->
        <div class="hidden md:block">
            <div class="overflow-x-auto">
                <table class="data-table">
                    <thead><tr><th>#</th><th>Tahun</th><th>Paper</th><th>GSM</th><th>Alasan</th><th>Tanggal</th><th>Keterangan</th></tr></thead>
                    <tbody>
                        @foreach($defects as $i => $def)
                        <tr>
                            <td class="text-gray-400">{{ $i+1 }}</td>
                            <td><span class="tag tag-gray">{{ $def->year }}</span></td>
                            <td>{{ $def->paper_type ?? '-' }}</td>
                            <td>{{ $def->gsm ?? '-' }}</td>
                            <td><span class="tag tag-red">{{ $def->reason }}</span></td>
                            <td>{{ $def->defect_date ?? '-' }}</td>
                            <td class="truncate" style="max-width: 150px;">{{ $def->keterangan ?? '-' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Mobile -->
        <div class="md:hidden p-4 space-y-2">
            @foreach($defects as $def)
            <div class="mobile-card">
                <div class="flex justify-between text-xs mb-1.5">
                    <span class="text-gray-400">Tahun</span>
                    <span class="tag tag-gray">{{ $def->year }}</span>
                </div>
                <div class="flex justify-between text-xs mb-1.5">
                    <span class="text-gray-400">Alasan</span>
                    <span class="tag tag-red">{{ $def->reason }}</span>
                </div>
                <div class="flex justify-between text-xs">
                    <span class="text-gray-400">Tanggal</span>
                    <span class="text-gray-600">{{ $def->defect_date ?? '-' }}</span>
                </div>
                @if($def->keterangan)
                <div class="flex justify-between text-xs mt-1.5">
                    <span class="text-gray-400">Ket</span>
                    <span class="truncate text-right text-gray-600" style="max-width: 60%;">{{ Str::limit($def->keterangan, 40) }}</span>
                </div>
                @endif
            </div>
            @endforeach
        </div>
    </div>
    @endif

</div>
@endsection
