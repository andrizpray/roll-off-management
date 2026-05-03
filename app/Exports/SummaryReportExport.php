<?php

namespace App\Exports;

use App\Models\DefectItem;
use App\Models\RollItem;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SummaryReportExport implements WithMultipleSheets
{
    protected string $year;
    protected string $month;

    public function __construct(string $year, string $month = '')
    {
        $this->year = $year;
        $this->month = $month;
    }

    public function sheets(): array
    {
        return [
            'Ringkasan' => new SummaryOverviewSheet($this->year, $this->month),
            'Defects' => new SummaryDefectsSheet($this->year, $this->month),
            'Paper Type' => new SummaryPaperTypeSheet($this->year, $this->month),
        ];
    }
}

class SummaryOverviewSheet implements FromCollection, WithHeadings, WithStyles, WithTitle, ShouldAutoSize
{
    protected string $year;
    protected string $month;

    public function __construct(string $year, string $month)
    {
        $this->year = $year;
        $this->month = $month;
    }

    public function collection()
    {
        $rows = collect();

        // Total rolls
        $rollQuery = RollItem::query();
        $defectQuery = DefectItem::where('year', $this->year);

        if ($this->month) {
            $defectQuery->where('month', $this->month);
        }

        $totalRolls = RollItem::count();
        $totalDefects = $defectQuery->count();
        $defectRate = $totalRolls > 0 ? round(($totalDefects / $totalRolls) * 100, 2) : 0;

        $rows->push(['Total Roll Items', number_format($totalRolls)]);
        $rows->push(['Total Defects (' . $this->year . ($this->month ? ' - ' . $this->month : '') . ')', number_format($totalDefects)]);
        $rows->push(['Defect Rate', $defectRate . '%']);
        $rows->push(['']);

        // Defects by month
        $monthOrder = ['JANUARY' => 1, 'FEBRUARY' => 2, 'MARCH' => 3, 'APRIL' => 4, 'MAY' => 5, 'JUNE' => 6, 'JULY' => 7, 'AUGUST' => 8, 'SEPTEMBER' => 9, 'OCTOBER' => 10, 'NOVEMBER' => 11, 'DECEMBER' => 12];
        $byMonth = DefectItem::selectRaw("month, COUNT(*) as cnt")
            ->where('year', $this->year)
            ->groupBy('month')
            ->get()
            ->sortBy(fn($d) => $monthOrder[strtoupper($d->month)] ?? 99);

        $rows->push(['--- Defect per Bulan ---', '']);
        foreach ($byMonth as $m) {
            $rows->push([$m->month, $m->cnt]);
        }

        $rows->push(['']);
        $rows->push(['--- Top 10 Alasan ---', 'Jumlah']);

        $topReasons = DefectItem::selectRaw("reason, COUNT(*) as cnt")
            ->where('year', $this->year)
            ->when($this->month, fn($q) => $q->where('month', $this->month))
            ->groupBy('reason')
            ->orderByDesc('cnt')
            ->limit(10)
            ->get();

        foreach ($topReasons as $r) {
            $pct = $totalDefects > 0 ? round(($r->cnt / $totalDefects) * 100, 1) : 0;
            $rows->push([$r->reason, $r->cnt . ' (' . $pct . '%)']);
        }

        return $rows;
    }

    public function headings(): array
    {
        return ['Keterangan', 'Nilai'];
    }

    public function styles(Worksheet $sheet): void
    {
        $sheet->getStyle('A1:B1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 12, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '1e3a5f']],
        ]);

        // Bold the separator rows
        $highest = $sheet->getHighestRow();
        for ($i = 1; $i <= $highest; $i++) {
            $val = $sheet->getCell("A{$i}")->getValue();
            if ($val && str_starts_with($val, '---')) {
                $sheet->getStyle("A{$i}:B{$i}")->applyFromArray([
                    'font' => ['bold' => true, 'size' => 11, 'color' => ['rgb' => '1e3a5f']],
                ]);
            }
        }
    }

    public function title(): string
    {
        return 'Ringkasan';
    }
}

class SummaryDefectsSheet implements FromCollection, WithHeadings, WithStyles, WithTitle, ShouldAutoSize
{
    protected string $year;
    protected string $month;

    public function __construct(string $year, string $month)
    {
        $this->year = $year;
        $this->month = $month;
    }

    public function collection()
    {
        return DefectItem::where('year', $this->year)
            ->when($this->month, fn($q) => $q->where('month', $this->month))
            ->orderByDesc('defect_date')
            ->get()
            ->map(fn($d) => [
                $d->defect_date,
                $d->month,
                $d->lot_id,
                $d->rew_id,
                $d->paper_type,
                $d->gsm,
                $d->width,
                $d->reason,
                $d->category,
                $d->keterangan,
            ]);
    }

    public function headings(): array
    {
        return ['Tanggal', 'Bulan', 'Lot ID', 'Rew ID', 'Paper Type', 'GSM', 'Width', 'Alasan', 'Kategori', 'Keterangan'];
    }

    public function styles(Worksheet $sheet): void
    {
        $sheet->getStyle('A1:J1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 11, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '991B1B']],
        ]);

        $sheet->freezePane('A2');
    }

    public function title(): string
    {
        return 'Defects';
    }
}

class SummaryPaperTypeSheet implements FromCollection, WithHeadings, WithStyles, WithTitle, ShouldAutoSize
{
    protected string $year;
    protected string $month;

    public function __construct(string $year, string $month)
    {
        $this->year = $year;
        $this->month = $month;
    }

    public function collection()
    {
        $defects = DefectItem::selectRaw("paper_type, COUNT(*) as defect_count")
            ->where('year', $this->year)
            ->when($this->month, fn($q) => $q->where('month', $this->month))
            ->whereNotNull('paper_type')
            ->groupBy('paper_type')
            ->orderByDesc('defect_count')
            ->get();

        $totalRolls = RollItem::count();
        $rollByPaper = RollItem::selectRaw("paper_type, COUNT(*) as roll_count")
            ->whereNotNull('paper_type')
            ->groupBy('paper_type')
            ->get()
            ->keyBy('paper_type');

        $totalDefects = $defects->sum('defect_count');

        return $defects->map(function ($d) use ($rollByPaper, $totalRolls, $totalDefects) {
            $rollCount = $rollByPaper[$d->paper_type]->roll_count ?? 0;
            $rate = $rollCount > 0 ? round(($d->defect_count / $rollCount) * 100, 2) : 0;
            $pctTotal = $totalDefects > 0 ? round(($d->defect_count / $totalDefects) * 100, 1) : 0;

            return [
                $d->paper_type,
                $rollCount,
                $d->defect_count,
                $rate . '%',
                $pctTotal . '%',
            ];
        });
    }

    public function headings(): array
    {
        return ['Paper Type', 'Total Rolls', 'Jumlah Defect', 'Defect Rate', '% dari Total Defect'];
    }

    public function styles(Worksheet $sheet): void
    {
        $sheet->getStyle('A1:E1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 11, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '7C3AED']],
        ]);

        $sheet->freezePane('A2');
    }

    public function title(): string
    {
        return 'Paper Type';
    }
}
