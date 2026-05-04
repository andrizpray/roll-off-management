<?php

namespace App\Http\Controllers;

use App\Models\DefectItem;
use App\Models\NotificationRead;
use App\Models\RollItem;
use App\Services\NotificationService;
use Illuminate\Http\Request;
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

    public function notifications()
    {
        $service = new NotificationService();
        return response()->json($service->getNotifications());
    }

    public function notificationsPage()
    {
        $service = new NotificationService();
        $data = $service->getAllNotifications();
        return view('notifications.index', $data);
    }

    public function markAsRead(Request $request)
    {
        $type = $request->input('type'); // 'no_location', 'recent_defects'
        $referenceId = $request->input('reference_id'); // specific item, or null = mark all of type

        if (!$type || !in_array($type, ['no_location', 'recent_defects'])) {
            return response()->json(['error' => 'Invalid type'], 400);
        }

        if ($referenceId) {
            // Mark single item
            NotificationRead::firstOrCreate([
                'type' => $type,
                'reference_id' => $referenceId,
            ]);
        } else {
            // Mark all items of this type as read
            $service = new NotificationService();
            $items = $type === 'no_location'
                ? $service->getAllItemsWithoutLocationRaw()
                : $service->getAllRecentDefectsRaw();

            foreach ($items as $item) {
                NotificationRead::firstOrCreate([
                    'type' => $type,
                    'reference_id' => $item->id,
                ]);
            }
        }

        // Return updated counts
        return response()->json($service->getNotifications());
    }
}
