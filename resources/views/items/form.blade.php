@extends('layouts.app')

@section('title', isset($item) ? 'Edit ' . $item->lot_id : 'Tambah Roll Item')
@section('page-title')
@if(isset($item))
    <i class="fas fa-edit mr-2 opacity-60"></i>Edit Roll Item
@else
    <i class="fas fa-plus mr-2 opacity-60"></i>Tambah Roll Item
@endif
@endsection

@section('content')
<div class="animate-in space-y-4">

    <!-- Back -->
    <a href="{{ isset($item) ? route('items.show', $item->id) : route('items.index') }}" class="inline-flex items-center gap-2 text-xs font-medium no-underline text-gray-500 hover:text-gray-700 transition">
        <i class="fas fa-arrow-left"></i> Kembali
    </a>

    <!-- Success message -->
    @if(session('success'))
        <div class="p-3 rounded-xl bg-green-50 border border-green-200 text-sm text-green-700 flex items-center gap-2">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif

    <form method="POST" action="{{ isset($item) ? route('items.update', $item->id) : route('items.store') }}" class="space-y-5">
        @csrf
        @method(isset($item) ? 'PUT' : 'POST')

        <!-- Informasi Utama -->
        <div class="card p-5">
            <h3 class="text-sm font-semibold text-gray-800 mb-4 flex items-center gap-2">
                <i class="fas fa-info-circle text-xs text-blue-500"></i>Informasi Utama
            </h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                <div>
                    <label class="block text-[10px] font-semibold uppercase tracking-wide mb-1.5 text-gray-400">Lot ID <span class="text-red-400">*</span></label>
                    <input type="text" name="lot_id" class="input-field w-full" value="{{ old('lot_id', $item->lot_id ?? '') }}" required autofocus>
                </div>
                <div>
                    <label class="block text-[10px] font-semibold uppercase tracking-wide mb-1.5 text-gray-400">Item ID</label>
                    <input type="text" name="item_id" class="input-field w-full" value="{{ old('item_id', $item->item_id ?? '') }}">
                </div>
                <div>
                    <label class="block text-[10px] font-semibold uppercase tracking-wide mb-1.5 text-gray-400">End Qty</label>
                    <input type="number" name="end_qty" class="input-field w-full" value="{{ old('end_qty', $item->end_qty ?? '') }}" step="any">
                </div>
                <div>
                    <label class="block text-[10px] font-semibold uppercase tracking-wide mb-1.5 text-gray-400">Rew ID</label>
                    <input type="text" name="rew_id" class="input-field w-full" value="{{ old('rew_id', $item->rew_id ?? '') }}">
                </div>
                <div>
                    <label class="block text-[10px] font-semibold uppercase tracking-wide mb-1.5 text-gray-400">Tanggal</label>
                    <input type="date" name="tr_date" class="input-field w-full" value="{{ old('tr_date', $item->tr_date ?? '') }}">
                </div>
                <div>
                    <label class="block text-[10px] font-semibold uppercase tracking-wide mb-1.5 text-gray-400">Waktu</label>
                    <input type="time" name="tr_time" class="input-field w-full" value="{{ old('tr_time', $item->tr_time ?? '') }}">
                </div>
            </div>
            <div class="mt-4">
                <label class="block text-[10px] font-semibold uppercase tracking-wide mb-1.5 text-gray-400">Deskripsi</label>
                <input type="text" name="description" class="input-field w-full" value="{{ old('description', $item->description ?? '') }}">
            </div>
        </div>

        <!-- Spesifikasi -->
        <div class="card p-5">
            <h3 class="text-sm font-semibold text-gray-800 mb-4 flex items-center gap-2">
                <i class="fas fa-ruler-combined text-xs text-purple-500"></i>Spesifikasi
            </h3>
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4">
                <div>
                    <label class="block text-[10px] font-semibold uppercase tracking-wide mb-1.5 text-gray-400">Paper Type</label>
                    <input type="text" name="paper_type" class="input-field w-full" value="{{ old('paper_type', $item->paper_type ?? '') }}" placeholder="Tissue">
                </div>
                <div>
                    <label class="block text-[10px] font-semibold uppercase tracking-wide mb-1.5 text-gray-400">GSM</label>
                    <input type="text" name="gsm" class="input-field w-full" value="{{ old('gsm', $item->gsm ?? '') }}">
                </div>
                <div>
                    <label class="block text-[10px] font-semibold uppercase tracking-wide mb-1.5 text-gray-400">Plybond</label>
                    <input type="text" name="plybond" class="input-field w-full" value="{{ old('plybond', $item->plybond ?? '') }}">
                </div>
                <div>
                    <label class="block text-[10px] font-semibold uppercase tracking-wide mb-1.5 text-gray-400">Width (MM)</label>
                    <input type="text" name="width" class="input-field w-full" value="{{ old('width', $item->width ?? '') }}">
                </div>
                <div>
                    <label class="block text-[10px] font-semibold uppercase tracking-wide mb-1.5 text-gray-400">Diameter</label>
                    <input type="text" name="diameter" class="input-field w-full" value="{{ old('diameter', $item->diameter ?? '') }}">
                </div>
                <div>
                    <label class="block text-[10px] font-semibold uppercase tracking-wide mb-1.5 text-gray-400">Thickness</label>
                    <input type="text" name="thickness" class="input-field w-full" value="{{ old('thickness', $item->thickness ?? '') }}">
                </div>
            </div>
            <div class="grid grid-cols-2 sm:grid-cols-2 gap-4 mt-4">
                <div>
                    <label class="block text-[10px] font-semibold uppercase tracking-wide mb-1.5 text-gray-400">Grade</label>
                    <input type="text" name="grade" class="input-field w-full" value="{{ old('grade', $item->grade ?? '') }}">
                </div>
                <div>
                    <label class="block text-[10px] font-semibold uppercase tracking-wide mb-1.5 text-gray-400">Status Barang</label>
                    <select name="status_barang" class="select-field w-full">
                        <option value="">-</option>
                        <option value="Good" {{ (old('status_barang', $item->status_barang ?? '') == 'Good') ? 'selected' : '' }}>Good</option>
                        <option value="Hold" {{ (old('status_barang', $item->status_barang ?? '') == 'Hold') ? 'selected' : '' }}>Hold</option>
                        <option value="Pending" {{ (old('status_barang', $item->status_barang ?? '') == 'Pending') ? 'selected' : '' }}>Pending</option>
                        <option value="Reject" {{ (old('status_barang', $item->status_barang ?? '') == 'Reject') ? 'selected' : '' }}>Reject</option>
                        <option value="Rusak" {{ (old('status_barang', $item->status_barang ?? '') == 'Rusak') ? 'selected' : '' }}>Rusak</option>
                    </select>
                </div>
            </div>
            <div class="mt-4">
                <label class="block text-[10px] font-semibold uppercase tracking-wide mb-1.5 text-gray-400">Comments</label>
                <textarea name="comments" class="input-field w-full" rows="2">{{ old('comments', $item->comments ?? '') }}</textarea>
            </div>
        </div>

        <!-- Tracking & Lokasi -->
        <div class="card p-5">
            <h3 class="text-sm font-semibold text-gray-800 mb-4 flex items-center gap-2">
                <i class="fas fa-route text-xs text-teal-500"></i>Tracking & Lokasi
            </h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                <div>
                    <label class="block text-[10px] font-semibold uppercase tracking-wide mb-1.5 text-gray-400">SO September 2025</label>
                    <input type="text" name="so_september" class="input-field w-full" value="{{ old('so_september', $item->so_september ?? '') }}">
                </div>
                <div>
                    <label class="block text-[10px] font-semibold uppercase tracking-wide mb-1.5 text-gray-400">PIC 2025</label>
                    <input type="text" name="pic_2025" class="input-field w-full" value="{{ old('pic_2025', $item->pic_2025 ?? '') }}">
                </div>
                <div>
                    <label class="block text-[10px] font-semibold uppercase tracking-wide mb-1.5 text-gray-400">Lokasi Receiving</label>
                    <input type="text" name="lokasi_receiving" class="input-field w-full" value="{{ old('lokasi_receiving', $item->lokasi_receiving ?? '') }}">
                </div>
                <div>
                    <label class="block text-[10px] font-semibold uppercase tracking-wide mb-1.5 text-gray-400">SO Desember 2025</label>
                    <input type="text" name="so_desember" class="input-field w-full" value="{{ old('so_desember', $item->so_desember ?? '') }}">
                </div>
                <div>
                    <label class="block text-[10px] font-semibold uppercase tracking-wide mb-1.5 text-gray-400">Receiving 2026</label>
                    <input type="text" name="receiving_2026" class="input-field w-full" value="{{ old('receiving_2026', $item->receiving_2026 ?? '') }}">
                </div>
                <div>
                    <label class="block text-[10px] font-semibold uppercase tracking-wide mb-1.5 text-gray-400">PIC 2026</label>
                    <input type="text" name="pic_2026" class="input-field w-full" value="{{ old('pic_2026', $item->pic_2026 ?? '') }}">
                </div>
                <div>
                    <label class="block text-[10px] font-semibold uppercase tracking-wide mb-1.5 text-gray-400">RCV/CNV 2026</label>
                    <input type="text" name="rcv_cnv_2026" class="input-field w-full" value="{{ old('rcv_cnv_2026', $item->rcv_cnv_2026 ?? '') }}">
                </div>
                <div>
                    <label class="block text-[10px] font-semibold uppercase tracking-wide mb-1.5 text-gray-400">SO Maret 2026</label>
                    <input type="text" name="so_maret_2026" class="input-field w-full" value="{{ old('so_maret_2026', $item->so_maret_2026 ?? '') }}">
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="flex flex-wrap gap-3">
            <button type="submit" class="btn btn-primary px-6 py-2.5">
                <i class="fas fa-save mr-1.5"></i>{{ isset($item) ? 'Update' : 'Simpan' }}
            </button>
            <a href="{{ isset($item) ? route('items.show', $item->id) : route('items.index') }}" class="btn btn-ghost px-6 py-2.5">Batal</a>
            @if(isset($item))
                <button type="button" onclick="confirmDelete({{ $item->id }})" class="btn px-6 py-2.5" style="background: #fef2f2; color: #dc2626; border: 1px solid #fecaca; border-radius: 10px;">
                    <i class="fas fa-trash mr-1.5"></i>Hapus
                </button>
                <form id="deleteForm" method="POST" action="{{ route('items.destroy', $item->id) }}" style="display:none;">
                    @csrf @method('DELETE')
                </form>
            @endif
        </div>
    </form>
</div>

<!-- Delete Confirm Modal -->
@if(isset($item))
<div id="deleteModal" class="fixed inset-0 bg-black/40 z-50 hidden items-center justify-center p-4" style="display:none;">
    <div class="bg-white rounded-xl shadow-2xl max-w-sm w-full p-6" style="border: 1px solid #e2e8f0;">
        <h3 class="text-sm font-bold text-gray-800 mb-2 flex items-center gap-2">
            <i class="fas fa-exclamation-triangle text-red-500"></i> Hapus Roll Item?
        </h3>
        <p class="text-xs text-gray-500 mb-5">Data <strong>{{ $item->lot_id }}</strong> akan dihapus secara permanen. Tindakan ini tidak bisa dibatalkan.</p>
        <div class="flex gap-2">
            <button onclick="document.getElementById('deleteForm').submit()" class="flex-1 py-2 rounded-lg text-sm font-semibold text-white" style="background: #dc2626;">Ya, Hapus</button>
            <button onclick="document.getElementById('deleteModal').style.display='none'" class="btn btn-ghost flex-1">Batal</button>
        </div>
    </div>
</div>
@endif

<script>
function confirmDelete(id) {
    document.getElementById('deleteModal').style.display = 'flex';
}
@if(isset($item))
document.getElementById('deleteModal').addEventListener('click', function(e) {
    if (e.target === this) this.style.display = 'none';
});
@endif
</script>
@endsection
