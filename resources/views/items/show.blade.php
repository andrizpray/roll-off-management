@extends('layouts.app')

@section('title', 'Detail ' . $item->lot_id)
@section('page-title', '<i class="fas fa-box mr-1"></i> Detail Roll')

@section('content')
<!-- Back -->
<div class="tw-mb-3">
    <a href="{{ URL::previous() }}" class="btn btn-outline-secondary btn-sm" style="border-radius:8px;">
        <i class="fas fa-arrow-left mr-1"></i> Kembali
    </a>
</div>

<!-- Header Card -->
<div class="card shadow tw-mb-3">
    <div class="card-header tw-py-3 tw-px-4">
        <div class="tw-flex tw-items-center tw-justify-between tw-flex-wrap tw-gap-2">
            <div>
                <h6 class="m-0 font-weight-bold" style="color: var(--accent-blue); font-size: 0.95rem;">
                    <i class="fas fa-barcode mr-1"></i>{{ $item->lot_id }}
                    @if($item->item_id) <span class="text-gray-500 tw-ml-2" style="font-size:0.8rem;">Item: {{ $item->item_id }}</span> @endif
                </h6>
                @if($item->description)<span class="text-gray-500" style="font-size:0.8rem;">{{ $item->description }}</span>@endif
            </div>
            <div class="tw-flex tw-items-center tw-gap-2 tw-flex-wrap">
                @if($item->current_location)
                    <span class="badge badge-loc" style="font-size:0.8rem; padding:5px 10px;">
                        <i class="fas fa-map-pin mr-1"></i>{{ $item->current_location }}
                    </span>
                @endif
                @php
                    $bc='badge-na'; $st=strtolower($item->status_barang ?? '');
                    if($st=='good') $bc='badge-good';
                    elseif(in_array($st,['hold','pending'])) $bc='badge-hold';
                    elseif(in_array($st,['reject','problem','rusak'])) $bc='badge-problem';
                @endphp
                @if($item->status_barang && $item->status_barang != '-')
                    <span class="badge {{ $bc }}" style="font-size:0.8rem; padding:5px 10px;">{{ $item->status_barang }}</span>
                @endif
            </div>
        </div>
    </div>
    <div class="card-body">
        <div class="info-grid">
            <div class="info-cell">
                <div class="info-label"><i class="fas fa-weight-hanging mr-1"></i>End Qty</div>
                <div class="info-value" style="font-size:1.15rem; color:var(--accent-green);">{{ number_format($item->end_qty) }}</div>
            </div>
            <div class="info-cell">
                <div class="info-label"><i class="fas fa-scroll mr-1"></i>Paper Type</div>
                <div class="info-value">{{ $item->paper_type ?? '-' }}</div>
            </div>
            <div class="info-cell">
                <div class="info-label"><i class="fas fa-ruler mr-1"></i>GSM</div>
                <div class="info-value">{{ $item->gsm ?? '-' }}</div>
            </div>
            <div class="info-cell">
                <div class="info-label"><i class="fas fa-layer-group mr-1"></i>Plybond</div>
                <div class="info-value">{{ $item->plybond ?? '-' }}</div>
            </div>
            <div class="info-cell">
                <div class="info-label"><i class="fas fa-arrows-alt-h mr-1"></i>Width</div>
                <div class="info-value">{{ $item->width ? $item->width . ' MM' : '-' }}</div>
            </div>
            <div class="info-cell">
                <div class="info-label"><i class="fas fa-sync mr-1"></i>Rew ID</div>
                <div class="info-value">{{ $item->rew_id ?? '-' }}</div>
            </div>
            <div class="info-cell">
                <div class="info-label"><i class="fas fa-circle mr-1"></i>Diameter</div>
                <div class="info-value">{{ $item->diameter ?? '-' }}</div>
            </div>
            <div class="info-cell">
                <div class="info-label"><i class="fas fa-arrows-alt-v mr-1"></i>Thickness</div>
                <div class="info-value">{{ $item->thickness ?? '-' }}</div>
            </div>
            <div class="info-cell">
                <div class="info-label"><i class="fas fa-star mr-1"></i>Grade</div>
                <div class="info-value">{{ $item->grade ?? '-' }}</div>
            </div>
            <div class="info-cell">
                <div class="info-label"><i class="fas fa-calendar mr-1"></i>Tanggal</div>
                <div class="info-value">{{ $item->tr_date ? \Carbon\Carbon::parse($item->tr_date)->format('d M Y') : '-' }}</div>
            </div>
        </div>
        @if($item->comments)
            <div class="tw-mt-3 tw-p-3 tw-rounded-lg" style="background:var(--bg-card-header); font-size:0.8rem;">
                <span class="text-gray-500"><i class="fas fa-sticky-note mr-1"></i>Comments: </span>
                <span style="color:var(--text-secondary);">{{ $item->comments }}</span>
            </div>
        @endif
    </div>
</div>

<!-- Location Timeline -->
<div class="card shadow tw-mb-3">
    <div class="card-header tw-py-2 tw-px-4">
        <h6 class="m-0 tw-font-bold" style="color: var(--accent-blue); font-size: 0.82rem;">
            <i class="fas fa-route mr-1"></i>Tracking Lokasi & SO
        </h6>
    </div>
    <div class="card-body">
        <div class="loc-timeline">
            @if($item->so_september && $item->so_september != '-')
            <div class="loc-timeline-item">
                <div class="loc-period">SO September 2025</div>
                <div class="loc-value"><i class="fas fa-file-invoice mr-1" style="color:var(--accent-purple);"></i>{{ $item->so_september }}</div>
            </div>
            @endif
            @if($item->so_desember && $item->so_desember != '-')
            <div class="loc-timeline-item">
                <div class="loc-period">SO Desember 2025</div>
                <div class="loc-value"><i class="fas fa-file-invoice mr-1" style="color:var(--accent-purple);"></i>{{ $item->so_desember }}</div>
                @if($item->pic_2025 && $item->pic_2025 != '-')
                    <div class="text-gray-500" style="font-size:0.78rem; margin-top:2px;"><i class="fas fa-user mr-1"></i>PIC: {{ $item->pic_2025 }}</div>
                @endif
            </div>
            @endif
            @if($item->lokasi_receiving && $item->lokasi_receiving != '-')
            <div class="loc-timeline-item">
                <div class="loc-period">Lokasi Receiving</div>
                <div class="loc-value"><i class="fas fa-map-marker-alt mr-1" style="color:var(--accent-blue);"></i>{{ $item->lokasi_receiving }}</div>
            </div>
            @endif
            @if($item->receiving_2026 && $item->receiving_2026 != '-')
            <div class="loc-timeline-item">
                <div class="loc-period">Receiving 2026</div>
                <div class="loc-value"><i class="fas fa-warehouse mr-1" style="color:var(--accent-blue);"></i>{{ $item->receiving_2026 }}</div>
            </div>
            @endif
            @if($item->pic_2026 && $item->pic_2026 != '-')
            <div class="loc-timeline-item">
                <div class="loc-period">PIC 2026</div>
                <div class="loc-value"><i class="fas fa-user mr-1" style="color:var(--accent-teal);"></i>{{ $item->pic_2026 }}</div>
            </div>
            @endif
            @if($item->rcv_cnv_2026 && $item->rcv_cnv_2026 != '-')
            <div class="loc-timeline-item">
                <div class="loc-period">RCV/CNV 2026</div>
                <div class="loc-value"><i class="fas fa-exchange-alt mr-1" style="color:var(--accent-yellow);"></i>{{ $item->rcv_cnv_2026 }}</div>
            </div>
            @endif
            @if($item->so_maret_2026 && $item->so_maret_2026 != '-')
            <div class="loc-timeline-item">
                <div class="loc-period">SO Maret 2026</div>
                <div class="loc-value"><i class="fas fa-file-invoice mr-1" style="color:var(--accent-purple);"></i>{{ $item->so_maret_2026 }}</div>
            </div>
            @endif
            @if(!$item->current_location)
                <div class="text-center text-gray-500 tw-py-4">
                    <i class="fas fa-route fa-2x tw-mb-2"></i><br>Belum ada data tracking
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Defects -->
@if($defects->count() > 0)
<div class="card shadow tw-mb-3" style="border-left: 3px solid var(--accent-red);">
    <div class="card-header tw-py-2 tw-px-4">
        <h6 class="m-0 tw-font-bold" style="color: var(--accent-red); font-size: 0.82rem;">
            <i class="fas fa-exclamation-triangle mr-1"></i>Defect Terkait ({{ $defects->count() }})
        </h6>
    </div>
    <div class="card-body tw-p-2">
        <div class="desktop-table">
            <div class="table-responsive">
                <table class="table table-bordered table-hover mb-0" style="font-size:0.78rem;">
                    <thead><tr><th>#</th><th>Tahun</th><th>Paper</th><th>GSM</th><th>Alasan</th><th>Tanggal</th><th>Keterangan</th></tr></thead>
                    <tbody>
                        @foreach($defects as $i => $def)
                        <tr>
                            <td>{{ $i+1 }}</td>
                            <td><span class="badge badge-secondary">{{ $def->year }}</span></td>
                            <td>{{ $def->paper_type ?? '-' }}</td>
                            <td>{{ $def->gsm ?? '-' }}</td>
                            <td><span class="badge badge-danger">{{ $def->reason }}</span></td>
                            <td>{{ $def->defect_date ?? '-' }}</td>
                            <td class="truncate" style="max-width:140px;">{{ $def->keterangan ?? '-' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="mobile-cards">
            @foreach($defects as $def)
            <div class="mobile-item-card">
                <div class="info-row"><span class="info-label">Tahun</span><span class="info-value"><span class="badge badge-secondary">{{ $def->year }}</span></span></div>
                <div class="info-row"><span class="info-label">Alasan</span><span class="info-value"><span class="badge badge-danger">{{ $def->reason }}</span></span></div>
                <div class="info-row"><span class="info-label">Tanggal</span><span class="info-value">{{ $def->defect_date ?? '-' }}</span></div>
                @if($def->keterangan)<div class="info-row"><span class="info-label">Ket</span><span class="info-value truncate" style="max-width:60%;">{{ Str::limit($def->keterangan, 40) }}</span></div>@endif
            </div>
            @endforeach
        </div>
    </div>
</div>
@endif
@endsection
