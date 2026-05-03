@extends('layouts.app')

@section('title', 'Detail ' . $item->lot_id)
@section('page-title', '<i class="fas fa-box mr-2"></i> Detail Roll Item')

@section('content')
<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-100">Detail Roll Item</h1>
    <a href="{{ URL::previous() }}" class="btn btn-outline-primary btn-sm">
        <i class="fas fa-arrow-left mr-1"></i> Kembali
    </a>
</div>

<!-- Info Cards -->
<div class="row">
    <!-- Main Info -->
    <div class="col-xl-6 col-lg-6 mb-4">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-info-circle mr-2"></i>Informasi Roll</h6>
            </div>
            <div class="card-body">
                <table class="table table-borderless mb-0">
                    <tbody>
                        <tr>
                            <td class="text-gray-500" style="width: 35%"><i class="fas fa-barcode mr-2"></i>Lot ID</td>
                            <td class="font-weight-bold text-gray-100">{{ $item->lot_id }}</td>
                        </tr>
                        <tr class="border-top" style="border-color: #30363d !important;">
                            <td class="text-gray-500"><i class="fas fa-hashtag mr-2"></i>Item ID</td>
                            <td class="text-gray-100">{{ $item->item_id }}</td>
                        </tr>
                        <tr class="border-top" style="border-color: #30363d !important;">
                            <td class="text-gray-500"><i class="fas fa-sync mr-2"></i>Rew ID</td>
                            <td class="text-gray-100">{{ $item->rew_id ?? '-' }}</td>
                        </tr>
                        <tr class="border-top" style="border-color: #30363d !important;">
                            <td class="text-gray-500"><i class="fas fa-align-left mr-2"></i>Description</td>
                            <td class="text-gray-100">{{ $item->description ?? '-' }}</td>
                        </tr>
                        <tr class="border-top" style="border-color: #30363d !important;">
                            <td class="text-gray-500"><i class="fas fa-weight-hanging mr-2"></i>End Qty</td>
                            <td class="font-weight-bold text-gray-100" style="font-size: 1.1rem;">{{ number_format($item->end_qty) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Specs -->
    <div class="col-xl-6 col-lg-6 mb-4">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-cogs mr-2"></i>Spesifikasi</h6>
            </div>
            <div class="card-body">
                <table class="table table-borderless mb-0">
                    <tbody>
                        <tr>
                            <td class="text-gray-500" style="width: 35%"><i class="fas fa-scroll mr-2"></i>Paper Type</td>
                            <td>
                                @if($item->paper_type)
                                    <span class="badge badge-secondary" style="font-size: 0.85rem;">{{ $item->paper_type }}</span>
                                @else
                                    <span class="text-gray-500">-</span>
                                @endif
                            </td>
                        </tr>
                        <tr class="border-top" style="border-color: #30363d !important;">
                            <td class="text-gray-500"><i class="fas fa-ruler mr-2"></i>GSM</td>
                            <td class="text-gray-100">{{ $item->gsm ?? '-' }}</td>
                        </tr>
                        <tr class="border-top" style="border-color: #30363d !important;">
                            <td class="text-gray-500"><i class="fas fa-layer-group mr-2"></i>Plybond</td>
                            <td class="text-gray-100">{{ $item->plybond ?? '-' }}</td>
                        </tr>
                        <tr class="border-top" style="border-color: #30363d !important;">
                            <td class="text-gray-500"><i class="fas fa-arrows-alt-h mr-2"></i>Width</td>
                            <td class="text-gray-100">{{ $item->width ? $item->width . ' MM' : '-' }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Location & Status -->
<div class="row">
    <div class="col-xl-6 col-lg-6 mb-4">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-map-marker-alt mr-2"></i>Lokasi & Waktu</h6>
            </div>
            <div class="card-body">
                <table class="table table-borderless mb-0">
                    <tbody>
                        <tr>
                            <td class="text-gray-500" style="width: 35%"><i class="fas fa-warehouse mr-2"></i>Lokasi</td>
                            <td class="text-gray-100">{{ $item->location_id ?? '-' }}</td>
                        </tr>
                        <tr class="border-top" style="border-color: #30363d !important;">
                            <td class="text-gray-500"><i class="fas fa-calendar mr-2"></i>Tanggal</td>
                            <td class="text-gray-100">{{ $item->tr_date ? \Carbon\Carbon::parse($item->tr_date)->format('d M Y') : '-' }}</td>
                        </tr>
                        <tr class="border-top" style="border-color: #30363d !important;">
                            <td class="text-gray-500"><i class="fas fa-clock mr-2"></i>Waktu</td>
                            <td class="text-gray-100">{{ $item->tr_time ?? '-' }}</td>
                        </tr>
                        <tr class="border-top" style="border-color: #30363d !important;">
                            <td class="text-gray-500"><i class="fas fa-file-alt mr-2"></i>SO Number</td>
                            <td class="text-gray-100">{{ $item->so_number ?? '-' }}</td>
                        </tr>
                        <tr class="border-top" style="border-color: #30363d !important;">
                            <td class="text-gray-500"><i class="fas fa-user mr-2"></i>PIC</td>
                            <td class="text-gray-100">{{ $item->pic ?? '-' }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-xl-6 col-lg-6 mb-4">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-clipboard-check mr-2"></i>Status & Catatan</h6>
            </div>
            <div class="card-body">
                <table class="table table-borderless mb-0">
                    <tbody>
                        <tr>
                            <td class="text-gray-500" style="width: 35%"><i class="fas fa-tag mr-2"></i>Status</td>
                            <td>
                                @php
                                    $badgeClass = 'badge-na';
                                    $status = strtolower($item->status_barang ?? '');
                                    if($status == 'good') $badgeClass = 'badge-good';
                                    elseif(in_array($status, ['hold', 'pending'])) $badgeClass = 'badge-hold';
                                    elseif(in_array($status, ['reject', 'problem', 'rusak'])) $badgeClass = 'badge-problem';
                                @endphp
                                @if($item->status_barang && $item->status_barang != '-')
                                    <span class="badge {{ $badgeClass }}" style="font-size: 1rem;">{{ $item->status_barang }}</span>
                                @else
                                    <span class="text-gray-500">-</span>
                                @endif
                            </td>
                        </tr>
                        <tr class="border-top" style="border-color: #30363d !important;">
                            <td class="text-gray-500"><i class="fas fa-sticky-note mr-2"></i>Notes</td>
                            <td class="text-gray-100">{{ $item->notes ?? '-' }}</td>
                        </tr>
                        <tr class="border-top" style="border-color: #30363d !important;">
                            <td class="text-gray-500"><i class="fas fa-database mr-2"></i>Input</td>
                            <td class="text-gray-500">{{ $item->created_at ? $item->created_at->format('d M Y H:i') : '-' }}</td>
                        </tr>
                        <tr class="border-top" style="border-color: #30363d !important;">
                            <td class="text-gray-500"><i class="fas fa-edit mr-2"></i>Update</td>
                            <td class="text-gray-500">{{ $item->updated_at ? $item->updated_at->format('d M Y H:i') : '-' }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Defects for this item -->
@if($defects->count() > 0)
<div class="row">
    <div class="col-12 mb-4">
        <div class="card shadow border-left-danger">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-danger">
                    <i class="fas fa-exclamation-triangle mr-2"></i>Defect Terkait ({{ $defects->count() }} items)
                </h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Tahun</th>
                                <th>Rew ID</th>
                                <th>Paper Type</th>
                                <th>GSM</th>
                                <th>Alasan</th>
                                <th>Kategori</th>
                                <th>Tanggal Defect</th>
                                <th>Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($defects as $i => $def)
                            <tr>
                                <td>{{ $i + 1 }}</td>
                                <td>{{ $def->year }}</td>
                                <td>{{ $def->rew_id }}</td>
                                <td>{{ $def->paper_type ?? '-' }}</td>
                                <td>{{ $def->gsm ?? '-' }}</td>
                                <td><span class="badge badge-danger">{{ $def->reason }}</span></td>
                                <td>{{ $def->category ?? '-' }}</td>
                                <td>{{ $def->defect_date }}</td>
                                <td class="text-gray-500">{{ $def->keterangan ?? '-' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endif
@endsection
