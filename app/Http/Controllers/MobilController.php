<?php

namespace App\Http\Controllers;

use App\Models\DeliveryOrder;
use App\Models\DoAssignment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class MobilController extends Controller
{
    /**
     * List all mobil IDs that have assignments, with DO counts.
     */
    public function index(Request $request)
    {
        // Get distinct mobil_id values from assignments
        $mobilIds = DoAssignment::distinct()
            ->whereNotNull('mobil_id')
            ->pluck('mobil_id')
            ->filter()
            ->sort()
            ->values();

        $mobilStats = [];
        foreach ($mobilIds as $mobilId) {
            $assignments = DoAssignment::where('mobil_id', $mobilId)
                ->with('deliveryOrder')
                ->orderByDesc('assigned_date')
                ->get();

            $activeCount = $assignments->filter(function ($a) {
                return $a->deliveryOrder && $a->deliveryOrder->status === 'in_transit';
            })->count();

            $mobilStats[] = [
                'mobil_id'      => $mobilId,
                'active_count'  => $activeCount,
                'total_assignments' => $assignments->count(),
                'last_assignment'   => $assignments->first(),
            ];
        }

        return view('mobil.index', compact('mobilStats'));
    }

    /**
     * Show details of a specific mobil + its DO assignments.
     */
    public function show(string $mobilId)
    {
        $assignments = DoAssignment::where('mobil_id', $mobilId)
            ->with('deliveryOrder.items')
            ->orderByDesc('assigned_date')
            ->paginate(30);

        return view('mobil.show', compact('mobilId', 'assignments'));
    }

    /**
     * Remove a DO assignment from a mobil.
     * Rolls back DO status to status_before.
     */
    public function removeDo(string $mobilId, int $doId)
    {
        $assignment = DoAssignment::where('mobil_id', $mobilId)
            ->where('delivery_order_id', $doId)
            ->latest('id')
            ->first();

        if (!$assignment) {
            abort(404, 'Assignment tidak ditemukan.');
        }

        $do = DeliveryOrder::find($doId);

        // ── Bug fix: guard against final statuses ──
        if ($do && in_array($do->status, ['delivered', 'cancelled'])) {
            abort(422, 'DO dengan status final tidak dapat dihapus dari kendaraan.');
        }

        // ── Bug fix: rollback to status_before, not always "confirmed" ──
        $previousStatus = $assignment->status_before;

        $assignment->delete();

        if ($do && $do->assignments()->count() === 0) {
            $do->update(['status' => $previousStatus]);
            Cache::forget('unassigned_do_count');
        }

        return back()->with('success', 'DO berhasil dihapus dari kendaraan.');
    }
}