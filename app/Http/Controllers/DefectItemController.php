<?php

namespace App\Http\Controllers;

use App\Models\DefectItem;
use App\Models\RollItem;
use Illuminate\Http\Request;

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
}
