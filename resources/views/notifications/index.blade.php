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
                <p class="text-xs text-gray-400">{{ number_format($total_count) }} belum dibaca</p>
            </div>
        </div>
        <div class="flex items-center gap-2">
            @if($total_count > 0)
            <button onclick="markAllReadPage()" id="markAllPageBtn" class="text-xs font-medium px-3 py-1.5 rounded-lg bg-blue-500 text-white hover:bg-blue-600 transition">
                <i class="fas fa-check-double mr-1"></i>Tandai Semua Dibaca
            </button>
            @endif
            <a href="{{ route('dashboard') }}" class="text-xs text-gray-400 hover:text-gray-600 font-medium">
                <i class="fas fa-arrow-left mr-1"></i>Kembali
            </a>
        </div>
    </div>

    @if($total_count === 0 && $no_location['count'] == 0 && $recent_defects['count'] == 0)
    <div class="card p-12 text-center">
        <div class="w-16 h-16 rounded-full bg-green-50 flex items-center justify-center mx-auto mb-4">
            <i class="fas fa-check-circle text-2xl text-green-400"></i>
        </div>
        <h3 class="text-sm font-bold text-gray-800 mb-1">Semua Aman!</h3>
        <p class="text-xs text-gray-400">Tidak ada notifikasi saat ini</p>
    </div>
    @endif

    <!-- Item Tanpa Lokasi -->
    @if($no_location['items']->count() > 0)
    <div class="card overflow-hidden" id="noLocationSection">
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
            <div class="flex items-center gap-2">
                @if($no_location['count'] > 0)
                <button onclick="markTypeRead('no_location')" class="text-[10px] font-medium px-2 py-1 rounded-md bg-red-100 text-red-600 hover:bg-red-200 transition">
                    Tandai Semua
                </button>
                @endif
                <span class="text-xs font-bold bg-red-100 text-red-600 px-2.5 py-1 rounded-full">{{ number_format($no_location['items']->count()) }}</span>
            </div>
        </div>
        <div class="divide-y divide-gray-50">
            @foreach($no_location['items'] as $item)
            <div class="flex items-center gap-4 px-5 py-3 transition {{ $item->is_read ? 'opacity-50 bg-gray-50' : 'hover:bg-gray-50' }}" id="notif-no_location-{{ $item->id }}">
                <div class="w-9 h-9 rounded-lg bg-red-50 flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-map-pin {{ $item->is_read ? 'text-red-200' : 'text-red-300' }} text-xs"></i>
                </div>
                <a href="{{ route('items.show', $item->id) }}" class="flex-1 min-w-0 text-decoration-none">
                    <p class="text-sm font-medium text-gray-800 truncate">{{ $item->lot_id }}
                        @if($item->paper_type)
                            <span class="text-xs text-gray-400 font-normal ml-1">· {{ $item->paper_type }}</span>
                        @endif
                        @if($item->is_read)
                            <span class="tag tag-gray ml-2" style="font-size:9px"><i class="fas fa-check mr-0.5"></i>Dibaca</span>
                        @endif
                    </p>
                    <p class="text-xs text-gray-400 truncate">
                        @if($item->gsm){{ $item->gsm }}@endif
                        @if($item->width)
                            {{ $item->gsm ? ' · ' : '' }}{{ $item->width }}mm
                        @endif
                        @if(!$item->gsm && !$item->width)Belum ada data lengkap@endif
                    </p>
                </a>
                <div class="text-right flex-shrink-0">
                    <span class="text-[10px] text-gray-300">{{ $item->created_at->diffForHumans() }}</span>
                    <p class="text-[10px] text-gray-300 mt-0.5">{{ $item->created_at->format('d M Y') }}</p>
                </div>
                @unless($item->is_read)
                <button onclick="markSingleRead('no_location', {{ $item->id }})" class="w-7 h-7 rounded-lg flex items-center justify-center flex-shrink-0 text-gray-300 hover:text-green-500 hover:bg-green-50 transition" title="Tandai dibaca">
                    <i class="fas fa-check text-xs"></i>
                </button>
                @endunless
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Defect Baru -->
    @if($recent_defects['items']->count() > 0)
    <div class="card overflow-hidden" id="recentDefectsSection">
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
            <div class="flex items-center gap-2">
                @if($recent_defects['count'] > 0)
                <button onclick="markTypeRead('recent_defects')" class="text-[10px] font-medium px-2 py-1 rounded-md bg-amber-100 text-amber-600 hover:bg-amber-200 transition">
                    Tandai Semua
                </button>
                @endif
                <span class="text-xs font-bold bg-amber-100 text-amber-600 px-2.5 py-1 rounded-full">{{ number_format($recent_defects['items']->count()) }}</span>
            </div>
        </div>
        <div class="divide-y divide-gray-50">
            @foreach($recent_defects['items'] as $item)
            <div class="flex items-center gap-4 px-5 py-3 transition {{ $item->is_read ? 'opacity-50 bg-gray-50' : 'hover:bg-gray-50' }}" id="notif-recent_defects-{{ $item->id }}">
                <div class="w-9 h-9 rounded-lg bg-amber-50 flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-triangle-exclamation {{ $item->is_read ? 'text-amber-200' : 'text-amber-300' }} text-xs"></i>
                </div>
                <a href="{{ route('defects.index') }}" class="flex-1 min-w-0 text-decoration-none">
                    <p class="text-sm font-medium text-gray-800 truncate">{{ $item->lot_id }}
                        @if($item->paper_type)
                            <span class="text-xs text-gray-400 font-normal ml-1">· {{ $item->paper_type }}</span>
                        @endif
                        @if($item->is_read)
                            <span class="tag tag-gray ml-2" style="font-size:9px"><i class="fas fa-check mr-0.5"></i>Dibaca</span>
                        @endif
                    </p>
                    <p class="text-xs text-gray-400 truncate">
                        @if($item->reason){{ $item->reason }}@elseif($item->gsm){{ $item->gsm }}@else-Barang bermasalah@endif
                    </p>
                </a>
                <div class="text-right flex-shrink-0">
                    <span class="text-[10px] text-gray-300">{{ $item->created_at->diffForHumans() }}</span>
                    <p class="text-[10px] text-gray-300 mt-0.5">{{ $item->created_at->format('d M Y') }}</p>
                </div>
                @unless($item->is_read)
                <button onclick="markSingleRead('recent_defects', {{ $item->id }})" class="w-7 h-7 rounded-lg flex items-center justify-center flex-shrink-0 text-gray-300 hover:text-green-500 hover:bg-green-50 transition" title="Tandai dibaca">
                    <i class="fas fa-check text-xs"></i>
                </button>
                @endunless
            </div>
            @endforeach
        </div>
    </div>
    @endif

</div>

@push('scripts')
<script>
    function markSingleRead(type, refId) {
        var el = document.getElementById('notif-' + type + '-' + refId);
        if (!el) return;

        fetch('/notifications/mark-read', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
            body: JSON.stringify({ type: type, reference_id: refId })
        })
        .then(function(r) { return r.json(); })
        .then(function(data) {
            // Fade out the item
            el.style.transition = 'opacity 0.3s, background 0.3s';
            el.style.opacity = '0.4';
            el.style.background = '#f9fafb';

            // Remove the check button
            var btn = el.querySelector('button[onclick*="markSingleRead"]');
            if (btn) btn.style.display = 'none';

            // Add "Dibaca" badge if not exists
            var titleP = el.querySelector('a > p:first-child');
            if (titleP && !titleP.querySelector('.tag-gray')) {
                var badge = document.createElement('span');
                badge.className = 'tag tag-gray ml-2';
                badge.style.fontSize = '9px';
                badge.innerHTML = '<i class="fas fa-check" style="margin-right:2px"></i>Dibaca';
                titleP.appendChild(badge);
            }
        })
        .catch(function() {});
    }

    function markTypeRead(type) {
        fetch('/notifications/mark-read', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
            body: JSON.stringify({ type: type })
        })
        .then(function(r) { return r.json(); })
        .then(function(data) {
            // Mark all items in section as read
            var section = type === 'no_location' ? document.getElementById('noLocationSection') : document.getElementById('recentDefectsSection');
            if (!section) return;

            section.querySelectorAll('button[onclick*="markSingleRead"]').forEach(function(btn) { btn.style.display = 'none'; });
            section.querySelectorAll('[id^="notif-' + type + '-"]').forEach(function(el) {
                el.style.transition = 'opacity 0.3s, background 0.3s';
                el.style.opacity = '0.4';
                el.style.background = '#f9fafb';
            });
        })
        .catch(function() {});
    }

    function markAllReadPage() {
        var btn = document.getElementById('markAllPageBtn');
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i>Menandai...';

        fetch('/notifications/mark-read', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
            body: JSON.stringify({ type: 'all' })
        })
        .then(function(r) { return r.json(); })
        .then(function(data) {
            btn.innerHTML = '<i class="fas fa-check mr-1"></i>Semua Dibaca';
            btn.classList.remove('bg-blue-500', 'hover:bg-blue-600');
            btn.classList.add('bg-green-500', 'hover:bg-green-600');

            document.querySelectorAll('button[onclick*="markSingleRead"]').forEach(function(btn) { btn.style.display = 'none'; });
            document.querySelectorAll('[id^="notif-"]').forEach(function(el) {
                el.style.transition = 'opacity 0.3s, background 0.3s';
                el.style.opacity = '0.4';
                el.style.background = '#f9fafb';
            });
        })
        .catch(function() {
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-check-double mr-1"></i>Tandai Semua Dibaca';
        });
    }
</script>
@endpush
@endsection
