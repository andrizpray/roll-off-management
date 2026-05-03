<?php

namespace App\Http\Controllers;

use App\Models\DefectItem;
use App\Models\RollItem;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $totalRolls = RollItem::count();
        $totalDefects = DefectItem::count();

        // Location stats: receiving_2026
        $locationStats = RollItem::selectRaw("receiving_2026, COUNT(*) as count")
            ->whereNotNull('receiving_2026')
            ->where('receiving_2026', '!=', '-')
            ->groupBy('receiving_2026')
            ->orderByDesc('count')
            ->take(15)
            ->get();

        // Paper type distribution
        $paperTypeStats = RollItem::selectRaw("paper_type, COUNT(*) as count")
            ->whereNotNull('paper_type')
            ->where('paper_type', '!=', '')
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

        // GSM distribution
        $gsmStats = RollItem::selectRaw("gsm, COUNT(*) as count")
            ->whereNotNull('gsm')
            ->where('gsm', '!=', '')
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

        // SO Desember stats
        $soDesemberCount = RollItem::whereNotNull('so_desember')->where('so_desember', '!=', '-')->count();
        // SO Maret 2026 stats
        $soMaretCount = RollItem::whereNotNull('so_maret_2026')->where('so_maret_2026', '!=', '-')->count();
        // Receiving 2026 count
        $receivingCount = RollItem::whereNotNull('receiving_2026')->where('receiving_2026', '!=', '-')->count();
        // PIC 2026 count
        $picCount = RollItem::whereNotNull('pic_2026')->where('pic_2026', '!=', '-')->count();

        return view('dashboard', compact(
            'totalRolls', 'totalDefects', 'locationStats',
            'paperTypeStats', 'defectReasonStats',
            'gsmStats', 'statusStats',
            'soDesemberCount', 'soMaretCount', 'receivingCount', 'picCount'
        ));
    }
}
