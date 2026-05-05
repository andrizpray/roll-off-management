<?php

namespace App\Exports;

use App\Models\DeliveryOrder;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ManifestExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle
{
    protected DeliveryOrder $do;

    public function __construct(DeliveryOrder $do)
    {
        $this->do = $do;
    }

    public function collection()
    {
        return $this->do->items->map(function ($item, $index) {
            return [
                $index + 1,
                $this->do->do_number,
                $this->do->recipient_name,
                $this->do->destination ?? '-',
                $this->do->status,
                $item->lot_id,
                $item->paper_type ?? '-',
                $item->gsm ?? '-',
                $item->width ?? '-',
                $item->qty_order,
                $item->qty_actual ?? '-',
                $item->weight_kg ?? '-',
                $item->notes ?? '-',
                $this->do->created_at->format('Y-m-d'),
            ];
        });
    }

    public function headings(): array
    {
        return [
            'No', 'DO Number', 'Penerima', 'Tujuan', 'Status',
            'Lot ID', 'Paper Type', 'GSM', 'Width',
            'Qty Order', 'Qty Actual', 'Weight (kg)', 'Notes', 'Tanggal DO',
        ];
    }

    public function map($row): array
    {
        return $row;
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font' => ['bold' => true, 'size' => 14],
            ],
            2 => [
                'font'  => ['bold' => true, 'size' => 11, 'color' => ['argb' => 'FFFFFFFF']],
                'fill'  => ['fillType' => 'solid', 'startColor' => ['argb' => 'FF1B4F8A']],
            ],
            'A2:N2' => [
                'alignment' => ['horizontal' => 'center'],
            ],
            'A3:N' . $sheet->getHighestRow() => [
                'font'   => ['size' => 10],
                'borders' => [
                    'allBorders' => ['borderStyle' => 'thin', 'color' => ['argb' => 'FFE2E8F0']],
                ],
            ],
        ];
    }

    public function title(): string
    {
        return 'Manifest ' . $this->do->do_number;
    }
}