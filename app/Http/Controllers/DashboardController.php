<?php

namespace App\Http\Controllers;

use App\Models\DefectItem;
use App\Models\RollItem;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Summary stats
        $totalRolls = RollItem::count();
        $totalDefects = DefectItem::count();
        $locations = RollItem::select('location_id')
            ->distinct()
            ->whereNotNull('location_id')
            ->pluck('location_id');

        // Per location counts
        $locationStats = RollItem::selectRaw("location_id, COUNT(*) as count")
            ->whereNotNull('location_id')
            ->groupBy('location_id')
            ->orderByDesc('count')
            ->get();

        // Paper type distribution
        $paperTypeStats = RollItem::selectRaw("paper_type, COUNT(*) as count")
            ->whereNotNull('paper_type')
            ->groupBy('paper_type')
            ->orderByDesc('count')
            ->limit(10)
            ->get();

        // Defect stats by reason
        $defectReasonStats = DefectItem::selectRaw("reason, COUNT(*) as count")
            ->whereNotNull('reason')
            ->groupBy('reason')
            ->orderByDesc('count')
            ->limit(10)
            ->get();

        // Defect stats by year
        $defectByYear = DefectItem::selectRaw("year, COUNT(*) as count")
            ->groupBy('year')
            ->orderBy('year')
            ->get()
            ->keyBy('year');

        // GSM distribution (top 10)
        $gsmStats = RollItem::selectRaw("gsm, COUNT(*) as count")
            ->whereNotNull('gsm')
            ->groupBy('gsm')
            ->orderByDesc('count')
            ->limit(10)
            ->get();

        // Status breakdown
        $statusStats = RollItem::selectRaw("status_barang, COUNT(*) as count")
            ->whereNotNull('status_barang')
            ->where('status_barang', '!=', '-')
            ->groupBy('status_barang')
            ->orderByDesc('count')
            ->limit(10)
            ->get();

        return view('dashboard', compact(
            'totalRolls', 'totalDefects', 'locationStats',
            'paperTypeStats', 'defectReasonStats', 'defectByYear',
            'gsmStats', 'statusStats'
        ));
    }
}
