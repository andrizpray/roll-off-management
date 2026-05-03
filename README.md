# 📦 Roll Off Management

Sistem manajemen inventory **paper roll** — tracking lokasi, monitoring defect, dan analisis data untuk gudang roll off.

![Laravel](https://img.shields.io/badge/Laravel-13-red)
![PHP](https://img.shields.io/badge/PHP-8.3-blue)
![MySQL](https://img.shields.io/badge/MySQL-8-orange)

## 🚀 Fitur

### Dashboard
- **4 chart interaktif** (Chart.js) — distribusi paper type, lokasi, GSM, dan defect
- **4 stat card** — total rolls, total defects, item tanpa lokasi, distribusi status
- Real-time clock di topbar

### Roll Items (Inventory)
- **CRUD lengkap** — create, read, update, delete roll items
- **Search & filter** — cari by Lot ID, Item ID, description, filter paper type, GSM, width, grade (multi-select), status, lokasi
- **Sorting** — klik kolom header untuk sort ascending/descending
- **Pagination** — 50 item per halaman
- **Export Excel** — export data yang terfilter ke .xlsx
- **QR Code** — generate QR per item (encode Lot ID), bisa download PNG
- **Detail page** — informasi lengkap + timeline lokasi + daftar defect terkait
- **Print layout** — CSS khusus untuk cetak

### Barang Bermasalah (Defects)
- **2 chart analitik** — trend defect per bulan, distribusi paper type defect
- **Filter** — year, paper type, reason, search
- **Export Excel** — export defect data
- **Summary Report** — export 3-sheet Excel (Ringkasan, Defects, Paper Type)
- **Detail modal** — ringkasan per paper type

### Smart Sync Import Excel
- **Upload via web** — drag & drop file Excel (.xlsx/.xls/.csv)
- **Preview sebelum sync** — tampilkan statistik: item baru, diupdate, tidak berubah
- **Diff perubahan** — tabel menunjukkan field yang berubah (old → new)
- **Upsert** — item baru ditambahkan, item yang sudah ada diupdate
- **Auto-import defect** — sheet "Barang Bermasalah 2025/2026" otomatis diimport
- **Konfirmasi** — tombol sync dengan konfirmasi dialog

### Notifikasi
- 🔔 **Bell icon** di topbar dengan badge counter
- 🔴 **Item Tanpa Lokasi** — roll items yang belum punya lokasi tracking
- 🟡 **Defect Baru** — barang bermasalah yang ditambahkan 7 hari terakhir
- Auto-load via AJAX, klik item → redirect ke detail

### Tema
- 🌙 **Dark/Light mode** — toggle via tombol sun/moon
- Otomatis detect preferensi OS (`prefers-color-scheme`)
- Persist via `localStorage`
- Chart.js rebuild warna saat ganti tema

### Mobile Responsive
- Desktop: tabel data dengan sticky header
- Mobile: card view yang rapi
- Sidebar collapsible dengan overlay di mobile

## 🛠️ Tech Stack

| Komponen | Teknologi |
|---|---|
| Framework | Laravel 13 |
| Frontend | Blade + Tailwind CSS CDN |
| Charts | Chart.js v4 |
| Icons | Font Awesome 6 |
| QR Code | QRCode.js v1 |
| Excel | Maatwebsite/Excel (PhpSpreadsheet) |
| Database | MySQL 8 |
| Server | Nginx + PHP 8.3 FPM |

## 📁 Struktur Project

```
app/
├── Models/
│   ├── RollItem.php          # Model roll item (21 fillable fields + accessors)
│   └── DefectItem.php        # Model defect item (14 fillable fields)
├── Services/
│   ├── ImportSyncService.php # Excel import: preview + sync
│   └── NotificationService.php # Notification data provider
├── Http/Controllers/
│   ├── DashboardController.php
│   ├── RollItemController.php
│   └── DefectItemController.php
├── Exports/
│   ├── RollItemsExport.php
│   ├── DefectItemsExport.php
│   └── SummaryReportExport.php
└── Console/Commands/
    ├── ImportExcelData.php   # CLI import from terminal
    └── ReparseDescriptions.php
```

## 🚀 Instalasi

```bash
git clone git@github.com:andrizpray/roll-off-management.git
cd roll-off-management

composer install
cp .env.example .env
php artisan key:generate

# Konfigurasi database di .env
php artisan migrate

# (Opsional) Import data dari Excel
php artisan import:excel /path/to/data.xlsx
```

## 📊 Data Model

### Roll Items
Kolom tracking lokasi (dicek berurutan):
`SO Desember → Receiving 2026 → SO Maret 2026 → PIC 2026 → RCV/CNV 2026 → SO September`

### Defect Items
- Data defect 2025 dan 2026 dari sheet terpisah
- Auto-fill spec (paper type, GSM, dll) dari roll_items via `lot_id` lookup

## 📝 License

Private repository.
