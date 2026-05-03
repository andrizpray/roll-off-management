<?php

namespace App\Services;

use App\Models\DefectItem;
use App\Models\RollItem;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ImportSyncService
{
    /**
     * Parse DATA sheet from Excel and return preview (no DB write).
     * Returns: ['total_rows', 'valid_rows', 'skipped', 'new', 'updated', 'unchanged', 'items']
     */
    public function previewDataSheet(string $filePath): array
    {
        $reader = IOFactory::createReaderForFile($filePath);
        $reader->setReadDataOnly(true);
        $wb = $reader->load($filePath);

        $sheet = $wb->getSheetByName('DATA');
        if (!$sheet) {
            throw new \Exception('Sheet "DATA" tidak ditemukan di file Excel.');
        }

        $totalRows = $sheet->getHighestRow();
        $existingLotIds = RollItem::pluck('lot_id', 'lot_id')->toArray();

        $items = [];
        $validRows = 0;
        $skipped = 0;
        $newCount = 0;
        $updatedCount = 0;
        $unchangedCount = 0;

        for ($row = 2; $row <= $totalRows; $row++) {
            $lotId = $this->getCellValue($sheet, 1, $row);
            if (empty($lotId)) {
                $skipped++;
                continue;
            }

            $description = $this->getCellValue($sheet, 7, $row);
            $parsed = $this->parseDescription($description);
            $trDateSerial = $this->getCellValue($sheet, 5, $row);
            $trTime = $this->getCellValue($sheet, 6, $row);

            $item = [
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
            ];

            // Determine status
            if (isset($existingLotIds[$lotId])) {
                $item['status'] = 'exists';
                $existing = RollItem::where('lot_id', $lotId)->first();
                $changed = false;
                $changes = [];
                foreach ($item as $key => $val) {
                    if (in_array($key, ['lot_id', 'status'])) continue;
                    $oldVal = $existing->$key ?? null;
                    if ((string) $oldVal !== (string) $val) {
                        $changed = true;
                        $changes[$key] = ['old' => $oldVal, 'new' => $val];
                    }
                }
                if ($changed) {
                    $item['status'] = 'updated';
                    $item['changes'] = $changes;
                    $updatedCount++;
                } else {
                    $unchangedCount++;
                }
            } else {
                $item['status'] = 'new';
                $newCount++;
            }

            $items[] = $item;
            $validRows++;
        }

        // Clear workbook from memory
        $wb->disconnectWorksheets();
        unset($wb);

        return [
            'total_rows' => $totalRows - 1, // minus header
            'valid_rows' => $validRows,
            'skipped' => $skipped,
            'new' => $newCount,
            'updated' => $updatedCount,
            'unchanged' => $unchangedCount,
            'items' => $items,
        ];
    }

    /**
     * Execute sync: upsert DATA sheet into roll_items.
     * Returns: ['created', 'updated', 'skipped']
     */
    public function syncDataSheet(string $filePath): array
    {
        $reader = IOFactory::createReaderForFile($filePath);
        $reader->setReadDataOnly(true);
        $wb = $reader->load($filePath);

        $sheet = $wb->getSheetByName('DATA');
        if (!$sheet) {
            throw new \Exception('Sheet "DATA" tidak ditemukan di file Excel.');
        }

        $totalRows = $sheet->getHighestRow();
        $chunkSize = 500;
        $created = 0;
        $updated = 0;
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
            }

            if (!empty($batch)) {
                // Track new vs updated
                $lotIds = array_column($batch, 'lot_id');
                $existing = RollItem::whereIn('lot_id', $lotIds)->pluck('lot_id')->toArray();
                $created += count(array_diff($lotIds, $existing));
                $updated += count(array_intersect($lotIds, $existing));

                RollItem::upsert($batch, ['lot_id'], array_keys($batch[0]));
            }
        }

        $wb->disconnectWorksheets();
        unset($wb);

        return [
            'created' => $created,
            'updated' => $updated,
            'skipped' => $skipped,
        ];
    }

    /**
     * Sync defect sheets (2025 + 2026) — same logic as CLI command.
     * Returns: ['defect_2025', 'defect_2026']
     */
    public function syncDefectSheets(string $filePath): array
    {
        $reader = IOFactory::createReaderForFile($filePath);
        $reader->setReadDataOnly(true);
        $wb = $reader->load($filePath);

        $result = ['defect_2025' => 0, 'defect_2026' => 0];

        $sheet2025 = $wb->getSheetByName('Barang Bermasalah 2025');
        if ($sheet2025) {
            $result['defect_2025'] = $this->importDefect2025($sheet2025);
        }

        $sheet2026 = $wb->getSheetByName('Barang Bermasalah 2026');
        if ($sheet2026) {
            $result['defect_2026'] = $this->importDefect2026($sheet2026);
        }

        $wb->disconnectWorksheets();
        unset($wb);

        return $result;
    }

    // ─── Private helpers (same as CLI command) ───

    private function importDefect2025($sheet): int
    {
        $totalRows = $sheet->getHighestRow();
        $count = 0;

        for ($row = 2; $row <= $totalRows; $row++) {
            $lotId = $this->getCellValue($sheet, 1, $row);
            if (empty($lotId)) continue;

            $dateSerial = $this->getCellValue($sheet, 6, $row);

            $item = [
                'year' => 2025,
                'lot_id' => $lotId,
                'paper_type' => $this->getCellValue($sheet, 2, $row) ?: null,
                'gsm' => $this->getCellValue($sheet, 3, $row) ?: null,
                'plybond' => null,
                'width' => $this->getCellValue($sheet, 4, $row) ?: null,
                'reason' => $this->getCellValue($sheet, 5, $row) ?: null,
                'defect_date' => $this->excelSerialToDate($dateSerial),
                'category' => $this->getCellValue($sheet, 8, $row) ?: null,
                'month' => $this->getCellValue($sheet, 7, $row) ?: null,
                'tr_type' => $this->getCellValue($sheet, 9, $row) ?: null,
                'keterangan' => $this->getCellValue($sheet, 10, $row) ?: null,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            $roll = RollItem::where('lot_id', $item['lot_id'])->first();
            if ($roll) {
                if (empty($item['rew_id']) && $roll->rew_id) $item['rew_id'] = $roll->rew_id;
                if (empty($item['paper_type']) && $roll->paper_type) $item['paper_type'] = $roll->paper_type;
                if (empty($item['gsm']) && $roll->gsm) $item['gsm'] = $roll->gsm;
                if (empty($item['plybond']) && $roll->plybond) $item['plybond'] = $roll->plybond;
                if (empty($item['width']) && $roll->width) $item['width'] = $roll->width;
                if ((empty($item['keterangan']) || $item['keterangan'] === '-') && $roll->comments && $roll->comments !== '-') {
                    $item['keterangan'] = $roll->comments;
                }
            }

            DefectItem::create($item);
            $count++;
        }

        return $count;
    }

    private function importDefect2026($sheet): int
    {
        $totalRows = $sheet->getHighestRow();
        $count = 0;

        for ($row = 2; $row <= $totalRows; $row++) {
            $lotId = $this->getCellValue($sheet, 2, $row);
            if (empty($lotId)) continue;

            $dateSerial = $this->getCellValue($sheet, 9, $row);

            $item = [
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

            if (empty($item['rew_id']) || empty($item['paper_type']) || empty($item['gsm'])) {
                $roll = RollItem::where('lot_id', $item['lot_id'])->first();
                if ($roll) {
                    if (empty($item['rew_id']) && $roll->rew_id) $item['rew_id'] = $roll->rew_id;
                    if (empty($item['paper_type']) && $roll->paper_type) $item['paper_type'] = $roll->paper_type;
                    if (empty($item['gsm']) && $roll->gsm) $item['gsm'] = $roll->gsm;
                    if (empty($item['plybond']) && $roll->plybond) $item['plybond'] = $roll->plybond;
                    if (empty($item['width']) && $roll->width) $item['width'] = $roll->width;
                    if ((empty($item['keterangan']) || $item['keterangan'] === '-') && $roll->comments && $roll->comments !== '-') {
                        $item['keterangan'] = $roll->comments;
                    }
                }
            }

            DefectItem::create($item);
            $count++;
        }

        return $count;
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
        $days = (int) round((float) $serial);
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
        $hours = (int) round($totalSeconds / 3600);
        $minutes = (int) round(fmod($totalSeconds, 3600) / 60);
        $seconds = (int) round(fmod($totalSeconds, 60));

        // Handle overflow from floating point rounding
        if ($seconds >= 60) { $seconds = 0; $minutes++; }
        if ($minutes >= 60) { $minutes = 0; $hours++; }
        if ($hours >= 24) { $hours = 0; }

        return sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
    }

    private function parseDescription(?string $description): array
    {
        $result = ['paper_type' => null, 'gsm' => null, 'plybond' => null, 'width' => null];
        if (empty($description)) return $result;

        $desc = trim($description);

        $paperPatterns = [
            'Barrier Coating Board', 'COB Core Board', 'OCT BASE PAPER COATING',
            'OCTN BASE PAPER COATING', 'COATED DUPLEX OCT', 'DUPLEX COATED',
            'DK DUPLEX KRAFT', 'Paper Medium', 'Core Board',
            'MPC Medium Paper', 'Grey Board', 'Brown Board', 'Chip Board', 'Yellow Board',
            'T/B B Kraft', 'PE03 B Kraft PE T/B Glossy', 'PE07 Natural Roll PE Glossy',
            'PE02 B Kraft PE Glossy', 'BPTB B Kraft PE T/B', 'BK03 B Kraft', 'BK02 B Kraft',
            'LAMINASI B KRAFT', 'LAMINASI B Kraft', 'Base Paper Coating Grey',
            'Base Paper Coating', 'PE B Kraft', 'PE Duplex Roll', 'OCT2 BASE', 'OCTN BASE',
            '01 SNI B Kraft T/B', '01 B Kraft Warna', 'B KRAFT', 'B Kraft', 'Non Spec B',
            'Non Spec', 'KBD KRAFT', 'KBD Kraft', 'White Kraft', 'SNI B', 'NO SPEC B', 'No Spec B',
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
            if (preg_match('/([A-Z]{2,5}?\d{2,3})\s+E\d{2,3}\s+(\d+)\s*$/', $desc, $m)) {
                $result['gsm'] = strtoupper($m[1]);
                $result['plybond'] = 'E' . substr(strtoupper($m[0]), strpos(strtoupper($m[0]), 'E') + 1);
                $result['width'] = $m[2];
            }
            return $result;
        }

        $result['paper_type'] = strtoupper($paperType);
        $result['paper_type'] = str_replace('CORE BORAD', 'CORE BOARD', $result['paper_type']);

        if (preg_match('/([A-Za-z]+\d{2,4})\s+(E\d{2,3})\s+(\d+)\s*$/', $remaining, $m)) {
            $result['gsm'] = strtoupper($m[1]);
            $result['plybond'] = strtoupper($m[2]);
            $result['width'] = $m[3];
        } elseif (preg_match('/(\d+)\s+(E\d{2,3})\s+(\d+)\s*$/', $remaining, $m)) {
            $result['gsm'] = $m[1];
            $result['plybond'] = strtoupper($m[2]);
            $result['width'] = $m[3];
        }

        return $result;
    }
}
