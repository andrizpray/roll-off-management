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
                    // Fill missing spec from roll_items via lot_id
                    if (empty($item['paper_type']) || empty($item['gsm'])) {
                        $roll = RollItem::where('lot_id', $item['lot_id'])->first();
                        if ($roll) {
                            if (empty($item['paper_type']) && $roll->paper_type) {
                                $item['paper_type'] = $roll->paper_type;
                            }
                            if (empty($item['gsm']) && $roll->gsm) {
                                $item['gsm'] = $roll->gsm;
                            }
                            if (empty($item['plybond']) && $roll->plybond) {
                                $item['plybond'] = $roll->plybond;
                            }
                            if (empty($item['width']) && $roll->width) {
                                $item['width'] = $roll->width;
                            }
                            if ((empty($item['keterangan']) || $item['keterangan'] === '-') && $roll->comments && $roll->comments !== '-') {
                                $item['keterangan'] = $roll->comments;
                            }
                        }
                    }
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

        $desc = trim($description);

        // Pattern: {Paper Type} {GSM_PREFIX+GSM} E{PLYBOND} {WIDTH}
        // Examples:
        //   B KRAFT BK125 E150 690
        //   Core Board CO300 E200 600
        //   Paper Medium MP120 E150 800
        //   Grey Board GB300 E150 665
        //   T/B B Kraft BKTB300 E150 850
        //   Chip Board CB250 E150 800
        //   COB Core Board B COB500 E300 100
        //   DUPLEX COATED DR0250 E150 350
        //   LAMINASI B KRAFT BKL270 E150 210
        //   PE03 B Kraft PE T/B Glossy BPTBG325 E150 950
        //   PE02 B Kraft PE Glossy BRPG290 E150 700
        //   Barrier Coating Board BCB290 E150 600
        //   Brown Board BB300 E150 600
        //   NO SPEC B KRAFT BKN125 E150 100
        //   01 SNI B Kraft T/B BKTBS500 E150 1200
        //   DK DUPLEX KRAFT DK310 E150 700
        //   OCT BASE PAPER COATING OCT270 E150 665
        //   PE Duplex Roll DRP0265 E150 790

        // Multi-word paper type patterns (order matters — longest match first)
        $paperPatterns = [
            'Barrier Coating Board',
            'COB Core Board',
            'OCT BASE PAPER COATING',
            'OCTN BASE PAPER COATING',
            'COATED DUPLEX OCT',
            'DUPLEX COATED',
            'DK DUPLEX KRAFT',
            'Paper Medium',
            'Core Board',
            // 'Core Borad' removed — typo fixed via post-processing
            'MPC Medium Paper',
            'Grey Board',
            'Brown Board',
            'Chip Board',
            'Yellow Board',
            'T/B B Kraft',
            'PE03 B Kraft PE T/B Glossy',
            'PE07 Natural Roll PE Glossy',
            'PE02 B Kraft PE Glossy',
            'BPTB B Kraft PE T/B',
            'BK03 B Kraft',
            'BK02 B Kraft',
            'LAMINASI B KRAFT',
            'LAMINASI B Kraft',
            'Base Paper Coating Grey',
            'Base Paper Coating',
            'PE B Kraft',
            'PE Duplex Roll',
            'OCT2 BASE',
            'OCTN BASE',
            '01 SNI B Kraft T/B',
            '01 B Kraft Warna',
            'B KRAFT',
            'B Kraft',
            'Non Spec B',
            'Non Spec',
            'KBD KRAFT',
            'KBD Kraft',
            'White Kraft',
            'SNI B',
            'NO SPEC B',
            'No Spec B',
        ];

        $paperType = null;
        $remaining = $desc;

        foreach ($paperPatterns as $pattern) {
            if (stripos($desc, $pattern) === 0) {
                $paperType = $pattern;
                $remaining = trim(substr($desc, strlen($pattern)));
                break;
            }
        }

        if (!$paperType) {
            // Fallback: try to get GSM from middle if no paper type match
            if (preg_match('/([A-Z]{2,5}?\d{2,3})\s+E\d{2,3}\s+(\d+)\s*$/', $desc, $m)) {
                $result['gsm'] = strtoupper($m[1]);
                $result['plybond'] = 'E' . substr(strtoupper($m[0]), strpos(strtoupper($m[0]), 'E') + 1);
                $result['width'] = $m[2];
            }
            return $result;
        }

        $result['paper_type'] = strtoupper($paperType);

        // Fix known typos from Excel data
        $result['paper_type'] = str_replace('CORE BORAD', 'CORE BOARD', $result['paper_type']);

        // Parse remaining: {GSM_PREFIX}{GSM} E{PLYBOND} {WIDTH}
        // GSM prefix patterns: BK, CO, MP, GB, CB, BB, BKTB, COB, DR, DRP, BKL, BPTBG, BRPG, BCB, BKN, BKP, BKE, BKTBS, BKW, NRPG, MPC, OCT, OCTN, DK, BPC, YB, DR, etc.
        // General pattern: letters + digits (e.g., BK125, CO300, DR0250, BKL270, BPTBG325, BKTBS500, NRPG265)
        if (preg_match('/([A-Za-z]+\d{2,4})\s+(E\d{2,3})\s+(\d+)\s*$/', $remaining, $m)) {
            $result['gsm'] = strtoupper($m[1]);
            $result['plybond'] = strtoupper($m[2]);
            $result['width'] = $m[3];
        } elseif (preg_match('/(\d+)\s+(E\d{2,3})\s+(\d+)\s*$/', $remaining, $m)) {
            // Fallback: GSM as plain number (e.g., "300 E150 600")
            $result['gsm'] = $m[1];
            $result['plybond'] = strtoupper($m[2]);
            $result['width'] = $m[3];
        }

        return $result;
    }
}
