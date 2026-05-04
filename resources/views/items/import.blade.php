@extends('layouts.app')

@section('title', 'Smart Sync Import')
@section('page-title')
    <i class="fas fa-file-import mr-2 opacity-60"></i>Smart Sync Import
@endsection

@section('content')
<div class="animate-in space-y-4">

    <!-- Back -->
    <a href="{{ route('items.index') }}" class="inline-flex items-center gap-2 text-xs font-medium no-underline text-gray-500 hover:text-gray-700 transition">
        <i class="fas fa-arrow-left"></i> Kembali ke Roll Items
    </a>

    <!-- Error message -->
    @if(session('error'))
        <div class="p-3 rounded-xl bg-red-50 border border-red-200 text-sm text-red-700 flex items-center gap-2">
            <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
        </div>
    @endif

    <!-- Success message -->
    @if(session('success'))
        <div class="p-4 rounded-xl bg-green-50 border border-green-200 text-sm text-green-700">
            <div class="flex items-center gap-2 mb-2">
                <i class="fas fa-check-circle"></i>
                <span class="font-semibold">Sync berhasil!</span>
            </div>
            <div class="text-green-600">{{ session('success') }}</div>
        </div>
    @endif

    @if(!isset($preview))
    <!-- Upload Form -->
    <div class="card p-6">
        <h3 class="text-sm font-semibold text-gray-800 mb-1 flex items-center gap-2">
            <i class="fas fa-upload text-xs text-blue-500"></i>Upload File Excel
        </h3>
        <p class="text-xs text-gray-400 mb-5">Upload file Excel. Sistem otomatis mendeteksi format — <strong>sheet DATA</strong> (sync lengkap) atau <strong>format detail</strong> (update field kosong + DetailLocation).</p>

        <form method="POST" action="{{ route('items.import.preview') }}" enctype="multipart/form-data" id="uploadForm">
            @csrf

            <!-- Drop Zone -->
            <div id="dropZone" class="border-2 border-dashed border-gray-300 rounded-xl p-8 text-center cursor-pointer hover:border-blue-400 hover:bg-blue-50/50 transition-all group"
                 onclick="document.getElementById('fileInput').click()">
                <input type="file" id="fileInput" name="file" accept=".xlsx,.xls,.csv" class="hidden" required>
                <div class="mb-3">
                    <i class="fas fa-cloud-arrow-up text-3xl text-gray-300 group-hover:text-blue-400 transition"></i>
                </div>
                <p class="text-sm font-medium text-gray-600 group-hover:text-blue-600 transition">
                    Klik atau drag & drop file di sini
                </p>
                <p class="text-xs text-gray-400 mt-1">Format: .xlsx, .xls, .csv (maks 20MB)</p>
                <div id="fileInfo" class="mt-3 hidden">
                    <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-blue-50 text-blue-700 text-xs font-medium">
                        <i class="fas fa-file-excel"></i>
                        <span id="fileName"></span>
                        <button type="button" onclick="event.stopPropagation(); clearFile()" class="ml-1 text-blue-400 hover:text-red-500">
                            <i class="fas fa-xmark"></i>
                        </button>
                    </span>
                </div>
            </div>

            <div class="mt-5 flex justify-end">
                <button type="submit" id="uploadBtn" class="btn btn-primary px-6 py-2.5 text-sm font-medium rounded-xl opacity-50 cursor-not-allowed" disabled>
                    <i class="fas fa-magnifying-glass mr-2"></i>Preview Sync
                </button>
            </div>
        </form>
    </div>

    <!-- Info Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="card p-4">
            <div class="flex items-start gap-3">
                <div class="w-8 h-8 rounded-lg bg-blue-50 flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-table text-blue-500 text-xs"></i>
                </div>
                <div>
                    <p class="text-xs font-semibold text-gray-800">Format Sheet DATA</p>
                    <p class="text-xs text-gray-400 mt-0.5">File dengan sheet "DATA" — sync lengkap semua field (upsert). Sheet defect otomatis diimport.</p>
                </div>
            </div>
        </div>
        <div class="card p-4">
            <div class="flex items-start gap-3">
                <div class="w-8 h-8 rounded-lg bg-purple-50 flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-file-lines text-purple-500 text-xs"></i>
                </div>
                <div>
                    <p class="text-xs font-semibold text-gray-800">Format Detail (LotID, PaperType, dll)</p>
                    <p class="text-xs text-gray-400 mt-0.5">Hanya mengisi field yang masih kosong. <strong>DetailLocation</strong> → lokasi, <strong>UpdateDetailLocation</strong> → timestamp.</p>
                </div>
            </div>
        </div>
    </div>

    @else
    <!-- Preview Results -->
    <div class="card p-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-5">
            <div>
                <h3 class="text-sm font-semibold text-gray-800 flex items-center gap-2">
                    <i class="fas fa-table text-xs text-blue-500"></i>Preview: {{ $fileName }}
                </h3>
                <p class="text-xs text-gray-400 mt-0.5">{{ number_format($preview['total_rows']) }} baris dibaca dari file Excel</p>
            </div>
            <div class="flex gap-2">
                <span class="tag {{ ($format ?? '') === 'detail' ? 'tag-purple' : 'tag-blue' }}">
                    @if(($format ?? '') === 'detail')
                        <i class="fas fa-file-lines mr-1"></i>Format Detail — Update Field Kosong
                    @else
                        <i class="fas fa-table mr-1"></i>Format DATA Sheet — Full Sync
                    @endif
                </span>
                <form method="POST" action="{{ route('items.import') }}" class="inline">
                    @csrf
                    <button type="submit" class="btn btn-ghost px-4 py-2 text-xs font-medium rounded-lg">
                        <i class="fas fa-arrow-rotate-left mr-1"></i>Upload Ulang
                    </button>
                </form>
            </div>
        </div>

        <!-- Stat Cards -->
        <div class="grid grid-cols-2 lg:grid-cols-5 gap-3 mb-5">
            <div class="stat-card blue card p-4 text-center">
                <div class="text-2xl font-bold text-gray-900">{{ number_format($preview['valid_rows']) }}</div>
                <div class="text-xs text-gray-400 mt-1">Valid Rows</div>
            </div>
            <div class="stat-card green card p-4 text-center">
                <div class="text-2xl font-bold text-green-600">{{ number_format($preview['new']) }}</div>
                <div class="text-xs text-gray-400 mt-1">Baru</div>
            </div>
            <div class="stat-card amber card p-4 text-center">
                <div class="text-2xl font-bold text-amber-600">{{ number_format($preview['updated']) }}</div>
                <div class="text-xs text-gray-400 mt-1">Diupdate</div>
            </div>
            <div class="stat-card teal card p-4 text-center">
                <div class="text-2xl font-bold text-gray-400">{{ number_format($preview['unchanged']) }}</div>
                <div class="text-xs text-gray-400 mt-1">Tidak Berubah</div>
            </div>
            @if(($preview['delete_count'] ?? 0) > 0)
            <div class="stat-card red card p-4 text-center">
                <div class="text-2xl font-bold text-red-600">{{ number_format($preview['delete_count']) }}</div>
                <div class="text-xs text-gray-400 mt-1">Akan Dihapus</div>
            </div>
            @endif
        </div>

        @if(($preview['delete_count'] ?? 0) > 0)
        <!-- Deletion Warning -->
        <div class="p-4 rounded-xl bg-red-50 border border-red-200 mb-5">
            <div class="flex items-start gap-2 mb-3">
                <i class="fas fa-triangle-exclamation text-red-500 mt-0.5"></i>
                <div>
                    <p class="text-sm font-semibold text-red-700">{{ number_format($preview['delete_count']) }} Lot ID akan dihapus</p>
                    <p class="text-xs text-red-500 mt-0.5">Lot ID berikut ada di database tapi <strong>tidak ditemukan</strong> di file import. Data roll item beserta defect terkait akan otomatis dihapus.</p>
                </div>
            </div>
            <div class="overflow-x-auto rounded-lg border border-red-200" style="max-height: 200px; overflow-y: auto;">
                <table class="data-table text-xs">
                    <thead>
                        <tr>
                            <th class="sticky top-0 z-10 bg-red-100">Lot ID</th>
                            <th class="sticky top-0 z-10 bg-red-100">Paper Type</th>
                            <th class="sticky top-0 z-10 bg-red-100">GSM</th>
                            <th class="sticky top-0 z-10 bg-red-100">Width</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach(($preview['to_delete'] ?? []) as $del)
                        <tr class="bg-red-50/50">
                            <td class="font-mono font-semibold text-red-700">{{ $del['lot_id'] }}</td>
                            <td class="text-red-600">{{ $del['paper_type'] ?? '-' }}</td>
                            <td class="text-red-600">{{ $del['gsm'] ?? '-' }}</td>
                            <td class="text-red-600">{{ $del['width'] ?? '-' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

        @if($preview['skipped'] > 0)
            <div class="p-3 rounded-lg bg-yellow-50 border border-yellow-200 text-xs text-yellow-700 mb-5 flex items-center gap-2">
                <i class="fas fa-triangle-exclamation"></i>
                {{ number_format($preview['skipped']) }} baris dilewati (Lot ID kosong)
            </div>
        @endif

        <!-- Changes Table -->
        @php
            $changedItems = array_filter($preview['items'], fn($i) => $i['status'] !== 'exists');
            $displayItems = array_slice($changedItems, 0, 100);
            $totalChanged = count($changedItems);
        @endphp

        @if($totalChanged > 0)
            <div class="mb-4">
                <h4 class="text-xs font-semibold text-gray-600 mb-3">
                    <i class="fas fa-list-check mr-1"></i>
                    Perubahan ({{ number_format($totalChanged) }} item)
                    @if($totalChanged > 100)
                        <span class="font-normal text-gray-400">— menampilkan 100 pertama</span>
                    @endif
                </h4>
            </div>

            <div class="overflow-x-auto rounded-xl border border-gray-200" style="max-height: 500px; overflow-y: auto;">
                <table class="data-table text-xs">
                    <thead>
                        <tr>
                            <th class="sticky top-0 z-10">Status</th>
                            <th class="sticky top-0 z-10">Lot ID</th>
                            <th class="sticky top-0 z-10">Paper Type</th>
                            <th class="sticky top-0 z-10">GSM</th>
                            <th class="sticky top-0 z-10">Width</th>
                            <th class="sticky top-0 z-10">Grade</th>
                            <th class="sticky top-0 z-10">Lokasi</th>
                            @if(($format ?? '') === 'detail')
                            <th class="sticky top-0 z-10">Detail Lokasi</th>
                            @endif
                            <th class="sticky top-0 z-10">Perubahan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($displayItems as $item)
                        <tr class="{{ $item['status'] === 'new' ? 'bg-green-50/50' : 'bg-amber-50/50' }}">
                            <td>
                                @if($item['status'] === 'new')
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-green-100 text-green-700 font-medium">
                                        <i class="fas fa-plus text-[8px]"></i> Baru
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-amber-100 text-amber-700 font-medium">
                                        <i class="fas fa-pen text-[8px]"></i> Update
                                    </span>
                                @endif
                            </td>
                            <td class="font-mono font-semibold text-gray-900">{{ $item['lot_id'] }}</td>
                            <td class="text-gray-700">{{ $item['paper_type'] ?? '-' }}</td>
                            <td class="text-gray-700">{{ $item['gsm'] ?? '-' }}</td>
                            <td class="text-gray-700">{{ $item['width'] ?? '-' }}</td>
                            <td class="text-gray-700">{{ $item['grade'] ?? '-' }}</td>
                            <td class="text-gray-700">{{ $item['location_id'] ?? '-' }}</td>
                            @if(($format ?? '') === 'detail')
                            <td class="text-gray-700 text-[10px]">{{ $item['detail_location'] ?? '-' }}</td>
                            @endif
                            <td>
                                @if($item['status'] === 'updated' && isset($item['changes']))
                                    <div class="space-y-0.5">
                                        @foreach(array_slice($item['changes'], 0, 3) as $field => $change)
                                            <div class="flex items-center gap-1">
                                                <span class="text-gray-400">{{ $field }}</span>
                                                <span class="text-red-400 line-through">{{ $change['old'] ?? 'null' }}</span>
                                                <i class="fas fa-arrow-right text-gray-300 text-[8px]"></i>
                                                <span class="text-green-600">{{ $change['new'] ?? 'null' }}</span>
                                            </div>
                                        @endforeach
                                        @if(count($item['changes']) > 3)
                                            <span class="text-gray-400">+{{ count($item['changes']) - 3 }} field lainnya</span>
                                        @endif
                                    </div>
                                @elseif($item['status'] === 'new')
                                    <span class="text-green-600">Item baru ditambahkan</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-8">
                <i class="fas fa-check-circle text-3xl text-green-300 mb-2"></i>
                <p class="text-sm text-gray-500">Semua data sudah sama — tidak ada perubahan yang diperlukan.</p>
            </div>
        @endif

        <!-- Sync Button -->
        @if($preview['new'] > 0 || $preview['updated'] > 0 || ($preview['delete_count'] ?? 0) > 0)
            <div class="mt-5 flex flex-col sm:flex-row items-center justify-between gap-3 p-4 rounded-xl bg-blue-50 border border-blue-200">
                <div class="text-xs text-blue-700">
                    <i class="fas fa-info-circle mr-1"></i>
                    @if($preview['new'] > 0 || $preview['updated'] > 0)
                    {{ number_format($preview['new']) }} item baru + {{ number_format($preview['updated']) }} update akan disimpan.
                    @endif
                    @if(($preview['delete_count'] ?? 0) > 0)
                    <span class="text-red-600 font-semibold">{{ number_format($preview['delete_count']) }} item akan dihapus</span> (tidak ada di file).
                    @endif
                    @if(($format ?? '') !== 'detail')
                    Defect sheets juga akan diimport otomatis.
                    @else
                    Hanya field kosong yang akan diisi (tidak menimpa data).
                    @endif
                </div>
                <form method="POST" action="{{ route('items.import.sync') }}" id="syncForm" onsubmit="return confirmSync()">
                    @csrf
                    <button type="submit" class="btn btn-primary px-6 py-2.5 text-sm font-medium rounded-xl whitespace-nowrap">
                        <i class="fas fa-sync mr-2"></i>Lanjutkan Sync
                    </button>
                </form>
            </div>
        @endif
    </div>
    @endif

</div>

<script>
    // File upload handlers
    const fileInput = document.getElementById('fileInput');
    const dropZone = document.getElementById('dropZone');
    const fileInfo = document.getElementById('fileInfo');
    const fileNameEl = document.getElementById('fileName');
    const uploadBtn = document.getElementById('uploadBtn');

    if (fileInput) {
        fileInput.addEventListener('change', function() {
            if (this.files.length > 0) {
                showFile(this.files[0]);
            }
        });
    }

    if (dropZone) {
        dropZone.addEventListener('dragover', function(e) {
            e.preventDefault();
            this.classList.add('border-blue-400', 'bg-blue-50');
        });

        dropZone.addEventListener('dragleave', function(e) {
            e.preventDefault();
            this.classList.remove('border-blue-400', 'bg-blue-50');
        });

        dropZone.addEventListener('drop', function(e) {
            e.preventDefault();
            this.classList.remove('border-blue-400', 'bg-blue-50');
            if (e.dataTransfer.files.length > 0) {
                fileInput.files = e.dataTransfer.files;
                showFile(e.dataTransfer.files[0]);
            }
        });
    }

    function showFile(file) {
        const ext = file.name.split('.').pop().toLowerCase();
        if (!['xlsx', 'xls', 'csv'].includes(ext)) {
            alert('Format file tidak didukung. Gunakan .xlsx, .xls, atau .csv');
            clearFile();
            return;
        }
        if (file.size > 20 * 1024 * 1024) {
            alert('File terlalu besar. Maksimal 20MB.');
            clearFile();
            return;
        }
        fileNameEl.textContent = file.name + ' (' + (file.size / 1024 / 1024).toFixed(1) + ' MB)';
        fileInfo.classList.remove('hidden');
        uploadBtn.disabled = false;
        uploadBtn.classList.remove('opacity-50', 'cursor-not-allowed');
    }

    function clearFile() {
        fileInput.value = '';
        fileInfo.classList.add('hidden');
        uploadBtn.disabled = true;
        uploadBtn.classList.add('opacity-50', 'cursor-not-allowed');
    }

    function confirmSync() {
        return confirm('Yakin ingin melanjutkan sync? Data akan ditambahkan/diupdate ke database.');
    }
</script>
@endsection
