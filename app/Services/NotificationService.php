<?php

namespace App\Services;

use App\Models\DefectItem;
use App\Models\RollItem;
use Illuminate\Support\Facades\DB;

class NotificationService
{
    /**
     * Get all notifications summary (for topbar badge + panel).
     * Returns array with counts and sample items.
     */
    public function getNotifications(): array
    {
        $noLocation = $this->getItemsWithoutLocation();
        $recentDefects = $this->getRecentDefects();

        return [
            'total_count' => $noLocation['count'] + $recentDefects['count'],
            'no_location' => $noLocation,
            'recent_defects' => $recentDefects,
        ];
    }

    /**
     * Get all notifications without limit (for full page view).
     */
    public function getAllNotifications(): array
    {
        $noLocation = $this->getAllItemsWithoutLocation();
        $recentDefects = $this->getAllRecentDefects();

        return [
            'total_count' => $noLocation['count'] + $recentDefects['count'],
            'no_location' => $noLocation,
            'recent_defects' => $recentDefects,
        ];
    }

    /**
     * Items without any location (all tracking columns empty/null/'-').
     */
    private function getItemsWithoutLocation(): array
    {
        $items = RollItem::whereRaw("
            COALESCE(NULLIF(so_desember,'-'), NULLIF(receiving_2026,'-'), NULLIF(so_maret_2026,'-'), NULLIF(pic_2026,'-'), NULLIF(rcv_cnv_2026,'-'), NULLIF(so_september,'-')) IS NULL
        ")
            ->select('id', 'lot_id', 'paper_type', 'gsm', 'width', 'created_at')
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();

        return [
            'count' => RollItem::whereRaw("
                COALESCE(NULLIF(so_desember,'-'), NULLIF(receiving_2026,'-'), NULLIF(so_maret_2026,'-'), NULLIF(pic_2026,'-'), NULLIF(rcv_cnv_2026,'-'), NULLIF(so_september,'-')) IS NULL
            ")->count(),
            'items' => $items,
        ];
    }

    /**
     * All items without location (no limit).
     */
    private function getAllItemsWithoutLocation(): array
    {
        $items = RollItem::whereRaw("
            COALESCE(NULLIF(so_desember,'-'), NULLIF(receiving_2026,'-'), NULLIF(so_maret_2026,'-'), NULLIF(pic_2026,'-'), NULLIF(rcv_cnv_2026,'-'), NULLIF(so_september,'-')) IS NULL
        ")
            ->select('id', 'lot_id', 'paper_type', 'gsm', 'width', 'created_at')
            ->orderByDesc('created_at')
            ->get();

        return [
            'count' => $items->count(),
            'items' => $items,
        ];
    }

    /**
     * Defect items added in the last 7 days.
     */
    private function getRecentDefects(): array
    {
        $items = DefectItem::where('created_at', '>=', now()->subDays(7))
            ->select('id', 'lot_id', 'year', 'reason', 'paper_type', 'gsm', 'created_at')
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();

        return [
            'count' => DefectItem::where('created_at', '>=', now()->subDays(7))->count(),
            'items' => $items,
        ];
    }

    /**
     * All defect items in the last 7 days (no limit).
     */
    private function getAllRecentDefects(): array
    {
        $items = DefectItem::where('created_at', '>=', now()->subDays(7))
            ->select('id', 'lot_id', 'year', 'reason', 'paper_type', 'gsm', 'created_at')
            ->orderByDesc('created_at')
            ->get();

        return [
            'count' => $items->count(),
            'items' => $items,
        ];
    }
}
