@extends('layouts.app')

@section('title', 'Import Defect')
@section('page-title')
<i class="fas fa-file-import mr-2 opacity-60"></i>Import Defect
@endsection

@section('content')
<div class="animate-in space-y-4">

    <!-- Back -->
    <a href="{{ route('defects.index') }}" class="inline-flex items-center gap-2 text-xs font-medium no-underline text-gray-500 hover:text-gray-700 transition">
        <i class="fas fa-arrow-left"></i> Kembali ke Barang Bermasalah
    </a>

    <!-- Error -->
    @if(session('error'))
        <div class="p-3 rounded-xl bg-red-50 border border-red-200 text-sm text-red-700 flex items-center gap-2">
            <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
        </div>
    @endif

    <!-- Success -->
    @if(session('success'))
        <div class="p-4 rounded-xl bg-green-50 border border-green-200 text-sm text-green-700">
            <div class="flex items-center gap-2 mb-2">
                <i class="fas fa-check-circle"></i>
                <span class="font-semibold">Import berhasil!</span>
            </div>
            <div class="text-green-600">{{ session('success') }}</div>
        </div>
    @endif

    <!-- Upload Form -->
    <div class="card p-6">
        <h3 class="text-sm font-semibold text-gray-800 mb-1 flex items-center gap-2">
            <i class="fas fa-upload text-xs text-blue-500"></i>Import Barang Bermasalah
        </h3>
        <p class="text-xs text-gray-400 mb-5">Upload file CSV/Excel. Sistem otomatis mendeteksi format file — <strong>mode baru</strong> (CSV sederhana) atau <strong>mode update</strong> (Excel detail).</p>

        <form method="POST" action="{{ route('defects.import.post') }}" enctype="multipart/form-data" id="importForm">
            @csrf

            <!-- Year -->
            <div class="mb-5">
                <label class="block text-[10px] font-semibold uppercase tracking-wide text-gray-400 mb-1.5">Tahun *</label>
                <select name="year" class="select-field w-full max-w-xs" required>
                    <option value="">Pilih tahun...</option>
                    <option value="2025" {{ old('year') == 2025 ? 'selected' : '' }}>2025</option>
                    <option value="2026" {{ old('year') == 2026 ? 'selected' : '' }}>2026</option>
                </select>
            </div>

            <!-- Mode Selector -->
            <div class="mb-5">
                <label class="block text-[10px] font-semibold uppercase tracking-wide text-gray-400 mb-2">Mode Import</label>
                <div class="flex flex-wrap gap-2">
                    <label class="flex items-center gap-2 px-4 py-2.5 rounded-xl border-2 cursor-pointer transition has-[:checked]:border-blue-500 has-[:checked]:bg-blue-50 border-gray-200">
                        <input type="radio" name="mode" value="auto" checked class="accent-blue-500">
                        <div>
                            <span class="text-xs font-semibold text-gray-800">🤖 Otomatis</span>
                            <p class="text-[10px] text-gray-400">Deteksi format dari kolom file</p>
                        </div>
                    </label>
                    <label class="flex items-center gap-2 px-4 py-2.5 rounded-xl border-2 cursor-pointer transition has-[:checked]:border-green-500 has-[:checked]:bg-green-50 border-gray-200">
                        <input type="radio" name="mode" value="new" class="accent-green-500">
                        <div>
                            <span class="text-xs font-semibold text-gray-800">➕ Data Baru</span>
                            <p class="text-[10px] text-gray-400">Tambah defect baru dari CSV sederhana</p>
                        </div>
                    </label>
                    <label class="flex items-center gap-2 px-4 py-2.5 rounded-xl border-2 cursor-pointer transition has-[:checked]:border-purple-500 has-[:checked]:bg-purple-50 border-gray-200">
                        <input type="radio" name="mode" value="update" class="accent-purple-500">
                        <div>
                            <span class="text-xs font-semibold text-gray-800">🔄 Update Data</span>
                            <p class="text-[10px] text-gray-400">Isi field kosong dari Excel detail</p>
                        </div>
                    </label>
                </div>
            </div>

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
                <p class="text-xs text-gray-400 mt-1">Format: .csv, .xlsx, .xls (maks 5MB)</p>
                <div id="fileInfo" class="mt-3 hidden">
                    <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-blue-50 text-blue-700 text-xs font-medium">
                        <i class="fas fa-file-excel"></i>
                        <span id="fileName"></span>
                        <button type="button" onclick="event.stopPropagation(); clearFile()" class="ml-1 text-blue-400 hover:text-red-500">
                            <i class="fas fa-times"></i>
                        </button>
                    </span>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex items-center gap-3 mt-5">
                <button type="submit" class="btn btn-primary" style="background: #7c3aed;">
                    <i class="fas fa-file-import text-xs"></i> Import
                </button>
                <a href="{{ route('defects.import.template') }}" class="btn btn-ghost flex items-center gap-1.5" style="padding: 6px 14px; font-size: 0.7rem;">
                    <i class="fas fa-download text-xs"></i> Template CSV (Data Baru)
                </a>
            </div>
        </form>
    </div>

    <!-- Info Card -->
    <div class="card p-5">
        <h3 class="text-sm font-semibold text-gray-800 mb-3 flex items-center gap-2">
            <i class="fas fa-info-circle text-xs text-blue-500"></i>Format yang Didukung
        </h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <!-- Mode Baru -->
            <div class="p-4 rounded-xl bg-green-50 border border-green-100">
                <h4 class="text-xs font-bold text-green-700 mb-2 flex items-center gap-1.5">
                    <i class="fas fa-plus-circle"></i> Mode Data Baru
                </h4>
                <p class="text-[11px] text-green-600 mb-2">CSV sederhana untuk menambah defect baru.</p>
                <div class="text-[10px] text-green-600 space-y-1">
                    <div class="flex items-start gap-1.5">
                        <span class="font-mono bg-green-100 px-1 rounded">lot_id</span>
                        <span class="text-green-500">— wajib</span>
                    </div>
                    <div class="flex items-start gap-1.5">
                        <span class="font-mono bg-green-100 px-1 rounded">reason</span>
                        <span class="text-green-500">— opsional</span>
                    </div>
                    <div class="flex items-start gap-1.5">
                        <span class="font-mono bg-green-100 px-1 rounded">category</span>
                        <span class="text-green-500">— opsional</span>
                    </div>
                    <div class="flex items-start gap-1.5">
                        <span class="font-mono bg-green-100 px-1 rounded">defect_date</span>
                        <span class="text-green-500">— opsional (default hari ini)</span>
                    </div>
                </div>
                <p class="text-[10px] text-green-500 mt-2 italic">Data spesifikasi diambil otomatis dari Roll Items.</p>
            </div>

            <!-- Mode Update -->
            <div class="p-4 rounded-xl bg-purple-50 border border-purple-100">
                <h4 class="text-xs font-bold text-purple-700 mb-2 flex items-center gap-1.5">
                    <i class="fas fa-sync-alt"></i> Mode Update Data
                </h4>
                <p class="text-[11px] text-purple-600 mb-2">Excel detail untuk mengisi field kosong di defect yang sudah ada.</p>
                <div class="text-[10px] text-purple-600 space-y-1">
                    <div class="flex items-start gap-1.5">
                        <span class="font-mono bg-purple-100 px-1 rounded">LotID</span>
                        <span class="text-purple-500">— wajib</span>
                    </div>
                    <div class="flex items-start gap-1.5">
                        <span class="font-mono bg-purple-100 px-1 rounded">PaperType</span>
                        <span class="text-purple-500">— opsional</span>
                    </div>
                    <div class="flex items-start gap-1.5">
                        <span class="font-mono bg-purple-100 px-1 rounded">Gramature</span>
                        <span class="text-purple-500">— opsional</span>
                    </div>
                    <div class="flex items-start gap-1.5">
                        <span class="font-mono bg-purple-100 px-1 rounded">RewID / Width / Plybond / Comment</span>
                        <span class="text-purple-500">— opsional</span>
                    </div>
                </div>
                <p class="text-[10px] text-purple-500 mt-2 italic">Hanya mengisi field yang masih kosong, tidak menimpa data.</p>
            </div>
        </div>
    </div>

</div>

<script>
const dropZone = document.getElementById('dropZone');
const fileInput = document.getElementById('fileInput');
const fileInfo = document.getElementById('fileInfo');
const fileName = document.getElementById('fileName');

fileInput.addEventListener('change', function() {
    if (this.files.length > 0) {
        fileName.textContent = this.files[0].name;
        fileInfo.classList.remove('hidden');
    }
});

function clearFile() {
    fileInput.value = '';
    fileInfo.classList.add('hidden');
}

['dragenter', 'dragover'].forEach(evt => {
    dropZone.addEventListener(evt, e => { e.preventDefault(); dropZone.classList.add('border-blue-400', 'bg-blue-50/50'); });
});
['dragleave', 'drop'].forEach(evt => {
    dropZone.addEventListener(evt, e => { e.preventDefault(); dropZone.classList.remove('border-blue-400', 'bg-blue-50/50'); });
});
dropZone.addEventListener('drop', e => {
    const files = e.dataTransfer.files;
    if (files.length > 0) {
        fileInput.files = files;
        fileName.textContent = files[0].name;
        fileInfo.classList.remove('hidden');
    }
});
</script>
@endsection
