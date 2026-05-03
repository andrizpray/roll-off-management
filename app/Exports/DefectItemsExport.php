<?php

namespace App\Exports;

use App\Models\DefectItem;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class DefectItemsExport implements FromQuery, WithHeadings, WithMapping, WithStyles, WithTitle, WithChunkReading
{
    protected array $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    public function query()
    {
        $query = DefectItem::query();

        if ($year = $this->filters['year'] ?? null) {
            $query->where('year', $year);
        }

        if ($search = $this->filters['search'] ?? null) {
            $query->where(function ($q) use ($search) {
                $q->where('lot_id', 'LIKE', "%{$search}%")
                  ->orWhere('rew_id', 'LIKE', "%{$search}%");
            });
        }

        if ($reason = $this->filters['reason'] ?? null) {
            $query->where('reason', $reason);
        }

        if ($paperType = $this->filters['paper_type'] ?? null) {
            $query->where('paper_type', $paperType);
        }

        if ($month = $this->filters['month'] ?? null) {
            $query->where('month', $month);
        }

        return $query->orderBy('defect_date', 'desc');
    }

    public function headings(): array
    {
        return [
            'Tahun', 'Lot ID', 'Rew ID', 'Paper Type', 'GSM', 'Plybond', 'Width',
            'Alasan', 'Kategori', 'Tanggal Defect', 'Bulan', 'TR Type', 'Keterangan',
        ];
    }

    public function map($item): array
    {
        return [
            $item->year,
            $item->lot_id,
            $item->rew_id,
            $item->paper_type,
            $item->gsm,
            $item->plybond,
            $item->width,
            $item->reason,
            $item->category,
            $item->defect_date,
            $item->month,
            $item->tr_type,
            $item->keterangan,
        ];
    }

    public function styles(Worksheet $sheet): void
    {
        $sheet->getStyle('A1:M1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 11, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '991B1B']],
        ]);

        $sheet->getStyle('A2:M' . $sheet->getHighestRow())->applyFromArray([
            'font' => ['size' => 10],
            'borders' => [
                'allBorders' => ['borderStyle' => 'thin', 'color' => ['rgb' => 'E2E8F0']],
            ],
        ]);

        foreach (range('A', 'M') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $sheet->freezePane('A2');
    }

    public function title(): string
    {
        return 'Barang Bermasalah';
    }

    public function chunkSize(): int
    {
        return 500;
    }
}
