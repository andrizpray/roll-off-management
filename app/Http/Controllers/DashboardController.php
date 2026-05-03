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

        // Lokasi rekap distribution (computed current_location)
        // We do it via raw SQL checking columns in order
        $locationRekap = DB::select("
            SELECT
                COALESCE(NULLIF(so_desember,'-'), NULLIF(receiving_2026,'-'), NULLIF(so_maret_2026,'-'), NULLIF(pic_2026,'-'), NULLIF(rcv_cnv_2026,'-'), NULLIF(so_september,'-')) as lokasi,
                COUNT(*) as count
            FROM roll_items
            WHERE COALESCE(NULLIF(so_desember,'-'), NULLIF(receiving_2026,'-'), NULLIF(so_maret_2026,'-'), NULLIF(pic_2026,'-'), NULLIF(rcv_cnv_2026,'-'), NULLIF(so_september,'-')) IS NOT NULL
            GROUP BY lokasi
            ORDER BY count DESC
            LIMIT 15
        ");

        // Items with no location
        $noLocationCount = DB::selectOne("
            SELECT COUNT(*) as cnt FROM roll_items
            WHERE COALESCE(NULLIF(so_desember,'-'), NULLIF(receiving_2026,'-'), NULLIF(so_maret_2026,'-'), NULLIF(pic_2026,'-'), NULLIF(rcv_cnv_2026,'-'), NULLIF(so_september,'-')) IS NULL
        ")->cnt;

        // Paper type distribution
        $paperTypeStats = RollItem::selectRaw("paper_type, COUNT(*) as count")
            ->whereNotNull('paper_type')->where('paper_type', '!=', '')
            ->groupBy('paper_type')->orderByDesc('count')->limit(10)->get();

        // Defect by reason
        $defectReasonStats = DefectItem::selectRaw("reason, COUNT(*) as count")
            ->whereNotNull('reason')->groupBy('reason')->orderByDesc('count')->limit(10)->get();

        // GSM distribution
        $gsmStats = RollItem::selectRaw("gsm, COUNT(*) as count")
            ->whereNotNull('gsm')->where('gsm', '!=', '')
            ->groupBy('gsm')->orderByDesc('count')->limit(10)->get();

        // Status breakdown
        $statusStats = RollItem::selectRaw("status_barang, COUNT(*) as count")
            ->whereNotNull('status_barang')->where('status_barang', '!=', '-')
            ->groupBy('status_barang')->orderByDesc('count')->limit(10)->get();

        return view('dashboard', compact(
            'totalRolls', 'totalDefects', 'locationRekap', 'noLocationCount',
            'paperTypeStats', 'defectReasonStats', 'gsmStats', 'statusStats'
        ));
    }
}
