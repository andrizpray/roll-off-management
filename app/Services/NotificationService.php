<?php

namespace App\Services;

use App\Models\DefectItem;
use App\Models\NotificationRead;
use App\Models\RollItem;

class NotificationService
{
    /**
     * Get notifications summary for topbar badge + panel (unread only, max 10 each).
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
     * Get all notifications for full page (unread first, then read, no limit).
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
     * Get raw IDs for mark-all-read (used by controller).
     */
    public function getAllItemsWithoutLocationRaw()
    {
        return RollItem::whereRaw("
            COALESCE(NULLIF(so_desember,'-'), NULLIF(receiving_2026,'-'), NULLIF(so_maret_2026,'-'), NULLIF(pic_2026,'-'), NULLIF(rcv_cnv_2026,'-'), NULLIF(so_september,'-')) IS NULL
        ")->select('id')->pluck('id');
    }

    public function getAllRecentDefectsRaw()
    {
        return DefectItem::where('created_at', '>=', now()->subDays(7))->select('id')->pluck('id');
    }

    /**
     * Base query for items without location, excluding read ones.
     */
    private function noLocationBaseQuery()
    {
        $readIds = NotificationRead::where('type', 'no_location')->pluck('reference_id')->toArray();

        return RollItem::whereRaw("
            COALESCE(NULLIF(so_desember,'-'), NULLIF(receiving_2026,'-'), NULLIF(so_maret_2026,'-'), NULLIF(pic_2026,'-'), NULLIF(rcv_cnv_2026,'-'), NULLIF(so_september,'-')) IS NULL
        ")
            ->select('id', 'lot_id', 'paper_type', 'gsm', 'width', 'created_at')
            ->when($readIds, fn($q) => $q->whereNotIn('id', $readIds))
            ->orderByDesc('created_at');
    }

    /**
     * Base query for recent defects, excluding read ones.
     */
    private function recentDefectsBaseQuery()
    {
        $readIds = NotificationRead::where('type', 'recent_defects')->pluck('reference_id')->toArray();

        return DefectItem::where('created_at', '>=', now()->subDays(7))
            ->select('id', 'lot_id', 'year', 'reason', 'paper_type', 'gsm', 'created_at')
            ->when($readIds, fn($q) => $q->whereNotIn('id', $readIds))
            ->orderByDesc('created_at');
    }

    /**
     * Unread items without location (max 10 for dropdown).
     */
    private function getItemsWithoutLocation(): array
    {
        $unread = (clone $this->noLocationBaseQuery())->limit(10)->get();

        return [
            'count' => $this->noLocationBaseQuery()->count(),
            'items' => $unread,
        ];
    }

    /**
     * All items without location for full page (include read items).
     */
    private function getAllItemsWithoutLocation(): array
    {
        $readIds = NotificationRead::where('type', 'no_location')->pluck('reference_id')->toArray();

        $allItems = RollItem::whereRaw("
            COALESCE(NULLIF(so_desember,'-'), NULLIF(receiving_2026,'-'), NULLIF(so_maret_2026,'-'), NULLIF(pic_2026,'-'), NULLIF(rcv_cnv_2026,'-'), NULLIF(so_september,'-')) IS NULL
        ")
            ->select('id', 'lot_id', 'paper_type', 'gsm', 'width', 'created_at')
            ->orderByDesc('created_at')
            ->get()
            ->map(function ($item) use ($readIds) {
                $item->is_read = in_array($item->id, $readIds);
                return $item;
            });

        $unreadCount = $allItems->filter(fn($i) => !$i->is_read)->count();

        return [
            'count' => $unreadCount,
            'items' => $allItems,
        ];
    }

    /**
     * Unread recent defects (max 10 for dropdown).
     */
    private function getRecentDefects(): array
    {
        $unread = (clone $this->recentDefectsBaseQuery())->limit(10)->get();

        return [
            'count' => $this->recentDefectsBaseQuery()->count(),
            'items' => $unread,
        ];
    }

    /**
     * All recent defects for full page (include read items).
     */
    private function getAllRecentDefects(): array
    {
        $readIds = NotificationRead::where('type', 'recent_defects')->pluck('reference_id')->toArray();

        $allItems = DefectItem::where('created_at', '>=', now()->subDays(7))
            ->select('id', 'lot_id', 'year', 'reason', 'paper_type', 'gsm', 'created_at')
            ->orderByDesc('created_at')
            ->get()
            ->map(function ($item) use ($readIds) {
                $item->is_read = in_array($item->id, $readIds);
                return $item;
            });

        $unreadCount = $allItems->filter(fn($i) => !$i->is_read)->count();

        return [
            'count' => $unreadCount,
            'items' => $allItems,
        ];
    }
}
