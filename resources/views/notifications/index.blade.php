@extends('layouts.app')

@section('page-title', 'Notifikasi')
@section('title', 'Notifikasi — Roll Off Management')

@section('content')
<div class="max-w-4xl mx-auto space-y-4">

    <!-- Header -->
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-blue-100 flex items-center justify-center">
                <i class="fas fa-bell text-blue-500"></i>
            </div>
            <div>
                <h2 class="text-lg font-bold text-gray-800">Semua Notifikasi</h2>
                <p class="text-xs text-gray-400">{{ number_format($total_count) }} notifikasi aktif</p>
            </div>
        </div>
        <a href="{{ route('dashboard') }}" class="text-xs text-blue-500 hover:text-blue-600 font-medium">
            <i class="fas fa-arrow-left mr-1"></i>Kembali
        </a>
    </div>

    @if($total_count === 0)
    <div class="card p-12 text-center">
        <div class="w-16 h-16 rounded-full bg-green-50 flex items-center justify-center mx-auto mb-4">
            <i class="fas fa-check-circle text-2xl text-green-400"></i>
        </div>
        <h3 class="text-sm font-bold text-gray-800 mb-1">Semua Aman!</h3>
        <p class="text-xs text-gray-400">Tidak ada notifikasi saat ini</p>
    </div>
    @endif

    <!-- Item Tanpa Lokasi -->
    @if($no_location['count'] > 0)
    <div class="card overflow-hidden">
        <div class="px-5 py-3.5 bg-red-50 border-b border-red-100 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-lg bg-red-100 flex items-center justify-center">
                    <i class="fas fa-map-pin text-red-500 text-xs"></i>
                </div>
                <div>
                    <h3 class="text-sm font-semibold text-red-700">Item Tanpa Lokasi</h3>
                    <p class="text-[10px] text-red-400">Roll item belum memiliki lokasi tracking</p>
                </div>
            </div>
            <span class="text-xs font-bold bg-red-100 text-red-600 px-2.5 py-1 rounded-full">{{ number_format($no_location['count']) }}</span>
        </div>
        <div class="divide-y divide-gray-50">
            @foreach($no_location['items'] as $item)
            <a href="{{ route('items.show', $item->id) }}" class="flex items-center gap-4 px-5 py-3 hover:bg-gray-50 transition text-decoration-none">
                <div class="w-9 h-9 rounded-lg bg-red-50 flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-map-pin text-red-300 text-xs"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-gray-800 truncate">{{ $item->lot_id }}
                        @if($item->paper_type)
                            <span class="text-xs text-gray-400 font-normal ml-1">· {{ $item->paper_type }}</span>
                        @endif
                    </p>
                    <p class="text-xs text-gray-400 truncate">
                        @if($item->gsm){{ $item->gsm }}@endif
                        @if($item->width)
                            {{ $item->gsm ? ' · ' : '' }}{{ $item->width }}mm
                        @endif
                        @if(!$item->gsm && !$item->width)Belum ada data lengkap@endif
                    </p>
                </div>
                <div class="text-right flex-shrink-0">
                    <span class="text-[10px] text-gray-300">
                        {{ $item->created_at->diffForHumans() }}
                    </span>
                    <p class="text-[10px] text-gray-300 mt-0.5">{{ $item->created_at->format('d M Y') }}</p>
                </div>
                <i class="fas fa-chevron-right text-gray-200 text-xs"></i>
            </a>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Defect Baru -->
    @if($recent_defects['count'] > 0)
    <div class="card overflow-hidden">
        <div class="px-5 py-3.5 bg-amber-50 border-b border-amber-100 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-lg bg-amber-100 flex items-center justify-center">
                    <i class="fas fa-triangle-exclamation text-amber-500 text-xs"></i>
                </div>
                <div>
                    <h3 class="text-sm font-semibold text-amber-700">Defect Baru (7 Hari Terakhir)</h3>
                    <p class="text-[10px] text-amber-400">Barang bermasalah yang baru ditambahkan</p>
                </div>
            </div>
            <span class="text-xs font-bold bg-amber-100 text-amber-600 px-2.5 py-1 rounded-full">{{ number_format($recent_defects['count']) }}</span>
        </div>
        <div class="divide-y divide-gray-50">
            @foreach($recent_defects['items'] as $item)
            <a href="{{ route('defects.index') }}" class="flex items-center gap-4 px-5 py-3 hover:bg-gray-50 transition text-decoration-none">
                <div class="w-9 h-9 rounded-lg bg-amber-50 flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-triangle-exclamation text-amber-300 text-xs"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-gray-800 truncate">{{ $item->lot_id }}
                        @if($item->paper_type)
                            <span class="text-xs text-gray-400 font-normal ml-1">· {{ $item->paper_type }}</span>
                        @endif
                    </p>
                    <p class="text-xs text-gray-400 truncate">
                        @if($item->reason){{ $item->reason }}@elseif($item->gsm){{ $item->gsm }}@else-Barang bermasalah@endif
                    </p>
                </div>
                <div class="text-right flex-shrink-0">
                    <span class="text-[10px] text-gray-300">
                        {{ $item->created_at->diffForHumans() }}
                    </span>
                    <p class="text-[10px] text-gray-300 mt-0.5">{{ $item->created_at->format('d M Y') }}</p>
                </div>
                <i class="fas fa-chevron-right text-gray-200 text-xs"></i>
            </a>
            @endforeach
        </div>
    </div>
    @endif

</div>
@endsection
