<?php

namespace App\Http\Controllers;

use App\Exports\DefectItemsExport;
use App\Exports\SummaryReportExport;
use App\Models\DefectItem;
use App\Models\RollItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Facades\Excel;

class DefectItemController extends Controller
{
    public function index(Request $request)
    {
        $query = DefectItem::query();

        // Filter: Year
        if ($year = $request->input('year')) {
            $query->where('year', $year);
        }

        // Search by LotID, RewID
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('lot_id', 'LIKE', "%{$search}%")
                  ->orWhere('rew_id', 'LIKE', "%{$search}%");
            });
        }

        // Filter: Reason
        if ($reason = $request->input('reason')) {
            $query->where('reason', $reason);
        }

        // Filter: Paper Type
        if ($paperType = $request->input('paper_type')) {
            $query->where('paper_type', $paperType);
        }

        // Filter: Month
        if ($month = $request->input('month')) {
            $query->where('month', $month);
        }

        $defects = $query->orderBy('defect_date', 'desc')->paginate(50)->withQueryString();

        // Eager load related roll item for grade & comments
        $defects->loadMissing(['rollItem']);

        // Dropdowns
        $reasons = DefectItem::whereNotNull('reason')->distinct()->orderBy('reason')->pluck('reason');
        $paperTypes = DefectItem::whereNotNull('paper_type')->distinct()->orderBy('paper_type')->pluck('paper_type');
        $months = DefectItem::whereNotNull('month')->distinct()->orderBy('month')->pluck('month');
        $years = DefectItem::distinct()->orderByDesc('year')->pluck('year');

        // Stats
        $totalDefects = DefectItem::count();
        $defect2025 = DefectItem::where('year', 2025)->count();
        $defect2026 = DefectItem::where('year', 2026)->count();
        $totalRolls = RollItem::count();
        $defectRate = $totalRolls > 0 ? round(($totalDefects / $totalRolls) * 100, 2) : 0;

        // 5.1: Defect rate per paper type
        $defectByPaper = DefectItem::selectRaw("paper_type, COUNT(*) as defect_count")
            ->whereNotNull('paper_type')
            ->groupBy('paper_type')
            ->orderByDesc('defect_count')
            ->limit(8)
            ->get();

        // Get roll count per paper type for rate calculation
        $rollByPaper = RollItem::selectRaw("paper_type, COUNT(*) as roll_count")
            ->whereNotNull('paper_type')
            ->groupBy('paper_type')
            ->get()
            ->keyBy('paper_type');

        // 5.2: Trend defect per bulan (month name -> numeric sort)
        $monthOrder = ['JANUARY' => 1, 'FEBRUARY' => 2, 'MARCH' => 3, 'APRIL' => 4, 'MAY' => 5, 'JUNE' => 6, 'JULY' => 7, 'AUGUST' => 8, 'SEPTEMBER' => 9, 'OCTOBER' => 10, 'NOVEMBER' => 11, 'DECEMBER' => 12];
        $defectTrend = DefectItem::selectRaw("year, month, COUNT(*) as count")
            ->whereNotNull('month')
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->get()
            ->map(function ($item) use ($monthOrder) {
                $item->month_num = $monthOrder[strtoupper($item->month)] ?? 0;
                return $item;
            })
            ->sortBy('year')
            ->sortBy('month_num');

        // 5.3: Top defect reasons with percentage
        $topReasons = DefectItem::selectRaw("reason, COUNT(*) as count")
            ->whereNotNull('reason')
            ->groupBy('reason')
            ->orderByDesc('count')
            ->limit(10)
            ->get()
            ->map(function ($item) use ($totalDefects) {
                $item->percentage = $totalDefects > 0 ? round(($item->count / $totalDefects) * 100, 1) : 0;
                return $item;
            });

        return view('defects.index', compact(
            'defects', 'reasons', 'paperTypes', 'months', 'years',
            'totalDefects', 'defect2025', 'defect2026', 'defectRate',
            'defectByPaper', 'rollByPaper', 'defectTrend', 'topReasons'
        ));
    }

    public function export(Request $request)
    {
        $filters = $request->only(['year', 'search', 'reason', 'paper_type', 'month']);
        $filename = 'defect-items-' . date('Y-m-d') . '.xlsx';

        ini_set('memory_limit', '512M');
        return Excel::download(new DefectItemsExport($filters), $filename);
    }

    public function summaryReport(Request $request)
    {
        $year = $request->input('year', date('Y'));
        $month = $request->input('month', '');
        $filename = 'summary-report-' . $year . ($month ? '-' . $month : '') . '.xlsx';

        ini_set('memory_limit', '512M');
        return Excel::download(new SummaryReportExport($year, $month), $filename);
    }

    public function importForm()
    {
        return view('defects.import');
    }

    public function importTemplate()
    {
        $headers = [
            'lot_id',
            'reason',
            'category',
            'defect_date',
        ];

        return response()->streamDownload(function () use ($headers) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $headers);
            fputcsv($file, ['LOT-001', 'WRAP BREAK', 'WRAPPING', '2026-05-04']);
            fputcsv($file, ['LOT-002', 'TEAR', 'PROCESS', '2026-05-04']);
            fclose($file);
        }, 'template-defect-import.csv', ['Content-Type' => 'text/csv']);
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,xlsx,xls|max:5120',
            'year' => 'required|integer|min:2020|max:2030',
            'mode' => 'nullable|in:new,update',
        ]);

        $year = $request->input('year');
        $mode = $request->input('mode', 'auto');
        $file = $request->file('file');
        $path = $file->getRealPath();

        $rows = Excel::toCollection(new class implements WithHeadingRow, ToCollection {
            public function collection(\Illuminate\Support\Collection $rows) { return $rows; }
        }, $path)->first();

        if (!$rows || $rows->isEmpty()) {
            return back()->with('error', 'File kosong atau tidak dapat dibaca.');
        }

        // Detect format: check if it has detail columns from Excel export
        $firstRow = $rows->first();
        $isDetailFormat = $this->isDetailFormat($firstRow);

        // Auto-detect mode if not explicitly set
        if ($mode === 'auto') {
            $mode = $isDetailFormat ? 'update' : 'new';
        }

        if ($mode === 'update') {
            return $this->importUpdate($rows, $year);
        }

        return $this->importNew($rows, $year);
    }

    /**
     * Check if row has detail columns (PaperType, Gramature, RewID, etc.)
     */
    private function isDetailFormat($row): bool
    {
        $detailKeys = ['PaperType', 'Gramature', 'RewID', 'paper_type', 'gramature', 'rew_id'];
        $rowKeys = array_map('strtolower', array_keys($row->toArray()));
        foreach ($detailKeys as $key) {
            if (in_array(strtolower($key), $rowKeys)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Import mode: Create new defect items (simple CSV format)
     */
    private function importNew($rows, $year)
    {
        $imported = 0;
        $skipped = 0;

        DB::beginTransaction();
        try {
            foreach ($rows as $row) {
                $lotId = trim((string) ($row['lot_id'] ?? $row['Lot Id'] ?? $row['lot'] ?? ''));
                if ($lotId === '') {
                    $skipped++;
                    continue;
                }

                $rollItem = RollItem::where('lot_id', $lotId)->first();

                DefectItem::create([
                    'year'         => $year,
                    'lot_id'       => $lotId,
                    'rew_id'       => $row['rew_id'] ?? $rollItem->rew_id ?? null,
                    'paper_type'   => $row['paper_type'] ?? $rollItem->paper_type ?? null,
                    'gsm'          => $row['gsm'] ?? $rollItem->gsm ?? null,
                    'plybond'      => $row['plybond'] ?? $rollItem->plybond ?? null,
                    'width'        => $row['width'] ?? $rollItem->width ?? null,
                    'reason'       => trim((string) ($row['reason'] ?? '')) ?: null,
                    'category'     => trim((string) ($row['category'] ?? '')) ?: null,
                    'defect_date'  => $row['defect_date'] ?? date('Y-m-d'),
                    'month'        => $row['month'] ?? null,
                    'tr_type'      => $row['tr_type'] ?? null,
                    'keterangan'   => $row['keterangan'] ?? ($rollItem && $rollItem->comments && $rollItem->comments !== '-' ? $rollItem->comments : null),
                ]);
                $imported++;
            }
            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', 'Import gagal: ' . $e->getMessage());
        }

        return back()->with('success', "Berhasil import {$imported} defect item baru." . ($skipped > 0 ? " {$skipped} baris dilewati (lot_id kosong)." : ''));
    }

    /**
     * Import mode: Update existing defect items from detail Excel format
     */
    private function importUpdate($rows, $year)
    {
        $updated = 0;
        $created = 0;
        $skipped = 0;
        $notFound = [];

        DB::beginTransaction();
        try {
            foreach ($rows as $row) {
                $lotId = trim((string) ($row['LotID'] ?? $row['lot_id'] ?? $row['Lot Id'] ?? $row['lot'] ?? ''));
                if ($lotId === '') {
                    $skipped++;
                    continue;
                }

                // Build data from Excel columns (flexible naming)
                $data = $this->extractDetailRow($row, $year);

                // Check if defect already exists
                $existing = DefectItem::where('lot_id', $lotId)->where('year', $year)->first();

                if ($existing) {
                    // Update only empty/null fields
                    $changed = false;
                    foreach (['paper_type', 'gsm', 'plybond', 'width', 'rew_id', 'keterangan'] as $field) {
                        if ((empty($existing->$field) || $existing->$field === '-') && !empty($data[$field])) {
                            $existing->$field = $data[$field];
                            $changed = true;
                        }
                    }
                    // Also update reason/category/defect_date if provided
                    foreach (['reason', 'category', 'defect_date', 'month', 'tr_type'] as $field) {
                        if (!empty($data[$field]) && empty($existing->$field)) {
                            $existing->$field = $data[$field];
                            $changed = true;
                        }
                    }
                    if ($changed) {
                        $existing->save();
                        $updated++;
                    } else {
                        $skipped++;
                    }
                } else {
                    // Try to find in any year
                    $anyYear = DefectItem::where('lot_id', $lotId)->first();
                    if ($anyYear) {
                        $changed = false;
                        foreach (['paper_type', 'gsm', 'plybond', 'width', 'rew_id', 'keterangan'] as $field) {
                            if ((empty($anyYear->$field) || $anyYear->$field === '-') && !empty($data[$field])) {
                                $anyYear->$field = $data[$field];
                                $changed = true;
                            }
                        }
                        if ($changed) {
                            $anyYear->save();
                            $updated++;
                        }
                    } else {
                        // Create new defect item
                        DefectItem::create($data);
                        $created++;
                    }
                }
            }
            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', 'Update gagal: ' . $e->getMessage());
        }

        $msg = "Berhasil: {$updated} data diupdate, {$created} data baru ditambahkan.";
        if ($skipped > 0) {
            $msg .= " {$skipped} baris dilewati.";
        }
        if ($notFound) {
            $msg .= " " . count($notFound) . " lot_id tidak ditemukan: " . implode(', ', array_slice($notFound, 0, 5)) . (count($notFound) > 5 ? '...' : '');
        }

        return back()->with('success', $msg);
    }

    /**
     * Extract data from a detail-format Excel row with flexible column names
     */
    private function extractDetailRow($row, $year): array
    {
        $v = function ($keys) use ($row) {
            foreach ($keys as $key) {
                $val = $row[$key] ?? null;
                if ($val !== null && trim((string) $val) !== '' && trim((string) $val) !== 'NULL' && trim((string) $val) !== '-') {
                    return trim((string) $val);
                }
            }
            return null;
        };

        return [
            'year'         => $year,
            'lot_id'       => trim((string) ($row['LotID'] ?? $row['lot_id'] ?? $row['Lot Id'] ?? '')),
            'paper_type'   => $v(['PaperType', 'paper_type', 'paper type']),
            'gsm'          => $v(['Gramature', 'gramature', 'gsm', 'GSM']),
            'plybond'      => $v(['Plybond', 'plybond']),
            'width'        => $v(['Width', 'width']),
            'rew_id'       => $v(['RewID', 'rew_id', 'rew id']),
            'keterangan'   => $v(['Comment', 'comment', 'Comments', 'comments', 'keterangan']),
            'reason'       => $v(['reason', 'Reason']),
            'category'     => $v(['category', 'Category']),
            'defect_date'  => $v(['defect_date', 'DefectDate', 'DateTime_']) ?? date('Y-m-d'),
            'month'        => $v(['month', 'Month']),
            'tr_type'      => $v(['TrType', 'tr_type', 'tr type']),
        ];
    }
}
