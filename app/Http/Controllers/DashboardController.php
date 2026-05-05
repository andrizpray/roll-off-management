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

        // Paper type distribution — parse from description since paper_type column is mostly NULL
        $paperTypeStats = $this->parseDescriptionStats('paper_type');

        // Defect by reason
        $defectReasonStats = DefectItem::selectRaw("reason, COUNT(*) as count")
            ->whereNotNull('reason')->groupBy('reason')->orderByDesc('count')->limit(10)->get();

        // GSM distribution — parse from description since gsm column is mostly NULL
        $gsmStats = $this->parseDescriptionStats('gsm');

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
        $type = $request->input('type'); // 'no_location', 'recent_defects', or 'all'
        $referenceId = $request->input('reference_id'); // specific item, or null = mark all of type

        $service = new NotificationService();

        // "all" = mark every type
        if ($type === 'all' || (!$type && !$referenceId)) {
            $types = ['no_location', 'recent_defects'];
            foreach ($types as $t) {
                $items = $t === 'no_location'
                    ? $service->getAllItemsWithoutLocationRaw()
                    : $service->getAllRecentDefectsRaw();
                foreach ($items as $item) {
                    NotificationRead::firstOrCreate([
                        'type' => $t,
                        'reference_id' => $item,
                    ]);
                }
            }
            return response()->json($service->getNotifications());
        }

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
            $items = $type === 'no_location'
                ? $service->getAllItemsWithoutLocationRaw()
                : $service->getAllRecentDefectsRaw();

            foreach ($items as $item) {
                NotificationRead::firstOrCreate([
                    'type' => $type,
                    'reference_id' => $item,
                ]);
            }
        }

        // Return updated counts
        return response()->json($service->getNotifications());
    }

    /**
     * Parse descriptions from roll_items and return distribution stats.
     * PHP-based parsing using the same logic as DefectItemController::parseDescription.
     *
     * @param string $field 'paper_type' or 'gsm'
     * @return \Illuminate\Support\Collection
     */
    private function parseDescriptionStats(string $field): \Illuminate\Support\Collection
    {
        // Get distinct descriptions to avoid processing 17k+ rows
        $descriptions = DB::table('roll_items')
            ->selectRaw('description, COUNT(*) as cnt')
            ->whereNotNull('description')
            ->where('description', '!=', '')
            ->where('description', '!=', '-')
            ->groupBy('description')
            ->get();

        $stats = [];

        foreach ($descriptions as $row) {
            $parsed = $this->parseDescriptionForDashboard($row->description);

            if ($field === 'paper_type' && !empty($parsed['paper_type'])) {
                $key = $parsed['paper_type'];
                $stats[$key] = ($stats[$key] ?? 0) + (int) $row->cnt;
            } elseif ($field === 'gsm' && !empty($parsed['gsm'])) {
                $key = (string) $parsed['gsm'];
                $stats[$key] = ($stats[$key] ?? 0) + (int) $row->cnt;
            }
        }

        arsort($stats);
        $stats = array_slice($stats, 0, 10, true);

        if ($field === 'paper_type') {
            return collect($stats)->map(fn($count, $label) => (object) [
                'paper_type' => $label,
                'count' => $count,
            ])->values();
        }

        return collect($stats)->map(fn($count, $label) => (object) [
            'gsm' => $label,
            'count' => $count,
        ])->values();
    }

    /**
     * Parse a single description string (same logic as DefectItemController).
     */
    private function parseDescriptionForDashboard(string $description): array
    {
        $result = ['paper_type' => null, 'gsm' => null];
        $desc = trim($description);

        // Extract plybond: E followed by digits
        $desc = preg_replace('/\bE\d{2,4}\b/i', ' ', $desc);

        // Handle "350g" format
        if (preg_match('/(\d{2,4})g\b/i', $desc, $mg)) {
            $result['gsm'] = (int) $mg[1];
            $desc = preg_replace('/\d{2,4}g\b/i', ' ', $desc);
        }

        // Clean up
        $desc = preg_replace('/\s+/', ' ', trim($desc));

        // Extract paper_type + gsm: "B KRAFT BK125 690"
        if (preg_match('/^(.*?)\s*([A-Za-z]+)(\d{2,4})\s*(\d{0,4}?)\s*$/i', $desc, $m)) {
            $prefix = trim($m[1]);
            $code = $m[2];
            $gsm = (int) $m[3];

            $result['paper_type'] = ($prefix !== '' ? $prefix . ' ' : '') . $code;
            if ($result['gsm'] === null) {
                $result['gsm'] = $gsm;
            }
        }

        return $result;
    }
}
