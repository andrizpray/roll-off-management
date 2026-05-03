@extends('layouts.app')

@section('title', 'Detail ' . $item->lot_id)
@section('page-title', '<i class="fas fa-box mr-1"></i> Detail Roll')

@section('content')
<!-- Back button -->
<div class="mb-3">
    <a href="{{ URL::previous() }}" class="btn btn-outline-secondary btn-sm" style="border-radius:6px;">
        <i class="fas fa-arrow-left mr-1"></i> Kembali
    </a>
</div>

<!-- Main Info Header -->
<div class="card shadow mb-3">
    <div class="card-header py-2">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div>
                <h6 class="m-0 font-weight-bold text-primary" style="font-size:0.9rem;">
                    <i class="fas fa-barcode mr-1"></i>{{ $item->lot_id }}
                    @if($item->item_id) <small class="text-gray-500 ml-2">Item: {{ $item->item_id }}</small> @endif
                </h6>
                @if($item->description)<small class="text-gray-500">{{ $item->description }}</small>@endif
            </div>
            @php
                $bc='badge-na'; $st=strtolower($item->status_barang ?? '');
                if($st=='good') $bc='badge-good';
                elseif(in_array($st,['hold','pending'])) $bc='badge-hold';
                elseif(in_array($st,['reject','problem','rusak'])) $bc='badge-problem';
            @endphp
            @if($item->status_barang && $item->status_barang != '-')
                <span class="badge {{ $bc }}" style="font-size:0.8rem; padding:6px 12px;">{{ $item->status_barang }}</span>
            @endif
        </div>
    </div>
    <div class="card-body">
        <div class="info-grid">
            <div class="info-cell">
                <div class="info-label"><i class="fas fa-weight-hanging mr-1"></i>End Qty</div>
                <div class="info-value" style="font-size:1.2rem; color:var(--accent-green);">{{ number_format($item->end_qty) }}</div>
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
            <div class="mt-3 p-2" style="background:var(--bg-card-header);border-radius:6px;font-size:0.8rem;">
                <span class="text-gray-500"><i class="fas fa-sticky-note mr-1"></i>Comments:</span>
                <span class="text-gray-300">{{ $item->comments }}</span>
            </div>
        @endif
    </div>
</div>

<!-- Location Tracking Timeline -->
<div class="card shadow mb-3">
    <div class="card-header py-2">
        <h6 class="m-0 font-weight-bold text-primary" style="font-size:0.85rem;"><i class="fas fa-route mr-1"></i>Tracking Lokasi & SO</h6>
    </div>
    <div class="card-body">
        <div class="loc-timeline">
            @if($item->so_september && $item->so_september != '-')
            <div class="loc-timeline-item">
                <div class="loc-period">SO September 2025</div>
                <div class="loc-value"><i class="fas fa-file-invoice mr-1 text-purple"></i>{{ $item->so_september }}</div>
            </div>
            @endif
            @if($item->so_desember && $item->so_desember != '-')
            <div class="loc-timeline-item">
                <div class="loc-period">SO Desember 2025</div>
                <div class="loc-value"><i class="fas fa-file-invoice mr-1 text-purple"></i>{{ $item->so_desember }}</div>
                @if($item->pic_2025 && $item->pic_2025 != '-')
                    <div class="text-gray-500" style="font-size:0.8rem; margin-top:2px;"><i class="fas fa-user mr-1"></i>PIC: {{ $item->pic_2025 }}</div>
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
                <div class="loc-value"><i class="fas fa-file-invoice mr-1 text-purple"></i>{{ $item->so_maret_2026 }}</div>
            </div>
            @endif
            @if(!$item->so_september || $item->so_september == '-')
                @if(!$item->so_desember || $item->so_desember == '-')
                    @if(!$item->receiving_2026 || $item->receiving_2026 == '-')
                        <div class="text-center text-gray-500 py-3"><i class="fas fa-route fa-2x mb-2"></i><br>Belum ada data tracking</div>
                    @endif
                @endif
            @endif
        </div>
    </div>
</div>

<!-- Related Defects -->
@if($defects->count() > 0)
<div class="card shadow mb-3 border-left-danger">
    <div class="card-header py-2">
        <h6 class="m-0 font-weight-bold text-danger" style="font-size:0.85rem;">
            <i class="fas fa-exclamation-triangle mr-1"></i>Defect Terkait ({{ $defects->count() }})
        </h6>
    </div>
    <div class="card-body p-2">
        <!-- Desktop -->
        <div class="desktop-table">
            <div class="table-responsive">
                <table class="table table-bordered table-hover mb-0" style="font-size:0.8rem;">
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
                            <td class="truncate" style="max-width:150px;">{{ $def->keterangan ?? '-' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <!-- Mobile -->
        <div class="mobile-cards">
            @foreach($defects as $def)
            <div class="mobile-item-card">
                <div class="info-row"><span class="info-label">Tahun</span><span class="info-value"><span class="badge badge-secondary">{{ $def->year }}</span></span></div>
                <div class="info-row"><span class="info-label">Alasan</span><span class="info-value"><span class="badge badge-danger">{{ $def->reason }}</span></span></div>
                <div class="info-row"><span class="info-label">Tanggal</span><span class="info-value">{{ $def->defect_date ?? '-' }}</span></div>
                @if($def->keterangan)<div class="info-row"><span class="info-label">Ket</span><span class="info-value">{{ Str::limit($def->keterangan, 40) }}</span></div>@endif
            </div>
            @endforeach
        </div>
    </div>
</div>
@endif
@endsection
