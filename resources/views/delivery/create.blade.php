@extends('layouts.app')

@section('title', 'DO Baru')
@section('page-title')
<i class="fas fa-truck mr-2 text-blue-500"></i>Delivery Order Baru
@endsection

@section('content')
<div class="max-w-4xl">
    <form method="POST" action="{{ route('delivery.store') }}" id="doForm">
        @csrf

        {{-- ── Header Info ── --}}
        <div class="card p-5 mb-4">
            <h3 class="font-semibold text-gray-900 mb-4">
                <i class="fas fa-user mr-2 text-blue-500"></i>Info Penerima
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-500 mb-1.5">Nama Penerima <span class="text-red-500">*</span></label>
                    <input type="text" name="recipient_name" class="input-field @error('recipient_name') border-red-400 @enderror"
                           value="{{ old('recipient_name') }}" required>
                    @error('recipient_name')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-500 mb-1.5">No. Telepon</label>
                    <input type="text" name="recipient_phone" class="input-field"
                           value="{{ old('recipient_phone') }}">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-xs font-semibold text-gray-500 mb-1.5">Alamat</label>
                    <input type="text" name="recipient_address" class="input-field"
                           value="{{ old('recipient_address') }}">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-xs font-semibold text-gray-500 mb-1.5">Tujuan / Kota</label>
                    <input type="text" name="destination" class="input-field"
                           value="{{ old('destination') }}">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-xs font-semibold text-gray-500 mb-1.5">Catatan</label>
                    <textarea name="notes" class="input-field" rows="2">{{ old('notes') }}</textarea>
                </div>
            </div>
        </div>

        {{-- ── Items ── --}}
        <div class="card p-5 mb-4">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-semibold text-gray-900">
                    <i class="fas fa-box mr-2 text-blue-500"></i>Item DO
                </h3>
                <button type="button" onclick="addItemRow()" class="btn btn-primary text-xs">
                    <i class="fas fa-plus"></i> Tambah Item
                </button>
            </div>

            <div class="overflow-x-auto">
                <table class="data-table" id="itemsTable">
                    <thead>
                        <tr>
                            <th>Lot ID</th>
                            <th>Info Roll</th>
                            <th>Stok</th>
                            <th>Qty Order</th>
                            <th>Catatan</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody id="itemsBody">
                        {{-- Dynamic rows --}}
                    </tbody>
                </table>
            </div>

            @error('items')
                <p class="text-xs text-red-500 mt-2">{{ $message }}</p>
            @enderror
        </div>

        {{-- ── Submit ── --}}
        <div class="flex items-center gap-3">
            <button type="submit" class="btn btn-primary" id="submitBtn">
                <i class="fas fa-save"></i> Simpan DO
            </button>
            <a href="{{ route('delivery.index') }}" class="btn btn-ghost">Batal</a>
        </div>
    </form>
</div>

{{-- Lot Lookup Modal --}}
<div id="lotLookupModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/30">
    <div class="modal-card rounded-xl p-6 w-full max-w-md mx-4">
        <div class="flex items-center justify-between mb-4">
            <h3 class="font-semibold text-gray-900">Cari Lot ID</h3>
            <button onclick="closeLotModal()" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <input type="text" id="modalLotSearch" class="input-field mb-3" placeholder="Ketik Lot ID..." autofocus>
        <div id="modalLotResult" class="min-h-[60px]"></div>
    </div>
</div>

@error('items')
<style>.items-error { border-color: #ef4444 !important; }</style>
@enderror
@endsection

@push('scripts')
<script>
let itemIndex = 0;

function addItemRow(lotId = '', qty = 1, notes = '') {
    const tbody = document.getElementById('itemsBody');
    const row = document.createElement('tr');
    row.dataset.index = itemIndex;
    row.innerHTML = `
        <td>
            <input type="hidden" name="items[${itemIndex}][lot_id]" id="lot_id_${itemIndex}" value="${lotId}">
            <div class="flex items-center gap-2">
                <span id="lot_display_${itemIndex}" class="text-sm font-medium text-gray-900 min-w-[100px]">${lotId || '-'}</span>
                <button type="button" onclick="openLotModal(${itemIndex})"
                        class="btn btn-ghost text-xs px-2 py-1">
                    <i class="fas fa-search"></i>
                </button>
            </div>
        </td>
        <td>
            <div id="item_info_${itemIndex}" class="text-xs text-gray-500">-</div>
        </td>
        <td>
            <div id="item_stok_${itemIndex}" class="text-xs text-gray-500 text-center">-</div>
        </td>
        <td>
            <input type="number" name="items[${itemIndex}][qty_order]"
                   id="qty_${itemIndex}" value="${qty}" min="1"
                   class="input-field w-20 text-center" onchange="updateRowIndex()">
        </td>
        <td>
            <input type="text" name="items[${itemIndex}][notes]"
                   id="notes_${itemIndex}" value="${notes}"
                   class="input-field" placeholder="Catatan...">
        </td>
        <td>
            <button type="button" onclick="removeRow(${itemIndex})" class="btn btn-delete text-xs px-2 py-1">
                <i class="fas fa-trash"></i>
            </button>
        </td>
    `;
    tbody.appendChild(row);
    itemIndex++;
}

function removeRow(idx) {
    document.querySelector(`tr[data-index="${idx}"]`).remove();
}

function updateRowIndex() {
    // Re-index after qty changes if needed
}

let activeRowIndex = null;

function openLotModal(rowIdx) {
    activeRowIndex = rowIdx;
    document.getElementById('modalLotSearch').value = '';
    document.getElementById('modalLotResult').innerHTML = '<p class="text-xs text-gray-400">Ketik Lot ID dan tekan Enter</p>';
    document.getElementById('lotLookupModal').classList.remove('hidden');
    document.getElementById('modalLotSearch').focus();
}

function closeLotModal() {
    document.getElementById('lotLookupModal').classList.add('hidden');
    activeRowIndex = null;
}

document.getElementById('modalLotSearch').addEventListener('keydown', function(e) {
    if (e.key === 'Enter') {
        e.preventDefault();
        lookupLot(this.value.trim());
    }
    if (e.key === 'Escape') closeLotModal();
});

function lookupLot(lotId) {
    if (!lotId) return;

    fetch(`/api/lot-lookup?lot_id=${encodeURIComponent(lotId)}`)
        .then(r => r.json())
        .then(data => {
            const result = document.getElementById('modalLotResult');
            if (!data.found) {
                result.innerHTML = `<div class="text-sm text-red-500"><i class="fas fa-times-circle mr-1"></i>Lot ID "${lotId}" tidak ditemukan</div>`;
                return;
            }
            result.innerHTML = `
                <div class="text-sm">
                    <div class="flex items-center gap-2 mb-2">
                        <span class="font-semibold text-green-600"><i class="fas fa-check-circle mr-1"></i>Ditemukan</span>
                    </div>
                    <div class="grid grid-cols-2 gap-1 text-xs text-gray-600">
                        <div>Lot ID: <span class="font-medium text-gray-900">${data.lot_id}</span></div>
                        <div>Rew ID: <span class="font-medium text-gray-900">${data.rew_id || '-'}</span></div>
                        <div>Paper: <span class="font-medium text-gray-900">${data.paper_type || '-'}</span></div>
                        <div>GSM: <span class="font-medium text-gray-900">${data.gsm || '-'}</span></div>
                        <div>Width: <span class="font-medium text-gray-900">${data.width || '-'}</span></div>
                        <div>Stok: <span class="font-medium text-gray-900">${data.end_qty}</span></div>
                    </div>
                    <button type="button" onclick="selectLot('${data.lot_id}', '${data.paper_type || ''}', '${data.gsm || ''}', '${data.width || ''}', ${data.end_qty})"
                            class="mt-2 btn btn-primary text-xs w-full justify-center">
                        <i class="fas fa-check mr-1"></i>Pilih Lot Ini
                    </button>
                </div>
            `;
        })
        .catch(() => {
            document.getElementById('modalLotResult').innerHTML = `<div class="text-sm text-red-500">Error saat lookup</div>`;
        });
}

function selectLot(lotId, paperType, gsm, width, stok) {
    if (activeRowIndex === null) return;

    document.getElementById(`lot_id_${activeRowIndex}`).value = lotId;
    document.getElementById(`lot_display_${activeRowIndex}`).textContent = lotId;
    document.getElementById(`item_info_${activeRowIndex}`).textContent = `${paperType} ${gsm}g / ${width}`;
    document.getElementById(`item_stok_${activeRowIndex}`).textContent = `${stok} roll`;

    closeLotModal();
}

// Init with one empty row
addItemRow();
</script>
@endpush