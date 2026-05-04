@extends('layouts.app')

@section('title', isset($defect) ? 'Edit Defect #' . $defect->id : 'Tambah Defect Item')
@section('page-title')
@if(isset($defect))
    <i class="fas fa-edit mr-2 opacity-60"></i>Edit Defect Item
@else
    <i class="fas fa-plus mr-2 opacity-60"></i>Tambah Defect Item
@endif
@endsection

@section('content')
<div class="animate-in space-y-4">

    <!-- Back -->
    <a href="{{ route('defects.index') }}" class="inline-flex items-center gap-2 text-xs font-medium no-underline text-gray-500 hover:text-gray-700 transition">
        <i class="fas fa-arrow-left"></i> Kembali
    </a>

    <!-- Success message -->
    @if(session('success'))
        <div class="p-3 rounded-xl bg-green-50 border border-green-200 text-sm text-green-700 flex items-center gap-2">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif

    <!-- Error message -->
    @if(session('error'))
        <div class="p-3 rounded-xl bg-red-50 border border-red-200 text-sm text-red-700 flex items-center gap-2">
            <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
        </div>
    @endif

    <form method="POST" action="{{ isset($defect) ? route('defects.update', $defect->id) : route('defects.store') }}" class="space-y-5">
        @csrf
        @isset($defect) @method('PUT') @endisset

        <!-- Lookup & Informasi Utama -->
        <div class="card p-5">
            <h3 class="text-sm font-semibold text-gray-800 mb-4 flex items-center gap-2">
                <i class="fas fa-info-circle text-xs text-blue-500"></i>Informasi Utama
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-[10px] font-semibold uppercase tracking-wide mb-1.5 text-gray-400">Lot ID</label>
                    <input type="text" name="lot_id" id="lot_id" class="input-field w-full" value="{{ old('lot_id', $defect->lot_id ?? '') }}" placeholder="Ketik Lot ID..." autocomplete="off">
                    <div id="lookupStatus" class="text-[10px] mt-1 text-gray-400 hidden"></div>
                </div>
                <div>
                    <label class="block text-[10px] font-semibold uppercase tracking-wide mb-1.5 text-gray-400">Tahun <span class="text-red-400">*</span></label>
                    <select name="year" class="select-field w-full" required>
                        <option value="">Pilih Tahun</option>
                        @foreach($years as $y)
                            <option value="{{ $y }}" {{ (old('year', $defect->year ?? '') == $y) ? 'selected' : '' }}>{{ $y }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-[10px] font-semibold uppercase tracking-wide mb-1.5 text-gray-400">Rew ID</label>
                    <input type="text" name="rew_id" id="rew_id" class="input-field w-full" value="{{ old('rew_id', $defect->rew_id ?? '') }}">
                </div>
                <div>
                    <label class="block text-[10px] font-semibold uppercase tracking-wide mb-1.5 text-gray-400">Tanggal Defect</label>
                    <input type="date" name="defect_date" class="input-field w-full" value="{{ old('defect_date', $defect->defect_date ?? date('Y-m-d')) }}">
                </div>
            </div>
        </div>

        <!-- Spesifikasi (auto-filled from Lot ID lookup) -->
        <div class="card p-5">
            <h3 class="text-sm font-semibold text-gray-800 mb-4 flex items-center gap-2">
                <i class="fas fa-ruler-combined text-xs text-purple-500"></i>Spesifikasi
                <span class="text-[9px] font-normal text-gray-400 ml-1">(auto-fill dari Lot ID)</span>
            </h3>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-[10px] font-semibold uppercase tracking-wide mb-1.5 text-gray-400">Paper Type</label>
                    <input type="text" name="paper_type" id="paper_type" class="input-field w-full" value="{{ old('paper_type', $defect->paper_type ?? '') }}">
                </div>
                <div>
                    <label class="block text-[10px] font-semibold uppercase tracking-wide mb-1.5 text-gray-400">GSM</label>
                    <input type="text" name="gsm" id="gsm" class="input-field w-full" value="{{ old('gsm', $defect->gsm ?? '') }}">
                </div>
                <div>
                    <label class="block text-[10px] font-semibold uppercase tracking-wide mb-1.5 text-gray-400">Plybond</label>
                    <input type="text" name="plybond" id="plybond" class="input-field w-full" value="{{ old('plybond', $defect->plybond ?? '') }}">
                </div>
                <div>
                    <label class="block text-[10px] font-semibold uppercase tracking-wide mb-1.5 text-gray-400">Width (MM)</label>
                    <input type="text" name="width" id="width" class="input-field w-full" value="{{ old('width', $defect->width ?? '') }}">
                </div>
            </div>
        </div>

        <!-- Defect Info -->
        <div class="card p-5">
            <h3 class="text-sm font-semibold text-gray-800 mb-4 flex items-center gap-2">
                <i class="fas fa-triangle-exclamation text-xs text-red-500"></i>Detail Defect
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-[10px] font-semibold uppercase tracking-wide mb-1.5 text-gray-400">Alasan (Reason)</label>
                    <select name="reason" id="reasonSelect" class="select-field w-full" onchange="toggleCustomReason()">
                        <option value="">Pilih Alasan</option>
                        <option value="__custom__">+ Input Custom...</option>
                        @foreach($reasons as $r)
                            <option value="{{ $r }}" {{ (old('reason', $defect->reason ?? '') == $r) ? 'selected' : '' }}>{{ $r }}</option>
                        @endforeach
                    </select>
                    <input type="text" name="reason_custom" id="reasonCustom" class="input-field w-full mt-2 hidden" placeholder="Ketik alasan custom..." value="{{ old('reason_custom', '') }}">
                </div>
                <div>
                    <label class="block text-[10px] font-semibold uppercase tracking-wide mb-1.5 text-gray-400">Kategori</label>
                    <select name="category" class="select-field w-full">
                        <option value="">-</option>
                        <option value="WRAPPING" {{ (old('category', $defect->category ?? '') == 'WRAPPING') ? 'selected' : '' }}>Wrapping</option>
                        <option value="PROCESS" {{ (old('category', $defect->category ?? '') == 'PROCESS') ? 'selected' : '' }}>Process</option>
                        <option value="MATERIAL" {{ (old('category', $defect->category ?? '') == 'MATERIAL') ? 'selected' : '' }}>Material</option>
                        <option value="STORAGE" {{ (old('category', $defect->category ?? '') == 'STORAGE') ? 'selected' : '' }}>Storage</option>
                        <option value="LAINNYA" {{ (old('category', $defect->category ?? '') == 'LAINNYA') ? 'selected' : '' }}>Lainnya</option>
                    </select>
                </div>
                <div>
                    <label class="block text-[10px] font-semibold uppercase tracking-wide mb-1.5 text-gray-400">Bulan</label>
                    <select name="month" class="select-field w-full">
                        <option value="">-</option>
                        @foreach($months as $m)
                            <option value="{{ $m }}" {{ (old('month', $defect->month ?? '') == $m) ? 'selected' : '' }}>{{ $m }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-[10px] font-semibold uppercase tracking-wide mb-1.5 text-gray-400">TR Type</label>
                    <input type="text" name="tr_type" class="input-field w-full" value="{{ old('tr_type', $defect->tr_type ?? '') }}">
                </div>
            </div>
            <div class="mt-4">
                <label class="block text-[10px] font-semibold uppercase tracking-wide mb-1.5 text-gray-400">Keterangan</label>
                <textarea name="keterangan" class="input-field w-full" rows="2" placeholder="Catatan tambahan...">{{ old('keterangan', $defect->keterangan ?? '') }}</textarea>
            </div>
        </div>

        <!-- Actions -->
        <div class="flex flex-wrap gap-3">
            <button type="submit" class="btn btn-primary px-6 py-2.5" style="background: #7c3aed;">
                <i class="fas fa-save mr-1.5"></i>{{ isset($defect) ? 'Update' : 'Simpan' }}
            </button>
            <a href="{{ route('defects.index') }}" class="btn btn-ghost px-6 py-2.5">Batal</a>
            @if(isset($defect))
                <button type="button" onclick="confirmDelete()" class="btn btn-delete px-6 py-2.5">
                    <i class="fas fa-trash mr-1.5"></i>Hapus
                </button>
                <form id="deleteForm" method="POST" action="{{ route('defects.destroy', $defect->id) }}" style="display:none;">
                    @csrf @method('DELETE')
                </form>
            @endif
        </div>
    </form>
</div>

<!-- Delete Confirm Modal -->
@if(isset($defect))
<div id="deleteModal" class="fixed inset-0 bg-black/40 z-50 hidden items-center justify-center p-4" style="display:none;">
    <div class="modal-card rounded-xl shadow-2xl max-w-sm w-full p-6">
        <h3 class="text-sm font-bold text-gray-800 mb-2 flex items-center gap-2">
            <i class="fas fa-exclamation-triangle text-red-500"></i> Hapus Defect Item?
        </h3>
        <p class="text-xs text-gray-500 mb-5">Data defect <strong>{{ $defect->lot_id ?? '#' . $defect->id }}</strong> akan dihapus secara permanen. Tindakan ini tidak bisa dibatalkan.</p>
        <div class="flex gap-2">
            <button onclick="document.getElementById('deleteForm').submit()" class="flex-1 py-2 rounded-lg text-sm font-semibold text-white" style="background: #dc2626;">Ya, Hapus</button>
            <button onclick="document.getElementById('deleteModal').style.display='none'" class="btn btn-ghost flex-1">Batal</button>
        </div>
    </div>
</div>
@endif

<script>
// Toggle custom reason input
function toggleCustomReason() {
    const sel = document.getElementById('reasonSelect');
    const custom = document.getElementById('reasonCustom');
    if (sel.value === '__custom__') {
        custom.classList.remove('hidden');
        custom.focus();
    } else {
        custom.classList.add('hidden');
        custom.value = '';
    }
}

// Check if reason is already set from server (edit mode)
@if(isset($defect) && $defect->reason)
    (function() {
        const sel = document.getElementById('reasonSelect');
        const custom = document.getElementById('reasonCustom');
        let found = false;
        for (let opt of sel.options) {
            if (opt.value === '{{ $defect->reason }}') {
                sel.value = opt.value;
                found = true;
                break;
            }
        }
        if (!found) {
            custom.classList.remove('hidden');
            custom.value = '{{ $defect->reason }}';
        }
    })();
@endif

// AJAX Lot ID lookup
let lookupTimer = null;
const lotInput = document.getElementById('lot_id');
const lookupStatus = document.getElementById('lookupStatus');

if (lotInput) {
    lotInput.addEventListener('input', function() {
        clearTimeout(lookupTimer);
        const val = this.value.trim();
        if (val.length < 2) {
            lookupStatus.classList.add('hidden');
            return;
        }
        lookupTimer = setTimeout(() => fetchLotData(val), 400);
    });

    // Also trigger on blur for immediate lookup
    lotInput.addEventListener('blur', function() {
        const val = this.value.trim();
        if (val.length >= 2) {
            clearTimeout(lookupTimer);
            fetchLotData(val);
        }
    });
}

function fetchLotData(lotId) {
    lookupStatus.classList.remove('hidden');
    lookupStatus.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i>Mencari...';
    lookupStatus.className = 'text-[10px] mt-1 text-blue-400';

    fetch('{{ route("defects.lookup") }}?lot_id=' + encodeURIComponent(lotId))
        .then(r => r.json())
        .then(data => {
            if (data.found) {
                lookupStatus.innerHTML = '<i class="fas fa-check-circle mr-1 text-green-500"></i>Ditemukan: ' + (data.description || '');
                lookupStatus.className = 'text-[10px] mt-1 text-green-500';

                // Auto-fill fields only if they're empty
                if (document.getElementById('rew_id').value === '') document.getElementById('rew_id').value = data.rew_id || '';
                if (document.getElementById('paper_type').value === '') document.getElementById('paper_type').value = data.paper_type || '';
                if (document.getElementById('gsm').value === '') document.getElementById('gsm').value = data.gsm || '';
                if (document.getElementById('plybond').value === '') document.getElementById('plybond').value = data.plybond || '';
                if (document.getElementById('width').value === '') document.getElementById('width').value = data.width || '';
            } else {
                lookupStatus.innerHTML = '<i class="fas fa-times-circle mr-1 text-red-400"></i>Lot ID tidak ditemukan';
                lookupStatus.className = 'text-[10px] mt-1 text-red-400';
            }
        })
        .catch(() => {
            lookupStatus.innerHTML = '<i class="fas fa-times-circle mr-1 text-red-400"></i>Error lookup';
            lookupStatus.className = 'text-[10px] mt-1 text-red-400';
        });
}

// Delete modal
function confirmDelete() {
    document.getElementById('deleteModal').style.display = 'flex';
}
@if(isset($defect))
document.getElementById('deleteModal').addEventListener('click', function(e) {
    if (e.target === this) this.style.display = 'none';
});
@endif
</script>
@endsection
