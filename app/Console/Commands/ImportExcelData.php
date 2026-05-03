<?php

namespace App\Console\Commands;

use App\Models\DefectItem;
use App\Models\RollItem;
use Carbon\Carbon;
use Illuminate\Console\Command;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ImportExcelData extends Command
{
    protected $signature = 'import:excel {file : Path to Excel file}';
    protected $description = 'Import roll off data from Excel template';

    public function handle(): int
    {
        $file = $this->argument('file');

        if (!file_exists($file)) {
            $this->error("File not found: {$file}");
            return self::FAILURE;
        }

        $reader = IOFactory::createReaderForFile($file);
        $reader->setReadDataOnly(true);
        $wb = $reader->load($file);

        // === Import Sheet: DATA ===
        $this->importDataSheet($wb);

        // === Import Sheet: Barang Bermasalah 2025 ===
        $sheet2025 = $wb->getSheetByName('Barang Bermasalah 2025');
        if ($sheet2025) {
            $this->importDefect2025($sheet2025);
        }

        // === Import Sheet: Barang Bermasalah 2026 ===
        $sheet2026 = $wb->getSheetByName('Barang Bermasalah 2026');
        if ($sheet2026) {
            $this->importDefect2026($sheet2026);
        }

        $this->newLine();
        $this->info('✅ Import complete!');
        $this->info("  Roll Items: " . RollItem::count());
        $this->info("  Defect Items: " . DefectItem::count());

        return self::SUCCESS;
    }

    private function importDataSheet($wb): void
    {
        $sheet = $wb->getSheetByName('DATA');
        $totalRows = $sheet->getHighestRow();

        $this->info("Importing DATA sheet ({$totalRows} rows)...");

        $chunkSize = 500;
        $count = 0;
        $skipped = 0;

        for ($startRow = 2; $startRow <= $totalRows; $startRow += $chunkSize) {
            $endRow = min($startRow + $chunkSize - 1, $totalRows);
            $batch = [];

            for ($row = $startRow; $row <= $endRow; $row++) {
                $lotId = $this->getCellValue($sheet, 1, $row);
                if (empty($lotId)) {
                    $skipped++;
                    continue;
                }

                $description = $this->getCellValue($sheet, 7, $row);
                $parsed = $this->parseDescription($description);

                $trDateSerial = $this->getCellValue($sheet, 5, $row);
                $trTime = $this->getCellValue($sheet, 6, $row);

                $batch[] = [
                    'lot_id' => $lotId,
                    'item_id' => $this->getCellValue($sheet, 2, $row) ?: null,
                    'end_qty' => (int) ($this->getCellValue($sheet, 3, $row) ?: 0),
                    'rew_id' => $this->getCellValue($sheet, 4, $row) ?: null,
                    'tr_date' => $this->excelSerialToDate($trDateSerial),
                    'tr_time' => $this->excelFractionToTime($trTime),
                    'description' => $description,
                    'paper_type' => $parsed['paper_type'],
                    'gsm' => $parsed['gsm'],
                    'plybond' => $parsed['plybond'],
                    'width' => $parsed['width'],
                    'diameter' => $this->getCellValue($sheet, 8, $row) ?: null,
                    'thickness' => $this->getCellValue($sheet, 9, $row) ?: null,
                    'grade' => $this->getCellValue($sheet, 10, $row) ?: null,
                    'comments' => $this->getCellValue($sheet, 11, $row) ?: null,
                    'location_id' => $this->getCellValue($sheet, 12, $row) ?: null,
                    'so_september' => $this->getCellValue($sheet, 13, $row) ?: null,
                    'pic_2025' => $this->getCellValue($sheet, 14, $row) ?: null,
                    'lokasi_receiving' => $this->getCellValue($sheet, 15, $row) ?: null,
                    'so_desember' => $this->getCellValue($sheet, 16, $row) ?: null,
                    'receiving_2026' => $this->getCellValue($sheet, 17, $row) ?: null,
                    'pic_2026' => $this->getCellValue($sheet, 18, $row) ?: null,
                    'rcv_cnv_2026' => $this->getCellValue($sheet, 19, $row) ?: null,
                    'so_maret_2026' => $this->getCellValue($sheet, 20, $row) ?: null,
                    'status_barang' => $this->getCellValue($sheet, 21, $row) ?: null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
                $count++;
            }

            if (!empty($batch)) {
                RollItem::upsert($batch, ['lot_id'], array_keys($batch[0]));
            }

            $progress = min($endRow, $totalRows);
            $this->output->write("\r  Progress: {$progress}/{$totalRows} rows ({$count} imported, {$skipped} skipped)");
        }

        $this->newLine();
        $this->info("  ✅ DATA: {$count} imported, {$skipped} skipped");
    }

    private function importDefect2025($sheet): void
    {
        $totalRows = $sheet->getHighestRow();
        $this->info("Importing Barang Bermasalah 2025 ({$totalRows} rows)...");

        $count = 0;
        $chunkSize = 500;

        for ($startRow = 2; $startRow <= $totalRows; $startRow += $chunkSize) {
            $endRow = min($startRow + $chunkSize - 1, $totalRows);
            $batch = [];

            for ($row = $startRow; $row <= $endRow; $row++) {
                $lotId = $this->getCellValue($sheet, 1, $row);
                if (empty($lotId)) continue;

                $dateSerial = $this->getCellValue($sheet, 6, $row);
                $month = $this->getCellValue($sheet, 7, $row);

                $batch[] = [
                    'year' => 2025,
                    'lot_id' => $lotId,
                    'paper_type' => $this->getCellValue($sheet, 2, $row) ?: null,
                    'gsm' => $this->getCellValue($sheet, 3, $row) ?: null,
                    'plybond' => null,
                    'width' => $this->getCellValue($sheet, 4, $row) ?: null,
                    'reason' => $this->getCellValue($sheet, 5, $row) ?: null,
                    'defect_date' => $this->excelSerialToDate($dateSerial),
                    'category' => $this->getCellValue($sheet, 8, $row) ?: null,
                    'month' => $month ?: null,
                    'tr_type' => $this->getCellValue($sheet, 9, $row) ?: null,
                    'keterangan' => $this->getCellValue($sheet, 10, $row) ?: null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
                $count++;
            }

            if (!empty($batch)) {
                foreach ($batch as $item) {
                    DefectItem::create($item);
                }
            }

            $progress = min($endRow, $totalRows);
            $this->output->write("\r  Progress: {$progress}/{$totalRows} rows");
        }

        $this->newLine();
        $this->info("  ✅ Defect 2025: {$count} imported");
    }

    private function importDefect2026($sheet): void
    {
        $totalRows = $sheet->getHighestRow();
        $this->info("Importing Barang Bermasalah 2026 ({$totalRows} rows)...");

        $count = 0;
        $chunkSize = 500;

        for ($startRow = 2; $startRow <= $totalRows; $startRow += $chunkSize) {
            $endRow = min($startRow + $chunkSize - 1, $totalRows);
            $batch = [];

            for ($row = $startRow; $row <= $endRow; $row++) {
                $lotId = $this->getCellValue($sheet, 2, $row);
                if (empty($lotId)) continue;

                $dateSerial = $this->getCellValue($sheet, 9, $row);

                $batch[] = [
                    'year' => 2026,
                    'lot_id' => $lotId,
                    'rew_id' => $this->getCellValue($sheet, 3, $row) ?: null,
                    'paper_type' => $this->getCellValue($sheet, 4, $row) ?: null,
                    'gsm' => $this->getCellValue($sheet, 5, $row) ?: null,
                    'plybond' => $this->getCellValue($sheet, 6, $row) ?: null,
                    'width' => $this->getCellValue($sheet, 7, $row) ?: null,
                    'reason' => $this->getCellValue($sheet, 8, $row) ?: null,
                    'defect_date' => $this->excelSerialToDate($dateSerial),
                    'category' => $this->getCellValue($sheet, 10, $row) ?: null,
                    'month' => null,
                    'tr_type' => null,
                    'keterangan' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
                $count++;
            }

            if (!empty($batch)) {
                foreach ($batch as $item) {
                    DefectItem::create($item);
                }
            }

            $progress = min($endRow, $totalRows);
            $this->output->write("\r  Progress: {$progress}/{$totalRows} rows");
        }

        $this->newLine();
        $this->info("  ✅ Defect 2026: {$count} imported");
    }

    private function getCellValue($sheet, int $col, int $row): ?string
    {
        $val = $sheet->getCellByColumnAndRow($col, $row)->getValue();
        if ($val === null || $val === '') return null;
        return (string) $val;
    }

    private function excelSerialToDate(?string $serial): ?string
    {
        if (!$serial || !is_numeric($serial)) return null;
        // Excel serial date: 1 = Jan 1, 1900
        $days = (int) $serial;
        if ($days < 1) return null;
        try {
            $date = Carbon::createFromFormat('Y-m-d', '1899-12-30')->addDays($days);
            return $date->format('Y-m-d');
        } catch (\Throwable $e) {
            return null;
        }
    }

    private function excelFractionToTime(?string $fraction): ?string
    {
        if (!$fraction || !is_numeric($fraction)) return null;
        $totalSeconds = (float) $fraction * 86400;
        $hours = (int) floor($totalSeconds / 3600);
        $minutes = (int) floor(($totalSeconds % 3600) / 60);
        $seconds = (int) ($totalSeconds % 60);
        return sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
    }

    private function parseDescription(?string $description): array
    {
        $result = [
            'paper_type' => null,
            'gsm' => null,
            'plybond' => null,
            'width' => null,
        ];

        if (empty($description)) return $result;

        // Pattern examples:
        // "B KRAFT BK125 E150 690" → paper_type=B KRAFT, gsm=BK125, plybond=E150, width=690
        // "B Kraft BK120 E150 210" → paper_type=B Kraft, gsm=BK120, plybond=E150, width=210
        // "B Kraft BK275 E150 2250"

        if (preg_match('/^(.+?)\s+(BK\d{2,3})\s+(E\d{2,3})\s+(\d+)\s*$/', trim($description), $m)) {
            $result['paper_type'] = strtoupper(trim($m[1]));
            $result['gsm'] = strtoupper($m[2]);
            $result['plybond'] = strtoupper($m[3]);
            $result['width'] = $m[4];
        }

        return $result;
    }
}
