@extends('layouts.app')

@section('title', 'Roll Items')
@section('page-title')
<i class="fas fa-box mr-2 opacity-60"></i>Roll Items
@endsection

@section('content')
<div class="animate-in space-y-4">

    <!-- Search Bar -->
    <form method="GET" action="{{ route('items.index') }}" class="flex gap-2 flex-wrap">
        <div class="relative flex-1 min-w-[200px]">
            <i class="fas fa-search absolute left-3.5 top-1/2 -translate-y-1/2 text-xs" class="text-gray-400"></i>
            <input type="text" name="search" class="input-field pl-9" placeholder="Cari Lot ID, deskripsi, lokasi, SO, PIC..."
                   value="{{ request('search') }}">
        </div>
        <a href="{{ route('items.create') }}" class="btn btn-primary" style="background: #7c3aed;">
            <i class="fas fa-plus text-xs"></i> Tambah
        </a>
        <button class="btn btn-primary" type="submit">
            <i class="fas fa-search text-xs"></i> Cari
        </button>
        @if(request('search') || request('paper_type') || request('gsm') || request('receiving_2026') || request('status') || request('grade'))
            <a href="{{ route('items.index') }}" class="btn btn-ghost">
                <i class="fas fa-times text-xs"></i> Reset
            </a>
        @endif
    </form>

    <!-- Filters -->
    <div class="card overflow-hidden">
        <button onclick="document.getElementById('advFilters').classList.toggle('hidden')"
                class="w-full px-4 py-3 flex items-center justify-between text-left hover:bg-gray-50 transition">
            <span class="text-xs font-semibold uppercase tracking-wide flex items-center gap-2" class="text-gray-500">
                <i class="fas fa-sliders-h"></i> Filter Lanjutan
            </span>
            <i class="fas fa-chevron-down text-xs transition-transform" id="filterChevron" class="text-gray-400"></i>
        </button>
        <div id="advFilters" {{ (request('paper_type') || request('gsm') || request('width') || request('receiving_2026') || request('status') || request('grade') || request('sort')) ? '' : 'class="hidden"' }}>
            <div class="px-4 pb-4 pt-1">
                <form method="GET" action="{{ route('items.index') }}" id="filterForm">
                    @if(request('search'))<input type="hidden" name="search" value="{{ request('search') }}">@endif
                    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-7 gap-3">
                        <div>
                            <label class="block text-[10px] font-semibold uppercase tracking-wide mb-1.5" class="text-gray-400">Paper Type</label>
                            <select name="paper_type" class="select-field w-full" onchange="document.getElementById('filterForm').submit()">
                                <option value="">Semua</option>
                                @foreach($paperTypes as $pt)<option value="{{ $pt }}" {{ request('paper_type') == $pt ? 'selected' : '' }}>{{ $pt }}</option>@endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-[10px] font-semibold uppercase tracking-wide mb-1.5" class="text-gray-400">GSM</label>
                            <select name="gsm" class="select-field w-full" onchange="document.getElementById('filterForm').submit()">
                                <option value="">Semua</option>
                                @foreach($gsms as $g)<option value="{{ $g }}" {{ request('gsm') == $g ? 'selected' : '' }}>{{ $g }}</option>@endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-[10px] font-semibold uppercase tracking-wide mb-1.5" class="text-gray-400">Width</label>
                            <select name="width" class="select-field w-full" onchange="document.getElementById('filterForm').submit()">
                                <option value="">Semua</option>
                                @foreach($widths as $w)<option value="{{ $w }}" {{ request('width') == $w ? 'selected' : '' }}>{{ $w }}</option>@endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-[10px] font-semibold uppercase tracking-wide mb-1.5" class="text-gray-400">Receiving</label>
                            <select name="receiving_2026" class="select-field w-full" onchange="document.getElementById('filterForm').submit()">
                                <option value="">Semua</option>
                                @foreach($locations as $loc)<option value="{{ $loc }}" {{ request('receiving_2026') == $loc ? 'selected' : '' }}>{{ $loc }}</option>@endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-[10px] font-semibold uppercase tracking-wide mb-1.5" class="text-gray-400">Status</label>
                            <select name="status" class="select-field w-full" onchange="document.getElementById('filterForm').submit()">
                                <option value="">Semua</option>
                                @foreach($statuses as $st)<option value="{{ $st }}" {{ request('status') == $st ? 'selected' : '' }}>{{ $st }}</option>@endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-[10px] font-semibold uppercase tracking-wide mb-1.5" class="text-gray-400">Grade</label>
                            <div class="flex flex-wrap gap-1.5">
                                @foreach($grades as $g)
                                    <label class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md text-xs font-medium cursor-pointer transition border {{ in_array($g, (array) request('grade')) ? 'bg-blue-50 border-blue-400 text-blue-700' : 'bg-gray-50 border-gray-200 text-gray-500 hover:border-gray-300' }}">
                                        <input type="checkbox" name="grade[]" value="{{ $g }}" {{ in_array($g, (array) request('grade')) ? 'checked' : '' }} class="hidden" onchange="document.getElementById('filterForm').submit()">
                                        {{ $g }}
                                    </label>
                                @endforeach
                                @if(request('grade'))
                                    <input type="hidden" name="grade[]" value="">
                                @endif
                            </div>
                        </div>
                        <div>
                            <label class="block text-[10px] font-semibold uppercase tracking-wide mb-1.5" class="text-gray-400">Sort</label>
                            <select name="sort" class="select-field w-full" onchange="document.getElementById('filterForm').submit()">
                                <option value="created_at" {{ request('sort') == 'created_at' ? 'selected' : '' }}>Terbaru</option>
                                <option value="lot_id" {{ request('sort') == 'lot_id' ? 'selected' : '' }}>Lot ID</option>
                                <option value="receiving_2026" {{ request('sort') == 'receiving_2026' ? 'selected' : '' }}>Lokasi</option>
                                <option value="end_qty" {{ request('sort') == 'end_qty' ? 'selected' : '' }}>End Qty</option>
                            </select>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @if(!request('paper_type') && !request('gsm') && !request('width') && !request('receiving_2026') && !request('status') && !request('grade') && !request('sort'))
        <script>document.getElementById('advFilters').classList.add('hidden');</script>
    @else
        <script>document.getElementById('advFilters').classList.remove('hidden');</script>
    @endif

    <!-- Result info -->
    <div class="flex items-center justify-between text-xs" class="text-gray-400">
        <span><i class="fas fa-list mr-1"></i>{{ number_format($items->total()) }} items</span>
        <div class="flex items-center gap-2">
            <span id="selectedCount" class="hidden text-blue-600 font-semibold"><i class="fas fa-check-square mr-1"></i><span id="selectedNum">0</span> dipilih</span>
            <button id="printSelectedBtn" onclick="printSelected()" class="hidden btn btn-ghost flex items-center gap-1.5" style="padding: 6px 14px; font-size: 0.7rem;">
                <i class="fas fa-print"></i> Print Dipilih
            </button>
            <a href="{{ route('items.export', request()->except('page')) }}" class="btn btn-primary flex items-center gap-1.5" style="padding: 6px 14px; font-size: 0.7rem;">
                <i class="fas fa-file-excel"></i> Export Excel
            </a>
            <span class="hidden sm:inline">Hal. {{ $items->currentPage() }}/{{ $items->lastPage() }}</span>
        </div>
    </div>

    <!-- DESKTOP TABLE -->
    <div class="hidden md:block card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="data-table" id="itemsTable">
                <thead>
                    <tr>
                        <th style="width: 35px;"><input type="checkbox" id="selectAll" onchange="toggleAll(this)" class="cursor-pointer"></th>
                        <th style="width: 35px;">#</th>
                        <th>Lot ID</th>
                        <th>Description</th>
                        <th>GSM</th>
                        <th>Grade</th>
                        <th>Width</th>
                        <th>TR Date</th>
                        <th>Qty</th>
                        <th>Lokasi</th>
                        <th>Status</th>
                        <th>Komentar</th>
                        <th style="width: 35px;"></th>
                        <th style="width: 35px;"></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($items as $i => $item)
                    <tr class="item-row" data-id="{{ $item->id }}">
                        <td><input type="checkbox" class="row-check cursor-pointer" value="{{ $item->id }}" onchange="updateSelection()"></td>
                        <td class="text-gray-400">{{ ($items->currentPage()-1)*$items->perPage()+$i+1 }}</td>
                        <td>
                            <a href="{{ route('items.show', $item->id) }}" class="font-semibold no-underline hover:underline" style="color: #3b82f6;" onclick="event.stopPropagation();">{{ $item->lot_id }}</a>
                        </td>
                        <td class="truncate" style="max-width: 140px;" title="{{ $item->description }}">{{ $item->description ?? '-' }}</td>
                        <td>{{ $item->parsed_gsm ?? '-' }}</td>
                        <td><span class="font-medium text-gray-700">{{ $item->grade ?? '-' }}</span></td>
                        <td>{{ $item->parsed_width ?? '-' }}</td>
                        <td class="text-xs text-gray-500">{{ $item->tr_date ? \Carbon\Carbon::parse($item->tr_date)->format('d M Y') : '-' }}</td>
                        <td class="font-semibold text-gray-900">{{ number_format($item->end_qty) }}</td>
                        <td>
                            @if($item->current_location)
                                <span class="tag tag-blue" title="{{ $item->current_location_label }}">{{ Str::limit($item->current_location, 16) }}</span>
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        <td>
                            @php
                                $tagClass = 'tag-gray';
                                $st = strtolower($item->status_barang ?? '');
                                if($st == 'good') $tagClass = 'tag-green';
                                elseif(in_array($st, ['hold','pending'])) $tagClass = 'tag-yellow';
                                elseif(in_array($st, ['reject','problem','rusak'])) $tagClass = 'tag-red';
                            @endphp
                            @if($item->status_barang && $item->status_barang != '-')
                                <span class="tag {{ $tagClass }}">{{ $item->status_barang }}</span>
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="truncate" style="max-width: 120px;" title="{{ $item->comments ?? '' }}">
                            @if($item->comments && $item->comments != '-')
                                <span class="text-xs text-gray-600"><i class="fas fa-comment-dots mr-1 text-gray-400"></i>{{ $item->comments }}</span>
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        <td>
                            <button onclick="showQR('{{ $item->lot_id }}', '{{ $item->paper_type ?? "-" }}', '{{ $item->gsm ?? "-" }}')" class="w-7 h-7 rounded-lg flex items-center justify-center hover:bg-gray-100 transition no-underline" onclick="event.stopPropagation();" title="QR Code">
                                <i class="fas fa-qrcode text-xs text-gray-400"></i>
                            </button>
                        </td>
                        <td>
                            <a href="{{ route('items.show', $item->id) }}" class="w-7 h-7 rounded-lg flex items-center justify-center hover:bg-gray-100 transition no-underline" onclick="event.stopPropagation();">
                                <i class="fas fa-arrow-right text-xs text-gray-400"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="14" class="text-center py-10">
                            <i class="fas fa-inbox text-2xl mb-2 block text-gray-400"></i>
                            <span class="text-gray-400">Tidak ada data</span>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- MOBILE CARDS -->
    <div class="md:hidden">
        @forelse($items as $item)
        <a href="{{ route('items.show', $item->id) }}" class="no-underline block">
            <div class="mobile-card item-row" data-id="{{ $item->id }}">
                <div class="flex items-center justify-between mb-2">
                    <span class="font-semibold text-sm" style="color: #3b82f6;">
                        <i class="fas fa-barcode mr-1 text-xs"></i>{{ $item->lot_id }}
                    </span>
                    @if($item->current_location)
                        <span class="tag tag-teal" style="font-size: 0.65rem;"><i class="fas fa-map-pin mr-1"></i>{{ Str::limit($item->current_location, 14) }}</span>
                    @endif
                </div>
                @if($item->description)
                    <div class="flex justify-between text-xs mb-1.5">
                        <span class="text-gray-400">Desc</span>
                        <span class="truncate text-right text-gray-600" style="max-width: 65%;">{{ Str::limit($item->description, 35) }}</span>
                    </div>
                @endif
                <div class="flex justify-between text-xs mb-1.5">
                    <span class="text-gray-400">Spec</span>
                    <span class="text-gray-600">{{ $item->parsed_gsm ?? '-' }} / {{ $item->parsed_width ?? '-' }} mm</span>
                </div>
                @if($item->grade)
                    <div class="flex justify-between text-xs mb-1.5">
                        <span class="text-gray-400">Grade</span>
                        <span class="text-gray-700 font-medium">{{ $item->grade }}</span>
                    </div>
                @endif
                @if($item->comments && $item->comments != '-')
                    <div class="flex justify-between text-xs mb-1.5">
                        <span class="text-gray-400"><i class="fas fa-comment-dots mr-1"></i>Komen</span>
                        <span class="truncate text-right text-gray-600" style="max-width: 65%;">{{ $item->comments }}</span>
                    </div>
                @endif
                <div class="flex justify-between text-xs mb-2">
                    <span class="text-gray-400">Qty</span>
                    <span class="font-semibold text-gray-900">{{ number_format($item->end_qty) }}</span>
                </div>
                @if($item->tr_date)
                    <div class="flex justify-between text-xs mb-2">
                        <span class="text-gray-400">TR Date</span>
                        <span class="text-gray-500">{{ \Carbon\Carbon::parse($item->tr_date)->format('d M Y') }}</span>
                    </div>
                @endif
                <div class="flex flex-wrap gap-1.5 mb-2">
                    @php
                        $tagClass = 'tag-gray';
                        $st = strtolower($item->status_barang ?? '');
                        if($st == 'good') $tagClass = 'tag-green';
                        elseif(in_array($st, ['hold','pending'])) $tagClass = 'tag-yellow';
                        elseif(in_array($st, ['reject','problem','rusak'])) $tagClass = 'tag-red';
                    @endphp
                    @if($item->status_barang && $item->status_barang != '-')
                        <span class="tag {{ $tagClass }}" style="font-size: 0.62rem;">{{ $item->status_barang }}</span>
                    @endif
                    <button onclick="event.preventDefault();showQR('{{ $item->lot_id }}', '{{ $item->paper_type ?? "-" }}', '{{ $item->gsm ?? "-" }}')" class="tag tag-gray" style="font-size:0.62rem;cursor:pointer;border:1px dashed #94a3b8;">
                        <i class="fas fa-qrcode mr-0.5"></i> QR
                    </button>
                </div>
            </div>
        </a>
        @empty
        <div class="text-center py-10">
            <i class="fas fa-inbox text-2xl mb-2 block text-gray-400"></i>
            <span class="text-gray-400">Tidak ada data</span>
        </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="flex items-center justify-between text-xs" class="text-gray-400">
        <span class="hidden sm:inline">
            {{ ($items->currentPage()-1)*$items->perPage()+1 }}-{{ min($items->currentPage()*$items->perPage(), $items->total()) }} / {{ number_format($items->total()) }}
        </span>
        <div class="ml-auto">{{ $items->links('vendor.pagination.custom') }}</div>
    </div>

</div>

<!-- QR Code Modal -->
<div id="qrModal" class="fixed inset-0 bg-black/40 z-50 hidden items-center justify-center p-4" style="display:none;" onclick="if(event.target===this)this.style.display='none'">
    <div class="modal-card rounded-xl shadow-2xl max-w-xs w-full p-6 text-center">
        <h3 class="text-sm font-bold text-gray-800 mb-4 flex items-center justify-center gap-2">
            <i class="fas fa-qrcode text-blue-500"></i> QR Code
        </h3>
        <div class="flex justify-center mb-4">
            <div id="qrCodeIndex" class="inline-block p-3 bg-white rounded-xl border border-gray-100"></div>
        </div>
        <div class="text-sm font-bold text-gray-900 mb-1" id="qrLotId"></div>
        <div class="text-xs text-gray-400 mb-4" id="qrSpec"></div>
        <div class="flex gap-2">
            <button onclick="downloadQRIndex()" class="btn btn-primary flex-1 flex items-center justify-center gap-1.5" style="font-size:0.75rem;">
                <i class="fas fa-download"></i> Download
            </button>
            <button onclick="document.getElementById('qrModal').style.display='none'" class="btn btn-ghost flex-1" style="font-size:0.75rem;">Tutup</button>
        </div>
    </div>
</div>

<script>
// Toggle filter chevron
const filterBtn = document.querySelector('[onclick*="advFilters"]');
const chevron = document.getElementById('filterChevron');
if(filterBtn && chevron) {
    filterBtn.addEventListener('click', () => {
        chevron.style.transform = document.getElementById('advFilters').classList.contains('hidden') ? 'rotate(0deg)' : 'rotate(180deg)';
    });
}

// Selection
function toggleAll(el) {
    document.querySelectorAll('.row-check').forEach(cb => { cb.checked = el.checked; });
    updateSelection();
}

function updateSelection() {
    const checked = document.querySelectorAll('.row-check:checked');
    const count = checked.length;
    const all = document.querySelectorAll('.row-check');
    document.getElementById('selectAll').checked = all.length > 0 && count === all.length;
    document.getElementById('selectedCount').classList.toggle('hidden', count === 0);
    document.getElementById('printSelectedBtn').classList.toggle('hidden', count === 0);
    document.getElementById('selectedNum').textContent = count;
    document.querySelectorAll('.item-row').forEach(row => {
        const cb = row.querySelector('.row-check');
        if(cb) row.style.background = cb.checked ? '#eff6ff' : '';
    });
}

function printSelected() {
    const ids = Array.from(document.querySelectorAll('.row-check:checked')).map(cb => cb.value);
    if(ids.length === 0) return;

    // Column definitions: [header, width%, extract(row)]
    // cells[0]=checkbox, [1]=#, [2]=Lot ID, [3]=Desc, [4]=GSM, [5]=Grade, [6]=Width, [7]=TR Date, [8]=Qty, [9]=Lokasi, [10]=Status, [11]=Komentar, [12]=QR, [13]=Arrow
    const cols = [
        { label: '#',         w: '4%',  fn: (r,i) => (i+1) },
        { label: 'Lot ID',    w: '14%', fn: r => r.cells[2]?.textContent?.trim() || '' },
        { label: 'Description', w: '18%', fn: r => r.cells[3]?.textContent?.trim() || '' },
        { label: 'GSM',       w: '6%',  fn: r => r.cells[4]?.textContent?.trim() || '' },
        { label: 'Grade',     w: '8%',  fn: r => r.cells[5]?.textContent?.trim() || '' },
        { label: 'Width',     w: '6%',  fn: r => r.cells[6]?.textContent?.trim() || '' },
        { label: 'TR Date',   w: '10%', fn: r => r.cells[7]?.textContent?.trim() || '' },
        { label: 'Qty',       w: '7%',  fn: r => r.cells[8]?.textContent?.trim() || '' },
        { label: 'Lokasi',    w: '10%', fn: r => r.cells[9]?.textContent?.trim() || '' },
        { label: 'Status',    w: '8%',  fn: r => r.cells[10]?.textContent?.trim() || '' },
        { label: 'Komentar',  w: '9%',  fn: r => r.cells[11]?.textContent?.trim() || '' },
    ];

    const ths = cols.map(c => `<th style="width:${c.w}">${c.label}</th>`).join('');

    let rows = '';
    let idx = 0;
    document.querySelectorAll('.row-check:checked').forEach(cb => {
        const row = cb.closest('tr.item-row');
        if (!row) return;
        const tds = cols.map(c => `<td>${c.fn(row, idx)}</td>`).join('');
        rows += `<tr>${tds}</tr>`;
        idx++;
    });

    const printHtml = `
    <!DOCTYPE html>
    <html><head>
        <title>Roll Items - Print</title>
        <style>
            @page { margin: 12mm 10mm; }
            * { box-sizing: border-box; }
            body { font-family: 'Inter', Arial, sans-serif; padding: 0; margin: 0; color: #1e293b; font-size: 10px; }
            .header { margin-bottom: 14px; border-bottom: 2px solid #e2e8f0; padding-bottom: 10px; }
            .header h2 { font-size: 15px; margin: 0 0 2px 0; color: #0f172a; }
            .header .sub { font-size: 10px; color: #64748b; }
            table { width: 100%; border-collapse: collapse; table-layout: fixed; }
            th { background: #f1f5f9; padding: 6px 8px; text-align: left; font-weight: 600; border-bottom: 2px solid #cbd5e1; text-transform: uppercase; letter-spacing: 0.3px; font-size: 8px; color: #475569; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
            td { padding: 5px 8px; border-bottom: 1px solid #f1f5f9; vertical-align: middle; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; font-size: 9.5px; }
            tr:nth-child(even) td { background: #f8fafc; }
            td:first-child { text-align: center; color: #94a3b8; }
            td:nth-child(2) { font-weight: 600; color: #1e40af; }
            td:nth-child(8) { text-align: right; font-weight: 600; }
            .status { display: inline-block; padding: 1px 5px; border-radius: 3px; font-size: 8px; font-weight: 600; }
            .s-good { background: #f0fdf4; color: #16a34a; }
            .s-hold, .s-pending { background: #fefce8; color: #ca8a04; }
            .s-reject, .s-problem, .s-rusak { background: #fef2f2; color: #dc2626; }
            .lokasi { background: #eff6ff; color: #2563eb; display: inline-block; padding: 1px 5px; border-radius: 3px; font-size: 8px; }
        </style>
    </head><body>
        <div class="header">
            <h2>Roll Off Management</h2>
            <div class="sub">${ids.length} item dipilih — dicetak pada ${new Date().toLocaleDateString('id-ID', {day:'numeric',month:'long',year:'numeric'})}</div>
        </div>
        <table><thead><tr>${ths}</tr></thead><tbody>${rows}</tbody></table>
    </body></html>`;

    const w = window.open('', '_blank', 'width=900,height=700');
    w.document.write(printHtml);
    w.document.close();
    w.onload = () => { w.print(); };
}

// QR Code
function showQR(lotId, paperType, gsm) {
    document.getElementById('qrLotId').textContent = lotId;
    document.getElementById('qrSpec').textContent = paperType + ' · ' + gsm + ' GSM';
    const container = document.getElementById('qrCodeIndex');
    container.innerHTML = '';
    new QRCode(container, {
        text: lotId,
        width: 180,
        height: 180,
        colorDark: '#1e293b',
        colorLight: '#ffffff',
        correctLevel: QRCode.CorrectLevel.M
    });
    document.getElementById('qrModal').style.display = 'flex';
}
function downloadQRIndex() {
    const canvas = document.querySelector('#qrCodeIndex canvas');
    if (!canvas) return;
    const a = document.createElement('a');
    a.href = canvas.toDataURL('image/png');
    a.download = 'QR-' + document.getElementById('qrLotId').textContent + '.png';
    a.click();
}
</script>
@endsection
