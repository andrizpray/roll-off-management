<?php

namespace App\Http\Controllers;

use App\Models\DefectItem;
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

        // Filter: Month (2025 data only)
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

        return view('defects.index', compact(
            'defects', 'reasons', 'paperTypes', 'months', 'years',
            'totalDefects', 'defect2025', 'defect2026'
        ));
    }
}
