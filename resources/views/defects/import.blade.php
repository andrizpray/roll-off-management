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
        <p class="text-xs text-gray-400 mb-5">Upload file CSV/Excel berisi daftar lot_id yang bermasalah. Data spesifikasi (paper type, GSM, dll) akan otomatis diambil dari <strong>Roll Items</strong>.</p>

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
                    <i class="fas fa-download text-xs"></i> Download Template
                </a>
            </div>
        </form>
    </div>

    <!-- Info Card -->
    <div class="card p-5">
        <h3 class="text-sm font-semibold text-gray-800 mb-3 flex items-center gap-2">
            <i class="fas fa-info-circle text-xs text-blue-500"></i>Petunjuk
        </h3>
        <div class="space-y-2.5">
            <div class="flex items-start gap-2 text-xs text-gray-600">
                <span class="inline-flex items-center justify-center w-5 h-5 rounded-md bg-blue-50 text-blue-600 font-bold shrink-0 mt-0.5">1</span>
                <span>Download template terlebih dahulu, atau buat CSV sendiri dengan kolom: <code class="px-1.5 py-0.5 bg-gray-100 rounded text-gray-700">lot_id, reason, category, defect_date</code></span>
            </div>
            <div class="flex items-start gap-2 text-xs text-gray-600">
                <span class="inline-flex items-center justify-center w-5 h-5 rounded-md bg-blue-50 text-blue-600 font-bold shrink-0 mt-0.5">2</span>
                <span>Isi minimal <strong>lot_id</strong>. Kolom lain bersifat opsional.</span>
            </div>
            <div class="flex items-start gap-2 text-xs text-gray-600">
                <span class="inline-flex items-center justify-center w-5 h-5 rounded-md bg-blue-50 text-blue-600 font-bold shrink-0 mt-0.5">3</span>
                <span>Data spesifikasi (<strong>paper_type, GSM, plybond, width, rew_id, keterangan</strong>) akan otomatis diambil dari data Roll Items berdasarkan lot_id.</span>
            </div>
            <div class="flex items-start gap-2 text-xs text-gray-600">
                <span class="inline-flex items-center justify-center w-5 h-5 rounded-md bg-blue-50 text-blue-600 font-bold shrink-0 mt-0.5">4</span>
                <span>Pilih <strong>tahun</strong> yang sesuai. Kolom <code class="px-1.5 py-0.5 bg-gray-100 rounded text-gray-700">defect_date</code> boleh dikosongkan (default: hari ini).</span>
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
