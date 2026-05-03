<?php

namespace App\Exports;

use App\Models\RollItem;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class RollItemsExport implements FromQuery, WithHeadings, WithMapping, WithStyles, WithTitle, WithChunkReading
{
    protected array $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    public function query()
    {
        $query = RollItem::query();

        if ($search = $this->filters['search'] ?? null) {
            $query->where(function ($q) use ($search) {
                $q->where('lot_id', 'LIKE', "%{$search}%")
                  ->orWhere('item_id', 'LIKE', "%{$search}%")
                  ->orWhere('description', 'LIKE', "%{$search}%")
                  ->orWhere('rew_id', 'LIKE', "%{$search}%")
                  ->orWhere('so_desember', 'LIKE', "%{$search}%")
                  ->orWhere('so_maret_2026', 'LIKE', "%{$search}%")
                  ->orWhere('pic_2026', 'LIKE', "%{$search}%")
                  ->orWhere('receiving_2026', 'LIKE', "%{$search}%");
            });
        }

        if ($paperType = $this->filters['paper_type'] ?? null) {
            $query->where('paper_type', $paperType);
        }

        if ($gsm = $this->filters['gsm'] ?? null) {
            $query->where('gsm', $gsm);
        }

        if ($width = $this->filters['width'] ?? null) {
            $query->where('width', $width);
        }

        if ($location = $this->filters['receiving_2026'] ?? null) {
            $query->where('receiving_2026', $location);
        }

        if ($status = $this->filters['status'] ?? null) {
            $query->where('status_barang', $status);
        }

        if ($grade = $this->filters['grade'] ?? null) {
            $query->where('grade', $grade);
        }

        $sortField = $this->filters['sort'] ?? 'lot_id';
        $sortDir = $this->filters['dir'] ?? 'asc';
        $allowedSort = ['lot_id', 'paper_type', 'gsm', 'width', 'receiving_2026', 'end_qty', 'so_desember', 'so_maret_2026', 'created_at', 'tr_date'];
        if (in_array($sortField, $allowedSort)) {
            $query->orderBy($sortField, $sortDir);
        } else {
            $query->orderBy('lot_id', 'asc');
        }

        return $query;
    }

    public function headings(): array
    {
        return [
            'Lot ID', 'Item ID', 'End Qty', 'Rew ID', 'TR Date', 'TR Time',
            'Description', 'Paper Type', 'GSM', 'Plybond', 'Width',
            'Diameter', 'Thickness', 'Grade', 'Comments',
            'SO Desember', 'PIC 2025', 'Lokasi Receiving',
            'SO September', 'Receiving 2026', 'PIC 2026', 'RCV/CNV 2026',
            'SO Maret 2026', 'Status Barang', 'Lokasi Rekap',
        ];
    }

    public function map($item): array
    {
        return [
            $item->lot_id,
            $item->item_id,
            $item->end_qty,
            $item->rew_id,
            $item->tr_date,
            $item->tr_time,
            $item->description,
            $item->paper_type,
            $item->gsm,
            $item->plybond,
            $item->width,
            $item->diameter,
            $item->thickness,
            $item->grade,
            $item->comments,
            $item->so_desember,
            $item->pic_2025,
            $item->lokasi_receiving,
            $item->so_september,
            $item->receiving_2026,
            $item->pic_2026,
            $item->rcv_cnv_2026,
            $item->so_maret_2026,
            $item->status_barang,
            $item->current_location_label,
        ];
    }

    public function styles(Worksheet $sheet): void
    {
        $sheet->getStyle('A1:Y1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 11, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '1e3a5f']],
        ]);

        $sheet->getStyle('A2:Y' . $sheet->getHighestRow())->applyFromArray([
            'font' => ['size' => 10],
            'borders' => [
                'allBorders' => ['borderStyle' => 'thin', 'color' => ['rgb' => 'E2E8F0']],
            ],
        ]);

        foreach (range('A', 'Y') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $sheet->freezePane('A2');
    }

    public function title(): string
    {
        return 'Roll Items';
    }

    public function chunkSize(): int
    {
        return 500;
    }
}
